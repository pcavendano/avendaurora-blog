---
title: Avendaurora Design Guide
subject: Aurora Avendaño Avendaño — Personal Chef Brand
version: 0.1
last-updated: 2026-04-18
---

# Avendaurora Design Guide

A visual language for Aurora Avendaño Avendaño's personal chef brand. Inspired by the vibrancy of a Mexican mercado — marigold (cempasúchil), hibiscus (jamaica), talavera tile, papel picado — but executed with the restraint and editorial polish of a professional cookbook. Vibrant, but never childish. Warm, but never folkloric kitsch.

> **Spirit in one line:** *the joy of a Oaxacan market table photographed for Kinfolk.*

---

## 1. Brand Pillars

| Pillar | Translation in design |
|---|---|
| **Heritage** | Folk motifs (papel picado, talavera, sarape stripes) used as accents, never wallpaper |
| **Craft** | Editorial typography, generous whitespace, careful photography framing |
| **Calor humano** | Warm cream backgrounds, hand-written chef notes, organic shapes |
| **Profesionalismo** | Strong grid, deliberate hierarchy, restraint in animation |

Reference inspiration: querícavida.com (colour energy & layering), but tilted toward the editorial calm of *Bon Appétit*, *Cherry Bombe*, *Kinfolk Table*.

---

## 2. Colour System

### Core palette

| Token | Hex | Role |
|---|---|---|
| `--c-marigold` | `#ECA825` | Signature surface. Used full-bleed on hero sections and category pages. |
| `--c-hibiscus` | `#C8194A` | Primary accent. CTAs, key links, decorative underlines. |
| `--c-talavera` | `#1B7FA8` | Secondary accent. Info badges, meta strips, tile patterns. |
| `--c-carbon` | `#1C140E` | Body text, headings. Warm near-black, never `#000`. |
| `--c-maiz` | `#FBEFD8` | Cream surface. Default content background. |
| `--c-papel` | `#FFFAF0` | Lightest surface. Recipe long-form reading. |

### Supporting palette

| Token | Hex | Role |
|---|---|---|
| `--c-rosa-mexicano` | `#E94B8E` | Tertiary pink. Decorative ribbons, papel picado. |
| `--c-nopal` | `#4A6B2E` | Deep herb green. Botanical accents, success states. |
| `--c-comal` | `#6B4423` | Toasted brown. Captions, secondary text on cream. |
| `--c-cal` | `#F2EAD3` | Tinted off-white. Card surfaces over marigold. |
| `--c-charro` | `#2A1A14` | Deepest shadow. Footer, modal overlay base. |

### Usage rules

1. **One dominant hue per page section.** Marigold + hibiscus + talavera together is a *celebration*; on every page is *noise*. Lead with one, accent with one, whisper a third.
2. **Cream is the silence.** Long-form recipe content lives on `--c-maiz` or `--c-papel`. Reserve marigold for hero, category headers, and CTAs.
3. **Carbon, not black.** All text uses `--c-carbon`. Pure black flattens against marigold.
4. **Pairing matrix:**
   - Marigold bg → carbon text + hibiscus accent ✅
   - Hibiscus bg → maíz text + marigold accent ✅
   - Talavera bg → maíz text + rosa accent ✅
   - Marigold + hibiscus + talavera adjacent → only as decorative ribbon, never as content blocks

---

## 3. Typography

### Stack

| Role | Family | Source | Notes |
|---|---|---|---|
| **Display** | **Fraunces** | Google Fonts (variable) | Use `SOFT` axis at ~50, `opsz` at 144 for hero. Italics for recipe names. |
| **Sub-display / Editorial** | **Rozha One** | Google Fonts | Reserved for the wordmark and category badges only — high-contrast didone energy. |
| **Body** | **DM Sans** | Google Fonts (variable) | Body, UI, ingredient lists, captions. |
| **Hand / Chef voice** | **Caveat** | Google Fonts | *Sparingly.* Chef signature, margin notes, "hecho con cariño". |

> **Why this stack?** Fraunces brings cookbook warmth without being twee. DM Sans is geometric but humanist — disappears in the body and never fights Fraunces. Rozha and Caveat are flavor; if they appear more than twice per page, cut one.

### Type scale (Major Third, 1.250)

