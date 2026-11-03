<?php
// Run: php tests/custom-service-style-version.test.php
function get_template_directory() { return __DIR__ . '/../wp-content/themes/bricks'; }
function get_template_directory_uri() { return 'https://example.test/ca/wp-content/themes/bricks'; }
function wp_enqueue_style($handle, $url, $dependencies = array(), $version = false) {
    $GLOBALS['captured_style_version'] = $version;
}
$source = file_get_contents(get_template_directory() . '/functions.php');
if (!preg_match("/wp_enqueue_style\(\s*'custom-service-css'.*?\);/s", $source, $match)) {
    throw new RuntimeException('Custom service stylesheet enqueue not found.');
}
eval($match[0]);
$expected = filemtime(get_template_directory() . '/assets/css/custom-service.css');
if ($GLOBALS['captured_style_version'] !== $expected) {
    throw new RuntimeException('Custom service CSS must use its file modification time as the cache version.');
}
echo "PASS: Custom service CSS uses its modification time as the cache version.\n";
