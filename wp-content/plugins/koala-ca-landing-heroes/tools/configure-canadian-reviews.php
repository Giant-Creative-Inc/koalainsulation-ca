<?php
/** wp eval-file <this file> [apply]; requires sources connected in RichPlugins first. */
if (!defined('WP_CLI') || !WP_CLI || wp_get_environment_type() !== 'staging'
    || home_url() !== 'https://staging.koalainsulation.com/ca') {
    throw new RuntimeException('Canadian staging only.');
}
$c = get_option('koala_efficiency_landing');
$location = get_post($c['location_id'] ?? 0);
if (empty($c['enabled']) || !$location || $location->post_type !== 'location' || $location->post_name !== 'efficiency') {
    throw new RuntimeException('Prepare the isolated Efficiency page first.');
}
if (!\Koala\CALandingHeroes\matches_request($c['route_path'] ?? '', $c, (array) get_post_meta($c['page_id'], 'related_location', true))) {
    throw new RuntimeException('Efficiency page relationship/scope validation failed.');
}
$manifest = json_decode(file_get_contents(dirname(__DIR__) . '/data/canadian-review-sources.json'), true, 512, JSON_THROW_ON_ERROR);
global $wpdb;
$connections = [];
foreach ($manifest as $source) {
    $place = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}grp_google_place WHERE place_id = %s", $source['place_id']));
    if (!$place || $place->name !== $source['name'] || !preg_match('/, Canada$/', $place->address)
        || $place->rating <= 0 || $place->rating > 5 || $place->review_count < 1) {
        throw new RuntimeException('Missing or invalid Canadian source: ' . $source['name']);
    }
    $connections[] = ['id' => $place->place_id, 'name' => $place->name, 'photo' => $place->photo,
        'lang' => 'en', 'platform' => 'google', 'refresh' => false, 'local_img' => false];
    WP_CLI::log($place->name . ': ' . $place->rating . '/5, ' . $place->review_count . ' reviews');
}
if (count(array_unique(array_column($connections, 'id'))) !== count($manifest)) {
    throw new RuntimeException('Duplicate review sources.');
}
$corporate = koala_get_google_review_shortcode(0);
if (!preg_match('/^\[grw\s+id=["\x27]?([1-9][0-9]*)["\x27]?\s*\/?\]$/', $corporate, $match)) {
    throw new RuntimeException('Corporate reference feed is missing.');
}
$reference = get_post((int) $match[1]);
$reference = $reference ? json_decode($reference->post_content) : null;
if (!$reference || !isset($reference->options)) { throw new RuntimeException('Corporate feed options are invalid.'); }
if (!in_array('apply', $args ?? [], true)) {
    WP_CLI::success('DRY RUN: create/update only Efficiency’s Canadian feed and reviews_feed_id setting.');
    return;
}
if (!current_user_can('manage_options')) { throw new RuntimeException('Run apply with an existing staging administrator via --user.'); }
if (!get_option('koala_efficiency_reviews_backup')) {
    add_option('koala_efficiency_reviews_backup', ['config' => $c, 'created_at' => gmdate('c')], '', false);
}
$feed_id = (int) ($c['reviews_feed_id'] ?? 0);
if ($feed_id && !get_post_meta($feed_id, '_koala_efficiency_reviews', true)) {
    throw new RuntimeException('Refusing to overwrite an unmanaged feed.');
}
$options = clone $reference->options;
$options->header_merge_social = false;
$options->header_hide_social = false;
$options->hide_writereview = true;
$options->reviews_limit = '';
$options->pagination = '9';
$options->cache = '12';
$content = wp_json_encode(['connections' => $connections, 'options' => $options], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$feed_id = wp_insert_post(['ID' => $feed_id, 'post_type' => 'grw_feed', 'post_status' => 'publish',
    'post_title' => 'Efficiency — Canadian Locations', 'post_name' => 'efficiency-canadian-reviews', 'post_content' => wp_slash($content)], true);
if (is_wp_error($feed_id)) { throw new RuntimeException($feed_id->get_error_message()); }
update_post_meta($feed_id, '_koala_efficiency_reviews', 1);
$c['reviews_feed_id'] = $feed_id;
update_option('koala_efficiency_landing', $c, false);
WP_CLI::success('Efficiency-only Canadian feed configured: ' . $feed_id . '. Corporate and location feed settings unchanged.');
