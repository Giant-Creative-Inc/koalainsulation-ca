<?php
namespace Koala\Beanstalk\Tests;
final class LocationPerformanceTest extends TestCase {
 public function test_fallback_hero_has_responsive_webp_sources(): void {
  $source=file_get_contents(dirname(__DIR__).'/location-hero.php');
  self::assertStringNotContainsString('location-hero/default-image.png',$source);
  self::assertStringContainsString('default-image-480.webp',$source);
  self::assertStringContainsString('srcset=',$source);
  foreach([480,768,930] as $width){$p=dirname(__DIR__).'/assets/location-hero/default-image-'.$width.'.webp';self::assertFileExists($p);self::assertLessThan(150000,filesize($p));}
 }
 public function test_location_css_is_split_into_critical_and_deferred_bundles(): void {
  $source=file_get_contents(dirname(__DIR__).'/location-page.php');
  self::assertStringContainsString('location-critical',$source);
  self::assertStringContainsString('location-deferred',$source);
  self::assertFileExists(dirname(__DIR__).'/assets/location-critical.css');
  self::assertFileExists(dirname(__DIR__).'/assets/location-deferred.css');
 }
 public function test_location_font_faces_use_compressed_woff2(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-critical.css');
  self::assertStringNotContainsString('format("woff")',$css);
  foreach(['Regular','Medium','Bold'] as $weight)self::assertFileExists(dirname(__DIR__).'/assets/location-fonts/FilsonPro'.$weight.'.woff2');
 }
 public function test_deferred_bundle_keeps_a_no_javascript_fallback(): void {
  \Brain\Monkey\Functions\when('add_action')->justReturn();\Brain\Monkey\Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-performance.php';
  \Brain\Monkey\Functions\when('koala_beanstalk_sidebar_preview')->justReturn(true);
  $link='<link rel="stylesheet" href="bundle.css" media="all">';
  $html=koala_beanstalk_location_performance_style($link,'koala-beanstalk-location-deferred');
  self::assertStringContainsString('media="print"',$html);
  self::assertStringContainsString('<noscript>'.$link.'</noscript>',$html);
 }
 public function test_identical_montserrat_weights_share_one_cached_font_url(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-critical.css');
  self::assertSame(2,substr_count($css,'location-fonts/Montserrat.woff2'));
  self::assertStringNotContainsString('montserrat--v31-normal-500.woff2',$css);
 }
}
