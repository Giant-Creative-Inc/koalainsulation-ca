<?php
/**
 * Run: php tests/zip-shape-map-shortcode.test.php
 *
 * Minimal WordPress stubs so the shortcode can render outside WordPress.
 */

$GLOBALS['zip_shape_map_test_meta'] = array();

function add_shortcode($tag, $callback) {}
function add_action($hook, $callback) {}
function wp_enqueue_script() {}
function wp_add_inline_script() {}
function get_template_directory() { return __DIR__; }
function get_template_directory_uri() { return ''; }
function rest_url($path) { return '/wp-json/' . $path; }
function get_the_ID() { return 1; }
function get_queried_object_id() { return 1; }
function absint($value) { return abs((int) $value); }
function esc_attr($value) { return htmlspecialchars((string) $value, ENT_QUOTES); }
function wp_json_encode($value) { return json_encode($value); }
function wp_strip_all_tags($value) { return strip_tags((string) $value); }

function shortcode_atts($defaults, $atts) {
    return array_merge($defaults, array_intersect_key((array) $atts, $defaults));
}

function get_post_meta($post_id, $key, $single) {
    return $GLOBALS['zip_shape_map_test_meta'][$key] ?? '';
}

require __DIR__ . '/../wp-content/themes/bricks/zip-shape-map-shortcode.php';

function render_map_config($meta) {
    $GLOBALS['zip_shape_map_test_meta'] = $meta;
    $html = zip_shape_map_render_shortcode(array());

    if (!preg_match('/zipShapeMapConfigs\.push\((.*)\);/', $html, $match)) {
        return array('html' => $html, 'config' => null);
    }

    return array('html' => $html, 'config' => json_decode($match[1], true));
}

$failures = 0;

function check($label, $condition) {
    global $failures;

    echo ($condition ? 'ok   ' : 'FAIL ') . $label . PHP_EOL;

    if (!$condition) {
        $failures++;
    }
}

$location = array(
    'location_latitude'  => '43.6532',
    'location_logitude'  => '-79.3832',
    'location_address'   => '123 King Street West, Toronto, ON M5H 1A1',
);

// Canadian location: map and pin still render, but no territory outline.
$canadian = render_map_config($location + array('additional_zipcodes' => 'M5H, M5V 3A8, M4C'));
check('Canadian location still renders a map', $canadian['config'] !== null);
check('Canadian location hides the territory outline', ($canadian['config']['showBoundary'] ?? null) === false);
check('Canadian location requests no boundary codes', ($canadian['config']['zipcodes'] ?? null) === array());
check('Canadian location keeps its street-address pin', ($canadian['config']['showPin'] ?? null) === true);

// Canadian location without postal codes: centre point is enough now.
$no_codes = render_map_config($location + array('additional_zipcodes' => ''));
check('Canadian location without postal codes still renders a map', $no_codes['config'] !== null);

// U.S. ZIP codes keep their outline.
$us = render_map_config($location + array('additional_zipcodes' => '53532, 53571'));
check('U.S. location keeps the territory outline', ($us['config']['showBoundary'] ?? null) === true);
check('U.S. location requests its ZIP boundaries', ($us['config']['zipcodes'] ?? null) === array('53532', '53571'));

// Missing coordinates still report missing data.
$missing = render_map_config(array('additional_zipcodes' => 'M5H'));
check('Missing coordinates still report missing map data', strpos($missing['html'], 'Map data is missing.') !== false);

echo PHP_EOL . ($failures ? $failures . ' failing check(s)' : 'All checks passed') . PHP_EOL;
exit($failures ? 1 : 0);
