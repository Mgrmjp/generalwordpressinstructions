<?php
/**
 * Plugin Name: General WordPress Instructions
 * Description: Bilingual WordPress instruction system with highlighted screenshots, native blocks, ACF blocks, and Flexible Content support.
 * Version: 0.1.3
 * Author: General WordPress Instructions
 * Text Domain: general-wp-instructions
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GWI_VERSION', '0.1.3');
define('GWI_PLUGIN_FILE', __FILE__);
define('GWI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GWI_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GWI_PLUGIN_DIR . 'includes/post-type.php';
require_once GWI_PLUGIN_DIR . 'includes/language-switcher.php';
require_once GWI_PLUGIN_DIR . 'includes/callout.php';
require_once GWI_PLUGIN_DIR . 'includes/blocks.php';
require_once GWI_PLUGIN_DIR . 'includes/acf.php';
require_once GWI_PLUGIN_DIR . 'includes/finnish-text.php';
require_once GWI_PLUGIN_DIR . 'includes/screenshot-users.php';
require_once GWI_PLUGIN_DIR . 'includes/seed-content.php';

if (defined('WP_CLI') && WP_CLI) {
    require_once GWI_PLUGIN_DIR . 'includes/cli.php';
}

register_activation_hook(__FILE__, 'gwi_activate');
register_deactivation_hook(__FILE__, 'gwi_deactivate');

function gwi_activate(): void
{
    gwi_register_instruction_post_type();
    gwi_register_instruction_taxonomy();
    gwi_seed_instruction_content();
    flush_rewrite_rules();
}

function gwi_deactivate(): void
{
    flush_rewrite_rules();
}
