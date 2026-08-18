<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Define constants
 *
 * @since 1.0
 */
define('BRICKS_VERSION', '1.9.7.1');
define('BRICKS_NAME', 'Bricks');
define('BRICKS_TEMP_DIR', 'bricks-temp'); // Template import/export (JSON & ZIP)
define('BRICKS_PATH', trailingslashit(get_template_directory()));    // require_once files
define('BRICKS_PATH_ASSETS', trailingslashit(BRICKS_PATH . 'assets'));
define('BRICKS_URL', trailingslashit(get_template_directory_uri())); // WP enqueue files
define('BRICKS_URL_ASSETS', trailingslashit(BRICKS_URL . 'assets'));
define('BRICKS_REMOTE_URL', 'https://bricksbuilder.io/');
define('BRICKS_REMOTE_ACCOUNT', BRICKS_REMOTE_URL . 'account/');

define('BRICKS_BUILDER_PARAM', 'bricks');
define('BRICKS_BUILDER_IFRAME_PARAM', 'brickspreview');
define('BRICKS_DEFAULT_IMAGE_SIZE', 'large');

define('BRICKS_DB_PANEL_WIDTH', 'bricks_panel_width');
define('BRICKS_DB_BUILDER_SCALE_OFF', 'bricks_builder_scale_off');
define('BRICKS_DB_BUILDER_WIDTH_LOCKED', 'bricks_builder_width_locked');

define('BRICKS_DB_COLOR_PALETTE', 'bricks_color_palette');
define('BRICKS_DB_BREAKPOINTS', 'bricks_breakpoints');
define('BRICKS_DB_GLOBAL_SETTINGS', 'bricks_global_settings');
define('BRICKS_DB_GLOBAL_ELEMENTS', 'bricks_global_elements');
define('BRICKS_DB_GLOBAL_CLASSES', 'bricks_global_classes');
define('BRICKS_DB_GLOBAL_CLASSES_CATEGORIES', 'bricks_global_classes_categories');
define('BRICKS_DB_GLOBAL_CLASSES_LOCKED', 'bricks_global_classes_locked');
define('BRICKS_DB_PSEUDO_CLASSES', 'bricks_global_pseudo_classes');
define('BRICKS_DB_PINNED_ELEMENTS', 'bricks_pinned_elements');
define('BRICKS_DB_SIDEBARS', 'bricks_sidebars');
define('BRICKS_DB_THEME_STYLES', 'bricks_theme_styles');
define('BRICKS_DB_ADOBE_FONTS', 'bricks_adobe_fonts');

define('BRICKS_DB_EDITOR_MODE', '_bricks_editor_mode');
define('BRICKS_BREAKPOINTS_LAST_GENERATED', 'bricks_breakpoints_last_generated');

define('BRICKS_CSS_FILES_LAST_GENERATED', 'bricks_css_files_last_generated');
define('BRICKS_CSS_FILES_LAST_GENERATED_TIMESTAMP', 'bricks_css_files_last_generated_timestamp');
define('BRICKS_CSS_FILES_ADMIN_NOTICE', 'bricks_css_files_admin_notice');

define('BRICKS_CODE_SIGNATURES_LAST_GENERATED', 'bricks_code_signatures_last_generated');
define('BRICKS_CODE_SIGNATURES_LAST_GENERATED_TIMESTAMP', 'bricks_code_signatures_last_generated_timestamp');
define('BRICKS_CODE_SIGNATURES_ADMIN_NOTICE', 'bricks_code_signatures_admin_notice');

/**
 * Syntax since 1.2 (container element)
 *
 * Pre 1.2: '_bricks_page_{$content_type}'
 */
define('BRICKS_DB_PAGE_HEADER', '_bricks_page_header_2');
define('BRICKS_DB_PAGE_CONTENT', '_bricks_page_content_2');
define('BRICKS_DB_PAGE_FOOTER', '_bricks_page_footer_2');
define('BRICKS_DB_PAGE_SETTINGS', '_bricks_page_settings');

define('BRICKS_DB_REMOTE_TEMPLATES', 'bricks_remote_templates');
define('BRICKS_DB_TEMPLATE_SLUG', 'bricks_template');
define('BRICKS_DB_TEMPLATE_TAX_BUNDLE', 'template_bundle');
define('BRICKS_DB_TEMPLATE_TAX_TAG', 'template_tag');
define('BRICKS_DB_TEMPLATE_TYPE', '_bricks_template_type');
define('BRICKS_DB_TEMPLATE_SETTINGS', '_bricks_template_settings');

define('BRICKS_DB_CUSTOM_FONTS', 'bricks_fonts');
define('BRICKS_DB_CUSTOM_FONT_FACES', 'bricks_font_faces');
define('BRICKS_DB_CUSTOM_FONT_FACE_RULES', 'bricks_font_face_rules'); // @since 1.7.2

define('BRICKS_EXPORT_TEMPLATES', 'brick_export_templates');

define('BRICKS_ADMIN_PAGE_URL_LICENSE', admin_url('admin.php?page=bricks-license'));

define('BRICKS_AUTH_CHECK_INTERVAL', 30);

if (!defined('BRICKS_DEBUG ')) {
    define('BRICKS_DEBUG', false);
}

if (!defined('BRICKS_MAX_REVISIONS_TO_KEEP')) {
    define('BRICKS_MAX_REVISIONS_TO_KEEP', 100);
}

/**
 * Multisite constants
 *
 * @since 1.0
 */

// Global data: Color palette
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_COLOR_PALETTE')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_COLOR_PALETTE', false);
}

// Global data: Global classes
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES', false);
}

// Global data: Global classes categories
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES_CATEGORIES')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES_CATEGORIES', false);
}

// Global data: Global elements
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_GLOBAL_ELEMENTS')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_GLOBAL_ELEMENTS', false);
}

/**
 * Use minified assets when SCRIPT_DEBUG is off
 *
 * @since 1.0
 */
if (BRICKS_DEBUG || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)) {
    define('BRICKS_ASSETS_SUFFIX', '');
} else {
    define('BRICKS_ASSETS_SUFFIX', '.min');
}

/**
 * Admin notice if PHP version is older than 5.4
 *
 * Required due to: array shorthand, array dereferencing etc.
 *
 * @since 1.0
 */
if (version_compare(PHP_VERSION, '5.4', '>=')) {
    require_once BRICKS_PATH . 'includes/init.php';
} else {
    add_action(
        'admin_notices',
        function () {
            // translators: %s: PHP version number
            $message = sprintf(esc_html__('Bricks requires PHP version %s+.', 'bricks'), '5.4');
            $html = sprintf('<div class="error">%s</div>', wpautop($message));
            echo wp_kses_post($html);
        }
    );
}


/**
 * Builder check
 *
 * @since 1.0
 */
function bricks_is_builder()
{
    return (!is_admin() && isset($_GET[BRICKS_BUILDER_PARAM]));
}

function bricks_is_builder_iframe()
{
    return (bricks_is_builder() && isset($_GET[BRICKS_BUILDER_IFRAME_PARAM]));
}

function bricks_is_builder_main()
{
    return (bricks_is_builder() && !isset($_GET[BRICKS_BUILDER_IFRAME_PARAM]));
}

function bricks_is_frontend()
{
    return !bricks_is_builder();
}

/**
 * Is AJAX call check
 *
 * @since 1.0
 */
function bricks_is_ajax_call()
{
    return defined('DOING_AJAX') && DOING_AJAX;
}

/**
 * Is WP REST API call check
 *
 * @since 1.5
 */
function bricks_is_rest_call()
{
    return defined('REST_REQUEST') && REST_REQUEST;
}

/**
 * Is builder call (AJAX OR REST API)
 *
 * @since 1.5
 */
function bricks_is_builder_call()
{
    // Use PHP constant BRICKS_IS_BUILDER @since 1.5.5 to perform builder check logic only once
    if (!defined('BRICKS_IS_BUILDER')) {
        define('BRICKS_IS_BUILDER', \Bricks\Builder::is_builder_call());
    }

    return BRICKS_IS_BUILDER;
}


/**
 * Render dynamic data tags inside of a content string
 *
 * Example: Inside an executing Code element, custom plugin, etc.
 *
 * Academy: https://academy.bricksbuilder.io/article/function-bricks_render_dynamic_data/
 *
 * @since 1.5.5
 *
 * @param string $content The content (including dynamic data tags).
 * @param int    $post_id The post ID.
 * @param string $context text, image, link, etc.
 *
 * @return string
 */
function bricks_render_dynamic_data($content, $post_id = 0, $context = 'text')
{
    return \Bricks\Integrations\Dynamic_Data\Providers::render_content($content, $post_id, $context);
}

function get_custom_field_value_current_post($meta_key)
{
    global $post;
    return get_post_meta($post->ID, $meta_key, true);
}

function get_custom_field_value_from_a_post($postId, $meta_key)
{
    return get_post_meta($postId, $meta_key, true);
}

