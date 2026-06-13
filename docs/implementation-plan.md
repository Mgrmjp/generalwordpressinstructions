# Implementation Plan: General WordPress Instructions

## Overview
Build the project in vertical slices: environment and docs first, then the WordPress plugin foundation, then editor/block/ACF support, then screenshot automation, then verification.

## Architecture Decisions
- Use a plugin for the instruction system because the content model, blocks, ACF fields, and seeding should survive theme changes.
- Use a small classic theme only for readable front-end templates and default styling.
- Keep Finnish and English as post meta plus a translation post ID. This avoids forcing Polylang/WPML into the first version.
- Use Playwright for screenshots because it can authenticate, navigate WordPress admin, inject highlight styles, and capture repeatable images.
- Keep ACF optional at runtime. ACF Blocks and Flexible Content field groups are registered only when ACF is available.

## Task List

### Phase 1: Foundation
- [x] Task 1: Write specification and implementation plan.
  - Acceptance: spec includes objective, commands, structure, style, testing, boundaries, success criteria.
  - Verify: read `docs/spec.md` and `docs/implementation-plan.md`.
  - Files: `docs/spec.md`, `docs/implementation-plan.md`.

- [x] Task 2: Add Lando, README, ignore rules, and screenshot package metadata.
  - Acceptance: repo documents local setup and exposes screenshot commands.
  - Verify: inspect `.lando.yml`, `README.md`, `package.json`.
  - Files: `.lando.yml`, `.gitignore`, `README.md`, `package.json`.

### Phase 2: WordPress Instruction System
- [x] Task 3: Register the instruction content type and bilingual metadata.
  - Acceptance: `wp_instruction` is public, REST-enabled, supports editor/thumbnails/revisions, and stores language/translation metadata.
  - Verify: PHP lint and WP-CLI activation.
  - Files: plugin main file and include files.

- [x] Task 4: Add front-end language switcher and seed instructions.
  - Acceptance: linked Finnish/English posts show a switcher; activation creates sample Block Editor and Classic Editor instructions.
  - Verify: activate plugin and inspect seeded posts.
  - Files: plugin include files.

### Phase 3: Editing Models
- [x] Task 5: Add native Gutenberg blocks.
  - Acceptance: step list and highlighted screenshot blocks register through `block.json` and render on front end.
  - Verify: PHP lint and block files exist with valid JSON.
  - Files: `blocks/step-list/*`, `blocks/highlighted-screenshot/*`.

- [x] Task 6: Add ACF Blocks and older Flexible Content support.
  - Acceptance: plugin registers ACF blocks if ACF is active; ACF JSON defines flexible layouts.
  - Verify: ACF field group JSON parses and PHP lint passes.
  - Files: `acf-json/*`, `templates/*`, plugin include files.

### Phase 4: Front End
- [x] Task 7: Add instruction theme.
  - Acceptance: archive and single instruction pages render content, metadata, language switcher, and screenshot styles.
  - Verify: PHP lint and theme activation through WP-CLI.
  - Files: theme PHP/CSS files.

### Phase 5: Screenshot Automation
- [x] Task 8: Add critical view config and Playwright capture tool.
  - Acceptance: config supports bilingual labels; script logs in, highlights selectors, and writes PNG files.
  - Verify: `node tools/capture-critical-views.mjs --help` or run against Lando once dependencies are installed.
  - Files: `config/critical-views.json`, `tools/capture-critical-views.mjs`.

### Phase 6: Comprehensive Instruction Library
- [x] Task 9: Add instruction_category taxonomy.
  - Acceptance: `instruction_category` taxonomy registered with 6 default terms: fundamentals, site-config, block-editor, classic-editor, multilingual, advanced.
  - Verify: `lando wp taxonomy list --path=/app/wordpress` shows taxonomy.
  - Files: `includes/post-type.php`.

