<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationAsset404Test extends TestCase {
 public function test_native_template_removes_legacy_image_preloads(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-page.php';
  Functions\when('koala_beanstalk_sidebar_preview')->justReturn(true);
  Functions\expect('remove_action')->once()->with('wp_head','add_preload_lcp_image');
  Functions\expect('remove_action')->once()->with('wp_footer','custom_body_code');
  koala_beanstalk_location_prepare();
  $this->addToAssertionCount(2);
 }
 public function test_optimizer_exclusions_keep_relative_urls_on_native_pages(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-performance.php';
  Functions\when('koala_beanstalk_sidebar_preview')->justReturn(true);
  self::assertTrue(function_exists('koala_beanstalk_location_css_exclusions'));
  $existing=array('other.css');
  self::assertSame(array('other.css','/wp-content/themes/bricks/beanstalk/assets/location-deferred.css'),koala_beanstalk_location_css_exclusions($existing));
  self::assertSame(array('other.css','beanstalk/assets/location-quote-banner/background.webp'),koala_beanstalk_location_background_exclusions($existing));
  Functions\when('koala_beanstalk_sidebar_preview')->justReturn(false);
  self::assertSame($existing,koala_beanstalk_location_css_exclusions($existing));
  self::assertSame($existing,koala_beanstalk_location_background_exclusions($existing));
 }
}