function get_all_locations_data()
{
    $locations_data = [];

    // Query all location posts (you can modify post_type or ffilters as needed)
    $location_posts = get_posts([
        'post_type' => 'location',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);


    foreach ($location_posts as $location) {
        $zip = get_field('location_zipcode', $location->ID);
        $additional_zips_raw = get_field('additional_zipcodes', $location->ID);
        $additional_zips = array_map('trim', explode(',', $additional_zips_raw));
        $hcp_key = get_field('housecall_pro_api_key', $location->ID);
        $sm_key = get_field('location_serviceminder_api_key', $location->ID);
        $url = get_permalink($location->ID);
        $title = get_field('location_name', $location->ID);
        $location_address = get_field('location_address', $location->ID);
        $lat = get_field('location_latitude', $location->ID);
        $long = get_field('location_logitude', $location->ID);
        $phone = get_field('location_phone_number', $location->ID);
        $location_service = get_field('location_area_serviced', $location->ID);
        $webhook_url = get_field('webhook_url', $location->ID);
        $location_service = is_array($location_service)
            ? (is_object($location_service[0]) ? $location_service[0]->post_title : $location_service[0])
            : $location_service;


        $locations_data[] = [
            'zipcode' => $zip,
            'additional_zips' => $additional_zips,
            'hcp_key' => $hcp_key,
            'sm_key' => $sm_key,
            'url' => $url ? $url : '',
            'title' => $title,
            'location_address' => $location_address,
            'lat' => $lat,
            'long' => $long,
            'phone' => $phone,
            'location_service' => $location_service ? $location_service : '',
            'webhook_url' => $webhook_url ? $webhook_url : ''
        ];
    }

    return $locations_data;
}

// 1) Register the CPT
add_action('init', function () {
    register_post_type('form_submission', [
        'label' => 'Form Submissions',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-feedback',
        'capability_type' => 'post',
    ]);
});

// 2) Helper to save any array of data + IP
function save_submission_to_cpt($data)
{
    // get real user IP
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    // include IP in data
    $data['ip_address'] = $ip;

    // build title: IP – First Last or fallback with timestamp
    if (!empty($data['full_name'])) {
        $title = $ip . ' – ' . sanitize_text_field($data['full_name']);
    } else {
        $title = $ip . ' – Submission ' . date('Y-m-d H:i:s');
    }

    // insert the post
    $post_id = wp_insert_post([
        'post_type' => 'form_submission',
        'post_title' => wp_strip_all_tags($title),
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // save each value as meta
    foreach ($data as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    return $post_id;
}

function enqueue_custom_scripts()
{
    global $post;
    $front_page = is_front_page();
    $faq_page = is_page('faq');
    $homeowner_incentives_page = is_page('homeowner-incentives');
    $service_page = is_page('services');
    $single_service_page = is_singular('location-service');
    $location_page = is_page('locations');
    $single_location_page = is_singular('location');
    $single_location_service = is_singular('location-service');
    $why_koala_page = ($post && $post->post_name === 'why-koala');
    $why_reinsulate = ($post && $post->post_name === 'why-reinsulate');

    if ($front_page || $single_location_page || $why_koala_page || $why_reinsulate || $single_service_page) {
        wp_enqueue_script(
            'swiper-bundle',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
            array(),
            null,
            true
        );
    }

    // Register the script first
    wp_register_script(
        'google-maps',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyBOBCV9KYqqwo8CRYhBbHfjBp5Jea72XQk',
        array(),
        null,
        true
    );

    if (!$single_location_page) {
        wp_enqueue_script('google-maps');
    }

    if (!is_admin() && !$single_location_page) {
        // Enqueue dotlottie-player.mjs
        wp_enqueue_script(
            'dotlottie-player',
            'https://cdn.jsdelivr.net/npm/@johanaarstein/dotlottie-player@1.5.23/dist/index.min.js',
            [], // No dependencies
            '0.36.2', // Version (match LottieFiles version or theme version)
            true // Load in footer
        );
    }

    if ($front_page) {
        wp_enqueue_script(
            'custom-map-init',
            get_template_directory_uri() . '/assets/js/custom/custom-map-init.js',
            array('google-maps', 'jquery'),
            null,
            true
        );
    }
    if ($faq_page || $homeowner_incentives_page) {
        wp_enqueue_script(
            'faq-accordion',
            get_template_directory_uri() . '/assets/js/custom/accordion.js',
            array('jquery'),
            null,
            true
        );
    }

    if ($location_page || $single_service_page) {
        wp_enqueue_script(
            'location-page',
            get_template_directory_uri() . '/assets/js/custom/location-page.js',
            array('google-maps', 'jquery'),
            null,
            true
        );
    }

    if ($location_page || $single_service_page) {
        wp_enqueue_script(
            'custom-service-script',
            get_template_directory_uri() . '/assets/js/custom/service-page.js',
            array(),
            null,
            true
        );
    }

    $all_pages_path = get_template_directory() . '/assets/js/custom/all-pages.js';
    $custom_service_path = get_template_directory() . '/assets/js/custom-service.js';
    $all_pages_version = file_exists($all_pages_path) ? filemtime($all_pages_path) : null;
    $custom_service_version = file_exists($custom_service_path) ? filemtime($custom_service_path) : null;

    wp_enqueue_script('all-pages-js', get_template_directory_uri() . '/assets/js/custom/all-pages.js', array('jquery'), $all_pages_version, true);
    wp_enqueue_script('custom-service-js', get_template_directory_uri() . '/assets/js/custom-service.js', array('jquery'), $custom_service_version, true);
    // if (!$single_location_page) {
    //wp_enqueue_script('estimate-js', get_template_directory_uri() . '/assets/js/estimate.js', array('jquery'), time(), true);
    // }


    wp_localize_script('custom-service-js', 'ajaxData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'match_location_nonce' => wp_create_nonce('match_location'),
        'zip_code_in_radius_nonce' => wp_create_nonce('zip_code_in_radius_nonce'),
        'zip_locations' => get_all_locations_data(),
    ]);
    wp_localize_script('estimate-js', 'estimateData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'estimate_sm_form_nonce' => wp_create_nonce('estimate_sm_form_nonce'),
        'estimate_form_nonce' => wp_create_nonce('estimate_form_nonce'),
        'zip_locations' => get_all_locations_data(),
        'acf_webhook_url' => get_field('webhook_url', get_the_ID()) ?: 'https://hooks.zapier.com/hooks/catch/512909/ulpdh7i/',
    ]);
    wp_localize_script('all-pages-js', 'koalaData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'is_location' => $location_page,
        'zip_locations' => get_all_locations_data(),
        'map_pin' => home_url() . '/wp-content/uploads/2024/09/map-pin.svg',
    ]);
    wp_enqueue_style('custom-service-css', get_template_directory_uri() . '/assets/css/custom-service.css');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function display_location_services()
{
    ob_start(); // Start output buffering
    ?>

    <a id="custom-service" class="brxe-dropdown nav-dropdown">
        <div class="brx-submenu-toggle">
            <span>Services</span>
            <button aria-expanded="false" aria-label="Toggle dropdown">
                <svg class="" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path
                        d="M8.99998 9.87855L12.7123 6.16626L13.773 7.22692L8.99998 11.9999L4.22705 7.22692L5.28771 6.16626L8.99998 9.87855Z"
                        fill="white"></path>
                </svg>
            </button>
        </div>
        <ul id="custom-service-ul" class="brxe-div dropdown-content brx-dropdown-content">
            <!-- JavaScript will handle_zapier_webhookpopulate this list -->
        </ul>
    </a>
    <?php

    return ob_get_clean(); // Return the buffered content
}

add_shortcode('location_services', 'display_location_services');

function display_location_services_footer()
{
    ob_start(); // Start output buffering
    ?>

    <div id="custom-service-footer" class="brxe-div footer-links-wrapper">
        <!-- JavaScript will populate this content -->
    </div>
    <?php

    return ob_get_clean(); // Return the buffered content
}

add_shortcode('location_services_footer', 'display_location_services_footer');

function handle_zapier_webhook() {

  // Build payload from POST
  $to_bool = function($v){ return in_array($v, ['1',1,true,'true','on','yes'], true); };

  $payload = [
    'first_name'    => sanitize_text_field($_POST['first_name'] ?? ''),
    'last_name'     => sanitize_text_field($_POST['last_name'] ?? ''),
    'email'         => sanitize_email($_POST['email'] ?? ''),
    'mobile_number' => sanitize_text_field($_POST['mobile_number'] ?? ''),
    'address1'      => sanitize_text_field($_POST['address1'] ?? ''),
    'address2'      => sanitize_text_field($_POST['address2'] ?? ''),
    'city'          => sanitize_text_field($_POST['city'] ?? ''),
    'state'         => sanitize_text_field($_POST['state'] ?? ''),
    'zip'           => sanitize_text_field($_POST['zip'] ?? ''),
    'DoNotText'     => $to_bool(!$_POST['DoNotText'] ?? false),
    'utm_source'    => sanitize_text_field($_POST['utm_source'] ?? ''),
    'utm_medium'    => sanitize_text_field($_POST['utm_medium'] ?? ''),
    'utm_campaign'  => sanitize_text_field($_POST['utm_campaign'] ?? ''),
    'utm_term'      => sanitize_text_field($_POST['utm_term'] ?? ''),
    'utm_content'   => sanitize_text_field($_POST['utm_content'] ?? ''),
    'sm_key'        => sanitize_text_field($_POST['sm_key'] ?? ''),
    'page_url'      => esc_url_raw($_POST['page_url'] ?? wp_get_referer()),
    'action'        => sanitize_text_field($_POST['action'] ?? ''),
    'timestamp'     => current_time('mysql'),
    'source'        => 'wordpress',
  ];

  // Choose webhook:
  $webhook = '';

  // Allow explicit override (only if host is Zapier)
  if ( empty($webhook) && !empty($_POST['webhook']) ) {
    $candidate = esc_url_raw($_POST['webhook']);
    $host = wp_parse_url($candidate, PHP_URL_HOST);
    if ($host === 'hooks.zapier.com') {
      $webhook = $candidate;
    }
  }

  if ( empty($webhook) ) {
    wp_send_json_error(['message' => 'No webhook found'], 404);
  }

  // Send server-side
  $resp = wp_remote_post($webhook, [
    'headers'  => ['Content-Type' => 'application/json'],
    'body'     => wp_json_encode(array_filter($payload, fn($v) => $v !== '' && $v !== null)),
    'timeout'  => 3,
    'blocking' => false, // fire-and-forget so you don’t delay the response
  ]);

  if ( is_wp_error($resp) ) {
    error_log('[Zapier] ' . $resp->get_error_message());
    wp_send_json_error(['message' => 'Webhook failed'], 500);
  }

  wp_send_json_success(['sent' => true]);
}

// AJAX routes
add_action('wp_ajax_handle_zapier_webhook', 'handle_zapier_webhook');
add_action('wp_ajax_nopriv_handle_zapier_webhook', 'handle_zapier_webhook');

function handle_estimate_form_submission()
{
    // Verify reCAPTCHA Enterprise
    $recaptcha_token = sanitize_text_field($_POST['recaptcha_token']);
    $recaptcha_project_id = 'virtual-equator-450216-g8';
    $recaptcha_api_key = 'AIzaSyCIQauwT_clQpb8smKc7Jkxs3176LCKGpc';

    $recaptcha_response = wp_remote_post("https://recaptchaenterprise.googleapis.com/v1/projects/$recaptcha_project_id/assessments?key=$recaptcha_api_key", [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'event' => [
                'token' => $recaptcha_token,
                'siteKey' => '6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt',
                'expectedAction' => 'submit'
            ]
        ])
    ]);

    if (is_wp_error($recaptcha_response)) {
        wp_send_json_error(['message' => 'reCAPTCHA request failed.']);
    }

    $recaptcha_body = json_decode(wp_remote_retrieve_body($recaptcha_response), true);

    if (
        empty($recaptcha_body['tokenProperties']['valid']) ||
        $recaptcha_body['riskAnalysis']['score'] < 0.5
    ) {
        wp_send_json_error(['message' => 'reCAPTCHA failed or suspicious activity detected.']);
    }

    // Check if the nonce is present and valid
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'estimate_form_nonce')) {
        wp_send_json_error(['message' => 'Invalid security token.']);
        wp_die(); // Required to stop execution and return a proper response
    }

    // Sanitize and collect form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['mobile_number']);
    $address1 = sanitize_text_field($_POST['address1']);
    $address2 = sanitize_text_field($_POST['address2']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    $consent = sanitize_text_field($_POST['DoNotText']);
    $consent_email = sanitize_text_field($_POST['DoNotEmail']);
    $key = sanitize_text_field($_POST['key']);
    $utm_source   = sanitize_text_field($_POST['UtmSource'] ?? '');
    $utm_medium   = sanitize_text_field($_POST['UtmMedium'] ?? '');
    $utm_campaign = sanitize_text_field($_POST['UtmCampaign'] ?? '');

    // Prepare data for the first API call (customers endpoint)
    $data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'addresses' => [
            [
                "street" => $address1,
                "street_line_2" => $address2,
                "city" => $city,
                "state" => $state,
                "zip" => $zip,
            ],
        ],
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];
 // Prepare data for the the post type
    $post_type_data = [
        'ip_address' => '',
        'full_name' => $first_name . ' ' . $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'address' => $address1 . ' ' . $address2 . ', ' . $city . ' ' . $state . ' ' . $zip,
    ];
    // First API call
    $response = wp_remote_post('https://api.housecallpro.com/customers', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the form.', 'error' => $response->get_error_message(), 'response' => $response]);
        wp_die();
    }

    $response_body = wp_remote_retrieve_body($response);
    $decoded_response = json_decode($response_body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        wp_die();
    }

    // Extract the customer ID from the first response
    $customer_id = $decoded_response['id'] ?? null;
    if (!$customer_id) {
        wp_send_json_error(['message' => 'Customer ID not returned from the first API call.']);
        wp_die();
    }

    // Prepare data for the second API call (leads endpoint)
    $lead_data = [
        'customer_id' => $customer_id,
        'address' => [
            "street" => $address1,
            "street_line_2" => $address2,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
        ],
        'lead_source' => "Website",
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];

    // Second API call
    $lead_response = wp_remote_post('https://api.housecallpro.com/leads', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($lead_data),
    ]);

    if (is_wp_error($lead_response)) {
        wp_send_json_error(['message' => 'There was an error submitting the lead to the second API endpoint.']);
        wp_die();
    }

    $lead_response_body = wp_remote_retrieve_body($lead_response);
    $decoded_lead_response = json_decode($lead_response_body, true);
    $status_code = wp_remote_retrieve_response_code($lead_response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(
            [
                'message' => 'Invalid JSON response received from the second API.',
                'raw_response' => $lead_response_body,
                'status_code' => $status_code
            ]
        );
        wp_die();
    }
