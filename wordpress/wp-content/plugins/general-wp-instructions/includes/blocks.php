<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'gwi_register_blocks');
add_action('enqueue_block_assets', 'gwi_enqueue_instruction_assets');

function gwi_register_blocks(): void
{
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type(GWI_PLUGIN_DIR . 'blocks/step-list');
    register_block_type(GWI_PLUGIN_DIR . 'blocks/highlighted-screenshot');
}

function gwi_enqueue_instruction_assets(): void
{
    wp_enqueue_style(
        'gwi-instructions',
        GWI_PLUGIN_URL . 'assets/css/instructions.css',
        [],
        GWI_VERSION
    );
}

function gwi_percent_attribute($value, float $fallback): float
{
    if (!is_numeric($value)) {
        return $fallback;
    }

    return max(0, min(100, (float) $value));
}
