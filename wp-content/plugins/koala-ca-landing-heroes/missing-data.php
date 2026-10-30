<?php
namespace Koala\CALandingHeroes;

/** Only the configured Efficiency homepage and its published content routes. */
function efficiency_content_request() {
    $c = config();
    $location = get_post($c['location_id'] ?? 0);
    if (empty($c['enabled']) || !$location || $location->post_type !== 'location' || $location->post_name !== 'efficiency') { return false; }
    $base = rtrim((string) parse_url(home_url(), PHP_URL_PATH), '/') . '/efficiency';
    $path = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($path === $base || is_efficiency_page()) { return true; }
    $suffix = substr($path, strlen($base));
    if (strpos($path, $base . '/') !== 0) { return false; }
    return in_array($suffix, ['/why-koala', '/why-reinsulate', '/faq', '/homeowner-incentives', '/testimonials', '/privacy-policy', '/terms-and-conditions'], true)
        || preg_match('#^/services/(spray-foam-insulation|blown-in-attic-insulation|insulation-removal-services|batt-insulation-installation|solar-attic-fans|air-sealing-service|crawl-space-encapsulation|commercial-insulation-services)$#', $suffix) === 1;
}
add_action('wp_enqueue_scripts', function () {
    if (!efficiency_content_request()) { return; }
    wp_enqueue_script('koala-efficiency-missing-data', plugins_url('assets/missing-data.js', __FILE__), [], filemtime(__DIR__ . '/assets/missing-data.js'), true);
}, 100);