save_submission_to_cpt($post_type_data);
    // Return success response with both API responses
    wp_send_json_success([
        'message' => 'Form submitted successfully.',
        'customer_response' => $decoded_response,
        'lead_response' => $decoded_lead_response,
        'status_code' => $status_code,

    ]);

    wp_die(); // this is required to terminate immediately and return a proper response
}
   

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_submit_estimate_form', 'handle_estimate_form_submission');
add_action('wp_ajax_nopriv_submit_estimate_form', 'handle_estimate_form_submission');

function handle_estimate_sm_form_submission()
{
    // Check if the nonce is present and valid
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'estimate_sm_form_nonce')) {
        wp_send_json_error(['message' => 'Invalid security token.']);
        wp_die(); // Required to stop execution and return a proper response
    }

    // Sanitize and collect form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['mobile_number']);
    $address1 = sanitize_text_field($_POST['address1']);
    $address2 = sanitize_text_field($_POST['address2']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    $consent = sanitize_text_field($_POST['DoNotText']);
    $consent_email = sanitize_text_field($_POST['DoNotEmail']);
    $key = sanitize_text_field($_POST['key']);
    $utm_source   = sanitize_text_field($_POST['UtmSource'] ?? '');
    $utm_medium   = sanitize_text_field($_POST['UtmMedium'] ?? '');
    $utm_campaign = sanitize_text_field($_POST['UtmCampaign'] ?? '');

    // Prepare data for the required payload structure
    $data = [
        "Matches" => [
            [
                "Name" => $first_name . " " . $last_name, // Combine first and last name
                "Phone" => $phone,
                "Email" => $email,
                "Address1" => $address1,
                "Address2" => $address2,
                "City" => $city,
                "State" => $state,
                "Zip" => $zip,
                "LeadSource" => "Website",
                'UtmSource' => $utm_source,
                'UtmMedium' => $utm_medium,
                'UtmCampaign' => $utm_campaign,

            ]
        ],
        "ApiKey" => $key, // Use the 'key' for the 'ApiKey' field
        "ResultCode" => "", // Placeholder, can be modified as per requirements
        "Message" => "" // Placeholder, can be modified as per requirements
    ];
    $post_type_data = [
    'ip_address'    => '',
    'full_name'     => trim($first_name . ' ' . $last_name),
    'email'         => $email,
    'mobile_number' => $phone,
    'address'       => trim($address1 . ' ' . $address2 . ', ' . $city . ' ' . $state . ' ' . $zip),
];

    $response = wp_remote_post('https://serviceminder.io/api/contacts/addupdate', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($data),
    ]);
save_submission_to_cpt($post_type_data);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the form.', 'error' => $response->get_error_message(), 'response' => $response]);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);
        //wp_send_json_success(['message' => 'Form submitted successfully.', 'response' => $response]);
        if (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success(['message' => 'Form submitted successfully.', 'response' => $decoded_response]);
        } else {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        }
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_submit_estimate_sm_form', 'handle_estimate_sm_form_submission');
add_action('wp_ajax_nopriv_submit_estimate_sm_form', 'handle_estimate_sm_form_submission');

function handle_both_submissions()
{

    // Sanitize and collect form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['mobile_number']);
    $address1 = sanitize_text_field($_POST['address1']);
    $address2 = sanitize_text_field($_POST['address2']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    $consent = sanitize_text_field($_POST['DoNotText']);
    $consent_email = sanitize_text_field($_POST['DoNotEmail']);
    $key = sanitize_text_field($_POST['key']);
    $sm_key = sanitize_text_field($_POST['sm_key']);
    $utm_source   = sanitize_text_field($_POST['UtmSource'] ?? '');
    $utm_medium   = sanitize_text_field($_POST['UtmMedium'] ?? '');
    $utm_campaign = sanitize_text_field($_POST['UtmCampaign'] ?? '');

    // Prepare data for the first API call (customers endpoint)
    $data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'addresses' => [
            [
                "street" => $address1,
                "street_line_2" => $address2,
                "city" => $city,
                "state" => $state,
                "zip" => $zip,
            ],
        ],
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];
 $post_type_data = [
        'ip_address' => '',
        'full_name' => $first_name . ' ' . $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'address' => $address1 . ' ' . $address2 . ', ' . $city . ' ' . $state . ' ' . $zip,
    ];
    // First API call
    $response = wp_remote_post('https://api.housecallpro.com/customers', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the form.', 'error' => $response->get_error_message(), 'response' => $response]);
        wp_die();
    }

    $response_body = wp_remote_retrieve_body($response);
    $decoded_response = json_decode($response_body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        wp_die();
    }

    // Extract the customer ID from the first response
    $customer_id = $decoded_response['id'] ?? null;
    if (!$customer_id) {
        wp_send_json_error(['message' => 'Customer ID not returned from the first API call.']);
        wp_die();
    }

    // Prepare data for the second API call (leads endpoint)
    $lead_data = [
        'customer_id' => $customer_id,
        'address' => [
            "street" => $address1,
            "street_line_2" => $address2,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
        ],
        'lead_source' => "Website",
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];

    // Second API call
    $lead_response = wp_remote_post('https://api.housecallpro.com/leads', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($lead_data),
    ]);

    if (is_wp_error($lead_response)) {
        wp_send_json_error(['message' => 'There was an error submitting the lead to the second API endpoint.']);
        wp_die();
    }

    $lead_response_body = wp_remote_retrieve_body($lead_response);
    $decoded_lead_response = json_decode($lead_response_body, true);
    $status_code = wp_remote_retrieve_response_code($lead_response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error([
            'message' => 'Invalid JSON response received from the second API.',
            'raw_response' => $lead_response_body,
            'status_code' => $status_code
        ]);
        wp_die();
    }

    $third_api_response = null;
    if ($status_code === 201) {
        // Prepare data for the third API call
        $third_api_data = [
            "Matches" => [
                [
                    "Name" => $first_name . " " . $last_name,
                    "Phone" => $phone,
                    "Email" => $email,
                    "Address1" => $address1,
                    "Address2" => $address2,
                    "City" => $city,
                    "State" => $state,
                    "Zip" => $zip,
                    "LeadSource" => "Website",
                    'UtmSource' => $utm_source,
                    'UtmMedium' => $utm_medium,
                    'UtmCampaign' => $utm_campaign,
                ]
            ],
            "ApiKey" => $sm_key,
            "ResultCode" => "",
            "Message" => ""
        ];

        // Third API call
        $third_api_response = wp_remote_post('https://serviceminder.io/api/contacts/addupdate', [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($third_api_data),
        ]);
    }

    // Retrieve third API response body
    $third_api_response_body = $third_api_response ? wp_remote_retrieve_body($third_api_response) : null;
    $decoded_third_api_response = $third_api_response_body ? json_decode($third_api_response_body, true) : null;
    save_submission_to_cpt($post_type_data);

    // Return success response with all API responses
    wp_send_json_success([
        'message' => 'Form submitted successfully.',
        'customer_response' => $decoded_response,
        'lead_response' => $decoded_lead_response,
        'third_api_response' => $decoded_third_api_response,
        'status_code' => $status_code,
    ]);

    wp_die();
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_handle_both_submissions', 'handle_both_submissions');
add_action('wp_ajax_nopriv_handle_both_submissions', 'handle_both_submissions');

