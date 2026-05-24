<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Resync seeded Finnish and English instruction posts from plugin seed files.
 *
 * ## EXAMPLES
 *
 *     wp gwi resync-instructions
 */
WP_CLI::add_command(
    'gwi resync-instructions',
    static function (): void {
        require_once GWI_PLUGIN_DIR . 'includes/seed-content.php';

        $count = gwi_resync_seed_instructions();

        WP_CLI::success(sprintf('Resynced %d instruction posts from seed content.', $count));
    }
);

/**
 * Create demo users for admin screenshots and demote the generic install admin.
 *
 * ## OPTIONS
 *
 * [--password=<password>]
 * : Password for demo accounts. Default: admin
 *
 * ## EXAMPLES
 *
 *     wp gwi ensure-screenshot-users
 *     wp gwi ensure-screenshot-users --password=admin
 */
WP_CLI::add_command(
    'gwi ensure-screenshot-users',
    static function (array $args, array $assoc_args): void {
        require_once GWI_PLUGIN_DIR . 'includes/screenshot-users.php';

        $password = isset($assoc_args['password']) ? (string) $assoc_args['password'] : 'admin';
        $result = gwi_ensure_screenshot_users($password);

        WP_CLI::success(
            sprintf(
                'Screenshot users ready (created: %d, updated: %d). Use WP_USER=%s for capture.',
                $result['created'],
                $result['updated'],
                $result['primary_login']
            )
        );
    }
);
