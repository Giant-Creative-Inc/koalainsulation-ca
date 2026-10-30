<?php
namespace Koala\CALandingHeroes;
function allowed_environment() {
    return function_exists('wp_get_environment_type') && in_array(wp_get_environment_type(), ['local', 'staging'], true);
}
/** Exact path plus the destination page's Efficiency relationship; query values cannot expand scope. */
function matches_request($uri, array $config, array $related) {
    if (empty($config['enabled']) || empty($config['page_id']) || empty($config['template_id']) || empty($config['location_id']) || empty($config['route_path'])) { return false; }
    $path = rtrim((string) parse_url($uri, PHP_URL_PATH), '/');
    return $path === rtrim($config['route_path'], '/')
        && in_array((string) $config['location_id'], array_map('strval', $related), true);
}
