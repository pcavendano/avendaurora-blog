# CLAUDE.md

Guidance for Claude Code when working in this repository.

## What this is

**Avenda Aurora** — a multilingual (ES/EN/FR) Mexican cuisine blog and shop for a
personal chef, built on **Kirby CMS 4** (file-based, no database). Spanish is the
default language and the site's URLs, slugs, and Panel are Spanish-first
(`/recetas`, `/ingredientes`, `/tiendas`, `/cuenta`, `/buscar`).

The long-form spec lives in `architecture-plan.md`; setup and run instructions in
`SETUP.md`; a system diagram in `PRESENTATION.md`.

## Running it

```bash
composer install                 # installs getkirby/cms into /kirby + /vendor
php -S localhost:8000            # Kirby serves through index.php
```

There is **no test suite** and no build step for CSS or JS — the stylesheet and
scripts under `assets/` are handwritten and served directly. Verify changes by
loading the affected page.

Secrets go in `site/config/secrets.php` (gitignored) — copy
`site/config/secrets.example.php` and fill it in. `OPENAI_API_KEY` powers the
recipe importer.

## Layout

| Path | What's there |
|---|---|
| `index.php` | Kirby bootstrap; defines custom roots (note `storage/`) |
| `site/config/config.php` | Config **and all custom routes / JSON API endpoints** |
| `site/templates/*.php` | Page templates — one per blueprint of the same name |
| `site/blueprints/pages/*.yml` | Panel field definitions, paired 1:1 with templates |
| `site/blueprints/{blocks,files,users}/` | Block, file, and user-role blueprints |
| `site/snippets/` | Shared partials; `snippets/blocks/` pairs with `blueprints/blocks/` |
| `site/plugins/*/index.php` | Five custom plugins (below) |
| `site/languages/{es,en,fr}.php` | Locale config + `translations` key/value maps |
| `content/` | The actual site content — **tracked in git** |
| `assets/css/style.css` | The real stylesheet (design tokens; see `docs/design-guide.md`) |
| `assets/js/` | `app.js` and `recipe-importer.js` |
| `scripts/`, `scrape-simplehomeedit.py` | Python recipe ingestion pipeline |

`styles.css` in the repo root is ~289KB of scraped Serious Eats CSS kept as a
visual reference. **No template loads it** — never edit it expecting the site to
change. Site styling is `assets/css/style.css`.

## Content model

Content is flat text files, one per page per language:

```
content/1_recetas/taco-soup/recipe.es.txt
```

The filename stem is the template name; the numeric directory prefix sets Panel
sort order. Fields use Kirby's key/value format separated by `----`:

```
Title: Taco Soup

----

Category: sopas-caldos, platos-fuertes
```

628 recipes are already imported. Prefer scripted edits over hand-editing many
files, and mirror any new field into the matching blueprint or the Panel won't
show it.

## Custom plugins

All registered under the `avendaurora/` namespace.

- **taxonomy** — categories and countries are a structure field on the `recetas`
  page, not hardcoded. Read them through `Taxonomy::all()` / `Taxonomy::slugs()`;
  don't duplicate the lists.
- **better-search** — replaces Kirby's search component with AND-style token
  matching (the default ORs tokens, which is useless for Spanish stopwords).
- **page-stats** — JSON page-view counter under `storage/stats/`, `flock()`-guarded,
  skips admins and bots.
- **recipe-authors** — `page.create:after` hook stamping `created_by` on recipes.
- **recipe-importer** — AI-assisted import wizard; extracts structured recipe JSON
  from images and creates pages.

## Conventions that matter

**Writing to content or users requires impersonation.** Kirby blocks writes from
unprivileged contexts, so wrap them:

```php
$kirby->impersonate('kirby');
$page->update([...]);
$kirby->impersonate(null);
```

Always restore with `impersonate(null)` — leaking elevated context into the rest
of the request is a real bug.

**Custom endpoints live in `site/config/config.php`,** not in plugins — see
`api/ingredients` and `api/favorite/(:any)`. Follow the existing shape: guard
admin-only routes with `$user->role()->name() !== 'admin'`, return
`\Kirby\Http\Response::json(...)` with a real status code, and wrap the body in
try/catch returning `500` on `\Throwable`.

**User-facing strings are translated, never inlined.** Add the key to all three
of `site/languages/{es,en,fr}.php` and call `t('nav.recipes')`. A key present in
one language and missing in another renders the raw key.

**Fields shown to visitors need `translate: true`** in the blueprint.

**Images go through `snippets/responsive-image.php`** and the `thumbs.srcsets`
presets in config, not bare `<img src>`.

## Deployment

Push to `main` → `.github/workflows/deploy.yml` → SSH to the Ploi droplet, which
does `git reset --hard origin/main`, `composer install --no-dev`, and clears the
Kirby page and thumb caches.

The `git clean` step deliberately excludes `content` (along with `storage`,
`media`, `vendor`) so **recipes created through the live Panel survive deploys**.
Don't remove those exclusions.

Two things are dev-only in `site/config/config.php` and should be flipped before a
real launch: `debug => true` and `cache.pages.active => false`.

## Still unbuilt

Per `architecture-plan.md` §12, phases 1–3 are substantially done. Outstanding:

- E-commerce — Snipcart is specified but **not integrated anywhere yet**; ingredient
  kits, `/shop`, and cart are unbuilt.
- Ingredients encyclopedia — only `chipotle` and `jalapeno` exist under
  `content/2_ingredientes/`.
- Partner stores — only `la-mexicana` under `content/3_tiendas/`.
- SEO pass, full trilingual content review, production launch checklist.

Accounts, login/registration, and recipe favorites **are** implemented
(`site/templates/{login,register,account,profile}.php` plus the `api/favorite`
route), though they predate any test coverage — tread carefully when changing them.
