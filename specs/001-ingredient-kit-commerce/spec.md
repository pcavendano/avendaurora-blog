# Feature Specification: Ingredient Kit Commerce

**Feature Branch**: `001-ingredient-kit-commerce`

**Created**: 2026-09-01

**Status**: Draft

**Input**: User description: "The e-commerce layer — Snipcart integration, ingredient kits, /shop, and cart. This is the last substantially unbuilt part of the site per architecture-plan.md §9 and §12 phase 4."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Find where to buy a recipe's ingredients (Priority: P1)

A visitor reads a recipe and wants to know where they can actually buy what it calls for.
The recipe page shows each partner store that stocks the recipe's ingredients, how complete
that store's coverage is, which items it is missing, and a link out to that store. The chef
earns a referral commission when the visitor buys.

**Why this priority**: This is the smallest slice that delivers the core promise of the site
— recipes tied to real, local sourcing — and it monetizes without a cart, a checkout, an
inventory, or a payment processor. It also stands alone: if nothing else in this feature
ships, the site is still meaningfully more useful than it is today.

**Independent Test**: Publish one recipe whose ingredients are mapped to catalogue entries,
and one partner store that stocks some of them. Load the recipe and confirm the store appears
with an accurate coverage figure, a correct missing-items list, and a working outbound link
carrying the store's referral code.

**Acceptance Scenarios**:

1. **Given** a recipe with 8 mapped ingredients and a store that stocks 8 of them, **When** a visitor opens the recipe, **Then** the store is listed at 100% coverage with no missing items.
2. **Given** the same recipe and a store that stocks 6 of the 8, **When** a visitor opens the recipe, **Then** the store is listed at 75% coverage and names the 2 missing ingredients.
3. **Given** two partner stores with different coverage, **When** a visitor opens the recipe, **Then** stores are ordered with the most complete first.
4. **Given** a store with a referral code configured, **When** a visitor follows its link, **Then** the destination URL carries that store's referral attribution.
5. **Given** a recipe whose ingredients are not yet mapped to the catalogue, **When** a visitor opens it, **Then** no sourcing section is shown and the rest of the recipe renders unchanged.
6. **Given** a visitor browsing in English or French, **When** the sourcing section renders, **Then** all of its labels appear in that language.

---

### User Story 2 - Build and take away a shopping list (Priority: P2)

A visitor wants the recipe's ingredients as a list they can act on: tick off what they already
have at home, see a running estimated total for what's left, and carry the remainder to a
store or keep it for later. A signed-in visitor can save the list to their account and
combine several recipes into one trip.

**Why this priority**: It converts browsing into an actual shopping trip and is the step that
makes the referral link in P1 worth following. It depends on P1's ingredient-to-store mapping
but adds no payment surface. It also builds directly on the account and favorites features
that already exist.

**Independent Test**: Open a mapped recipe, deselect two ingredients, confirm the estimated
total drops accordingly, save the list while signed in, sign out and back in, and confirm the
list is still there with the same selections.

**Acceptance Scenarios**:

1. **Given** a mapped recipe, **When** a visitor opens the shopping list, **Then** every non-optional ingredient is pre-selected and every optional one is not.
2. **Given** a shopping list with items selected, **When** the visitor deselects an item, **Then** the estimated total decreases by that item's estimated price.
3. **Given** an item with no known price, **When** it is selected, **Then** it is included in the list and the total is presented as a partial estimate rather than a firm figure.
4. **Given** a signed-in visitor, **When** they save a shopping list and return in a later session, **Then** the list and its selections are restored.
5. **Given** a signed-out visitor, **When** they build a list and are invited to save it, **Then** they are prompted to sign in and the list survives that sign-in.
6. **Given** a saved list drawn from two recipes that both call for the same ingredient, **When** the list is displayed, **Then** that ingredient appears once with the combined quantity.

---

### User Story 3 - Buy the chef's own products (Priority: P3)

A visitor buys something the chef sells directly — a jar of house salsa, a spice blend, a
recipe collection as a download — adds it to a cart, and pays. A signed-in buyer can find
their order and re-download digital purchases from their account.

**Why this priority**: This is the first slice that introduces payment, tax, fulfilment and
refunds, so it carries by far the most operational risk and legal surface. It is genuinely
independent of P1 and P2 — it needs no ingredient mapping at all — which is why it can be
built last without blocking anything.

**Independent Test**: Publish one physical product and one digital product, complete a real
test purchase of each, and confirm the order is recorded, the digital item is downloadable
from the account area, and the download link is not usable by a signed-out stranger.

**Acceptance Scenarios**:

