<?php
namespace Koala\CALandingHeroes;

function resolve_post($slug, $type) {
    $post = get_page_by_path($slug, OBJECT, $type);
    if (!$post || $post->post_status !== 'publish') { throw new \RuntimeException("Missing published {$type}: {$slug}"); }
    return $post;
}

/** Give every copied element a destination-specific identity, including parent/CSS references. */
function remap_elements(array $elements, array $ids, array $classes, array $media, $uploads, array $urlmap = []) {
    $walk = function ($value, $key = '') use (&$walk, $ids, $classes, $media, $uploads, $urlmap) {
        if (is_array($value)) {
            foreach ($value as $k => $v) { $value[$k] = $walk($v, (string) $k); }
            return $value;
        }
        if (!is_string($value) && !is_int($value)) { return $value; }
        if ($key === 'id' && isset($media[(string) $value])) { return $media[(string) $value]; }
        if (isset($ids[$value])) { return $ids[$value]; }
        if (isset($classes[$value])) { return $classes[$value]; }
        if (is_string($value)) {
            foreach ($ids as $old => $new) { $value = str_replace('brxe-' . $old, 'brxe-' . $new, $value); }
            return strtr(str_replace('{{UPLOADS}}', $uploads, $value), $urlmap);
        }
        return $value;
    };
    return $walk($elements);
}

function destination_ids(array $elements, $template_id) {
    $ids = [];
    foreach ($elements as $element) { $ids[$element['id']] = 'ef' . substr(hash('sha256', $template_id . ':' . $element['id']), 0, 10); }
    if (count(array_unique($ids)) !== count($elements)) { throw new \RuntimeException('Element identity collision'); }
    return $ids;
}

function shared_template_digest() {
    return hash('sha256', serialize([get_post_meta(20847, '_bricks_page_content_2', true), get_post_meta(20847, '_bricks_page_settings', true), get_post_meta(20847, '_bricks_template_settings', true)]));
}

function snapshot_meta($id, array $keys) {
    $out = [];
    foreach ($keys as $key) { $out[$key] = ['exists' => metadata_exists('post', $id, $key), 'value' => get_post_meta($id, $key, true)]; }
    return $out;
}