// Hook into wp_head to run the sessionStorage-based script in the head section
function inject_location_before_head_tag_script()
{
    // Retrieve the national GTM tag value from the post type "national-gtm-tag"
    $national_gtm_tag_value = '';
    $national_gtm_post = get_posts(array(
        'post_type' => 'national-gtm-tag',
        'name' => 'tag',
        'posts_per_page' => 1
    ));

    if (!empty($national_gtm_post)) {
        $national_gtm_tag_value = get_field('national_gtm_tag_script', $national_gtm_post[0]->ID); // Assuming ACF is used for the field
    }
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve the HTML string from sessionStorage
            var locationBeforeHeadTag = sessionStorage.getItem('location_before_head_tag');
            var locationBeforeHeadTagNational = <?php echo json_encode($national_gtm_tag_value); ?>;

            //console.log('locationBeforeHeadTag--', locationBeforeHeadTag);

            if (locationBeforeHeadTag && locationBeforeHeadTag !== "") {
                // Create a temporary container to hold the HTML string
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = locationBeforeHeadTag;
                //console.log('tempDiv--', tempDiv.innerHTML);

                // Append each node to the head tag
                Array.from(tempDiv.childNodes).forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        document.head.appendChild(node);
                    }
                });
            } else {
                //console.log('locationBeforeHeadTagNational--', locationBeforeHeadTagNational);
                // Create a temporary container to hold the HTML string
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = locationBeforeHeadTagNational;
                //console.log('tempDiv--', tempDiv.innerHTML);

                // Append each node to the head tag
                Array.from(tempDiv.childNodes).forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        document.head.appendChild(node);
                    }
                });
            }
        });
    </script>
    <?php
}
// add_action('wp_head', 'inject_location_before_head_tag_script');


// Hook into wp_footer to run the sessionStorage-based script in the before body tag
function inject_location_before_body_tag_script()
{
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve the HTML string from sessionStorage
            var locationBeforeBodyTag = sessionStorage.getItem('location_before_body_tag');
            //console.log('locationBeforeBodyTag--', locationBeforeBodyTag);

            if (locationBeforeBodyTag) {
                // Create a temporary container to hold the HTML string
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = locationBeforeBodyTag;
                //console.log('tempDiv--', tempDiv.innerHTML);

                // Append each node to the head tag
                Array.from(tempDiv.childNodes).forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        document.body.appendChild(node);
                    }
                });
            }
        });
    </script>
    <?php
}
// add_action('wp_footer', 'inject_location_before_body_tag_script');

// function koala_add_location_service_rewrite_rules($rules)
// {
//     $new_rules = array(
//         '([^/]+)/services/([^/]+)/?$' => 'index.php?location=$matches[1]&service=$matches[2]',
//     );

//     return $new_rules + $rules; // Add new rules at the top
// }
// add_filter('rewrite_rules_array', 'koala_add_location_service_rewrite_rules');

// function koala_add_blog_location_rewrite_rules($rules)
// {
//     $new_rules = array(

//         '([^/]+)/blog-page/([^/]+)/?$' => 'index.php?location=$matches[1]&blog=$matches[2]',
//     );

//     return $new_rules + $rules; // Add new rules at the top
// }
// add_filter('rewrite_rules_array', 'koala_add_blog_location_rewrite_rules');

// function add_custom_query_vars($vars)
// {
//     $vars[] = 'location';
//     $vars[] = 'service';
//     $vars[] = 'blog';
//     return $vars;
// }
// add_filter('query_vars', 'add_custom_query_vars');

// function koala_custom_template_redirect($template)
// {
//     if (get_query_var('location') && get_query_var('service')) {
//         // Load your custom template
//         return get_stylesheet_directory() . '/services-single.php';
//     }
//     return $template;
// }
// add_filter('template_include', 'koala_custom_template_redirect');

// function koala_custom_blog_template_redirect($template)
// {
//     if (get_query_var('location') && get_query_var('blog')) {
//         // Load your custom template
//         return get_stylesheet_directory() . '/blog-page-single.php';
//     }
//     return $template; 
// }
// add_filter('template_include', 'koala_custom_blog_template_redirect');

// function koala_rewrite_rules() {
//     // Location: /location_slug/
//     add_rewrite_rule(
//         '^([^/]+)/?$',
//         'index.php?post_type=location&name=$matches[1]',
//         'top'
//     );
//     // Location-service: /ca/location_slug/services/service_slug/
//     // Location-service: /location_slug/services/service_slug/
//     add_rewrite_rule(
//         '^([^/]+)/services/([^/]+)/?$',
//         'index.php?custom_location_service_redirect=1&location_slug=$matches[1]&service_slug=$matches[2]',
//         'top'
//     );
//     // Blog-location: /location_slug/blog/blog_slug/
//     add_rewrite_rule(
//         '^([^/]+)/blog/([^/]+)/?$',
//         'index.php?custom_location_blog_redirect=1&location_slug=$matches[1]&blog_slug=$matches[2]',
//         'top'
//     );
// }
// add_action('init', 'koala_rewrite_rules');

function custom_location_service_rewrite_rules($rules)
{
    $new_rules = array(
        '([^/]+)/services/([^/]+)/?$' => 'index.php?custom_location_service_redirect=1&location_slug=$matches[1]&service_slug=$matches[2]',
    );
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'custom_location_service_rewrite_rules');

function custom_location_blog_rewrite_rules($rules)
{
    $new_rules = array(
        '([^/]+)/blog/([^/]+)/?$' => 'index.php?custom_location_blog_redirect=1&location_slug=$matches[1]&blog_slug=$matches[2]',
    );
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'custom_location_blog_rewrite_rules');

function custom_location_service_query_vars($vars) {
    $vars[] = 'custom_location_service_redirect';
    $vars[] = 'location_slug';
    $vars[] = 'service_slug';
    return $vars;
}
add_filter('query_vars', 'custom_location_service_query_vars');

function custom_location_blog_query_vars($vars) {
    $vars[] = 'custom_location_blog_redirect';
    $vars[] = 'location_slug';
    $vars[] = 'blog_slug';
    return $vars;
}
add_filter('query_vars', 'custom_location_blog_query_vars');

function custom_location_service_template()
{
    if (get_query_var('custom_location_service_redirect')) {
        $location_slug = get_query_var('location_slug');
        $service_slug = get_query_var('service_slug');

        // Generate the expected post slug: "spray-foam-insulation-austin"
        $expected_slug = $service_slug . '-' . $location_slug;

        // Query the post by post_name (slug)
        $args = array(
            'post_type' => 'location-service', // Ensure you're querying the correct post type
            'name' => $expected_slug,     // Query by post_name (slug)
            'posts_per_page' => 1,
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            // We have found a matching post
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                // Debug: confirm term exists
                $term = get_term_by('slug', 'blown-in-insulation-services', 'service_category');

                // Get all term slugs on the post (more reliable than get_the_terms in loops)
                $term_slugs = wp_get_post_terms($post_id, 'service_category', array('fields' => 'slugs'));

                $map = my_location_service_category_template_map();

                // Pick the first matching template ID from the map
                $template_id = 0;
                foreach ($term_slugs as $slug) {
                    if (!empty($map[$slug])) {
                        $template_id = (int) $map[$slug];
                        break;
                    }
                }

                // Get the custom field value for the related location (this is a relationship field)
                $location_post = get_post_meta(get_the_ID(), 'related_location', true); // This should return an array of post IDs

                if (!empty($location_post)) {
                    // Assuming 'related_location' is a relationship field that stores post IDs, get the first related location ID
                    $location_post_id = $location_post[0];  // Get the first related location ID
                    $location_name = get_the_title($location_post_id);  // Get the title of the related location post
                } else {
                    $location_name = 'No Location Available'; // Fallback in case no location is found
                }

                // Get the service name (make sure you replace 'service_name' with the actual custom field key)
                $service_name = get_post_meta(get_the_ID(), 'service_name', true);

                if (!$service_name) {
                    $service_name = 'No Service Name Available'; // Fallback if no service name is found
                }

                // Include the custom template for displaying the post
                if ($template_id > 0) {
                  get_header();

                  echo '<main id="brx-content">';

                    if (class_exists('\Bricks\Templates')) {
                      echo (new \Bricks\Templates())->render_shortcode(array('id' => $template_id));
                    } else {
                      echo do_shortcode('[bricks_template id="' . $template_id . '"]');
                    }
                  echo '</main>';

                  get_footer();
                } else {
                  include get_template_directory() . '/services-single.php';
                }

                exit;

            }
        } else {
            // Redirect to 404 if no matching post found
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            include(get_template_directory() . '/404.php');
            exit;
        }
    }
}
add_action('template_redirect', 'custom_location_service_template');
/**
 * Map service_category term slug => Bricks template ID
 * Return 0 (or null) to use the PHP template.
 */