1. **Given** a published product, **When** a visitor adds it to the cart, **Then** the cart shows the item, quantity, and running total, and survives navigation to another page.
2. **Given** a cart with items, **When** the visitor checks out and pays successfully, **Then** they receive an order confirmation and the order appears in their account.
3. **Given** a completed purchase of a digital product, **When** the buyer opens their account, **Then** the file is downloadable there.
4. **Given** a digital download belonging to another buyer, **When** someone else requests it, **Then** access is refused.
5. **Given** a product that is out of stock, **When** a visitor views it, **Then** it cannot be added to the cart and is clearly marked unavailable.
6. **Given** a payment that fails or is abandoned, **When** the visitor returns to the site, **Then** no order is recorded and the cart contents are preserved.
7. **Given** a visitor browsing in any of the three site languages, **When** they view a product and check out, **Then** product names, descriptions, and prices are presented in that language and in a single stated currency.

---

### Edge Cases

- A recipe calls for an ingredient no partner store stocks — the shortfall is named plainly rather than silently omitted, so the visitor is not sent on a wasted trip.
- Every partner store has low coverage for a recipe (below a useful threshold) — the site says the recipe is not well covered rather than presenting a misleading "best" option.
- A store's referral link or code is missing or expired — the store still appears with its coverage, linking to its plain public site, and no broken referral URL is emitted.
- Estimated prices are stale — prices are consistently framed as estimates with a visible "as of" date, never as the amount the visitor will be charged.
- A partner store is unpublished or deleted while shopping lists reference it — saved lists degrade to plain ingredient lists instead of erroring.
- An ingredient page is deleted while recipes reference it — affected recipes fall back to their free-text ingredient names.
- A recipe is translated into only one language — the sourcing and list features work in that language and do not emit raw translation keys in the others.
- Two currencies or two countries' stores are configured at once — out of scope for this feature; see Assumptions.
- A visitor's saved shopping list grows very large (dozens of recipes) — the list remains usable and the account page remains responsive.
- Checkout succeeds but the buyer closes the browser before returning to the site — the order is still recorded and still appears in their account.

## Requirements *(mandatory)*

### Functional Requirements

**Ingredient catalogue and sourcing (US1)**

- **FR-001**: The system MUST let the chef record, for each partner store, which catalogue ingredients that store stocks.
- **FR-002**: The system MUST let the chef record an estimated price and a price-observed date for each ingredient at each store that stocks it.
- **FR-003**: The system MUST compute, for a given recipe and a given partner store, the proportion of that recipe's ingredients the store stocks.
- **FR-004**: The system MUST display, on a recipe, each partner store with its coverage proportion and the names of the ingredients it does not stock, ordered by coverage descending.
- **FR-005**: The system MUST exclude ingredients marked optional from the coverage calculation while still showing them in the ingredient list.
- **FR-006**: The system MUST link out to each partner store using that store's configured referral attribution when one is present, and to its plain public address when one is not.
- **FR-007**: The system MUST omit the sourcing section entirely for recipes whose ingredients are not mapped to the catalogue, rather than showing an empty or zero-coverage section.
- **FR-008**: The system MUST present all sourcing labels and messages in the visitor's active language.

**Shopping lists (US2)**

- **FR-009**: Visitors MUST be able to select and deselect individual ingredients within a recipe's shopping list, with non-optional ingredients selected by default.
- **FR-010**: The system MUST show a running estimated total for the selected ingredients at a chosen store, labelled as an estimate with its observation date.
- **FR-011**: The system MUST include ingredients with no known price in the list and indicate that the total is incomplete.
- **FR-012**: Signed-in visitors MUST be able to save a shopping list to their account, retrieve it in a later session, and delete it.
- **FR-013**: The system MUST preserve a shopping list built while signed out across the visitor signing in.
- **FR-014**: The system MUST combine identical ingredients drawn from multiple recipes into a single line with a summed quantity when they share a unit, and list them separately when they do not.
- **FR-015**: Visitors MUST be able to take a shopping list to the chosen store's site with referral attribution applied.

**Direct sales (US3)**

- **FR-016**: The chef MUST be able to publish products of at least three kinds: physical goods, digital downloads, and recipe collections, each with a name, description, images, and price in all three site languages.
- **FR-017**: Visitors MUST be able to add products to a cart, change quantities, remove items, and see a running total, with cart contents persisting across page navigation within a session.
- **FR-018**: The system MUST take payment and issue an order confirmation on success.
- **FR-019**: The system MUST record every completed order and make it visible to the buyer in their account.
- **FR-020**: The system MUST make digital purchases downloadable from the buyer's account and MUST refuse those downloads to anyone else.
- **FR-021**: The system MUST prevent out-of-stock products from being added to the cart and mark them unavailable.
- **FR-022**: The system MUST record no order and preserve cart contents when a payment fails or is abandoned.
- **FR-023**: The system MUST display product information in the visitor's active language and all prices in a single stated currency.
- **FR-024**: The system MUST NOT store payment card details.

**Across the feature**

- **FR-025**: The system MUST keep all commerce content editable by the chef through the existing admin interface without developer involvement.
- **FR-026**: The system MUST continue to serve recipes, ingredients, stores, search, and accounts unchanged for visitors who never touch the commerce features.

