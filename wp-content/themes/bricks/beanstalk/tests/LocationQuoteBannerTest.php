<?php
namespace Koala\Beanstalk\Tests;
final class LocationQuoteBannerTest extends TestCase {
 public function test_banner_is_an_editable_core_block_pattern_with_the_shared_quote_trigger(): void {
  $file=dirname(__DIR__).'/patterns/location-quote-banner.php';
  self::assertFileExists($file);
  $source=file_get_contents($file);
  self::assertStringContainsString('<!-- wp:heading',$source);
  self::assertStringContainsString('<!-- wp:paragraph',$source);
  self::assertStringContainsString('<!-- wp:koala/location-hero-quote',$source);
  self::assertStringContainsString('Get a FREE quote',$source);
  self::assertStringNotContainsString('get_field(',$source);
 }
 public function test_banner_background_is_compressed_webp(): void {
  $file=dirname(__DIR__).'/assets/location-quote-banner/background.webp';
  self::assertFileExists($file);
  self::assertSame('image/webp',getimagesize($file)['mime']);
  self::assertLessThan(100000,filesize($file));
 }
 public function test_template_sections_append_after_block_wrapper_filters(): void {
  $source=file_get_contents(dirname(__DIR__).'/location-hero.php');
  self::assertStringContainsString("add_filter( 'render_block_core/group', 'koala_beanstalk_location_hero_extras', 1000, 2 )",$source);
 }
 public function test_banner_hover_uses_white_text_and_border(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-quote-banner.css');
  self::assertStringContainsString('.koala-location-quote-banner .koala-location-hero__quote:hover{background:transparent!important;color:#fff!important;border-color:#fff!important}',$css);
 }
}
