<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationServicesSectionTest extends TestCase {
 protected function setUp(): void {
  parent::setUp();
  Functions\when('add_action')->justReturn(true);Functions\when('add_filter')->justReturn(true);
  if(is_file(dirname(__DIR__).'/location-services.php'))require_once dirname(__DIR__).'/location-services.php';
 }
 public function test_legacy_service_block_is_routed_only_on_location_pages(): void {
  $block=['blockName'=>'koala/location-services','attrs'=>['test'=>'preserved']];
  Functions\when('get_post_type')->justReturn('location');
  $mapped=koala_beanstalk_location_services_block_data($block);
  self::assertSame('koala/location-services-section',$mapped['blockName']);
  self::assertSame($block['attrs'],$mapped['attrs']);
  Functions\when('get_post_type')->justReturn('resources-landing-pa');
  self::assertSame($block,koala_beanstalk_location_services_block_data($block));
 }
 public function test_location_editor_uses_a_distinct_services_block_name(): void {
  self::assertStringContainsString("registerBlockType('koala/location-services-section'",file_get_contents(dirname(__DIR__).'/assets/location-services-editor.js'));
 }
 public function test_selected_location_services_keep_all_eight_in_saved_order(): void {
  self::assertTrue(function_exists('koala_beanstalk_location_services_data'));
  Functions\when('get_post_meta')->alias(function($id,$key){
   if($key==='location_service')return [8,7,6,5,4,3,2,1];
   if($key==='location_service_name')return 'Service '.$id;
   if($key==='services_title')return 'Premium Insulation Services in Test Town';
   return '';
  });
  Functions\when('get_post_status')->justReturn('publish');
  Functions\when('get_post_type')->justReturn('location-service');
  $data=koala_beanstalk_location_services_data(4923);
  self::assertSame('Premium Insulation Services in Test Town',$data['title']);
  self::assertSame([8,7,6,5,4,3,2,1],array_column($data['items'],'id'));
  self::assertSame('Service 8',$data['items'][0]['name']);
 }
 public function test_general_services_are_not_substituted_and_unpublished_entries_are_excluded(): void {
  self::assertTrue(function_exists('koala_beanstalk_location_services_data'));
  Functions\when('get_post_meta')->alias(function($id,$key){return $key==='location_service'?[8,7]:($key==='service'?[99]:'');});
  Functions\when('get_post_status')->alias(function($id){return $id===8?'draft':'publish';});
  Functions\when('get_post_type')->justReturn('location-service');
  self::assertSame([7],array_column(koala_beanstalk_location_services_data(4923)['items'],'id'));
 }
 public function test_markup_has_accessible_tabs_and_retains_the_bricks_about_video(): void {
  $file=dirname(__DIR__).'/location-services.php';
  self::assertFileExists($file);
  $source=file_get_contents($file);
  self::assertStringContainsString('role="tablist"',$source);
  self::assertStringContainsString('role="tabpanel"',$source);
  self::assertStringContainsString('CsvHGsgMjkQ',$source);
  self::assertStringContainsString("koala_beanstalk_location_legacy_media( 'poster' )",$source);
  self::assertStringContainsString('koala_beanstalk_render_location_hero_quote',$source);
 }
 public function test_services_use_bricks_body_and_heading_line_heights(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-services.css');
  self::assertStringContainsString('.koala-location-services__description{line-height:30.6px}',$css);
  self::assertStringContainsString('font-size:48px;line-height:1;',$css);
  self::assertStringContainsString('font-size:18px;line-height:27px;',$css);
 }
 public function test_desktop_service_tabs_have_twelve_pixel_gap(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-services.css');
  self::assertStringContainsString('.koala-location-services__tabs{display:flex;flex-direction:column;gap:12px}',$css);
 }
 public function test_only_services_heading_uses_bricks_green(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-services.css');
  self::assertStringContainsString('.koala-location-content .koala-location-services__menu>h2,.editor-styles-wrapper .koala-location-services__menu>h2{color:#8bc34a}',$css);
 }
 public function test_service_card_heading_has_twelve_pixel_top_margin(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-services.css');
  self::assertStringContainsString('.koala-location-content .koala-location-services__panel>h3,.editor-styles-wrapper .koala-location-services__panel>h3{margin-top:12px}',$css);
 }
 public function test_mobile_panels_move_below_their_own_service_buttons(): void {
  $js=file_get_contents(dirname(__DIR__).'/assets/location-services.js');
  self::assertStringContainsString('tab.after(panels[i])',$js);
  self::assertStringContainsString("'aria-expanded'",$js);
  self::assertStringContainsString("'change', arrange",$js);
 }
 public function test_mobile_accordion_animates_and_honours_reduced_motion(): void {
  $js=file_get_contents(dirname(__DIR__).'/assets/location-services.js');
  self::assertStringContainsString('prefers-reduced-motion: reduce',$js);
  self::assertStringContainsString('panel.animate(',$js);
  self::assertStringContainsString('animation.cancel()',$js);
 }
 public function test_empty_services_intro_fields_do_not_create_spacer_elements(): void {
  $source=file_get_contents(dirname(__DIR__).'/location-services.php');
  self::assertStringContainsString("if ( '' !== trim( \$data['title'] ) )",$source);
  self::assertStringContainsString("if ( '' !== trim( \$data['description'] ) )",$source);
 }
 public function test_mobile_selection_always_aligns_below_the_header(): void {
  $js=file_get_contents(dirname(__DIR__).'/assets/location-services.js');
  self::assertStringNotContainsString('if(top<headerBottom+12)',$js);
  self::assertStringContainsString("scrollBy({top:top-headerBottom-12,behavior:reduced.matches?'instant':'smooth'})",$js);
 }
}
