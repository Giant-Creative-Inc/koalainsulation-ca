<?php
namespace Koala\CALandingHeroes;

/** Preserve the campaign query while keeping the logo on this site's origin. */
function efficiency_logo_url($home, $request_uri) {
    $base = parse_url($home);
    if (!is_string($request_uri) || substr($request_uri, 0, 1) !== '/' || substr($request_uri, 0, 2) === '//') {
        return $home;
    }
    return $base['scheme'] . '://' . $base['host']
        . (isset($base['port']) ? ':' . $base['port'] : '') . $request_uri;
}

/** Keep campaign navigation inside Efficiency without changing shared menus or posts. */
function efficiency_navigation_url($href, $home) {
    $url = parse_url($href);
    $base = parse_url($home);
    if ($url === false || !isset($url['path']) || isset($url['scheme']) && !in_array($url['scheme'], ['http', 'https'], true)
        || isset($url['host']) && strtolower($url['host']) !== strtolower($base['host'])) { return $href; }
    $root = rtrim($base['path'] ?? '', '/');
    $path = rtrim($url['path'], '/');
    $suffix = substr($path, strlen($root));
    if ($path !== $root && strpos($path, $root . '/') !== 0) { return $href; }
    if ($suffix === '/services') { $suffix = ''; }
    $pages = ['', '/services', '/why-koala', '/why-reinsulate', '/homeowner-incentives', '/faq', '/testimonials', '/privacy-policy', '/terms-and-conditions'];
    $services = ['spray-foam-insulation', 'blown-in-attic-insulation', 'insulation-removal-services', 'batt-insulation-installation', 'solar-attic-fans', 'air-sealing-service', 'crawl-space-encapsulation', 'commercial-insulation-services'];
    if (strpos($suffix, '/services/') === 0) {
        $slug = preg_replace('/-2$/', '', substr($suffix, strlen('/services/')));
        if ($slug === 'crawl-space-services') { $slug = 'crawl-space-encapsulation'; }
        if (!in_array($slug, $services, true)) { return $href; }
        $suffix = '/services/' . $slug;
    } elseif (!in_array($suffix, $pages, true)) { return $href; }
    return rtrim($home, '/') . '/efficiency' . $suffix
        . (isset($url['query']) ? '?' . $url['query'] : '')
        . (isset($url['fragment']) ? '#' . $url['fragment'] : '');
}

function efficiency_navigation_html($html) {
    if (!is_efficiency_page() || !class_exists('WP_HTML_Tag_Processor')) { return $html; }
    $tags = new \WP_HTML_Tag_Processor($html);
    while ($tags->next_tag('A')) {
        if ($tags->get_attribute('id') === 'nav-home') {
            $tags->set_attribute('href', efficiency_logo_url(home_url(), $_SERVER['REQUEST_URI'] ?? ''));
            continue;
        }
        $href = $tags->get_attribute('href');
        if (!is_string($href) || $href === '' || $href[0] === '#') { continue; }
        $path = rtrim((string) parse_url($href, PHP_URL_PATH), '/');
        $root = rtrim((string) parse_url(home_url(), PHP_URL_PATH), '/');
        // Efficiency has no blog or local equivalent for these corporate resources.
        if (in_array($path, [$root . '/blog', $root . '/national-partnerships'], true) || strpos($path, $root . '/resources-landing-pa/') === 0) {
            $tags->set_attribute('hidden', true);
            $tags->set_attribute('style', 'display:none!important');
            continue;
        }
        $updated = $path === $root . '/franchise' ? 'https://koalainsulationfranchise.ca/' : efficiency_navigation_url($href, home_url());
        if ($updated !== $href) { $tags->set_attribute('href', $updated); }
    }
    return $tags->get_updated_html();
}

add_action('template_redirect', function () {
    if (is_efficiency_page()) { ob_start(__NAMESPACE__ . '\\efficiency_navigation_html'); }
}, 1);

// Remember the ad entry point within this tab, including its hero and tracking query.
add_action('wp_enqueue_scripts', function () {
    $c = config();
    $location = get_post($c['location_id'] ?? 0);
    $base = rtrim((string) parse_url(home_url(), PHP_URL_PATH), '/') . '/efficiency';
    $path = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if (empty($c['enabled']) || !$location || $location->post_type !== 'location' || $location->post_name !== 'efficiency'
        || ($path !== $base && strpos($path, $base . '/') !== 0)) { return; }
    wp_enqueue_script('koala-efficiency-logo', plugins_url('assets/efficiency-logo.js', __FILE__), [], filemtime(__DIR__ . '/assets/efficiency-logo.js'), true);
    wp_add_inline_script('koala-efficiency-logo', 'window.koalaEfficiencyLogo=' . wp_json_encode([
        'route' => $c['route_path'],
        'entry' => is_efficiency_page() ? efficiency_logo_url(home_url(), $_SERVER['REQUEST_URI'] ?? '') : null,
    ]) . ';', 'before');
}, 110);
