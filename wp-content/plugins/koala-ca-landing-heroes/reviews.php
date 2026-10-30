<?php
namespace Koala\CALandingHeroes;

/** Weight Google location averages by their full review counts, once per Place ID. */
function combined_review_summary(array $businesses) {
    $count = 0;
    $weighted = 0.0;
    $seen = [];
    foreach ($businesses as $business) {
        $business = (array) $business;
        $id = (string) ($business['id'] ?? '');
        $rating = (float) ($business['rating'] ?? 0);
        $reviews = (int) ($business['review_count'] ?? 0);
        if ($id === '' || isset($seen[$id]) || $rating <= 0 || $rating > 5 || $reviews <= 0) { continue; }
        $seen[$id] = true;
        $count += $reviews;
        $weighted += $rating * $reviews;
    }
    return $count ? ['rating' => round($weighted / $count, 1), 'count' => $count, 'locations' => count($seen)] : [];
}

/** This feed is private to the exact Efficiency route; never change the corporate feed. */
function reviews_feed() {
    if (!is_efficiency_page()) { return null; }
    $id = (int) (config()['reviews_feed_id'] ?? 0);
    if (!$id) {
        $shortcode = function_exists('koala_get_google_review_shortcode') ? koala_get_google_review_shortcode(0) : '';
        if (preg_match('/^\[grw\s+id=["\x27]?([1-9][0-9]*)["\x27]?\s*\/?\]$/', $shortcode, $match)) { $id = (int) $match[1]; }
    }
    $feed = get_post($id);
    return $feed && $feed->post_type === 'grw_feed' ? $feed : null;
}
