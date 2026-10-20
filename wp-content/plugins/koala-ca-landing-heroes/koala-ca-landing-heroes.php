<?php
/**
 * Plugin Name: Koala Efficiency Landing Page
 * Description: Isolated Efficiency campaign template for Local and staging.
 * Version: 1.2.0
 */
namespace Koala\CALandingHeroes;
if (!defined('ABSPATH')) { exit; }
require __DIR__ . '/scope.php';
require __DIR__ . '/reviews.php';
if (defined('WP_CLI') && WP_CLI) { require __DIR__ . '/cli.php'; }
if (!allowed_environment()) { return; }
function config() { return (array) get_option('koala_efficiency_landing', []); }
function is_efficiency_page() {
    $c = config();
    if (empty($c['enabled'])) { return false; }
    $page = get_post($c['page_id'] ?? 0);
    $location = get_post($c['location_id'] ?? 0);
    if (!$page || $page->post_type !== 'landing-pages' || $page->post_name !== ($c['page_slug'] ?? '')
        || !$location || $location->post_type !== 'location' || $location->post_name !== 'efficiency') { return false; }
    return matches_request($_SERVER['REQUEST_URI'] ?? '', $c, (array) get_post_meta($page->ID, 'related_location', true));
}
add_filter('my_landing_page_template_id', function ($template_id) {
    return is_efficiency_page() ? (int) config()['template_id'] : $template_id;
});
add_filter('body_class', function ($classes) {
    if (is_efficiency_page()) { $classes[] = 'koala-efficiency-landing'; }
    return $classes;
});
require __DIR__ . '/presets.php';
function selected_key() {
    $value = $_GET['hero'] ?? '';
    return is_string($value) && array_key_exists($value, presets()) ? $value : '';
}
function in_template() {
    if (!is_efficiency_page()) { return false; }
    if (!class_exists('\\Bricks\\Frontend')) { return false; }
    $elements = \Bricks\Frontend::$elements;
    return isset($elements[config()['hero_id']], $elements[config()['fallback_id']])
        && ($elements[config()['hero_id']]['settings']['shortcode'] ?? '') === '[koala_ca_landing_hero template="20847"]';
}
function review_panel($campaign = false) {
    if (!in_template()) { return ''; }
    $class = $campaign ? 'ca-landing-hero__proof' : 'review-link-block';
    $html = '<a class="' . esc_attr($class) . '" href="#ca-landing-reviews"><p>Top Insulation Contractor</p>';
    $summary = review_summary();
    if ($summary) {
        $stars = esc_url(plugins_url('assets/stars.png', __FILE__));
        $label = number_format_i18n($summary['rating'], 1) . '/5 on Google · ' . number_format_i18n($summary['count']) . ' reviews';
        if ($summary['locations'] > 1) {
            $label = number_format_i18n($summary['rating'], 1) . '/5 combined · ' . number_format_i18n($summary['count']) . ' Google reviews';
        }
        $html .= '<div class="ca-landing-hero__review-row"><span class="ca-landing-rating" aria-hidden="true"><img class="ca-landing-rating__empty" src="' . $stars . '" alt=""><span style="width:' . esc_attr((string) ($summary['rating'] * 20)) . '%"><img src="' . $stars . '" alt=""></span></span><span>' . esc_html($label) . '</span></div>';
    }
    return $html . '</a>';
}
function review_summary() {
    static $summary = null;
    if ($summary !== null) { return $summary; }
    $summary = [];
    $feed = reviews_feed();
    if (!$feed || get_option('grw_active') === '0' || !class_exists('\\WP_Rplg_Google_Reviews\\Includes\\Core\\Core')) { return $summary; }
    $core = new \WP_Rplg_Google_Reviews\Includes\Core\Core();
    $widget = $core->get_reviews($feed);
    $summary = combined_review_summary((array) ($widget['businesses'] ?? []));
    return $summary;
}
function reviews_widget() {
    if (!in_template()) { return ''; }
    $feed = reviews_feed();
    if (!$feed || get_option('grw_active') === '0' || !class_exists('\\WP_Rplg_Google_Reviews\\Includes\\Core\\Core')) { return ''; }
    // Use the installed widget renderer, normalizing rating-only reviews for PHP 8.3.
    $core = new \WP_Rplg_Google_Reviews\Includes\Core\Core();
    $widget = $core->get_reviews($feed);
    $sources = '';
    $summary = combined_review_summary((array) $widget['businesses']);
    if (!empty(config()['reviews_feed_id']) && $summary) {
        foreach ($widget['businesses'] as $business) {
            $sources .= '<li><a href="' . esc_url($business->url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($business->name) . '</a></li>';
        }
        // The stock grid header uses only the first business. Give this render a
        // combined header, without mutating any source business or cached feed.
        $combined = clone $widget['businesses'][0];
        $combined->name = 'Koala Insulation — Canadian Locations';
        $combined->rating = $summary['rating'];
        $combined->review_count = $summary['count'];
        $combined->url = '#ca-landing-review-sources';
        $widget['businesses'] = [$combined];
        $widget['options'] = clone $widget['options'];
        $widget['options']->hide_writereview = true;
        $widget['options']->header_hide_photo = true;
        $sources = '<details id="ca-landing-review-sources"><summary>Reviews from ' . (int) $summary['locations'] . ' Canadian locations</summary><p>A selection of Google reviews is shown below; the combined rating uses each location’s total review count.</p><ul>' . $sources . '</ul></details>';
    }
    foreach ($widget['reviews'] as $review) {
        foreach (['text', 'images'] as $field) { if (!isset($review->$field)) { $review->$field = ''; } }
    }
    $view = new \WP_Rplg_Google_Reviews\Includes\View();
    return '<div id="ca-landing-reviews">' . $sources . $view->render($feed->ID, $widget['businesses'], $widget['reviews'], $widget['options']) . '</div>';
}
function form($campaign = false) {
    if (!in_template() || !function_exists('gravity_form')) { return ''; }
    $form_id = 1;
    if (get_field('use_gravity_form')) {
        $configured = (string) get_field('hero_gravity_form');
        if (preg_match('/^\[gravityform\s+id=["\x27]?([1-9][0-9]*)["\x27]?\b/', $configured, $m)) { $form_id = (int) $m[1]; }
    }
    $heading = get_field('hero_form_heading') ?: 'Talk to Our Insulation Experts Today.';
    $subheading = get_field('hero_form_subheading');
    $submit = get_field('hero_form_submit_text') ?: '';
    $button_filter = static function ($button) use ($submit) {
        if ($submit === '') { return $button; }
        return preg_replace_callback('/\bvalue=(["\x27]).*?\1/', static function ($m) use ($submit) { return 'value=' . $m[1] . esc_attr($submit) . $m[1]; }, $button, 1);
    };
    add_filter('gform_submit_button_' . $form_id, $button_filter);
    try {
        // Reuse the original landing-page form heading and inherited styles.
        $html = '<div class="hero-form-title-wrap"><h5>' . esc_html($heading) . '</h5></div>';
        if ($subheading) { $html .= '<div class="estimate-form-input-wrapper"><div class="consent-wrap"><p style="color:#363636;padding-bottom:15px;">' . esc_html($subheading) . '</p></div></div>'; }
        $html .= do_shortcode('[gravityform id="' . $form_id . '" title="false" description="false" ajax="true" tabindex="200"]');
    } finally { remove_filter('gform_submit_button_' . $form_id, $button_filter); }
    return $html;
}
add_shortcode('koala_ca_landing_hero', function ($attrs) {
    $key = selected_key();
    if (($attrs['template'] ?? '') !== '20847' || !in_template() || !$key) { return ''; }
    $preset = presets()[$key];
    $url = plugins_url('assets/' . $key . '.webp', __FILE__);
    ob_start(); ?>
    <section class="ca-landing-hero" data-hero="<?php echo esc_attr($key); ?>" aria-labelledby="ca-landing-hero-heading">
      <img class="ca-landing-hero__image" src="<?php echo esc_url($url); ?>" width="<?php echo (int) $preset['width']; ?>" height="<?php echo (int) $preset['height']; ?>" alt="" loading="eager" fetchpriority="high" decoding="async">
      <div class="ca-landing-hero__inner">
        <div class="ca-landing-hero__copy">
          <h1 id="ca-landing-hero-heading" class="ca-landing-hero__heading"><?php echo wp_kses($preset['headline'], ['span' => ['class' => []], 'br' => []]); ?></h1>
          <?php echo review_panel(true); ?>
        </div>
        <div class="ca-landing-hero__form hero-form-wrapper"><div class="hero-form"><?php echo form(false); ?></div></div>
      </div>
    </section>
    <?php return ob_get_clean();
});
add_filter('bricks/element/render', function ($render, $element) {
    if (!in_template()) { return $render; }
    if ($element->id === config()['fallback_id'] && selected_key()) { return false; }
    if ($element->id === config()['hero_id'] && !selected_key()) { return false; }
    return $render;
}, 20, 2);
// Scoped repair for the legacy panel: the saved Google feed replaces NiceJob polling.
add_action('bricks/frontend/before_render_data', function ($elements) {
    if (!is_efficiency_page()) { return; }
    foreach ($elements as $element) {
        if (($element['id'] ?? '') === config()['hero_id']) { wp_dequeue_script('koala-reviews'); break; }
    }
});
function request_uses_template() {
    return is_efficiency_page();
}

add_action('wp_enqueue_scripts', function () {
    if (!request_uses_template()) { return; }
    wp_register_style('koala-ca-landing-heroes', false, [], filemtime(__DIR__ . '/assets/heroes.css'));
    wp_enqueue_style('koala-ca-landing-heroes');
    $css = file_get_contents(__DIR__ . '/assets/heroes.css');
    foreach (config()['element_ids'] as $old => $new) { $css = str_replace('brxe-' . $old, 'brxe-' . $new, $css); }
    $css = preg_replace_callback('/url\(([^)]+)\)/', static function ($m) {
        $file = trim($m[1], "\"'");
        return 'url("' . esc_url(plugins_url('assets/' . $file, __FILE__)) . '")';
    }, $css);
    wp_add_inline_style('koala-ca-landing-heroes', $css);
    wp_add_inline_script('all-pages-js', 'window.koalaEfficiencyLanding = true;', 'before');
    if (function_exists('gravity_form_enqueue_scripts')) { gravity_form_enqueue_scripts(1, true); }
}, 99);
// Remove the old image preload at read time only for a selected campaign.
add_filter('get_post_metadata', function ($value, $object_id, $key, $single) {
    static $reading = false;
    if ($reading || (int) $object_id !== (int) (config()['template_id'] ?? 0) || $key !== '_bricks_page_settings' || !selected_key() || !is_efficiency_page()) { return $value; }
    $reading = true;
    $settings = get_post_meta($object_id, $key, true);
    $reading = false;
    if (!is_array($settings)) { return $value; }
    $settings['customScriptsHeader'] = preg_replace_callback('/<link\b[^>]*\brel=["\x27]preload["\x27][^>]*>/i', static function ($match) { return strpos($match[0], '/2025/02/2.webp') !== false ? '' : $match[0]; }, $settings['customScriptsHeader'] ?? '');
    return [$settings];
}, 10, 4);
add_action('wp_head', function () {
    if (!request_uses_template() || !selected_key()) { return; }
    echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url(plugins_url('assets/' . selected_key() . '.webp', __FILE__)) . '">';
}, 2);