function my_location_service_category_template_map() {
    $map = array(
        'spray-foam-insulation-services' => 20483
    );

    // Let you override in a child theme or plugin
    return apply_filters('my_location_service_category_template_map', $map);
}

function custom_location_blog_template()
{
    if (get_query_var('custom_location_blog_redirect')) {
        $location_slug = get_query_var('location_slug');
        $blog_slug = get_query_var('blog_slug');

        // Query the post by post_name (slug)
        $args = array(
            'post_type' => 'blog-location', // Your custom post type
            'name' => $blog_slug,      // Query by the blog slug
            'posts_per_page' => 1,                // Only 1 post needed
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            // We have found a matching post
            while ($query->have_posts()):
                $query->the_post();

                // Get the custom field value for the related location (relationship field)
                $location_post = get_post_meta(get_the_ID(), 'related_location', true); // This should return an array of post IDs

                // Get the location name (title of the related location post)
                if (!empty($location_post)) {
                    // Assuming 'related_location' stores post IDs, get the first related location ID
                    $location_post_id = $location_post[0]; // Get the first related location ID
                    $location_name = get_the_title($location_post_id); // Get the title of the related location post
                } else {
                    $location_name = 'No Location Available'; // Fallback if no location found
                }

                // Get the blog post title (post name)
                $blog_name = get_the_title(); // This will get the blog post's title

                // If no blog name, fallback
                if (!$blog_name) {
                    $blog_name = 'No Blog Available'; // Fallback text if no blog name found
                }

                // Include the custom template for displaying the post
                include(get_template_directory() . '/blog-page-single.php');
                exit;

            endwhile;
        } else {
            // Redirect to 404 if no matching post found
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            include(get_template_directory() . '/404.php');
            exit;
        }
    }
}
add_action('template_redirect', 'custom_location_blog_template');

add_action('template_redirect', function () {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Match /ca/{location}/landing-pages/{page-slug}
    if (!preg_match('#^ca/([^/]+)/landing-pages/([^/]+)/?$#', $uri, $matches)) {
        return;
    }

    $location_slug    = sanitize_title($matches[1]);
    $landing_page_slug = sanitize_title($matches[2]);

    // Validate location exists
    $location_obj = get_page_by_path($location_slug, OBJECT, 'location');
    if (!$location_obj) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        include(get_template_directory() . '/404.php');
        exit;
    }

    // Find landing page post by slug
    $query = new WP_Query(array(
        'post_type'      => 'landing-pages',
        'name'           => $landing_page_slug,
        'posts_per_page' => 1,
    ));

    if (!$query->have_posts()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        include(get_template_directory() . '/404.php');
        exit;
    }

    $query->the_post();
    $post_id = get_the_ID();

    // Validate the post's related_location matches the URL location
    $related = get_post_meta($post_id, 'related_location', true);
    $related_ids = is_array($related) ? $related : (is_numeric($related) ? [(int) $related] : []);

    $location_match = false;
    foreach ($related_ids as $rid) {
        $loc = get_post($rid);
        if ($loc && $loc->post_name === $location_slug) {
            $location_match = true;
            break;
        }
    }

    if (!$location_match) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        include(get_template_directory() . '/404.php');
        exit;
    }

    // Set query context
    global $wp_query, $post;
    $current_post = get_post($post_id);
    $wp_query->is_404           = false;
    $wp_query->is_singular      = true;
    $wp_query->is_page          = true;
    $wp_query->queried_object    = $current_post;
    $wp_query->queried_object_id = $post_id;
    $wp_query->posts             = [$current_post];
    $wp_query->post              = $current_post;
    status_header(200);

    // Set query vars so nav can read location context
    set_query_var('location_slug', $location_slug);
    set_query_var('landing_page_slug', $landing_page_slug);

    // Inject canonical URL into <head>
    add_action('wp_head', function () use ($location_slug, $landing_page_slug) {
        $canonical_url = home_url("/{$location_slug}/landing-pages/{$landing_page_slug}");
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />';
    });

    $template_id = my_landing_page_template_id();

    if ($template_id > 0) {
        // Set $post to the location for get_header() so wp_enqueue_scripts
        // inherits the location's HCP key, SM key, webhook URL — same as location pages.
        $post = $location_obj;
        setup_postdata($location_obj);

        get_header();

        // Swap back to the landing page post so Bricks reads the correct ACF fields.
        $post = $current_post;
        setup_postdata($current_post);

        echo '<main id="brx-content">';
        if (class_exists('\Bricks\Templates')) {
            echo (new \Bricks\Templates())->render_shortcode(array('id' => $template_id));
        } else {
            echo do_shortcode('[bricks_template id="' . $template_id . '"]');
        }
        echo '</main>';
        get_footer();
    } else {
        include(get_template_directory() . '/landing-page-single.php');
    }

    exit;
});

/**
 * Bricks template ID for landing pages.
 * Update the ID to match the template created in Bricks.
 */
function my_landing_page_template_id() {
    return apply_filters('my_landing_page_template_id', 20847);
}

add_filter('post_type_link', function ($url, $post) {
    if ($post->post_type !== 'landing-pages') {
        return $url;
    }

    $related = get_post_meta($post->ID, 'related_location', true);
    $related_ids = is_array($related) ? $related : (is_numeric($related) ? [(int) $related] : []);

    if (empty($related_ids)) {
        return $url;
    }

    $location = get_post($related_ids[0]);
    if (!$location) {
        return $url;
    }

    return home_url('/' . $location->post_name . '/landing-pages/' . $post->post_name . '/');
}, 10, 2);

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/location-data/(?P<slug>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_location_data',
        'permission_callback' => '__return_true', // Make sure it's publicly accessible
    ]);
});

function get_location_data($data)
{
    $slug = $data['slug'];

    // Query for the location post using the slug
    $location_post = get_posts([
        'post_type' => 'location',
        'name' => $slug,
        'post_status' => 'publish',
        'numberposts' => 1,
    ]);

    if (empty($location_post)) {
        return new WP_Error('no_location', 'Location not found', ['status' => 404]);
    }

    // Extract the required data (e.g., post title)
    $location_data = [
        'name' => get_post_meta($location_post[0]->ID, 'location_name', true),
        'address' => get_post_meta($location_post[0]->ID, 'location_address', true),
        'url' => get_permalink($location_post[0]->ID),
        'phone1' => get_post_meta($location_post[0]->ID, 'location_phone_number', true),
        'phone2' => get_post_meta($location_post[0]->ID, 'location_phone_number2', true),
        'state' => get_post_meta($location_post[0]->ID, 'location_state', true),
        'nicejobId' => get_post_meta($location_post[0]->ID, 'location_nicejob_id', true),
        'hcpKey' => get_post_meta($location_post[0]->ID, 'housecall_pro_api_key', true),
        'smKey' => get_post_meta($location_post[0]->ID, 'location_serviceminder_api_key', true),
        'grShortcode' => get_post_meta($location_post[0]->ID, 'google_review_shortcode', true),
        'fbLink' => get_post_meta($location_post[0]->ID, 'location_facebook_link', true),
        'instaLink' => get_post_meta($location_post[0]->ID, 'location_instagram_link', true),
        'linkedinLink' => get_post_meta($location_post[0]->ID, 'location_linkedin_link', true),
        'ytLink' => get_post_meta($location_post[0]->ID, 'location_youtube_link', true),
        'scriptInHead' => get_post_meta($location_post[0]->ID, 'script_in_head_tag', true),
//         'scriptBeforeHead' => get_post_meta($location_post[0]->ID, 'location_before_head_tag_script', true),
        'scriptInBody' => get_post_meta($location_post[0]->ID, 'script_in_body_tag', true),
        'services' => [],
        'wkPage' => [],
        'wrPage' => [],
        'hoPage' => [],
        'rlPage' => [],
        'blogPage' => [],
        'navText' => get_post_meta($location_post[0]->ID, 'location_nav_top_text', true),
        'navLink' => get_post_meta($location_post[0]->ID, 'location_nav_top_link', true),
        'faqs' => [],
    ];

    // Fetch the custom field for services (location_service) as an array of post IDs
    $custom_location_services_arr = get_post_meta($location_post[0]->ID, 'location_service', true);

    if (is_array($custom_location_services_arr)) {
        foreach ($custom_location_services_arr as $custom_location_services_arr_item) {
            $title = get_post_meta($custom_location_services_arr_item, 'location_service_name', true);
            $link = get_permalink($custom_location_services_arr_item);

            // Create an associative array (object in JSON)
            $serviceObject = [
                'title' => $title,
                'link' => $link
            ];

            $location_data['services'][] = $serviceObject; // Append to the array
        }
    }

    // Process related pages (wkPage, wrPage, hoPage, rlPage, blogPage) similarly:
    $related_pages = [
        'wkPage' => 'related_wk_page',
        'wrPage' => 'related_wr_page',
        'hoPage' => 'related_ho_page',
        'blogPage' => 'related_blog_page'
    ];

    foreach ($related_pages as $key => $meta_key) {
        $custom_location_arr = get_post_meta($location_post[0]->ID, $meta_key, true);
        if (is_array($custom_location_arr)) {
            foreach ($custom_location_arr as $custom_location_item) {
                $link = get_permalink($custom_location_item);

                $serviceObject = ['link' => $link];
                $location_data[$key][] = $serviceObject; // Append to the respective array
            }
        }
    }

    // Process related FAQ pages
    $custom_location_faqs_arr = get_post_meta($location_post[0]->ID, 'related_faqs', true);
    if (is_array($custom_location_faqs_arr)) {
        foreach ($custom_location_faqs_arr as $custom_location_faqs_arr_item) {
            $title = get_custom_field_value_from_a_post($custom_location_faqs_arr_item, 'faq_name');
            $content = get_custom_field_value_from_a_post($custom_location_faqs_arr_item, 'faq_content');

            $faqObject = [
                'title' => $title,
                'content' => $content
            ];

            $location_data['faqs'][] = $faqObject; // Append to the array
        }
    }


    $custom_location_rl_arr = get_post_meta($location_post[0]->ID, 'related_rl_page', true);
    if (is_array($custom_location_rl_arr)) {
        foreach ($custom_location_rl_arr as $custom_location_rl_arr_item) {
            // Get the terms for the specified taxonomy
            $terms = wp_get_post_terms($custom_location_rl_arr_item, 'resources-page-type');
            $term_slugs = [];

            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    $term_slugs[] = $term->slug; // Get term slug
                }
            }

            // Fetch post details without skipping 'areas-served'
            $title = get_the_title($custom_location_rl_arr_item);
            $link = get_permalink($custom_location_rl_arr_item);

            $term_names = array_map(function ($term) {
                return $term->name;
            }, $terms);

            // Create an associative array (object in JSON)
            $rlObject = [
                'title' => $title,
                'link' => $link,
                'terms' => $term_names,
            ];

            $location_data['rlPage'][] = $rlObject; // Append to the array
        }
    }
    return $location_data;
}

