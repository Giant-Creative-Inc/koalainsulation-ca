<?php
require __DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/reviews.php';
use function Koala\CALandingHeroes\combined_review_summary;
function check($actual, $expected, $label) {
    if ($actual !== $expected) { throw new RuntimeException($label . ': ' . json_encode($actual)); }
    echo "PASS {$label}\n";
}
$a = (object) ['id' => 'halton', 'rating' => 5, 'review_count' => 90];
$b = (object) ['id' => 'winnipeg', 'rating' => 4, 'review_count' => 10];
check(combined_review_summary([$a, $b]), ['rating' => 4.9, 'count' => 100, 'locations' => 2], 'weights by review volume rather than averaging location ratings');
check(combined_review_summary([$a, $a, $b]), ['rating' => 4.9, 'count' => 100, 'locations' => 2], 'duplicate place is counted once');
check(combined_review_summary([$b, ['id' => 'bad', 'rating' => 6, 'review_count' => 99], ['id' => 'empty', 'rating' => 5, 'review_count' => 0]]), ['rating' => 4.0, 'count' => 10, 'locations' => 1], 'invalid ratings and empty sources are excluded');
check(combined_review_summary([]), [], 'empty feed does not invent a rating');