| Token | Size | Line-height | Use |
|---|---|---|---|
| `--t-display-xl` | `clamp(3.5rem, 8vw, 6rem)` (56–96px) | 0.95 | Home hero |
| `--t-display-lg` | `clamp(2.75rem, 5vw, 4rem)` (44–64px) | 1.05 | Recipe titles |
| `--t-display-md` | `2.5rem` (40px) | 1.1 | Section headings |
| `--t-display-sm` | `1.953rem` (~31px) | 1.2 | Sub-sections |
| `--t-h4` | `1.563rem` (~25px) | 1.3 | Card titles |
| `--t-h5` | `1.25rem` (20px) | 1.4 | Inline headings |
| `--t-body-lg` | `1.125rem` (18px) | 1.65 | Long-form intro paragraph |
| `--t-body` | `1rem` (16px) | 1.65 | Default body |
| `--t-body-sm` | `0.875rem` (14px) | 1.55 | Captions, meta |
| `--t-micro` | `0.75rem` (12px) | 1.4 | Tags, eyebrow labels (uppercase, tracked +0.12em) |

### Treatments

- **Eyebrow labels** (e.g. "ANTOJITOS · YUCATÁN"): DM Sans 12px, uppercase, letter-spacing 0.18em, hibiscus colour.
- **Recipe titles** (Fraunces): allow italic, allow ligatures (`font-feature-settings: "liga", "dlig"`).
- **Drop caps** in long-form: Fraunces, 4 lines tall, hibiscus colour, slight overhang.
- **Numerals** in ingredient lists: tabular (`font-variant-numeric: tabular-nums`).

---

## 4. Vertical Rhythm (spacing scale)

Base unit **8px**. Powers of 1.5/2 from there.

```
4   — hairline gaps
8   — inline padding
12  — small gaps
16  — default content gap
24  — paragraph gap, card padding
32  — section internal gap
48  — between subsections
64  — between content blocks
96  — between major sections
128 — hero ↔ first section
192 — page rest (rare; use for editorial breathing rooms)
```

### Section rhythm

- **Hero → first section:** 128px desktop / 80px mobile
- **Major sections:** 96px desktop / 64px mobile
- **Subsections inside a section:** 48px
- **Inside a card:** 24px
- **Recipe step ↔ recipe step:** 32px

### Line-length

- Long-form body: **62–72ch**
- Ingredient column: **28–34ch**
- Captions: **40ch max**

---

## 5. Horizontal Rhythm (grid)

### Container

- **Max content width:** 1280px
- **Editorial reading width:** 720px (centred)
- **Outer margins:** 24px mobile / 48px tablet / 80px desktop / `auto` on XL
- **Gutters:** 24px mobile / 32px desktop

### Columns

12-column flexible grid. Common compositions:

| Layout | Cols | Use |
|---|---|---|
| Editorial | 8 (text) + 4 (sidebar) | Recipe single page |
| Two-up | 6 + 6 | Story + portrait, ingredients + steps |
| Hero asymmetric | 5 (text) + 7 (image, overflow right) | Home hero |
| Card grid | 4 / 4 / 4 (or 3 / 3 / 3 / 3) | Recipes index |
| Full-bleed feature | 12 | Quote, decorative ribbon, large photo |

### Grid-breaking (intentional)

- Hero photos should **bleed off the right edge** by 5–8% on desktop.
- Decorative plates (circular images) should **overlap section boundaries** with negative margins of -64px.
- Image frames should rotate **-2° to -3°** (washi-tape feel). Never more than 4°.

---

## 6. Decorative Motifs

These are the "Mexican voice" of the system. Use sparingly, intentionally.

### Papel picado border (SVG)
A horizontal ribbon with semicircular cut-outs, used as a divider between major page sections. Default colour: rosa-mexicano on marigold, or marigold on cream. ~48px tall.

### Talavera tile (CSS-repeating)
Tiled blue-on-cream geometric pattern. Used as a footer band or behind category labels. Implement as a single SVG tile + `background-repeat`.

### Sarape stripes
Diagonal multi-colour stripe band: hibiscus / marigold / talavera / nopal / hibiscus. Used once per page max — usually as a thin (8px) accent under section headings.

### Washi-frame
Photos sit inside a 2px maíz stroke offset by 12px (like an old Polaroid). Optional rotation -2°. Box-shadow soft, warm: `0 24px 60px -20px rgba(28, 20, 14, 0.35)`.

### Hand-drawn rule
Section dividers use `stroke-dasharray: 2 6` on an SVG line, not a flat `<hr>`. Adds craft without being literal.

### Marigold petal scatter
Small SVG petals (4–8 per cluster) used as decorative anchors near section titles. Optional.

---

## 7. Component Inventory

### Buttons