add_action('wp_ajax_load_initial_blogs', 'load_initial_blogs_callback');
add_action('wp_ajax_nopriv_load_initial_blogs', 'load_initial_blogs_callback');

function load_initial_blogs_callback()
{
    load_blogs_query(array('posts_per_page' => 9, 'paged' => 1));
    wp_die();
}

add_action('wp_ajax_search_blogs', 'search_blogs_callback');
add_action('wp_ajax_nopriv_search_blogs', 'search_blogs_callback');

function search_blogs_callback()
{
    $search_query = sanitize_text_field($_POST['query']);
    load_blogs_query(array(
        //     'posts_per_page' => 9,
        'paged' => 1,
        's' => $search_query,
        'search_columns' => array('post_title'),
    ));
    wp_die();
}

add_action('wp_ajax_load_more_blogs', 'load_more_blogs_callback');
add_action('wp_ajax_nopriv_load_more_blogs', 'load_more_blogs_callback');

function load_more_blogs_callback()
{
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 2;
    load_blogs_query(array(
        'posts_per_page' => 9,
        'paged' => $paged,
    ));
    wp_die();
}

function load_blogs_query($args)
{
    $defaults = array(
        'post_type' => 'blog-article', // Replace with your post type
    );
    $args = wp_parse_args($args, $defaults);

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $blog_id = get_the_ID();
            $blog_title = get_the_title($blog_id);
            $blog_date = get_field('date', $blog_id); // ACF field for date
            $blog_link = get_permalink($blog_id);
            $blog_image = get_field('thumbnail_image', $blog_id); // ACF field for thumbnail
            $blog_taxonomy_terms = get_the_terms($blog_id, 'category');
            $blog_taxonomy_names = $blog_taxonomy_terms && !is_wp_error($blog_taxonomy_terms)
                ? wp_list_pluck($blog_taxonomy_terms, 'name')
                : [];

            ?>
            <li class="brxe-bhgzqc brxe-div" data-animi="up" data-duration="0.6">
                <?php if ($blog_image): ?>
                    <div class="brxe-pabbtz brxe-block">
                        <img width="593" height="465" src="<?php echo esc_url($blog_image); ?>"
                            class="brxe-ygbmyt brxe-image image-cover css-filter size-large"
                            alt="<?php echo esc_attr($blog_title); ?>" />
                    </div>
                <?php endif; ?>
                <div class="brxe-swwkfr brxe-block">
                    <div class="brxe-mhbrou brxe-block">
                        <div class="brxe-pojbhz brxe-text-basic text-size-regular">
                            <?php echo esc_html($blog_date); ?>
                        </div>
                        <h5 class="brxe-lhjnhb brxe-post-title heading-style-h5">
                            <a href="<?php echo esc_url($blog_link); ?>">
                                <?php echo esc_html($blog_title); ?>
                            </a>
                        </h5>
                    </div>
                    <a href="<?php echo esc_url($blog_link); ?>" class="brxe-fmfqwh brxe-div btn-secondary">
                        <div class="brxe-wbymrh brxe-text-basic">Read More</div>
                    </a>
                    <div class="brxe-ehypiw brxe-div tag bricks-lazy-hidden">
                        <div class="brxe-xuqski brxe-text-basic">
                            <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                        </div>
                    </div>
                </div>
            </li>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No blogs found.</p>';
    }
}

function handle_get_zip_codes_in_radius()
{
    // Check nonce if needed (recommended for security)
    // if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_zip_codes_in_radius_nonce')) {
    //     wp_send_json_error(['message' => 'Invalid security token.']);
    //     wp_die();
    // }

    $rawZip = sanitize_text_field($_POST['zip_code']);
    $cleanZip = strtoupper(preg_replace('/\s+/', '', $rawZip));

    if (strlen($cleanZip) === 6) {
        $formattedZip = substr($cleanZip, 0, 3) . ' ' . substr($cleanZip, 3);
    } else {
        // Fallback for non-standard inputs
        $formattedZip = $cleanZip;
    }

    $encodedZip = urlencode($formattedZip);

    $radius = sanitize_text_field($_POST['radius']);
    $apiKey = sanitize_text_field($_POST['api_key']);

    $url = "https://www.zipcodeapi.com/rest/v2/CA/{$apiKey}/radius.json/{$encodedZip}/{$radius}/mile";

    $response = wp_remote_get($url, [
        'method' => 'GET',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error fetching the ZIP codes.']);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);

        // Check for API-specific error messages inside the successful HTTP response
        if (isset($decoded_response['error_msg'])) {
            wp_send_json_error(['message' => $decoded_response['error_msg']]);
        } 
        elseif (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success(['message' => 'ZIP codes fetched successfully.', 'response' => $decoded_response]);
        } else {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        }
    }

    wp_die();
}
// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_get_zip_codes_in_radius', 'handle_get_zip_codes_in_radius');
add_action('wp_ajax_nopriv_get_zip_codes_in_radius', 'handle_get_zip_codes_in_radius');

function handle_get_zip_codes_distance_in_miles() {
    // Get the input ZIP code
    $input_zip = isset($_POST['input_zip']) ? sanitize_text_field($_POST['input_zip']) : '';
    $nearby_zips_raw = isset($_POST['nearby_zips']) ? $_POST['nearby_zips'] : '';

    // Decode JSON string received from JavaScript
    $nearby_zips = json_decode(stripslashes($nearby_zips_raw), true);

    if (empty($input_zip) || empty($nearby_zips) || !is_array($nearby_zips)) {
        wp_send_json_error(['message' => 'Missing or invalid input ZIP or nearby ZIPs.']);
    }

    $api_key = "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII"; // Ideally, store this securely
    $base_url = "https://www.zipcodeapi.com/rest/v2/CA/$api_key/distance.json";
    $distances = [];

    foreach ($nearby_zips as $zip) {
        $zip = sanitize_text_field($zip);
        $api_url = "$base_url/$input_zip/$zip/mile";

        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            error_log("ZIP API error: " . $response->get_error_message()); // Log errors
            continue;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['distance'])) {
            $distances[] = ['zip' => $zip, 'distance' => $data['distance']];
        }
    }

    // Sort ZIP codes by distance (ascending order)
    usort($distances, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    wp_send_json_success($distances);
}

// Register AJAX actions
add_action('wp_ajax_get_zip_codes_distance_in_miles', 'handle_get_zip_codes_distance_in_miles');
add_action('wp_ajax_nopriv_get_zip_codes_distance_in_miles', 'handle_get_zip_codes_distance_in_miles');

function remove_trailing_slash_on_pages($string, $type)
{
    if (in_array($type, array('single', 'page'))) {
        return untrailingslashit($string);
    }
    return $string;
}
add_filter('user_trailingslashit', 'remove_trailing_slash_on_pages', 10, 2);

add_filter('wpseo_canonical', function ($canonical) {
    return untrailingslashit($canonical); });