- [x] Task 10: Create seed content for WordPress Fundamentals (6 topics).
  - Acceptance: 12 posts created (6 EN + 6 FI) covering Dashboard, Posts, Categories, Pages, Media, Comments.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-fundamentals.php`.

- [x] Task 11: Create seed content for Site Configuration (5 topics).
  - Acceptance: 10 posts created (5 EN + 5 FI) covering Navigation, Users, Profile Admin Color Scheme, Settings, Appearance and Fonts.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-site-config.php`.

- [x] Task 12: Create seed content for Block Editor (6 topics).
  - Acceptance: 12 posts created (6 EN + 6 FI) covering Block Editor Basics, Working with Blocks, Content Blocks, Media Blocks, Layout Blocks, Synced Patterns.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-block-editor.php`.

- [x] Task 13: Create seed content for Classic Editor (4 topics).
  - Acceptance: 8 posts created (4 EN + 4 FI) covering Classic Editor Basics, Editor Switching, Classic Formatting, Classic Media.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-classic-editor.php`.

- [x] Task 13a: Create seed content for Multilingual/Polylang (4 topics).
  - Acceptance: 8 posts created (4 EN + 4 FI) covering language setup, content translation, media translations, and language switchers.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-polylang.php`.

- [x] Task 13b: Create seed content for Multilingual/WPML (5 topics).
  - Acceptance: 10 posts created (5 EN + 5 FI) covering language setup, content translation, media translation, language switchers, and String Translation.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-wpml.php`.

- [x] Task 14: Create seed content for Advanced Features (5 topics).
  - Acceptance: 10 posts created (5 EN + 5 FI) covering ACF Fields, Flexible Content, ACF Blocks, SEO, Performance.
  - Verify: PHP lint and WP-CLI post count.
  - Files: `includes/seed-advanced.php`.

- [x] Task 15: Update seed-content.php to require new files and assign taxonomy terms.
  - Acceptance: `gwi_seed_instruction_content()` calls all category functions and assigns terms.
  - Verify: Activate plugin and confirm taxonomy terms assigned.
  - Files: `includes/seed-content.php`.

- [x] Task 16: Expand critical-views.json with new screenshot views.
  - Acceptance: 38 total views with bilingual labels covering all instruction topics.
  - Verify: JSON parses and Playwright script accepts config.
  - Files: `config/critical-views.json`.

### Phase 7: Documentation and Verification
- [x] Task 17: Update documentation.
  - Acceptance: spec.md, implementation-plan.md, and README.md reflect full instruction library.
  - Verify: Documentation matches implementation.
  - Files: `docs/spec.md`, `docs/implementation-plan.md`, `README.md`.

- [x] Task 18: Verify full instruction library.
  - Acceptance: 76 posts exist, taxonomy terms assigned, language switcher works, screenshots capture.
  - Verify: WP-CLI commands and Playwright run.
  - Files: N/A.

## Checkpoints
- Foundation checkpoint: docs and Lando scaffold are present.
- WordPress checkpoint: plugin/theme PHP files pass syntax checks.
- Screenshot checkpoint: Playwright script validates config and has clear environment variables.
- Complete checkpoint: setup path is documented end to end.

## Risks and Mitigations
| Risk | Impact | Mitigation |
|---|---:|---|
| ACF Pro is unavailable locally | Medium | Keep native blocks and post meta fully functional without ACF. |
| WordPress admin selectors change | Medium | Keep selectors in `config/critical-views.json`, not hard-coded in the script. |
| Screenshot login fails on first install | Low | Use explicit `WP_BASE_URL`, `WP_USER`, and `WP_PASSWORD` environment variables. |
| Translation requirements grow | Medium | Keep the lightweight model replaceable by not coupling it deeply to rendering. |

## Open Questions
- Decide later whether screenshots should be stored in Git, WordPress uploads, or external documentation storage.
- Decide later whether generated instruction content should be exported/imported as WXR, JSON, or plugin seed data.