- **Primary:** hibiscus bg, maíz text, 14px vertical / 28px horizontal padding, 4px border-radius, no shadow. Hover: shift +2px down, gain 1px carbon underline.
- **Secondary:** carbon outline 1.5px, transparent bg, carbon text. Hover: marigold bg.
- **Ghost link:** carbon text + 2px hibiscus underline that grows to full-width on hover.

### Cards (recipe)

- Cream surface, 24px padding, 4px corner radius (subtle).
- Image at top, 4:5 aspect, washi-frame treatment.
- Eyebrow tag (region/category) → Fraunces title → 1-line italic descriptor → meta strip.
- No drop shadow; lift via warm shadow on hover only.

### Meta strip (prep / total / ingredients / servings)

- Talavera-blue background ribbon, maíz text, all-caps DM Sans.
- Slight left-edge tilt (clip-path, ~4° angle) — echoes the reference image.

### Eyebrow / tag

- Uppercase DM Sans micro, tracked +0.18em, hibiscus colour, no background.

### Hero composition

- Asymmetric: 5/7 split. Text left, photo right (bleeding off edge).
- Decorative element top-left (small papel picado fragment or petal scatter).
- Primary CTA + secondary CTA stacked horizontally below intro paragraph.

### Footer

- Charro (deepest brown) bg, maíz text.
- Talavera tile band runs along the top edge (12px tall).
- Three columns: brand + tagline / nav / contact + socials.

---

## 8. Iconography & Imagery

- **Icons:** thin-line, 1.5px stroke, hibiscus or carbon. Avoid filled glyphs. Use **Phosphor Icons** (Light weight) or **Lucide** as base.
- **Photography direction:**
  - Overhead and 3/4 angles preferred.
  - Warm natural light, never flash.
  - Imperfect plating (a smudge of mole, a dropped herb).
  - Linen, terracotta, talavera plates as styling props.
  - Edit toward: warm shadows, slightly desaturated greens, punchy reds.

---

## 9. Motion

Restraint. The food is the motion.

- **Page entry:** stagger-fade hero text (60ms increments per element) + slow scale on hero image (1.04 → 1.0 over 800ms ease-out).
- **Hover on cards:** lift -4px, deepen shadow over 240ms ease-out.
- **Hover on links:** underline grows L→R over 220ms.
- **Decorative papel picado:** *very* subtle sway on idle (3° rotation, 6s ease-in-out infinite). Optional, off by default for accessibility (`prefers-reduced-motion`).
- **Never:** parallax, scroll-jacking, full-page transitions.

---

## 10. Site Information Architecture

| Route | Page | Purpose |
|---|---|---|
| `/` | Home | Chef hero, signature dishes, story teaser, recent recipes, journal preview, CTA (private dinners / contact) |
| `/recetas` | Recipes index | Filterable grid by region (Yucatán, Oaxaca, CDMX, Norte…) and category (antojitos, mariscos, postres…) |
| `/recetas/{slug}` | Recipe single | Hero photo, story intro, meta strip, ingredients (sidebar) + steps (main), chef notes, related recipes |
| `/historia` | About / Mi Historia | Chef bio, training, philosophy, kitchen portrait, testimonials |
| `/diario` | Journal / Diario | Editorial posts — markets visited, ingredient deep-dives, technique essays |
| `/diario/{slug}` | Journal single | Long-form, pull-quotes, drop caps, gallery |
| `/servicios` | Services | Private dinners, classes, consulting — booking-friendly |
| `/contacto` | Contact | Form, locations served, response promise |

### Global

- Header: wordmark (Rozha One) left · nav (DM Sans uppercase micro) right · language toggle far right (ES / EN)
- Footer: as defined above
- 404: marigold full-bleed, Fraunces "Se nos quemó la receta" headline, link home

---

## 11. Implementation Notes (Kirby)

- Add CSS custom properties to `assets/css/tokens.css`.
- Load fonts via `<link>` from Google Fonts in `site/snippets/header.php` (`Fraunces`, `Rozha+One`, `DM+Sans`, `Caveat`).
- Decorative SVGs (papel picado, talavera, sarape) live in `assets/svg/`.
- Build component snippets under `site/snippets/components/` matching section 7.
- Apply tokens via Tailwind config OR plain CSS vars — guide is framework-agnostic.

---

## 12. What this design **is not**

- Not folkloric pastiche. No sombrero icons, no cactus illustrations, no "fiesta" font.
- Not minimalist Scandinavian. The colour and pattern are the point.
- Not Instagram-cookbook generic. Avoid Inter, avoid sage-green-on-cream.
- Not a quericavida clone. We borrow energy and motif logic; we do not copy.