### Key Entities

- **Catalogue Ingredient**: A canonical ingredient the site knows about, with a name in each language and any educational content. Referenced by recipes and stocked by stores. Today only two exist against roughly 5,400 distinct free-text ingredient strings in the recipe corpus.
- **Recipe Ingredient**: One line of a recipe — quantity, unit, preparation note, optional flag — which may reference a Catalogue Ingredient. Today none of the 628 recipes carry that reference.
- **Partner Store**: A real shop with locations, a public address, referral attribution, and the set of Catalogue Ingredients it stocks with estimated prices and observation dates.
- **Kit Coverage**: The derived relationship between one Recipe and one Partner Store — proportion stocked, missing items, estimated total. Not stored; computed for display.
- **Shopping List**: A visitor's selection of ingredients, optionally drawn from several recipes and pinned to a chosen store, belonging to a signed-in account.
- **Product**: Something the chef sells directly — physical, digital, or a collection — with translated name, description, images, price, and availability.
- **Order**: A completed purchase belonging to a buyer, listing products, amounts, and any download entitlements it grants.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A visitor landing on a mapped recipe can identify where to buy its ingredients within 15 seconds, without scrolling past the ingredient list.
- **SC-002**: Coverage figures shown for a store match a manual audit of that store's stocked list for 100% of sampled recipes.
- **SC-003**: At least 80% of published recipes show at least one partner store at 70% coverage or better, once the catalogue and at least three stores are populated.
- **SC-004**: A visitor can go from opening a recipe to arriving at a store's site with a referral-attributed shopping list in under 60 seconds and no more than four interactions.
- **SC-005**: At least 30% of visitors who open a shopping list follow it through to a partner store.
- **SC-006**: A signed-in visitor's saved shopping lists survive sign-out and sign-in with zero data loss across 100% of trials.
- **SC-007**: A buyer can complete a purchase of a chef's product in under 3 minutes from first adding to cart.
- **SC-008**: 100% of completed purchases appear in the buyer's account within one minute of payment.
- **SC-009**: Zero digital downloads are accessible to anyone other than their purchaser.
- **SC-010**: Every visitor-facing string added by this feature is present in all three languages, with zero raw translation keys rendered in any of them.
- **SC-011**: Recipe pages that use no commerce features load no slower after this feature ships than before it.

## Assumptions

- **Kits are referrals, not inventory.** Ingredient kits send visitors to partner stores and earn commission; the chef does not stock, pick, or ship ingredients. This follows architecture-plan.md §9.3, which specifies the affiliate model explicitly, and §9.1, which lists ingredient kits as delivered by "link to store cart". Direct payment applies only to the chef's own products in US3.
- **Store fulfilment is out of scope.** Whether a partner store offers its own online cart, delivery, or pickup is that store's concern; this feature hands off at their door.
- **Estimated prices are chef-maintained and advisory.** Prices are entered by the chef, shown with an observation date, and never presented as the amount a visitor will be charged. There is no live price feed from any store.
- **The existing account system is reused.** Saved shopping lists, order history, and download entitlements attach to the accounts, login, and registration already implemented, in the same way favorites do.
- **The existing admin interface is reused.** Products, stores, stocked-ingredient lists, and prices are managed as ordinary site content, not through a separate commerce back office.
- **One currency and one market at launch.** Prices, tax treatment, and stores are all assumed to sit in a single market; multi-currency and cross-border tax are deferred.
- **A hosted payment provider handles checkout.** Card details never reach this site. architecture-plan.md §9.2 names Snipcart as the intended provider; this spec does not depend on that choice.
- **Ingredient mapping is a hard prerequisite for US1 and US2.** Coverage, missing-item lists, and price estimates are all impossible against free text. The corpus today has zero mapped ingredients and inconsistent units (2,833 blank, plus singular/plural variants and size words such as "medium" recorded as units). Whether closing that gap is part of this feature is Q1 below.
- **US3 has no such prerequisite** and can be built and shipped whether or not the mapping work has started.

## Outstanding Clarifications

The following materially change scope and are carried as open questions rather than guesses.

- **Q1 — Ingredient normalisation scope**: [NEEDS CLARIFICATION: Is normalising the recipe corpus — mapping ~5,400 free-text ingredient strings across 628 recipes onto a catalogue of ingredient pages, and cleaning the unit field — inside this feature, a separate feature that blocks it, or handled by only mapping a small curated subset of recipes at launch?]
- **Q2 — Market and currency**: [NEEDS CLARIFICATION: Which market do the partner stores and the chef's products sell into, and in what currency? This fixes tax treatment, shipping rules, and the plausibility of the partner-store model.]
- **Q3 — Physical fulfilment at launch**: [NEEDS CLARIFICATION: Does US3 ship with physical goods (stock counts, packing, shipping rates, returns) or digital-only downloads first, with physical products following later?]
