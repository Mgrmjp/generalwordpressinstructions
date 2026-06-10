<?php

if (!defined('ABSPATH')) {
    exit;
}

const GWI_SCREENSHOT_USER_AVATAR_META = 'gwi_screenshot_user_avatar';

/**
 * Demo accounts used in admin screenshots (users list, profile bar, etc.).
 *
 * @return list<array{
 *   user_login: string,
 *   user_email: string,
 *   first_name: string,
 *   last_name: string,
 *   display_name: string,
 *   role: string,
 *   description: string,
 *   avatar?: string
 * }>
 */
function gwi_screenshot_user_definitions(): array
{
    return [
        [
            'user_login' => 'maria.korhonen',
            'user_email' => 'maria.korhonen@yritys.example.fi',
            'first_name' => 'Maria',
            'last_name' => 'Korhonen',
            'display_name' => 'Maria Korhonen',
            'role' => 'administrator',
            'description' => 'Sivuston pääkäyttäjä',
        ],
        [
            'user_login' => 'jukka.virtanen',
            'user_email' => 'jukka.virtanen@yritys.example.fi',
            'first_name' => 'Jukka',
            'last_name' => 'Virtanen',
            'display_name' => 'Jukka Virtanen',
            'role' => 'editor',
            'description' => 'Sisällön toimittaja',
        ],
        [
            'user_login' => 'liisa.nieminen',
            'user_email' => 'liisa.nieminen@yritys.example.fi',
            'first_name' => 'Liisa',
            'last_name' => 'Nieminen',
            'display_name' => 'Liisa Nieminen',
            'role' => 'author',
            'description' => 'Kirjoittaja',
        ],
        [
            'user_login' => 'aino.laine',
            'user_email' => 'aino.laine@yritys.example.fi',
            'first_name' => 'Aino',
            'last_name' => 'Laine',
            'display_name' => 'Aino Laine',
            'role' => 'editor',
            'description' => 'Sisältösuunnittelija',
            'avatar' => 'assets/images/users/aino-laine.png',
        ],
    ];
}

/**
 * Login name to use for Playwright screenshot capture.
 */
function gwi_screenshot_primary_user_login(): string
{
    return 'maria.korhonen';
}

/**
 * Create or update demo users; demote the default install account off the Users screen.
 *
 * @return array{primary_login: string, created: int, updated: int}
 */
function gwi_ensure_screenshot_users(string $password = 'admin'): array
{
    $password = $password !== '' ? $password : 'admin';
    $created = 0;
    $updated = 0;

    foreach (gwi_screenshot_user_definitions() as $definition) {
        $user_id = username_exists($definition['user_login']);

        if ($user_id === false) {
            $user_id = wp_create_user(
                $definition['user_login'],
                $password,
                $definition['user_email']
            );

            if (is_wp_error($user_id)) {
                continue;
            }

            $created++;
        } else {
            $user_id = (int) $user_id;
            $updated++;
        }

        wp_update_user([
            'ID' => (int) $user_id,
            'user_email' => $definition['user_email'],
            'first_name' => $definition['first_name'],
            'last_name' => $definition['last_name'],
            'display_name' => $definition['display_name'],
            'description' => $definition['description'],
            'role' => $definition['role'],
            'user_pass' => $password,
        ]);

        if (isset($definition['avatar'])) {
            update_user_meta((int) $user_id, GWI_SCREENSHOT_USER_AVATAR_META, $definition['avatar']);
        }
    }

    gwi_demote_default_install_admin();

    return [
        'primary_login' => gwi_screenshot_primary_user_login(),
        'created' => $created,
        'updated' => $updated,
    ];
}

/**
 * Keep the bootstrap admin account for CLI/dev, but hide it from the Users list UI.
 */
function gwi_demote_default_install_admin(): void
{
    $admin = get_user_by('login', 'admin');

    if (!$admin instanceof WP_User) {
        return;
    }

    $primary = get_user_by('login', gwi_screenshot_primary_user_login());

    if (!$primary instanceof WP_User) {
        return;
    }

    if (in_array('administrator', $admin->roles, true)) {
        $admin->set_role('subscriber');
    }
}

add_action('pre_get_users', 'gwi_hide_install_admin_from_users_screen');

/**
 * @param WP_User_Query $query
 */
function gwi_hide_install_admin_from_users_screen(WP_User_Query $query): void
{
    if (!is_admin() || !function_exists('get_current_screen')) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || $screen->id !== 'users') {
        return;
    }

    global $wpdb;

    $query->query_where .= $wpdb->prepare(' AND user_login != %s', 'admin');
}

add_filter('pre_get_avatar_data', 'gwi_use_screenshot_user_avatar', 10, 2);

/**
 * Use project-owned avatar assets for screenshot demo users.
 *
 * @param array<string, mixed> $args
 * @param mixed               $id_or_email
 *
 * @return array<string, mixed>
 */
function gwi_use_screenshot_user_avatar(array $args, $id_or_email): array
{
    if (!empty($args['force_default'])) {
        return $args;
    }

    $user = gwi_get_user_from_avatar_identifier($id_or_email);

    if (!$user instanceof WP_User) {
        return $args;
    }

    $avatar = get_user_meta($user->ID, GWI_SCREENSHOT_USER_AVATAR_META, true);

    if (!is_string($avatar) || $avatar === '') {
        return $args;
    }

    $relative_path = ltrim($avatar, '/');

    if (!str_starts_with($relative_path, 'assets/images/users/')) {
        return $args;
    }

    if (!file_exists(GWI_PLUGIN_DIR . $relative_path)) {
        return $args;
    }

    $args['url'] = GWI_PLUGIN_URL . gwi_rawurlencode_path($relative_path);
    $args['found_avatar'] = true;

    return $args;
}

/**
 * @param mixed $id_or_email
 */
function gwi_get_user_from_avatar_identifier($id_or_email): ?WP_User
{
    if ($id_or_email instanceof WP_User) {
        return $id_or_email;
    }

    if ($id_or_email instanceof WP_Post) {
        return gwi_get_avatar_user_by('id', (int) $id_or_email->post_author);
    }

    if ($id_or_email instanceof WP_Comment) {
        if ($id_or_email->user_id > 0) {
            return gwi_get_avatar_user_by('id', (int) $id_or_email->user_id);
        }

        return gwi_get_avatar_user_by('email', $id_or_email->comment_author_email);
    }

    if (is_object($id_or_email) && isset($id_or_email->comment_ID)) {
        $comment = get_comment($id_or_email);

        if ($comment instanceof WP_Comment) {
            return gwi_get_user_from_avatar_identifier($comment);
        }
    }

    if (is_numeric($id_or_email)) {
        return gwi_get_avatar_user_by('id', (int) $id_or_email);
    }

    if (is_string($id_or_email) && is_email($id_or_email)) {
        return gwi_get_avatar_user_by('email', $id_or_email);
    }

    return null;
}

/**
 * @param int|string $value
 */
function gwi_get_avatar_user_by(string $field, $value): ?WP_User
{
    $user = get_user_by($field, $value);

    return $user instanceof WP_User ? $user : null;
}

function gwi_rawurlencode_path(string $path): string
{
    return implode('/', array_map('rawurlencode', explode('/', $path)));
}