class Staging_Command {
    /**
     * Prepare an isolated Efficiency preview. Defaults to a read-only dry run.
     *
     * ## OPTIONS
     *
     * [--apply]
     * : Create the preview after preflight. Omit for dry run.
     *
     * [--page-slug=<slug>]
     * : Default: efficiency-ad-preview. An unrelated location page is never reassigned.
     *
     * [--route-prefix=<path>]
     * : Canadian URL prefix, normally /ca.
     */
    public function prepare($args, $assoc) {
        try { $this->prepare_checked($assoc); }
        catch (\Throwable $e) { \WP_CLI::error($e->getMessage()); }
    }
    private function prepare_checked($assoc) {
        if (!allowed_environment()) { throw new \RuntimeException('Only WP_ENVIRONMENT_TYPE=local or staging is allowed. Production is blocked.'); }
        if (!function_exists('my_landing_page_template_id') || !class_exists('Bricks\\Helpers') || !function_exists('get_field') || !class_exists('GFAPI')) {
            throw new \RuntimeException('Bricks, ACF, Gravity Forms and the Canadian landing-page router are required.');
        }
        if (!\Bricks\Helpers::code_execution_enabled()) { throw new \RuntimeException('Bricks code execution must already be enabled; this installer does not change security settings.'); }
        $data = json_decode(file_get_contents(__DIR__ . '/data/efficiency.json'), true, 512, JSON_THROW_ON_ERROR);
        $location = resolve_post('efficiency', 'location');
        $slug = sanitize_title($assoc['page-slug'] ?? 'efficiency-ad-preview');
        if (!$slug) { throw new \RuntimeException('A nonempty page slug is required.'); }
        $page = get_page_by_path($slug, OBJECT, 'landing-pages');
        $managed = get_option('koala_efficiency_landing', []);
        if ($page && empty($managed['page_id']) && !in_array((string) $location->ID, array_map('strval', (array) get_post_meta($page->ID, 'related_location', true)), true)) {
            throw new \RuntimeException('That page belongs to another location. Choose a new --page-slug; it will not be reassigned.');
        }
        if (get_option('koala_efficiency_landing_backup') && empty($managed)) { throw new \RuntimeException('An incomplete installation has a recovery backup. Run rollback --apply before trying again.'); }
        if (!empty($managed) && (!$page || (int) $managed['page_id'] !== $page->ID || (int) $managed['location_id'] !== $location->ID)) {
            throw new \RuntimeException('An installation already manages a different page. Roll it back first.');
        }
        $services = [];
        foreach ($data['services'] as $service_slug) { $services[] = resolve_post($service_slug, 'service')->ID; }
        $globals = get_option('bricks_global_classes', []); $classmap = [];
        foreach ($data['global_classes'] as $old => $name) {
            $matches = array_values(array_filter($globals, static function ($c) use ($name) { return $c['name'] === $name; }));
            if (count($matches) !== 1) { throw new \RuntimeException('Missing or ambiguous Bricks global class: ' . $name); }
            $classmap[$old] = $matches[0]['id'];
        }
        foreach ([1] as $form_id) { if (!\GFAPI::get_form($form_id)) { throw new \RuntimeException('Missing Gravity Form ' . $form_id); } }
        $shortcode = function_exists('koala_get_google_review_shortcode') ? koala_get_google_review_shortcode(0) : '';
        if (!preg_match('/^\[grw\s+id=["\x27]?([1-9][0-9]*)["\x27]?\s*\/?\]$/', $shortcode, $m) || get_post_type((int) $m[1]) !== 'grw_feed') {
            throw new \RuntimeException('The destination corporate Google Reviews feed is missing. Configure it before applying.');
        }
        $relationship_field = null;
        foreach (get_posts(['post_type' => 'landing-pages', 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids']) as $existing_id) {
            $candidate = get_post_meta($existing_id, '_related_location', true);
            $field = $candidate ? acf_get_field($candidate) : false;
            if ($field && $field['name'] === 'related_location') { $relationship_field = $candidate; break; }
        }
        if (!$relationship_field) { throw new \RuntimeException('Cannot resolve the landing-page ACF related_location field on this destination.'); }
        $uploads = wp_upload_dir(null, false); $media = []; $missing_media = [];
        global $wpdb;
        foreach ($data['media'] as $old => $relative) {
            $found = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s", $relative));
            if (count($found) > 1) { throw new \RuntimeException('Ambiguous media path: ' . $relative); }
            $id = $found ? (int) $found[0] : 0;
            if (!$id) { $id = (int) get_option('koala_efficiency_media_' . $old, 0); }
            if ($id && get_post_type($id) === 'attachment' && is_file(get_attached_file($id))) { $media[(string) $old] = $id; }
            else {
                $file = __DIR__ . '/data/media/' . $old . '-' . basename($relative);
                if (!is_file($file)) { throw new \RuntimeException('Missing packaged media: ' . basename($file)); }
                $missing_media[$old] = $file;
            }
        }
        $prefix = '/' . trim((string) parse_url(home_url(), PHP_URL_PATH), '/');
        if (isset($assoc['route-prefix'])) { $prefix = '/' . trim($assoc['route-prefix'], '/'); }
        $route = rtrim($prefix, '/') . '/efficiency/landing-pages/' . $slug;
        \WP_CLI::log(wp_json_encode(['mode' => isset($assoc['apply']) ? 'APPLY' : 'DRY RUN', 'location_id' => $location->ID, 'existing_page_id' => $page ? $page->ID : null, 'route_path' => $route, 'services' => $services, 'media_to_import' => count($missing_media), 'shared_template' => '20847 will not be modified'], JSON_PRETTY_PRINT));
        if (!isset($assoc['apply'])) { return; }
        if (!current_user_can('manage_options') || !\Bricks\Capabilities::current_user_can_execute_code()) { throw new \RuntimeException('An administrator with Bricks code execution permission is required for apply.'); }
        if (!empty($managed)) { \WP_CLI::success('Already installed. No page or template content overwritten.'); return; }
        $template_id = wp_insert_post(['post_type' => 'bricks_template', 'post_status' => 'draft', 'post_title' => 'Efficiency Ad Landing Page (Staging)'], true);
        if (is_wp_error($template_id)) { throw new \RuntimeException($template_id->get_error_message()); }
        $created_page = !$page;
        if (!$page) {
            $page_id = wp_insert_post(['post_type' => 'landing-pages', 'post_status' => 'draft', 'post_name' => $slug, 'post_title' => 'Efficiency Insulation Campaign'], true);
            if (is_wp_error($page_id)) { throw new \RuntimeException($page_id->get_error_message()); }
        } else { $page_id = $page->ID; }
        $state = ['page_id' => $page_id, 'template_id' => $template_id, 'created_page' => $created_page, 'page_status' => $page ? $page->post_status : 'draft', 'meta' => snapshot_meta($page_id, array_merge(array_keys($data['page_meta']), ['related_location', '_related_location'])), 'config' => $managed, 'shared_digest' => shared_template_digest()];
        update_option('koala_efficiency_landing_backup', $state, false);
        foreach ($missing_media as $old => $source) {
            $dir = $uploads['basedir'] . '/koala-efficiency'; wp_mkdir_p($dir);
            $file = $dir . '/' . basename($source);
            if (file_exists($file) && hash_file('sha256', $file) !== hash_file('sha256', $source)) { throw new \RuntimeException('Media collision: ' . $file); }
            if (!copy($source, $file)) { throw new \RuntimeException('Cannot copy media: ' . $file); }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $types = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'webp' => 'image/webp'];
            if (!isset($types[$ext])) { throw new \RuntimeException('Unsupported packaged media'); }
            $id = wp_insert_attachment(['post_mime_type' => $types[$ext], 'post_title' => pathinfo($file, PATHINFO_FILENAME), 'post_status' => 'inherit'], $file, 0, true);
            if (is_wp_error($id)) { throw new \RuntimeException($id->get_error_message()); }
            update_attached_file($id, $file); $media[(string) $old] = $id;
            update_option('koala_efficiency_media_' . $old, $id, false);
        }
        $ids = destination_ids($data['elements'], $template_id);
        $urlmap = [];
        foreach ($data['media'] as $old => $relative) { $urlmap[$uploads['baseurl'] . '/' . $relative] = wp_get_attachment_url($media[(string) $old]); }
        $elements = remap_elements($data['elements'], $ids, $classmap, $media, $uploads['baseurl'], $urlmap);
        foreach ($elements as &$element) {
            if (isset($element['settings']['executeCode'])) { $element['settings']['signature'] = wp_hash($element['settings']['code']); $element['settings']['user_id'] = get_current_user_id(); $element['settings']['time'] = time(); }
            if ($element['id'] === $ids['jkwfxp']) { $element['settings']['query']['post__in'] = $services; }
        } unset($element);
        update_post_meta($template_id, '_bricks_template_type', 'content');
        update_post_meta($template_id, '_bricks_page_content_2', wp_slash($elements));
        update_post_meta($template_id, '_bricks_page_settings', wp_slash(remap_elements($data['page_settings'], $ids, $classmap, $media, $uploads['baseurl'], $urlmap)));
        update_post_meta($template_id, '_bricks_template_settings', ['templateConditions' => [['id' => 'effonly', 'main' => 'ids', 'ids' => [$page_id]]]]);
        foreach ($data['page_meta'] as $key => $value) {
            if (isset($media[(string) (is_scalar($value) ? $value : '')]) && preg_match('/(?:image|icon|picture|photo)$/', $key)) { $value = $media[(string) $value]; }
            if (is_string($value)) { $value = str_replace('{{UPLOADS}}', $uploads['baseurl'], $value); }
            update_post_meta($page_id, $key, wp_slash($value));
        }
        update_post_meta($page_id, 'related_location', [(string) $location->ID]);
        update_post_meta($page_id, '_related_location', $relationship_field);
        if (get_post_meta($template_id, '_bricks_page_content_2', true) !== $elements) { throw new \RuntimeException('Template readback failed; run rollback.'); }
        update_option('koala_efficiency_landing', ['enabled' => true, 'page_id' => $page_id, 'page_slug' => $slug, 'location_id' => $location->ID, 'template_id' => $template_id, 'route_path' => $route, 'hero_id' => $ids['caadhr'], 'fallback_id' => $ids['trohhj'], 'element_ids' => $ids], false);
        wp_update_post(['ID' => $template_id, 'post_status' => 'publish']);
        if ($created_page) { wp_update_post(['ID' => $page_id, 'post_status' => 'publish']); }
        \WP_CLI::success('Efficiency-only preview installed. Test ' . home_url() . ' with path ' . $route . '/?hero=ice-storm');
    }
    /** Read-only checks after staging installation; never submits a form. */
    public function verify($args, $assoc) {
        if (!allowed_environment()) { \WP_CLI::error('Local/staging only.'); }
        $c = config(); $backup = get_option('koala_efficiency_landing_backup', []);
        if (empty($c['enabled'])) { \WP_CLI::error('No enabled installation.'); }
        $errors = [];
        if (($backup['shared_digest'] ?? '') !== shared_template_digest()) { $errors[] = 'Shared template 20847 changed since preparation; inspect before accepting.'; }
        $settings = get_post_meta($c['template_id'], '_bricks_template_settings', true);
        $conditions = $settings['templateConditions'] ?? [];
        if (count($conditions) !== 1 || ($conditions[0]['main'] ?? '') !== 'ids' || array_map('intval', $conditions[0]['ids'] ?? []) !== [(int) $c['page_id']]) { $errors[] = 'Dedicated template conditions are not exact-page-only.'; }
        $shared_ids = array_column((array) get_post_meta(20847, '_bricks_page_content_2', true), 'id');
        $elements = (array) get_post_meta($c['template_id'], '_bricks_page_content_2', true);
        if (array_intersect($shared_ids, array_column($elements, 'id'))) { $errors[] = 'Copied element IDs collide with shared template 20847.'; }
        foreach ($elements as $element) {
            if (isset($element['settings']['executeCode']) && !\Bricks\Helpers::verify_code_signature($element['settings']['signature'] ?? '', $element['settings']['code'])) { $errors[] = 'Invalid destination code signature: ' . $element['id']; }
        }
        if (!matches_request($c['route_path'], $c, (array) get_post_meta($c['page_id'], 'related_location', true))) { $errors[] = 'Efficiency relationship/scope check failed.'; }
        foreach (get_posts(['post_type' => 'landing-pages', 'posts_per_page' => -1, 'post_status' => 'publish']) as $page) {
            if ($page->ID === (int) $c['page_id']) { continue; }
            $related = (array) get_post_meta($page->ID, 'related_location', true);
            foreach ($related as $location_id) {
                $location = get_post($location_id); if (!$location) { continue; }
                $path = rtrim(dirname(dirname(dirname($c['route_path']))), '/') . '/' . $location->post_name . '/landing-pages/' . $page->post_name;
                if (matches_request($path . '?hero=ice-storm', $c, $related)) { $errors[] = 'Unrelated page opted in: ' . $page->ID; }
            }
        }
        if ($errors) { \WP_CLI::error(implode("\n", $errors)); }
        \WP_CLI::success('Exact Efficiency scope, unique IDs, signatures, relationship and unchanged shared template verified. Browser/cache/lead routing tests still required.');
    }
    /**
     * Restore only this installation; retain imported media for safe recovery.
     *
     * ## OPTIONS
     *
     * [--apply]
     * : Apply rollback. Omit for dry run.
     */
    public function rollback($args, $assoc) {
        if (!allowed_environment()) { \WP_CLI::error('Local/staging only.'); }
        $state = get_option('koala_efficiency_landing_backup');
        if (!$state) { \WP_CLI::error('No installation backup exists.'); }
        if (!isset($assoc['apply'])) { \WP_CLI::log('DRY RUN: disable configuration, restore page metadata/status, draft the dedicated template. Media retained.'); return; }
        update_option('koala_efficiency_landing', $state['config'], false);
        foreach ($state['meta'] as $key => $saved) {
            if ($saved['exists']) { update_post_meta($state['page_id'], $key, wp_slash($saved['value'])); }
            else { delete_post_meta($state['page_id'], $key); }
        }
        wp_update_post(['ID' => $state['page_id'], 'post_status' => $state['page_status']]);
        wp_update_post(['ID' => $state['template_id'], 'post_status' => 'draft']);
        delete_option('koala_efficiency_landing_backup');
        \WP_CLI::success('Installation rolled back. Shared templates and imported media were not modified.');
    }
}
\WP_CLI::add_command('koala-efficiency', __NAMESPACE__ . '\\Staging_Command');
