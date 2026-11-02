<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationOverviewTest extends TestCase {
 protected function setUp(): void {
  parent::setUp();
  Functions\when('add_action')->justReturn(true);
  if(is_file(dirname(__DIR__).'/location-overview.php'))require_once dirname(__DIR__).'/location-overview.php';
 }
 public function test_hide_toggle_prevents_section_output(): void {
  self::assertTrue(function_exists('koala_beanstalk_render_location_overview'));
  Functions\when('get_the_ID')->justReturn(7);
  Functions\when('get_post_meta')->justReturn('1');
  self::assertSame('',koala_beanstalk_render_location_overview());
 }
 public function test_overview_keeps_bricks_heading_and_reads_rich_text_fields(): void {
  self::assertTrue(function_exists('koala_beanstalk_location_overview_data'));
  Functions\when('get_the_title')->justReturn('Test Town');
  Functions\when('get_post_meta')->alias(function($id,$key){return ['location_overview_title'=>'Unused legacy title','location_insulation_rich_text_1'=>'<p>Intro copy</p>','location_insulation_rich_text_2'=>'<h3>Services we offer</h3>','location_phone_number'=>'(231) 202-2233'][$key]??'';});
  $data=koala_beanstalk_location_overview_data(7);
  self::assertSame('Koala Insulation Of Test Town',$data['title']);
  self::assertSame('<p>Intro copy</p>',$data['intro']);
  self::assertSame('<h3>Services we offer</h3>',$data['more']);
  self::assertSame('2312022233',$data['telephone']);
 }
 public function test_overview_has_accessible_disclosure_and_original_bricks_image(): void {
  $file=dirname(__DIR__).'/location-overview.php';self::assertFileExists($file);
  $source=file_get_contents($file);
  self::assertStringContainsString('<details',$source);
  self::assertStringContainsString('<summary',$source);
  self::assertStringContainsString("koala_beanstalk_location_legacy_media( 'overview' )",$source);
  self::assertStringContainsString("if ( '' !== trim( \$data['more'] ) )",$source);
  self::assertStringContainsString('koala_beanstalk_render_location_hero_quote',$source);
 }
 public function test_smaller_desktop_image_has_a_shorter_height(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-overview.css');
  self::assertStringContainsString('@media(min-width:992px) and (max-width:1440px){.koala-location-overview__image{height:340px}}',$css);
 }
}
