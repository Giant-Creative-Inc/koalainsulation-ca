<?php
namespace Koala\CALandingHeroes {
    function config() { return ['enabled' => true, 'location_id' => 66343]; }
    function get_post($id) { return (object) ['post_type' => 'location', 'post_name' => 'efficiency']; }
    function is_admin() { return $GLOBALS['admin'] ?? false; }
    function add_action(...$args) {}
}
namespace {
    require __DIR__ . '/../wp-content/plugins/koala-ca-landing-heroes/directory.php';
    class DirectoryQuery {
        public function __construct(public array $vars, public bool $single = false) {}
        public function get($key) { return $this->vars[$key] ?? ''; }
        public function set($key, $value) { $this->vars[$key] = $value; }
        public function is_singular() { return $this->single; }
    }
    $cases = [
        [['post_type' => 'location'], false, false, true],
        [['post_type' => ['location'], 'post__not_in' => [42]], false, false, true],
        [['post_type' => 'location', 'post__in' => [42, 66343]], false, false, true],
        [['post_type' => 'location', 'name' => 'efficiency'], false, false, false],
        [['post_type' => 'location', 'p' => 66343], true, false, false],
        [['post_type' => 'location', 'post__in' => [66343]], false, false, false],
        [['post_type' => 'location'], false, true, false],
        [['post_type' => 'location-service'], false, false, false],
    ];
    foreach ($cases as [$vars, $single, $admin, $exclude]) {
        $GLOBALS['admin'] = $admin;
        $q = new DirectoryQuery($vars, $single);
        \Koala\CALandingHeroes\exclude_efficiency_directory_location($q);
        if (in_array(66343, (array) $q->get('post__not_in'), true) !== $exclude) { throw new RuntimeException('Directory exclusion scope mismatch'); }
        if ($exclude && !empty($vars['post__in']) && in_array(66343, $q->get('post__in'), true)) { throw new RuntimeException('Campaign retained in inclusion list'); }
        if (isset($vars['post__not_in']) && !in_array(42, $q->get('post__not_in'), true)) { throw new RuntimeException('Existing exclusions lost'); }
    }
    echo "PASS directory collections exclude only Efficiency; direct routes, admin and services preserved\n";
}
