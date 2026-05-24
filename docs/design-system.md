# Design System — Instruction Manual

Bold Edge visual language for the bilingual WordPress instruction site. Sharp 2px borders, hard offset shadows, spark (`#2ef2c5`) accent on dark ink (`#0a0c0e`).

## Architecture

```
design-tokens.css          ← single source of truth (--manual-*)
    ↓
style.css                  ← layout + components (.manual-*)
    ↓
instruction-reader.css     ← single guide page (optional)
    ↓
theme-edge.css             ← Bold Edge overrides (loads last)
    ↓
instructions.css (plugin)  ← block components (.gwi-*)
```

**Load order** (theme `functions.php`):

1. Mona Sans → `fonts-mona-sans.css`
2. `instruction-manual-tokens` → `design-tokens.css`
3. `instruction-manual` → `style.css` (base)
4. `gwi-instructions` → plugin blocks (single pages)
5. `instruction-manual-surface` → `theme-surface.css` (primary UI, loads last)

Plugin CSS depends on `instruction-manual-tokens` when the theme is active.

## Naming

| Prefix | Owner | Use |
|--------|-------|-----|
| `--manual-*` | Theme tokens | Colors, type, spacing, shadows |
| `.manual-*` | Theme components | Shell, cards, navigation, reader |
| `.gwi-*` | Plugin components | Steps, screenshots, callouts, lightbox |

Always use semantic tokens (`--manual-accent`) over raw hex in new CSS.

## Color

### Primitives

| Token | Value | Role |
|-------|-------|------|
| `--manual-color-spark` | `#2ef2c5` | Primary accent |
| `--manual-color-spark-dim` | `#006f60` | Links, strong accent |
| `--manual-color-black` | `#0a0c0e` | Ink, borders, shadows |
| `--manual-color-paper` | `#f4f3ee` | Page background |
| `--manual-color-canvas` | `#0a0f12` | Dark canvas / reader bg |

### Semantic

| Token | Use |
|-------|-----|
| `--manual-ink` | Body text, borders |
| `--manual-muted` | Secondary text |
| `--manual-surface` | Cards, panels |
| `--manual-accent` | CTAs, highlights |
| `--manual-accent-subtle` | Tinted backgrounds |
| `--manual-line` | Dividers, borders |

### Category accents

`--manual-blue`, `--manual-orange`, `--manual-olive`, `--manual-purple` — each with a `-subtle` background variant for intent cards and glossary icons.

### Context overrides

- **`body.single-wp_instruction`** — softer `--manual-line`, reader canvas
- **`html.manual-theme-dark`** — inverted surfaces, spark links

## Typography

- **Family:** `--manual-body` (Mona Sans variable, weights 200–900)
- **Scale:** `--manual-font-size-xs` through `--manual-font-size-3xl`
- **Weights:** 500 → 950 (`--manual-font-weight-*`)
- **Small labels:** `--manual-letter-spacing-caps` optional + weight 700–900

### Text roles (CSS classes)

| Class | Use |
|-------|-----|
| `.manual-eyebrow` | Section label |
| `.manual-title` | Hero H1 |
| `.manual-lead` | Hero intro |
| `.manual-section-title` | Section H2 with spark underline (edge) |
| `.manual-article__title` | Guide title in reader |

## Spacing

8px base rhythm via `--manual-space-1` (0.375rem) through `--manual-space-16` (5.5rem). Layout semantics: `--manual-section-gap`, `--manual-block-gap`, `--manual-card-padding`, `--manual-content-gap`.

Applied sitewide in `spacing-rhythm.css` (loads last).

## Radius & shadows

| Token | Value |
|-------|-------|
| `--manual-radius-edge` | `2px` — Bold Edge default |
| `--manual-hard-shadow` | `6px 6px 0 var(--manual-ink)` |
| `--manual-shadow-lg` | Hard shadow + soft drop |

Cards and buttons use hard offset shadows; hover often translates `(2px, 2px)` and shrinks shadow.

## Layout

| Token | Value |
|-------|-------|
| `--manual-container` | `1240px` |
| `--manual-container-reader` | `1360px` |
| `--manual-container-narrow` | `980px` |

Breakpoints used in CSS: `1180px`, `980px`, `860px`, `760px`, `640px`, `560px`, `520px`, `420px`.

## Components (theme)

### Shell

- `.manual-site-header` — sticky nav, dark bar + spark border (edge)
- `.manual-main` — content wrapper
- `.manual-site-footer` — dark footer + spark top border

### Marketing / home

- `.manual-hero`, `.manual-hero--home` — dark gradient hero
- `.manual-intent-card`, `.manual-tutorial-card` — category cards
- `.manual-path-single` — learning path timeline
- `.manual-glossary-strip` — inline glossary preview

### Library

- `.manual-filterbar`, `.manual-filter` — sticky filters
- `.manual-cabinet`, `.manual-doc-row` — grouped tutorial list
- `.manual-search-suggestions` — typeahead dropdown

### Reader

- `.manual-doc-workspace` — 3-column grid (TOC | content | aside)
- `.manual-document` — main guide card
- `.manual-progress` — section pill nav
- `.manual-check-box`, `.manual-mistakes-box`, `.manual-before-box` — callout panels
- `.manual-doc-aside`, `.manual-on-page` — sticky sidebar

### Glossary page

- `.manual-glossary-page`, `.manual-glossary-term` — A–Z listing

## Components (plugin)

- `.gwi-step-list` — numbered step timeline
- `.gwi-highlighted-screenshot` — annotated screenshot block
- `.gwi-callout--note|warning|success` — inline callouts
- `.gwi-screenshot-lightbox` — fullscreen image viewer
- `.gwi-language-switcher` — FI/EN toggle

## Buttons & inputs

Primary button pattern (edge):

```css
background: var(--manual-spark);
border: 2px solid var(--manual-ink);
border-radius: var(--manual-radius-edge);
box-shadow: 3px 3px 0 #000;
font-weight: 900;
```

Search inputs: 2px border, focus ring `--manual-focus-ring`.

## Dark mode

Toggle adds `manual-theme-dark` on `<html>`. Token overrides live in `design-tokens.css`; component adjustments in `theme-edge.css`.

## Adding new UI

1. Use existing `--manual-*` tokens — extend `design-tokens.css` if a new semantic is needed.
2. Follow BEM: `.manual-block`, `.manual-block__element`, `.manual-block--modifier`.
3. Plugin blocks use `.gwi-` prefix and consume theme tokens (keep CSS fallbacks for editor).
4. Put edge-specific polish in `theme-edge.css`, not scattered `:root` blocks.

## Files

| File | Purpose |
|------|---------|
| `themes/instruction-manual/assets/css/fonts-mona-sans.css` | Mona Sans @font-face |
| `themes/instruction-manual/assets/css/design-tokens.css` | Tokens |
| `themes/instruction-manual/style.css` | Base components |
| `themes/instruction-manual/assets/css/theme-polish.css` | Final cohesion / polish |
| `themes/instruction-manual/assets/css/theme-edge.css` | Bold Edge layer |
| `themes/instruction-manual/assets/css/instruction-reader.css` | Single guide layout |
| `plugins/general-wp-instructions/assets/css/instructions.css` | Block styles |