function redirect_trailing_slash($redirect_url, $requested_url)
{ // Only redirect if the requested URL ends with a slash (and is not the homepage)
    if (!is_front_page() && substr($requested_url, -1) === '/') {
        return untrailingslashit($requested_url);
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'redirect_trailing_slash', 10, 2);

function custom_body_code()
{
    echo '<div id="loader-wrapper">
    
</div>
';
// <dotlottie-player src="https://lottie.host/27636781-5f64-413f-a584-a0d167232e0e/hYfTDaqXi1.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
}
add_action('wp_footer', 'custom_body_code');

/*
add_action('init', function () {
    error_log('CURRENT URI: ' . $_SERVER['REQUEST_URI']);
});
*/

add_filter('redirect_canonical', function ($redirect_url) {
    if (preg_match('#^/ca/[^/]+/[^/]+#', $_SERVER['REQUEST_URI'])) {
        return false;
    }
    return $redirect_url;
});

add_action('template_redirect', function () {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Match /ca/{location}/{subpage}
    if (preg_match('#^ca/([^/]+)/([^/]+)$#', $uri, $matches)) {
        $location_slug = $matches[1];
        $virtual_page_slug = $matches[2];

        // Check if there's a real Page for this slug (e.g. "why-koala", "faq", etc.)
        $real_page = get_page_by_path($virtual_page_slug);
        if ($real_page) {
            global $wp_query;
            $wp_query->is_404 = false;
            $wp_query->is_page = true;
            $wp_query->queried_object = $real_page;
            $wp_query->queried_object_id = $real_page->ID;
            $wp_query->posts = [$real_page];
            $wp_query->post = $real_page;
            setup_postdata($real_page);
            include get_page_template();
            exit;
        }
    }
});

/*
add_action( 'init', function() {
    flush_rewrite_rules();
}, 99 );
*/

add_action('wp_ajax_match_location_by_zip', 'match_location_by_zip');
add_action('wp_ajax_nopriv_match_location_by_zip', 'match_location_by_zip');

function match_location_by_zip()
{
    // Check nonce if needed in production
    // check_ajax_referer('match_location', 'nonce');
    // 
    $input_zip = strtoupper(preg_replace('/\s+/', '', sanitize_text_field($_POST['zip_code'])));

    $args = [
        'post_type' => 'location',
        'posts_per_page' => -1,
    ];

    $query = new WP_Query($args);

    $all_locations = [];

    foreach ($query->posts as $post) {
        $main_zip = get_field('location_zipcode', $post->ID);
        $additional_zips_raw = get_field('additional_zipcodes', $post->ID);

        $additional_zips_array = [];
        if (is_array($additional_zips_raw)) {
            $additional_zips_array = $additional_zips_raw;
        } elseif (is_string($additional_zips_raw)) {
            $additional_zips_array = explode(',', $additional_zips_raw);
        }

        // Merge main zip and additional zips
        $all_raw_zips = array_merge([$main_zip], $additional_zips_array);
        
        $comparison_zips = [];
        foreach ($all_raw_zips as $z) {
            if (is_string($z) || is_numeric($z)) {
                // Remove spaces and uppercase the database value
                $comparison_zips[] = strtoupper(preg_replace('/\s+/', '', $z));
            }
        }

        $location_data = [
            'title' => html_entity_decode(get_the_title($post), ENT_QUOTES, 'UTF-8'),
            'address' => get_field('location_address', $post->ID),
            'state' => get_field('location_state', $post->ID),
            'phone' => get_field('location_phone_number', $post->ID),
            'zipcode' => $main_zip, // Keep original format for display
            'website' => get_the_permalink($post),
            'key' => get_field('housecall_pro_api_key', $post->ID),
            'sm_key' => get_field('location_serviceminder_api_key', $post->ID),
            'additional_zipcodes' => $additional_zips_array, // Keep original format for display
            'id'   => $post->ID,
            'slug' => $post->post_name,
        ];

        if (in_array($input_zip, $comparison_zips)) {
            wp_send_json_success([
                'matched' => true,
                'location' => $location_data,
            ]);
            // Stop execution here since we found a match
            return; 
        }

        $all_locations[] = $location_data;
    }

    // No match found – send all locations as fallback
    wp_send_json_success([
        'matched' => false,
        'locations' => $all_locations,
    ]);
}



function output_custom_or_default_gtm_head()
{
    $custom_head_script = '';

    if (is_singular('location')) {
        $custom_head_script = get_field('script_in_head_tag');
    }

    // Keep noscript fallbacks in the original HTML. JavaScript-dependent
    // markup is queued and injected only after visitor interaction.
    $has_custom_head_script = !empty($custom_head_script);
    $custom_head_noscript = '';
    if ($custom_head_script) {
        $custom_head_script = preg_replace_callback(
            '/<noscript\b[^>]*>.*?<\/noscript>/is',
            function ($matches) use (&$custom_head_noscript) {
                $custom_head_noscript .= $matches[0];
                return '';
            },
            $custom_head_script
        );
        echo $custom_head_noscript;
    }

    ?>
    <script type="text/javascript">
        window.koalaHeadScriptOutput = <?php echo $has_custom_head_script ? 'true' : 'false'; ?>;
        window.koalaBodyScriptOutput = false;
        window.koalaLocationHeadMarkup = <?php echo wp_json_encode($custom_head_script ?: ''); ?>;
        window.koalaLocationBodyMarkup = '';
        window.koalaInteractionScriptsLoaded = false;

        // Recreate saved script elements so they execute, while preserving
        // the order of non-async external and inline scripts.
        window.koalaInjectLocationMarkup = async function(markup, target) {
            if (!markup || !target) return;

            var template = document.createElement('template');
            template.innerHTML = markup;
            var fragment = template.content.cloneNode(true);
            var scripts = Array.prototype.slice.call(fragment.querySelectorAll('script'));
            var queuedScripts = [];

            scripts.forEach(function(oldScript) {
                var marker = document.createComment('koala-location-script');
                oldScript.parentNode.replaceChild(marker, oldScript);
                queuedScripts.push({ source: oldScript, marker: marker });
            });

            target.appendChild(fragment);

            for (var i = 0; i < queuedScripts.length; i++) {
                var item = queuedScripts[i];
                var script = document.createElement('script');

                Array.prototype.slice.call(item.source.attributes).forEach(function(attribute) {
                    script.setAttribute(attribute.name, attribute.value);
                });

                if (item.source.src) {
                    if (!item.source.hasAttribute('async')) {
                        script.async = false;
                    }
                    await new Promise(function(resolve) {
                        script.addEventListener('load', resolve, { once: true });
                        script.addEventListener('error', resolve, { once: true });
                        item.marker.parentNode.replaceChild(script, item.marker);
                    });
                } else {
                    script.textContent = item.source.textContent;
                    item.marker.parentNode.replaceChild(script, item.marker);
                }
            }
        };

        // Delay analytics, reCAPTCHA, Hotjar, and location markup until the
        // visitor first interacts with the page.
        var koalaThirdPartyScriptsLoaded = false;

        async function loadThirdPartyScripts() {
            if (koalaThirdPartyScriptsLoaded) return;
            koalaThirdPartyScriptsLoaded = true;
            window.koalaInteractionScriptsLoaded = true;

            await window.koalaInjectLocationMarkup(
                window.koalaLocationHeadMarkup,
                document.head
            );
            await window.koalaInjectLocationMarkup(
                window.koalaLocationBodyMarkup,
                document.body
            );

            // reCAPTCHA Enterprise loading disabled.
            // var recaptcha = document.createElement('script');
            // recaptcha.src = 'https://www.google.com/recaptcha/enterprise.js?render=6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt';
            // recaptcha.async = true;
            // document.head.appendChild(recaptcha);

            (function(h, o, t, j, a, r) {
                h.hj = h.hj || function() { (h.hj.q = h.hj.q || []).push(arguments) };
                h._hjSettings = { hjid: 6387685, hjsv: 6 };
                a = o.getElementsByTagName('head')[0];
                r = o.createElement('script'); r.async = 1;
                r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                a.appendChild(r);
            })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');

            if (!document.querySelector('script[src*="id=GTM-KSNRRFL8"]')) {
                (function(w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
                    var f = d.getElementsByTagName(s)[0],
                        tag = d.createElement(s),
                        dl = l != 'dataLayer' ? '&l=' + l : '';
                    tag.async = true;
                    tag.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(tag, f);
                })(window, document, 'script', 'dataLayer', 'GTM-KSNRRFL8');
            }

            // Preserve the CA-specific Google Analytics destinations.
            if (!document.querySelector('script[src*="googletagmanager.com/gtag/js"]')) {
                var ga = document.createElement('script');
                ga.async = true;
                ga.src = 'https://www.googletagmanager.com/gtag/js?id=G-9RQMD52K78';
                document.head.appendChild(ga);
                ga.addEventListener('load', function() {
                    window.dataLayer = window.dataLayer || [];
                    window.gtag = window.gtag || function() { window.dataLayer.push(arguments); };
                    window.gtag('js', new Date());
                    window.gtag('config', 'G-9RQMD52K78', { 'anonymize_ip': true });
                    window.gtag('config', 'G-RFK3ZB6M00');
                    window.gtag('config', 'G-1HN84YS8QG');
                }, { once: true });
            }
        }

        ['click', 'keydown', 'touchstart', 'wheel'].forEach(function(eventName) {
            window.addEventListener(eventName, loadThirdPartyScripts, {
                once: true,
                passive: true
            });
        });

        // Load immediately on thank-you URLs and in GTM Preview / Tag Assistant
        // mode so conversion tags do not depend on a subsequent interaction.
        var isThankYouUrl = window.location.href.toLowerCase().indexOf('thank-you') !== -1;
        if (isThankYouUrl || /[?&]gtm_debug=/.test(window.location.search)) {
            loadThirdPartyScripts();
        }
    </script>
    <?php
}
add_action('wp_head', 'output_custom_or_default_gtm_head', 1);

add_action('wp_head', 'koala_output_location_schema', 2);
function koala_output_location_schema()
{
    if (is_admin() || ! is_singular('location')) {
        return;
    }

    $schema_raw = get_field('schema');
    if (empty($schema_raw)) {
        return;
    }

    $schema_data = json_decode($schema_raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return;
    }

    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <?php
}

add_filter('wpseo_json_ld_output', '__return_false');

function output_custom_or_default_gtm_body()
{
    $custom_body_script = '';

    if (is_singular('location')) {
        $custom_body_script = get_field('script_in_body_tag');
    }

    // noscript content must remain in the original response because it cannot
    // be injected when JavaScript is unavailable.
    $has_custom_body_script = !empty($custom_body_script);
    $custom_body_noscript = '';
    if ($custom_body_script) {
        $custom_body_script = preg_replace_callback(
            '/<noscript\b[^>]*>.*?<\/noscript>/is',
            function ($matches) use (&$custom_body_noscript) {
                $custom_body_noscript .= $matches[0];
                return '';
            },
            $custom_body_script
        );
        echo $custom_body_noscript;
    }

    ?>
    <script>
        window.koalaBodyScriptOutput = <?php echo $has_custom_body_script ? 'true' : 'false'; ?>;
        window.koalaLocationBodyMarkup = <?php echo wp_json_encode($custom_body_script ?: ''); ?>;

        if (
            window.koalaInteractionScriptsLoaded &&
            window.koalaLocationBodyMarkup &&
            window.koalaInjectLocationMarkup
        ) {
            window.koalaInjectLocationMarkup(
                window.koalaLocationBodyMarkup,
                document.body
            );
        }
    </script>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe loading="lazy" src="https://www.googletagmanager.com/ns.html?id=GTM-KSNRRFL8" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
    </noscript>
    <?php
}

if (function_exists('wp_body_open')) {
    add_action('wp_body_open', 'output_custom_or_default_gtm_body');
} else {
    add_action('wp_footer', 'output_custom_or_default_gtm_body');
}

add_action('wp_head', function () {
    ?>
    <style>
        .text-size-regular.brxe-text-basic p {
            font-size: 18px;
            line-height: 27px;
        }

        @media (max-width: 478px) {
            #brxe-trohhj {
                background-image: linear-gradient(#8bc34a, #8bc34a);
            }
        }
    </style>
    <?php
}, 1);

function add_preload_lcp_image()
{
    // Only on homepage or specific page IDs - change conditions as needed
    if (is_singular('location')) {
        ?>
        <link rel="preload" as="image" href="<?php echo home_url(); ?>/wp-content/uploads/2025/02/2-600x400.webp"
            fetchpriority="high" />
        <link rel="preload" as="image" href="<?php echo home_url(); ?>/wp-content/uploads/2024/06/Vector.png"
            fetchpriority="high" />
        <?php
    }
}
add_action('wp_head', 'add_preload_lcp_image');
/**
 * Remove unused block-library styles from the front end.
 */
function smart_remove_wp_block_library_css()
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'smart_remove_wp_block_library_css', 100);

