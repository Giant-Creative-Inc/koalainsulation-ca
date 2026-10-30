<?php
/** Site-wide Canadian staging design update; wp eval-file <file> [apply] --user=<admin>. */
if (!defined('WP_CLI') || !WP_CLI || wp_get_environment_type() !== 'staging'
    || home_url() !== 'https://staging.koalainsulation.com/ca') {
    throw new RuntimeException('Canadian staging only.');
}
$id = 4865;
$elements = get_post_meta($id, '_bricks_page_content_2', true);
$by_id = array_column($elements, null, 'id');
foreach (['fexrky','ppbvsx','lzppjw','sctdmt','haubzq','bvurvf','kwcarb','twcjxf','ldryot','xmabys','raymwp','kiobfp'] as $required) {
    if (!isset($by_id[$required])) { throw new RuntimeException('Unexpected popup structure: ' . $required); }
}
if (strpos($by_id['xmabys']['settings']['code'], '[gravityform id="3"') === false) {
    throw new RuntimeException('Expected Canadian Gravity Form 3.');
}
global $wpdb;
$protected = static function () use ($wpdb) {
    return hash('sha256', serialize([
        GFAPI::get_form(3),
        $wpdb->get_results("SELECT * FROM {$wpdb->prefix}gf_form_meta ORDER BY form_id", ARRAY_A),
        get_post_meta(20847, '_bricks_page_content_2', true),
        $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}gf_entry"),
    ]));
};
$before = $protected();
if (!in_array('apply', $args ?? [], true)) {
    WP_CLI::success('DRY RUN: update shared Canadian popup 4865 design only; retain form 3, code signatures, routing IDs and feeds.');
    return;
}
if (!current_user_can('manage_options')) { throw new RuntimeException('Existing staging administrator required.'); }
if (!get_option('koala_staging_popup_design_backup')) {
    add_option('koala_staging_popup_design_backup', [
        'elements' => $elements,
        'template_settings' => get_post_meta($id, '_bricks_template_settings', true),
        'protected_digest' => $before,
        'created_at' => gmdate('c'),
    ], '', false);
}
$image_id = (int) get_option('koala_staging_popup_artwork');
if (!$image_id) {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    $source = download_url('https://staging.koalainsulation.com/wp-content/uploads/2026/07/Obie-Smile-Wave-Eyes-Open-1-1.png');
    if (is_wp_error($source)) { throw new RuntimeException($source->get_error_message()); }
    $editor = wp_get_image_editor($source);
    if (is_wp_error($editor)) { throw new RuntimeException($editor->get_error_message()); }
    $editor->resize(300, null, false);
    $editor->set_quality(80);
    $saved = $editor->save('/tmp/koala-staging-popup-obie.webp', 'image/webp');
    unlink($source);
    if (is_wp_error($saved)) { throw new RuntimeException($saved->get_error_message()); }
    $image_id = media_handle_sideload(['name' => 'koala-popup-obie.webp', 'tmp_name' => $saved['path']], 0, 'Koala estimate popup mascot');
    if (is_wp_error($image_id)) { throw new RuntimeException($image_id->get_error_message()); }
    update_option('koala_staging_popup_artwork', $image_id, false);
}
$by_id['fexrky']['settings'] = ['_width' => '100%', '_widthMax' => '768px', '_position' => 'relative'];
$by_id['lzppjw']['children'] = ['sctdmt', 'bvurvf'];
$by_id['lzppjw']['settings'] = ['_direction' => 'column', '_alignItems' => 'center', '_rowGap' => '0', '_columnGap' => '0'];
$by_id['sctdmt']['settings'] = [
    '_width' => '100%', '_display' => 'flex', '_justifyContent' => 'center', '_margin' => ['bottom' => '10px'],
    '_gradient' => ['colors' => [
        ['id' => 'cagrn', 'color' => ['hex' => '#8bc34a']],
        ['id' => 'cablu', 'color' => ['hex' => '#81d4fa']],
    ], 'angle' => '90', 'applyTo' => 'background'],
    '_cssCustom' => '#brxe-sctdmt {clip-path:ellipse(60% 90% at 50% 10%);}',
];
$url = wp_get_attachment_url($image_id);
$by_id['haubzq']['settings'] = ['image' => ['id' => $image_id, 'url' => $url, 'full' => $url, 'size' => 'full'],
    '_width' => '100px', '_width:mobile_landscape' => '80px', '_transform' => ['translateX' => '-5%', 'translateY' => '1%']];
