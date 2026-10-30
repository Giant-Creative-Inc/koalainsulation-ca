<?php
namespace Koala\CALandingHeroes;

/** Efficiency is a campaign destination, not a physical franchise listing. */
function exclude_efficiency_directory_location($query) {
    if (is_admin() || (defined('WP_CLI') && WP_CLI)) { return; }
    $types = (array) $query->get('post_type');
    if (!in_array('location', $types, true)) { return; }
    // Keep direct location lookups, REST location-data and published campaign routes working.
    foreach (['p', 'page_id', 'name', 'pagename'] as $key) {
        if ($query->get($key)) { return; }
    }
    if ($query->is_singular()) { return; }
    $c = config();
    $id = (int) ($c['location_id'] ?? 0);
    $location = get_post($id);
    if (empty($c['enabled']) || !$location || $location->post_type !== 'location' || $location->post_name !== 'efficiency') { return; }
    $included = array_map('intval', (array) $query->get('post__in'));
    if ($included === [$id]) { return; }
    if ($included) {
        $included = array_values(array_diff($included, [$id]));
        $query->set('post__in', $included ?: [0]);
    }
    $excluded = array_map('intval', (array) $query->get('post__not_in'));
    $query->set('post__not_in', array_values(array_unique(array_merge($excluded, [$id]))));
}
add_action('pre_get_posts', __NAMESPACE__ . '\\exclude_efficiency_directory_location', 99);
