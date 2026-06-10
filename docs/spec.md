# Spec: General WordPress Instructions

## Assumptions
- This is a local WordPress training/documentation site, not a SaaS product.
- Finnish and English content are separate instruction posts linked as translations.
- The site must work without paid dependencies, but ACF Pro unlocks ACF Blocks and Flexible Content.
- Screenshots are captured from the local Lando WordPress admin using Playwright and highlighted by CSS outlines before capture.
- The instruction library covers common WordPress tasks comprehensively for training new users.

## Objective
Build a WordPress-based instruction system for general WordPress use. Editors can publish bilingual instruction pages for Finnish and English readers, including guidance for the Block Editor, Classic Editor, ACF Blocks, native Gutenberg blocks, and older ACF Flexible Content layouts.

Success means a developer can start the Lando site, activate the included plugin/theme, seed example instructions, capture highlighted admin screenshots, and view readable bilingual instruction pages on the front end.

## Tech Stack
- Local environment: Lando WordPress recipe.
- Runtime: WordPress, PHP 8.2, MariaDB.
- Plugin: `general-wp-instructions`, plain PHP with no Composer dependency.
- Theme: `instruction-manual`, classic PHP theme for instruction pages.
- Blocks: one native Gutenberg dynamic block and one native highlighted screenshot block.
- ACF: optional runtime integration for ACF Blocks and ACF Pro Flexible Content.
- Screenshot tooling: Node.js script using Playwright.

## Commands
- Start local services: `lando start`
- Download WordPress core: `lando wp core download --path=/app/wordpress --force`
- Create config: `lando wp config create --path=/app/wordpress --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=database --skip-check`
- Install WordPress: `lando wp core install --path=/app/wordpress --url=https://generalwordpressinstructions.lndo.site --title="WordPress-ohjeet" --admin_user=admin --admin_password=admin --admin_email=admin@example.com`
- Activate plugin: `lando wp plugin activate general-wp-instructions --path=/app/wordpress`
- Activate theme: `lando wp theme activate instruction-manual --path=/app/wordpress`
- Install screenshot tooling: `npm install`
- Capture screenshots: `WP_BASE_URL=https://generalwordpressinstructions.lndo.site WP_USER=maria.korhonen WP_PASSWORD=admin npm run screenshots`
- PHP syntax check: `find wordpress/wp-content -name '*.php' -print0 | xargs -0 -n1 php -l`

## Project Structure
- `docs/` contains the product spec and implementation plan.
- `config/critical-views.json` declares admin views, selectors, and bilingual highlight labels for screenshots.
- `tools/capture-critical-views.mjs` logs into WordPress, highlights selectors, and saves screenshots.
- `wordpress/wp-content/plugins/general-wp-instructions/` contains the instruction system plugin.
- `wordpress/wp-content/themes/instruction-manual/` contains the front-end rendering theme.
- `wordpress/wp-content/uploads/instruction-screenshots/` is the intended screenshot output path inside WordPress.

### Plugin Include Files
- `includes/post-type.php` — CPT registration, taxonomy, meta boxes
- `includes/language-switcher.php` — bilingual front-end switcher
- `includes/blocks.php` — native Gutenberg block registration
- `includes/acf.php` — optional ACF Blocks and Flexible Content
- `includes/seed-content.php` — seed orchestrator
- `includes/seed-fundamentals.php` — Dashboard, Posts, Categories, Pages, Media, Comments
- `includes/seed-site-config.php` — Navigation, Users, Settings, Appearance and Fonts
- `includes/seed-block-editor.php` — Block Editor deep dive
- `includes/seed-classic-editor.php` — Classic Editor deep dive
- `includes/seed-advanced.php` — ACF Fields, Flexible Content, ACF Blocks, SEO, Performance

## Instruction Library

The plugin seeds 48 instruction posts (24 topics × 2 languages) organized into 5 categories:

### WordPress Fundamentals
- Dashboard Overview
- Creating and Editing Posts
- Categories and Tags
- Creating and Editing Pages
- Media Library
- Managing Comments

### Site Configuration
- Navigation Menus
- Managing Users
- WordPress Settings
- Theme Appearance and Fonts

### Block Editor
- Block Editor Basics
- Working with Blocks
- Common Content Blocks
- Media Blocks
- Layout Blocks
- Synced Patterns and Patterns

### Classic Editor
- Classic Editor Basics
- Classic Editor Formatting
- Classic Editor Media

### Advanced Features
- Custom Fields with ACF
- Flexible Content Layouts
- ACF Blocks
- SEO Basics
- Performance and Caching

## Code Style
Use small WordPress hooks and explicit escaping at output boundaries:

```php
add_action('init', function (): void {
    register_post_type('wp_instruction', [
        'label' => __('Instructions', 'general-wp-instructions'),
        'public' => true,
        'show_in_rest' => true,
    ]);
});

echo esc_html($title);
```

Conventions:
- Prefix PHP functions with `gwi_`.
- Prefix block names with `general-wp-instructions/`.
- Escape output with `esc_html`, `esc_attr`, `esc_url`, or `wp_kses_post`.
- Keep optional ACF behavior behind `function_exists()` checks.

## Testing Strategy
- Static verification: run PHP lint across plugin and theme files.
- WordPress verification: activate plugin and theme through WP-CLI in Lando.
- Block verification: confirm native blocks appear in the Block Editor inserter.
- ACF verification: if ACF Pro is installed, confirm ACF field groups sync from `acf-json` and ACF blocks render.
- Screenshot verification: run the Playwright script and confirm highlighted PNG files are written.

## Boundaries
- Always: keep ACF optional, make all public output escaped, keep Finnish/English labels available.
- Always: make screenshot highlight outlines visually obvious in generated screenshots.
- Ask first: adding paid plugins, adding external services, changing from Lando to another dev stack.
- Ask first: replacing the bilingual pairing model with Polylang/WPML.
- Never: bundle ACF Pro or any commercial plugin.
- Never: commit local WordPress config secrets or generated uploads as source.

## Success Criteria
- A Lando WordPress project can be started from the repo.
- The plugin registers an `Instruction` post type with Finnish/English metadata and translation links.
- The plugin registers an `instruction_category` taxonomy with 5 default terms.
- The front end shows a Finnish/English language switcher when translations are linked.
- The plugin provides native Gutenberg blocks for instruction steps and highlighted screenshots.
- The plugin registers ACF Blocks when ACF is active.
- ACF JSON defines an older Flexible Content editing model for instruction sections.
- The plugin seeds 48 instruction posts covering 24 topics in Finnish and English.
- Each instruction uses step-list and highlighted-screenshot blocks.
- A screenshot script captures 22 critical admin views with strong outlines around configured buttons.

## Open Questions
- Should production translation management later use Polylang/WPML instead of lightweight post meta?
- Should screenshots be captured manually by maintainers or automatically in CI after WordPress updates?
- Which exact client-specific admin views should be added after the general WordPress examples?