/**
 * Defer audited non-critical JavaScript without modifying wp-admin or the
 * Bricks Builder interface.
 */
function koala_defer_scripts($tag, $handle, $src)
{
    if (is_admin()) {
        return $tag;
    }

    if (function_exists('bricks_is_builder') && bricks_is_builder()) {
        return $tag;
    }

    $defer_scripts = [
        'all-pages-js',
        'custom-service-js',
        'custom-map-init',
        'location-page',
        'faq-accordion',
        'custom-service-script',
        'js-cookie',
        'bricks-js-cookie',
        'bricks-fontfaceobserver',
        'handl-utm-grabber',
        'wpda_rest_api',
        'wp-polyfill',
        'underscore',
        'backbone',
        'wp-api',
        'api-request',
        'smush-lazy-load',
        'bricks-filters',
    ];

    if (in_array($handle, $defer_scripts, true) && strpos($tag, ' defer') === false) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'koala_defer_scripts', 10, 3);

/**
 * Load audited non-critical styles without blocking first paint.
 */
function koala_async_css($html, $handle, $href, $media)
{
    if (is_admin() || (function_exists('bricks_is_builder') && bricks_is_builder())) {
        return $html;
    }

    $async_handles = [
        'bricks-ionicons',
        'bricks-font-awesome-6',
        'bricks-animate',
        'bricks-tooltips',
        'bricks-photoswipe',
        'bricks-google-fonts',
        'wpda_public_css',
        'cookieblocker-css',
        'koala-custom-css',
        'custom-service-css',
        'custom-blog-template',
        'custom-blog-template-css',
        'custom-service-template',
        'custom-service-template-css',
    ];
    $async_url_patterns = [
        'custom-blog-template.css',
        'custom-service-template.css',
        'cookieblocker',
    ];

    $should_async = in_array($handle, $async_handles, true);
    if (!$should_async) {
        foreach ($async_url_patterns as $pattern) {
            if (strpos($href, $pattern) !== false) {
                $should_async = true;
                break;
            }
        }
    }

    if ($should_async) {
        return '<link rel="stylesheet" href="' . esc_url($href) .
            '" media="print" onload="this.media=\'all\'; this.onload=null;">' .
            '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
    }

    return $html;
}
add_filter('style_loader_tag', 'koala_async_css', 10, 4);

/**
 * Remove jQuery Migrate from the public site.
 */
function remove_jquery_migrate($scripts)
{
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, ['jquery-migrate']);
        }
    }
}
add_action('wp_default_scripts', 'remove_jquery_migrate');


add_action('pre_get_posts', 'force_yoast_to_see_location_service_post');
function force_yoast_to_see_location_service_post($query)
{
    // Only affect the main frontend query
    if (!is_admin() && $query->is_main_query()) {
        $location = get_query_var('location_slug');
        $service = get_query_var('service_slug');

        if ($location && $service) {
            $slug = $service . '-' . $location;
            $post = get_page_by_path($slug, OBJECT, 'location-service');

            if ($post) {
                // Force WP to treat this like a single post view
                $query->set('post_type', 'location-service');
                $query->set('p', $post->ID); // force it to load this post
                $query->is_single = true;
                $query->is_singular = true;
                $query->is_page = false;
            }
        }
    }
}

add_action('pre_get_posts', 'force_yoast_to_see_blog_location_post');
function force_yoast_to_see_blog_location_post($query)
{
    // Only on the frontend main query
    if (!is_admin() && $query->is_main_query()) {
        $location = get_query_var('location_slug');
        $blog = get_query_var('blog_slug');

        if ($location && $blog) {
            $post = get_page_by_path($blog, OBJECT, 'blog-location');

            if ($post) {
                $query->set('post_type', 'blog-location');
                $query->set('p', $post->ID); // Load by ID
                $query->is_single = true;
                $query->is_singular = true;
                $query->is_page = false;
            }
        }
    }
}

/**
 * Filter: Bricks Dynamic Tag Replacer (Native Meta Version)
 * * This function searches for the literal string '{acf_related_location_name}' 
 * within content rendered by Bricks and replaces it with actual post metadata.
 * By using get_post_meta(), we bypass ACF's extra processing for better performance.
 *
 * @param  string $content The raw content being processed by Bricks or WordPress.
 * @return string          The sanitized content with the placeholder replaced.
 */
function my_custom_bricks_placeholder_replace( $content ) {
    
    // 1. Define the placeholder text we are searching for
    $placeholder = '{acf_related_location_name}';

    // 2. Perform a quick check to see if the placeholder exists in this string
    // This prevents unnecessary database calls on elements that don't need it.
    if ( is_string( $content ) && strpos( $content, $placeholder ) !== false ) {
        
        /**
         * 3. Retrieve Data using Native WordPress Meta
         * We use get_the_ID() to target the current post.
         * Change 'related_location_name' to your exact meta key/field slug.
         */
        $meta_key   = 'related_location_name'; 
        $real_value = get_post_meta( get_the_ID(), $meta_key, true );

        // 4. Swap the placeholder for the real value if it exists
        if ( ! empty( $real_value ) ) {
            $content = str_replace( $placeholder, $real_value, $content );
        } else {
            /**
             * 5. Cleanup
             * If the field is empty, we remove the placeholder entirely
             * to avoid showing raw curly brackets to the user.
             */
            $content = str_replace( $placeholder, '', $content );
        }
    }

    return $content;
}

/**
 * Hook into Bricks standard render process.
 * This covers Heading, Basic Text, and other standard elements.
 */
add_filter( 'bricks/frontend/render_data', 'my_custom_bricks_placeholder_replace', 10, 1 );

/**
 * Hook into Bricks Dynamic Data parser.
 * This is crucial if your placeholder is nested inside another dynamic tag.
 */
add_filter( 'bricks/dynamic_data/render_content', 'my_custom_bricks_placeholder_replace', 10, 1 );

/**
 * Hook into standard WordPress content filter.
 * This ensures the replacement happens inside the "Post Content" (Gutenberg) area.
 */
add_filter( 'the_content', 'my_custom_bricks_placeholder_replace', 20 );


add_action('wp_ajax_get_ca_distances', 'handle_get_ca_distances');
add_action('wp_ajax_nopriv_get_ca_distances', 'handle_get_ca_distances');

function handle_get_ca_distances() {
    $user_zip = strtoupper(preg_replace('/\s+/', '', $_POST['user_zip']));
    $store_data = json_decode(stripslashes($_POST['store_data']), true);
    $radius = (int)$_POST['radius'];
    $api_key = sanitize_text_field($_POST['api_key']);
    
    $matches = [];

    foreach ($store_data as $store) {
        $store_zip = strtoupper(preg_replace('/\s+/', '', $store['zip']));
        
        // Use the Distance API endpoint
        $url = "https://www.zipcodeapi.com/rest/v2/CA/{$api_key}/distance.json/{$user_zip}/{$store_zip}/mile";
        
        $response = wp_remote_get($url);
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['distance']) && $body['distance'] <= $radius) {
                $matches[] = [
                    'title' => $store['title'],
                    'distance' => $body['distance']
                ];
            }
        }
    }

    usort($matches, function($a, $b) { return $a['distance'] <=> $b['distance']; });
    wp_send_json_success($matches);
}

/**
 * Add a dedicated 48x48 favicon tag for Google Search/Ads.
 *
 * WordPress Site Icons do not always output a 48x48 favicon,
 * even when the original Site Icon is large enough.
 *
 * Google prefers a favicon that is at least 48x48.
 * This filter adds an explicit 48x48 favicon reference
 * using the existing WordPress Site Icon.
 */
add_filter('site_icon_meta_tags', function($meta_tags) {

    // Generate the Site Icon URL at 48x48 size.
    $favicon_48 = get_site_icon_url(48);

    // Only add the tag if WordPress successfully generated the image.
    if ($favicon_48) {

        // Add the favicon link tag to the existing Site Icon meta tags.
        $meta_tags[] = sprintf(
            '<link rel="icon" type="image/png" sizes="48x48" href="%s" />',
            esc_url($favicon_48)
        );
    }

    // Return the updated list of favicon/meta tags.
    return $meta_tags;

});


require_once get_stylesheet_directory() . '/zip-shape-map-shortcode.php';
