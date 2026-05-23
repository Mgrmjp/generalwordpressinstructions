# General WordPress Instructions

Local WordPress training site for bilingual Finnish/English instructions, highlighted admin screenshots, Block Editor and Classic Editor examples, native Gutenberg blocks, ACF Blocks, and older ACF Flexible Content layouts.

## Local Setup

1. Start Lando:

   ```sh
   lando start
   ```

2. Download and install WordPress:

   ```sh
   lando setup-wp
   ```

   Or run the setup manually:

   ```sh
   lando wp core download --path=/app/wordpress --force
   lando wp config create --path=/app/wordpress --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=database --skip-check
   lando wp core install --path=/app/wordpress --url=https://generalwordpressinstructions.lndo.site --title="WordPress-ohjeet" --admin_user=admin --admin_password=admin --admin_email=admin@example.com
   ```

3. If you used the manual setup path, activate the included plugin and theme:

   ```sh
   lando wp plugin activate general-wp-instructions --path=/app/wordpress
   lando wp theme activate instruction-manual --path=/app/wordpress
   ```

4. Open the site:

   ```text
   https://generalwordpressinstructions.lndo.site
   ```

## Instruction Library

The plugin seeds 44 instruction posts (22 topics × 2 languages) organized into 5 categories:

### WordPress Fundamentals
- Dashboard Overview
- Creating and Editing Posts
- Categories and Tags
- Creating and Editing Pages
- Media Library
- Managing Comments

### Site Configuration
- Creating Menus
- Managing Users
- WordPress Settings
- Theme Customizer

### Block Editor
- Block Editor Basics
- Working with Blocks
- Common Content Blocks
- Media Blocks
- Layout Blocks
- Reusable Blocks and Patterns

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

## Screenshot Capture

Install Node dependencies on the host:

```sh
npm install
npx playwright install chromium
```

Copy `.env.example` to `.env` or pass variables inline, then capture English screenshots:

```sh
WP_BASE_URL=https://generalwordpressinstructions.lndo.site WP_USER=admin WP_PASSWORD=admin npm run screenshots:en
```

Capture Finnish screenshots:

```sh
WP_BASE_URL=https://generalwordpressinstructions.lndo.site WP_USER=admin WP_PASSWORD=admin npm run screenshots:fi
```

Critical views and selectors live in `config/critical-views.json`. The script applies thick yellow outlines, dark contrast shadows, and labels before each screenshot is captured.

## ACF Notes

ACF is optional. Native Gutenberg blocks and instruction posts work without it.

If ACF Pro is installed, the plugin registers ACF Blocks and the `acf-json` field group exposes an older Flexible Content authoring model for instructions.

Flexible Content rows render automatically on single instruction pages. If a page needs manual placement, add this shortcode in the content:

```text
[gwi_flexible_content]
```

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
