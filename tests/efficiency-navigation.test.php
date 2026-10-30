<?php
function add_action(...$args) {}
require __DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/navigation.php';
$home = 'https://staging.koalainsulation.com/ca';
$cases = [
    [$home, $home . '/efficiency'],
    [$home . '/services', $home . '/efficiency'],
    [$home . '/services/crawl-space-services', $home . '/efficiency/services/crawl-space-encapsulation'],
    [$home . '/why-koala', $home . '/efficiency/why-koala'],
    ['/ca/services/spray-foam-insulation?campaign=roof#details', $home . '/efficiency/services/spray-foam-insulation?campaign=roof#details'],
    [$home . '/services/air-sealing-service-2', $home . '/efficiency/services/air-sealing-service'],
    [$home . '/services/commercial-insulation-services-2', $home . '/efficiency/services/commercial-insulation-services'],
];
foreach (['/efficiency/why-koala', '/halton/services/spray-foam-insulation', '/locations', '/national-partnerships', '/services/unknown', '/efficiency/landing-pages/efficiency-ad-preview/?hero=snowstorm'] as $path) { $cases[] = [$home . $path, $home . $path]; }
foreach (['https://example.com/ca/why-koala', 'tel:4165550100', 'mailto:test@example.com', '#estimate-form', 'https://staging.koalainsulation.com/ca-other/why-koala'] as $href) { $cases[] = [$href, $href]; }
foreach ($cases as [$input, $expected]) {
    $actual = \Koala\CALandingHeroes\efficiency_navigation_url($input, $home);
    if ($actual !== $expected) { throw new RuntimeException("Unexpected destination: {$input} => {$actual}"); }
}
echo 'PASS ' . count($cases) . " Efficiency link mappings and exclusions\n";
