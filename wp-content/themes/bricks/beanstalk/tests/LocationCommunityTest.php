<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationCommunityTest extends TestCase {
 public function test_relationship_ids_preserve_selected_order_and_remove_empty_values(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  Functions\when('get_post_meta')->justReturn(['12','7','',0,'12']);
  self::assertSame([12,7],koala_beanstalk_location_selected_ids(4923,'local_cities'));
 }
 public function test_patterns_are_available_for_each_section(): void {
  foreach(['reviews','communities','nap','offers','education'] as $name){
   self::assertFileExists(dirname(__DIR__).'/patterns/location-'.$name.'.php');
  }
 }
 public function test_nap_formats_saved_24_hour_ranges_without_changing_other_hours_copy(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  Functions\when('get_the_ID')->justReturn(4923);
  Functions\when('esc_html')->alias('strval');Functions\when('esc_attr')->alias('strval');
  Functions\when('do_shortcode')->justReturn('<div>map</div>');
  Functions\when('get_post_meta')->alias(static fn($id,$key)=>['show_hours'=>'1','location_hours_mon'=>'07:00-17:00','location_hours_tues'=>'00:00–12:00','location_hours_wed'=>'Closed','location_hours_thurs'=>'8am–5pm','location_hours_fri'=>'09:30-23:15'][$key]??'');
  $html=koala_beanstalk_render_location_community(['section'=>'nap']);
  foreach(['7:00 AM–5:00 PM','12:00 AM–12:00 PM','Closed','8am–5pm','9:30 AM–11:15 PM'] as $hours)self::assertStringContainsString($hours,$html);
  self::assertStringNotContainsString('07:00-17:00',$html);
 }
 public function test_hours_display_supports_end_of_day_midnight_and_all_day_ranges(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  self::assertSame('Open 24 hours',koala_beanstalk_location_display_hours('00:00-24:00'));
  self::assertSame('7:00 PM–12:00 AM',koala_beanstalk_location_display_hours('19:00-24:00'));
  self::assertSame('07:00-24:30',koala_beanstalk_location_display_hours('07:00-24:30'));
 }
 public function test_replaces_old_standalone_review_feed_to_prevent_duplicates(): void {
  $source=file_get_contents(dirname(__DIR__).'/location-template.php');
  self::assertStringNotContainsString('echo koala_beanstalk_render_location_review_feed()',$source);
 }
 public function test_disabled_communities_and_empty_offers_do_not_render(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  Functions\when('get_the_ID')->justReturn(4923);
  Functions\when('get_post_meta')->justReturn('');
  self::assertSame('',koala_beanstalk_render_location_community(['section'=>'communities']));
  self::assertSame('',koala_beanstalk_render_location_community(['section'=>'offers']));
 }
 public function test_nap_hours_follow_visibility_and_skip_empty_weekend_fields(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  Functions\when('get_the_ID')->justReturn(4923);
  Functions\when('esc_html')->alias('strval');Functions\when('esc_attr')->alias('strval');
  Functions\when('do_shortcode')->justReturn('<div>map</div>');
  Functions\when('get_post_meta')->alias(static function($id,$key){return ['location_name'=>'Traverse City','show_hours'=>'1','location_hours_mon'=>'8am–5pm','location_phone_number'=>'(231) 202-2233'][$key]??'';});
  $html=koala_beanstalk_render_location_community(['section'=>'nap']);
  self::assertStringContainsString('Hours of Operation',$html);
  self::assertStringContainsString('8am–5pm',$html);
  self::assertStringNotContainsString('Sat:',$html);
  self::assertStringContainsString('tel:2312022233',$html);
 }
 public function test_empty_education_fields_hide_saved_static_content(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  Functions\when('get_the_ID')->justReturn(4923);
  Functions\when('get_post_meta')->justReturn('');
  self::assertSame('',koala_beanstalk_render_location_community(['section'=>'education']));
  self::assertSame('',koala_beanstalk_location_education_saved('<section>Old static resources</section>',['attrs'=>['metadata'=>['name'=>'koala-location-education']]]));
 }
 public function test_education_uses_location_acf_instead_of_static_resources(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-community.php';
  Functions\when('get_the_ID')->justReturn(4923);
  Functions\when('get_post_meta')->alias(static fn($id,$key)=>['insulation_education_title'=>'Local learning','insulation_education_description'=>'Our local resources'][$key]??'');
  Functions\when('esc_html')->alias('strval');Functions\when('wp_kses_post')->alias('strval');Functions\when('wpautop')->alias('strval');Functions\when('wp_strip_all_tags')->alias('strip_tags');
  $html=koala_beanstalk_render_location_community(['section'=>'education']);
  self::assertStringContainsString('Local learning',$html);
  self::assertStringContainsString('Our local resources',$html);
  self::assertStringNotContainsString('energy.gov',$html);
 }
 public function test_lower_typography_matches_bricks_sizes_and_line_heights(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-community.css');
  self::assertStringContainsString('font:700 48px/48px "Alegreya Sans"',$css);
  self::assertStringContainsString('font:400 18px/30.6px "Filson Pro"',$css);
  self::assertStringContainsString('font:700 24px/28.8px',$css);
  self::assertStringContainsString('font:700 27px/37.8px "Alegreya Sans"',$css);
  self::assertStringContainsString('font-size:32px;line-height:32px',$css);
 }
}
