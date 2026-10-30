<?php
/**
 * Plugin Name: Koala Canada Campaign Directory Exclusion
 * Description: Keeps Efficiency campaign pages accessible while excluding them from physical-location collections.
 */
namespace Koala\CanadaCampaignDirectory;
if (!defined('ABSPATH')) { exit; }

add_action('pre_get_posts', function ($query) {
    if (is_admin() || (defined('WP_CLI') && WP_CLI) || rtrim(home_url(), '/') !== 'https://koalainsulation.com/ca') { return; }
    if (!in_array('location', (array) $query->get('post_type'), true)) { return; }
    foreach (['p', 'page_id', 'name', 'pagename'] as $key) {
        if ($query->get($key)) { return; }
    }
    if ($query->is_singular()) { return; }
    $location = get_page_by_path('efficiency', OBJECT, 'location');
    if (!$location || $location->post_status !== 'publish') { return; }
    $id = (int) $location->ID;
    $included = array_map('intval', (array) $query->get('post__in'));
    if ($included === [$id]) { return; }
    if ($included) {
        $query->set('post__in', array_values(array_diff($included, [$id])) ?: [0]);
    }
    $excluded = array_map('intval', (array) $query->get('post__not_in'));
    $query->set('post__not_in', array_values(array_unique(array_merge($excluded, [$id]))));
}, 99);
