<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationHeroTest extends TestCase {
 protected function setUp(): void {
  parent::setUp();
  Functions\when('add_action')->justReturn(true);
  if(is_file(dirname(__DIR__).'/location-hero.php'))require_once dirname(__DIR__).'/location-hero.php';
 }
 public function test_hero_does_not_reserve_a_fixed_minimum_height(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-hero.css');
  preg_match('/\.koala-location-hero\{([^}]+)\}/',$css,$match);
  self::assertDoesNotMatchRegularExpression('/min-height:\s*[1-9][0-9]*px/',$match[1]);
 }
 public function test_acf_changes_update_only_the_named_hero_content(): void {
  self::assertTrue(function_exists('koala_beanstalk_patch_location_hero'),'Hero ACF synchronization must exist.');
  Functions\when('esc_html')->alias('htmlspecialchars');
  $blocks=[['blockName'=>'core/group','attrs'=>['metadata'=>['name'=>'koala-location-hero']], 'innerBlocks'=>[
   ['blockName'=>'core/heading','attrs'=>['metadata'=>['name'=>'koala-location-hero-heading']],'innerHTML'=>'<h1>Old</h1>','innerContent'=>['<h1>Old</h1>']],
   ['blockName'=>'core/paragraph','attrs'=>['metadata'=>['name'=>'koala-location-hero-introduction']],'innerHTML'=>'<p>Old</p>','innerContent'=>['<p>Old</p>']],
   ['blockName'=>'koala/location-hero-media','attrs'=>[]],
   ['blockName'=>'koala/location-hero-quote','attrs'=>['label'=>'My editable label']]
  ]],['blockName'=>'core/paragraph','attrs'=>[],'innerHTML'=>'<p>Keep this</p>','innerContent'=>['<p>Keep this</p>']]];
  $values=['heading'=>'New & local','introduction'=>'New description','imageId'=>51,'provider'=>'image','videoId'=>'','privacyHash'=>'','posterId'=>0,'reviewHeading'=>''];
  $updated=koala_beanstalk_patch_location_hero($blocks,$values);
  self::assertStringContainsString('New &amp; local',$updated[0]['innerBlocks'][0]['innerHTML']);
  self::assertStringContainsString('New description',$updated[0]['innerBlocks'][1]['innerHTML']);
  self::assertSame(51,$updated[0]['innerBlocks'][2]['attrs']['imageId']);
  self::assertSame('My editable label',$updated[0]['innerBlocks'][3]['attrs']['label']);
  self::assertSame($blocks[1],$updated[1]);
  $wrapped=koala_beanstalk_patch_location_hero([['blockName'=>'core/group','attrs'=>[],'innerBlocks'=>$blocks]],$values);
  self::assertStringContainsString('New &amp; local',$wrapped[0]['innerBlocks'][0]['innerBlocks'][0]['innerHTML']);
  self::assertSame($updated,koala_beanstalk_patch_location_hero($updated,$values));
  $values['introduction']='';
  self::assertStringNotContainsString('New description',koala_beanstalk_patch_location_hero($updated,$values)[0]['innerBlocks'][1]['innerHTML']);
 }
 public function test_video_sources_are_allowlisted_and_private_vimeo_hash_is_preserved(): void {
  self::assertTrue(function_exists('koala_beanstalk_location_hero_video_url'),'Video source resolver must exist.');
  self::assertSame('https://www.youtube-nocookie.com/embed/abc123XYZ_-?autoplay=1',koala_beanstalk_location_hero_video_url(['provider'=>'YouTube','videoId'=>'abc123XYZ_-']));
  self::assertSame('https://player.vimeo.com/video/123456?h=abc123&autoplay=1&muted=1&loop=1&color=043968',koala_beanstalk_location_hero_video_url(['provider'=>'Vimeo','videoId'=>'123456','privacyHash'=>'abc123']));
  self::assertSame('',koala_beanstalk_location_hero_video_url(['provider'=>'YouTube','videoId'=>'https://evil.test/x']));
 }
 public function test_cta_opens_separate_popup_and_never_opens_sidebar(): void {
  self::assertTrue(function_exists('koala_beanstalk_render_location_hero_quote'),'Hero CTA must exist.');
  Functions\when('esc_html')->alias('htmlspecialchars');
  Functions\when('esc_url')->alias('htmlspecialchars');
  Functions\when('get_template_directory_uri')->justReturn('https://example.test/theme');
  $html=koala_beanstalk_render_location_hero_quote(['label'=>'Get a FREE Quote']);
  self::assertStringContainsString('data-location-quote-popup-trigger',$html);
  self::assertStringNotContainsString('data-sidebar-open',$html);
  self::assertStringNotContainsString('show-sidebar-form',$html);
  self::assertStringNotContainsString('disabled',$html);
  self::assertStringContainsString('aria-haspopup="dialog"',$html);
 }
 public function test_empty_title_and_legacy_video_are_resolved_from_location_fields(): void {
  $meta=['location_name'=>'Test Town','vimeo_video_id'=>'123456'];
  Functions\when('get_post_meta')->alias(function($id,$key)use(&$meta){return $meta[$key]??'';});
  $values=koala_beanstalk_location_hero_values(7);
  self::assertSame("Test Town's Insulation Installation Experts",$values['heading']);
  self::assertSame('Vimeo',$values['provider']);
  self::assertSame('123456',$values['videoId']);
  $meta['hero_video_types']='YouTube'; $meta['location_hero_video']='abc123XYZ_-';
  self::assertSame('YouTube',koala_beanstalk_location_hero_values(7)['provider']);
  $meta['location_hero_title']='Custom title';
  self::assertSame('Custom title',koala_beanstalk_location_hero_values(7)['heading']);
 }
 public function test_booking_provider_is_selected_without_copying_scripts_into_blocks(): void {
  self::assertTrue(function_exists('koala_beanstalk_location_booking_code'));
  $meta=['lead_service'=>'service_minder','service_minder_button'=>'<button>Service Minder</button>','housecall_pro_button_code'=>'<button>Housecall</button>'];
  Functions\when('get_post_meta')->alias(function($id,$key)use(&$meta){return $meta[$key]??'';});
  self::assertSame('<button>Service Minder</button>',koala_beanstalk_location_booking_code(7));
  $meta['lead_service']='houseprocall';
  self::assertSame('<button>Housecall</button>',koala_beanstalk_location_booking_code(7));
 }
 public function test_programmatic_meta_changes_queue_only_location_hero_fields(): void {
  self::assertTrue(function_exists('koala_beanstalk_queue_location_hero_sync'));
  Functions\when('get_post_type')->alias(function($id){return $id===7?'location':'post';});
  koala_beanstalk_queue_location_hero_sync(1,7,'location_hero_title','New title');
  koala_beanstalk_queue_location_hero_sync(2,8,'location_hero_title','Ignore');
  koala_beanstalk_queue_location_hero_sync(3,9,'unrelated_field','Ignore');
  self::assertSame([7=>true],$GLOBALS['koala_location_hero_sync_queue']);
  unset($GLOBALS['koala_location_hero_sync_queue']);
 }
 public function test_optional_promo_uses_attachment_and_disappears_when_empty(): void {
  self::assertTrue(function_exists('koala_beanstalk_render_location_hero_promo'));
  $image=51;
  Functions\when('get_the_ID')->justReturn(7);
  Functions\when('get_post_meta')->alias(function()use(&$image){return $image;});
  Functions\when('wp_get_attachment_image')->alias(function($id){return '<img data-id="'.$id.'">';});
  self::assertStringContainsString('data-id="51"',koala_beanstalk_render_location_hero_promo());
  $image=0;
  self::assertSame('',koala_beanstalk_render_location_hero_promo());
 }
 public function test_reviews_link_has_a_matching_native_destination(): void {
  $source=file_get_contents(dirname(__DIR__).'/location-hero.php');
  self::assertStringContainsString('href="#location-reviews"',$source);
  self::assertStringContainsString('id="location-reviews"',$source);
 }
}
