# General WordPress Instructions

Local WordPress training site for bilingual Finnish/English instructions, highlighted admin screenshots, Block Editor and Classic Editor examples, Polylang and WPML guidance, native Gutenberg blocks, ACF Blocks, and older ACF Flexible Content layouts.

## Local Setup

1. Start Lando:

   ```sh
   lando start
   ```

2. Download and install WordPress:

   ```sh
   lando setup-wp
   ```

   This installs Classic Editor from WordPress.org, so the setup command needs network access.

   Or run the setup manually:

   ```sh
   lando wp core download --path=/app/wordpress --force
   lando wp config create --path=/app/wordpress --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=database --skip-check
   lando wp core install --path=/app/wordpress --url=https://generalwordpressinstructions.lndo.site --title="WordPress-ohjeet" --admin_user=admin --admin_password=admin --admin_email=admin@example.com
   ```

3. If you used the manual setup path, install Classic Editor, configure editor defaults, and activate the included plugin and theme:

   ```sh
   lando wp plugin install classic-editor --activate --path=/app/wordpress
   lando wp option update classic-editor-replace block --path=/app/wordpress
   lando wp option update classic-editor-allow-users allow --path=/app/wordpress
   lando wp plugin activate general-wp-instructions --path=/app/wordpress
   lando wp theme activate instruction-manual --path=/app/wordpress
   ```

4. Open the site:

   ```text
   https://generalwordpressinstructions.lndo.site
   ```

## Instruction Library

The plugin seeds 76 instruction posts (38 topics × 2 languages) organized into 6 categories:

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
- Change Your Admin Color Scheme
- WordPress Settings
- Theme Appearance and Fonts

### Block Editor
- Block Editor Basics
- Working with Blocks
- Common Content Blocks
- Media Blocks
- Layout Blocks
- Synced Patterns and Patterns
- Preview and Publish Changes
- Use List View
- Add Images with Alt Text

### Classic Editor
- Classic Editor Basics
- Switch Between Block and Classic Editors
- Classic Editor Formatting
- Classic Editor Media

### Multilingual
- Configure Languages with Polylang
- Translate Content with Polylang
- Polylang Media Translations
- Add a Polylang Language Switcher
- Configure Languages with WPML
- Translate Content with WPML
- WPML Media Translation
- Add a WPML Language Switcher
- WPML String Translation

### Advanced Features
- Custom Fields with ACF
- Flexible Content Layouts
- ACF Blocks
- SEO Basics
- Performance and Caching

## Screenshot Capture

Install Node dependencies on the host:

```sh
npm install
npx playwright install chromium
```

Copy `.env.example` to `.env` or pass variables inline, then capture English screenshots:

```sh
WP_BASE_URL=https://generalwordpressinstructions.lndo.site WP_USER=maria.korhonen WP_PASSWORD=admin npm run screenshots:en
```

Capture Finnish screenshots:

```sh
WP_BASE_URL=https://generalwordpressinstructions.lndo.site WP_USER=maria.korhonen WP_PASSWORD=admin npm run screenshots:fi
```

Critical views and selectors live in `config/critical-views.json`. The script applies thick yellow outlines, dark contrast shadows, and labels before each screenshot is captured.

## ACF Notes

ACF is optional. Native Gutenberg blocks and instruction posts work without it.

Polylang and WPML are optional and are not installed or activated by this repo. The multilingual instruction guides and optional screenshot views are available for local environments where those plugins have been set up separately.

If ACF Pro is installed, the plugin registers ACF Blocks and the `acf-json` field group exposes an older Flexible Content authoring model for instructions.

Flexible Content rows render on single instruction pages (the theme prints a dedicated region after the main content when rows exist and no shortcode is present). If a page needs manual placement, add this shortcode in the content:

```text
[gwi_flexible_content]
```

### Flexible demo seed and screenshots

Live FI/EN examples on the flexible-content guides use ACF rows seeded from the plugin manifest. Screenshot rows resolve in order: media attachment (by screenshot key), import from `uploads/instruction-screenshots/{id}-{lang}.png`, then URL; if none are available, the front end shows an unavailable state (row is never dropped).

After capturing or copying PNGs into `uploads/instruction-screenshots/`, refresh examples:

```sh
lando wp gwi seed-flexible-examples --force --path=wordpress
```

`lando wp gwi resync-instructions --path=wordpress` updates post content from seed PHP but **does not** overwrite flexible example rows unless their seed version meta is outdated. Use `--force` on `seed-flexible-examples` when you change the manifest or screenshots.

## Verification

Run PHP syntax checks from the host if PHP is installed:

```sh
find wordpress/wp-content -name '*.php' -print0 | xargs -0 -n1 php -l
```

Or through Lando after `lando start`:

```sh
lando php -l /app/wordpress/wp-content/plugins/general-wp-instructions/general-wp-instructions.php
```

Check seeded instruction count:

```sh
lando wp post list --post_type=wp_instruction --post_status=publish --path=/app/wordpress --format=count
```

Verify taxonomy terms:

```sh
lando wp term list instruction_category --path=/app/wordpress
```
