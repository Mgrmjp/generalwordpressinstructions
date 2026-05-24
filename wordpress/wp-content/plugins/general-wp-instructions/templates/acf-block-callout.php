<?php

if (!defined('ABSPATH')) {
    exit;
}

$variant = function_exists('get_field') ? (string) get_field('variant') : 'note';
$content = function_exists('get_field') ? (string) get_field('content') : '';
$post_id = get_the_ID();
$language = ($post_id > 0 && function_exists('gwi_get_instruction_language'))
    ? gwi_get_instruction_language($post_id)
    : '';

gwi_render_callout($variant, $content, $language);
