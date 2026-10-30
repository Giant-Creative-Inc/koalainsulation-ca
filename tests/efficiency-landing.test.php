<?php
/** Run with PHP CLI; no WordPress database required. */
function wp_get_environment_type() { return $GLOBALS['environment']; }
class WP_CLI { public static function add_command($name, $class) {} }
require __DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/scope.php';
require __DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/cli.php';
use function Koala\CALandingHeroes\matches_request;
use function Koala\CALandingHeroes\allowed_environment;
use function Koala\CALandingHeroes\destination_ids;
use function Koala\CALandingHeroes\remap_elements;
function check($ok, $label) { if (!$ok) { throw new RuntimeException($label); } echo "PASS {$label}\n"; }
foreach (['production' => false, 'development' => false, 'staging' => true, 'local' => true] as $env => $expected) { $GLOBALS['environment'] = $env; check(allowed_environment() === $expected, "environment {$env}"); }
$c = ['enabled' => true, 'page_id' => 99001, 'template_id' => 99002, 'location_id' => 99003, 'route_path' => '/ca/efficiency/landing-pages/efficiency-ad-preview'];
check(matches_request($c['route_path'] . '/?hero=ice-storm&utm_source=test', $c, ['99003']), 'exact Efficiency request with tracking');
foreach (['/ca/halton/landing-pages/efficiency-ad-preview', '/ca/efficiency/landing-pages/partners', '/services/spray-foam-insulation', '/ca/efficiency/landing-pages/efficiency-ad-preview/extra'] as $path) { check(!matches_request($path . '?hero=ice-storm', $c, [99003]), 'unrelated route cannot opt in: ' . $path); }
check(!matches_request($c['route_path'], $c, [20180]), 'wrong related location blocked');
check(!matches_request($c['route_path'], [], [99003]), 'uninstalled plugin inert');
$disabled = $c; $disabled['enabled'] = false;
check(!matches_request($c['route_path'], $disabled, [99003]), 'disabled configuration inert');
$data = json_decode(file_get_contents(__DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/data/efficiency.json'), true, 512, JSON_THROW_ON_ERROR);
$ids = destination_ids($data['elements'], 99002);
check(count($ids) === count(array_unique($ids)), 'all copied element IDs unique');
check(!array_intersect(array_keys($ids), array_values($ids)), 'no original element ID reused');
check(!array_intersect(array_values($ids), array_values(destination_ids($data['elements'], 99004))), 'separate installation IDs cannot collide');
$classes = array_map(static function ($id) { return 'dest-' . $id; }, array_combine(array_keys($data['global_classes']), array_keys($data['global_classes'])));
$media = array_map(static function ($id) { return (int) $id + 100000; }, array_combine(array_keys($data['media']), array_keys($data['media'])));
$mapped = remap_elements($data['elements'], $ids, $classes, $media, 'https://staging.example/ca/wp-content/uploads');
foreach ($mapped as $e) { check(isset($ids[array_search($e['id'], $ids, true)]), 'mapped element ' . $e['id']); foreach ($e['children'] as $child) { if (!in_array($child, $ids, true)) { throw new RuntimeException('Stale child reference'); } } }
check(strpos(json_encode($mapped), '{{UPLOADS}}') === false, 'media origin tokens resolved');
$code_ids = []; foreach ($mapped as $e) { if ($e['name'] === 'code') { $code_ids[] = $e['id']; } }
check(!array_intersect($code_ids, ['plleut', 'mvcrmr', 'kjihim', 'huzfvg']), 'shared code lookups cannot resolve to Efficiency');
foreach ($data['media'] as $id => $relative) { check(is_file(__DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/data/media/' . $id . '-' . basename($relative)), 'bundled media ' . $id); }
check(count($data['services']) === 5, 'five selected corporate services');
echo "All Efficiency scope and migration tests passed.\n";
