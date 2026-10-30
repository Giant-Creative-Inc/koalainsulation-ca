<?php
namespace Bricks { class Frontend { public static $elements = []; } }
namespace {
    define('ABSPATH', __DIR__);
    $GLOBALS['environment'] = 'staging'; $GLOBALS['hooks'] = []; $GLOBALS['assets'] = [];
    $GLOBALS['config'] = ['enabled' => true, 'page_id' => 901, 'page_slug' => 'preview', 'location_id' => 902, 'template_id' => 903, 'route_path' => '/ca/efficiency/landing-pages/preview', 'hero_id' => 'efhero', 'fallback_id' => 'effallback', 'element_ids' => ['caadhr' => 'efhero', 'trohhj' => 'effallback']];
    function wp_json_encode($value) { return json_encode($value); }
    function home_url() { return 'https://staging.example/ca'; }
    function wp_get_environment_type() { return $GLOBALS['environment']; }
    function get_option($key, $default = null) { return $key === 'koala_efficiency_landing' ? $GLOBALS['config'] : $default; }
    function get_post($id) { if ($id === 901) { return (object) ['ID' => 901, 'post_type' => 'landing-pages', 'post_name' => 'preview']; } if ($id === 902) { return (object) ['ID' => 902, 'post_type' => 'location', 'post_name' => 'efficiency']; } return $GLOBALS['review_feeds'][$id] ?? null; }
    function get_post_meta($id, $key, $single = true) { return $id === 901 && $key === 'related_location' ? $GLOBALS['related'] : null; }
    function add_filter($tag, $callback, ...$rest) { $GLOBALS['hooks'][$tag][] = $callback; }
    function add_action($tag, $callback, ...$rest) { add_filter($tag, $callback); }
    function add_shortcode($tag, $callback) { $GLOBALS['shortcodes'][$tag] = $callback; }
    function wp_register_style(...$args) {}
    function wp_enqueue_style($handle) { $GLOBALS['assets'][] = $handle; }
    function wp_enqueue_script($handle, ...$args) { $GLOBALS['assets'][] = $handle; }
    function wp_add_inline_style($handle, $css) { $GLOBALS['css'] = $css; }
    function wp_add_inline_script($handle, $script, $position) { $GLOBALS['scripts'][] = [$handle, $script, $position]; }
    function plugins_url($path, $file = '') { return 'https://staging.example/ca/wp-content/plugins/koala-ca-landing-heroes/' . $path; }
    function esc_url($url) { return $url; }
    require __DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/koala-ca-landing-heroes.php';
    function check($ok, $label) { if (!$ok) { throw new \RuntimeException($label); } echo "PASS {$label}\n"; }
    function selected_template() { $id = 20847; foreach ($GLOBALS['hooks']['my_landing_page_template_id'] as $callback) { $id = $callback($id); } return $id; }
    $GLOBALS['related'] = ['902'];
    $_SERVER['REQUEST_URI'] = '/ca/efficiency/landing-pages/preview/?hero=ice-storm';
    $GLOBALS['review_feeds'][904] = (object) ['ID' => 904, 'post_type' => 'grw_feed'];
    $GLOBALS['config']['reviews_feed_id'] = 904;
    check(\Koala\CALandingHeroes\reviews_feed()->ID === 904, 'Efficiency selects its private Canadian review feed');
    $GLOBALS['config']['reviews_feed_id'] = 905;
    check(\Koala\CALandingHeroes\reviews_feed() === null, 'invalid private feed cannot silently fall back to headquarters');
    $GLOBALS['config']['reviews_feed_id'] = 904;
    check(selected_template() === 903, 'Efficiency selects its destination template without relying on current loop post ID');
    foreach ($GLOBALS['hooks']['wp_enqueue_scripts'] as $callback) { $callback(); }
    check(in_array(['all-pages-js', 'window.koalaEfficiencyLanding = true;', 'before'], $GLOBALS['scripts'], true), 'corporate navigation flag is inserted before shared script');
    check(in_array('koala-efficiency-header-estimate', $GLOBALS['assets'], true), 'Efficiency alone receives the header estimate popup handler');
    check(strpos($GLOBALS['css'], '#brxe-efhero') !== false && strpos($GLOBALS['css'], '#brxe-caadhr') === false, 'CSS references destination-specific IDs');
    check(strpos($GLOBALS['css'], 'url("https://staging.example/ca/wp-content/plugins/koala-ca-landing-heroes/assets/filson-black.woff")') !== false, 'inline stylesheet fonts keep correct absolute asset URLs');
    foreach (['/ca/efficiency', '/ca/efficiency/services/spray-foam-insulation', '/ca/efficiency/why-koala'] as $uri) {
        $_SERVER['REQUEST_URI'] = $uri;
        check(\Koala\CALandingHeroes\efficiency_content_request(), 'missing-data cleanup allowed on ' . $uri);
    }
    foreach (['/ca/halton', '/ca/efficiency-other', '/ca/efficiency/landing-pages/other'] as $uri) {
        $_SERVER['REQUEST_URI'] = $uri;
        check(!\Koala\CALandingHeroes\efficiency_content_request(), 'missing-data cleanup excludes ' . $uri);
    }
    $GLOBALS['assets'] = []; $GLOBALS['scripts'] = [];
    $_SERVER['REQUEST_URI'] = '/ca/halton/landing-pages/partners/?hero=ice-storm';
    check(\Koala\CALandingHeroes\reviews_feed() === null, 'other franchise cannot access the Efficiency review feed');
    check(selected_template() === 20847, 'other franchise retains shared template even with hero query');
    foreach ($GLOBALS['hooks']['wp_enqueue_scripts'] as $callback) { $callback(); }
    check(!$GLOBALS['assets'] && !$GLOBALS['scripts'], 'other franchise receives no campaign assets or navigation flag');
    $_SERVER['REQUEST_URI'] = '/ca/efficiency/landing-pages/preview/'; $GLOBALS['related'] = ['20180'];
    check(selected_template() === 20847, 'wrong page relationship cannot activate dedicated template');
    foreach (['unknown', ['ice-storm'], 'ICE-STORM'] as $value) { $_GET['hero'] = $value; check(\Koala\CALandingHeroes\selected_key() === '', 'invalid hero selects fallback'); }
    $GLOBALS['config'] = [];
    check(selected_template() === 20847, 'activation without configuration leaves original template');
}