$by_id['twcjxf']['settings'] = ['_padding' => ['top' => '40px', 'right' => '16px', 'left' => '16px', 'bottom' => '40px'],
    '_padding:mobile_landscape' => ['top' => '16px', 'right' => '0', 'left' => '0', 'bottom' => '16px'],
    '_direction' => 'column', '_alignItems' => 'flex-start', '_rowGap' => '16px'];
$by_id['raymwp']['settings']['_width:tablet_portrait'] = '40px';
$by_id['raymwp']['settings']['_height:tablet_portrait'] = '40px';
$by_id['raymwp']['settings']['_top:tablet_portrait'] = '10px';
$by_id['raymwp']['settings']['_right:tablet_portrait'] = '10px';
$by_id['kiobfp']['settings']['_width:tablet_portrait'] = '18px';
// Canadian field IDs and integration data remain intact. Only visual order/layout changes.
$by_id['fexrky']['settings']['_cssCustom'] = <<<'CSS'
#brxe-fexrky #brxe-lzppjw {padding:0;border:0;gap:0}
#brxe-fexrky #brxe-sctdmt {height:100px;min-width:0;width:100%}
#brxe-fexrky #brxe-haubzq {width:100px;height:100px;object-fit:cover}
#brxe-fexrky #gform_fields_3 {display:grid!important;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:10px!important;align-items:start}
#brxe-fexrky #gform_fields_3 > .gfield {grid-column:1/-1!important;min-width:0;margin:0!important}
#brxe-fexrky #gform_fields_3 #field_3_1 {order:1;grid-column:1!important}
#brxe-fexrky #gform_fields_3 #field_3_3 {order:2;grid-column:2!important}
#brxe-fexrky #gform_fields_3 #field_3_5 {order:3;grid-column:1!important}
#brxe-fexrky #gform_fields_3 #field_3_6 {order:4;grid-column:2!important}
#brxe-fexrky #field_3_4 {order:5}
#brxe-fexrky #field_3_9 {order:6}
#brxe-fexrky #field_3_10 {order:7}
#brxe-fexrky .gfield--type-hidden {display:none!important}
#brxe-fexrky #gform_wrapper_3 {width:100%;margin:0}
#brxe-fexrky #gform_submit_button_3 {border:2px solid #042e53!important}
#brxe-fexrky #estimate-popup-close-btn {z-index:2}
@media(max-width:478px){
  #brxe-fexrky #gform_fields_3 {grid-template-columns:minmax(0,1fr)}
  #brxe-fexrky #gform_fields_3 #field_3_1,
  #brxe-fexrky #gform_fields_3 #field_3_3,
  #brxe-fexrky #gform_fields_3 #field_3_5,
  #brxe-fexrky #gform_fields_3 #field_3_6 {grid-column:1/-1!important}
  #brxe-fexrky #brxe-sctdmt, #brxe-fexrky #brxe-haubzq {height:80px}
  #brxe-fexrky #brxe-haubzq {width:80px}
}
CSS;
unset($by_id['zyzxib']);
$updated = array_values($by_id);
// All executed PHP and its signatures must remain byte-for-byte unchanged.
foreach ($elements as $old) {
    if ($old['name'] === 'code' && $old !== $by_id[$old['id']]) { throw new RuntimeException('Code changed.'); }
}
update_post_meta($id, '_bricks_page_content_2', wp_slash($updated));
if (get_post_meta($id, '_bricks_page_content_2', true) !== $updated || $protected() !== $before) {
    update_post_meta($id, '_bricks_page_content_2', wp_slash($elements));
    throw new RuntimeException('Verification failed; popup content restored.');
}
WP_CLI::success('Shared Canadian staging popup updated. Form 3, feeds, entry count, shared landing template and routing/code elements unchanged. Artwork ' . $image_id . '.');
