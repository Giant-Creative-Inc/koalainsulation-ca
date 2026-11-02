<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationLazyWidgetsTest extends TestCase {
 public function test_widget_scripts_are_inert_until_requested_but_forms_are_unchanged(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-lazy-widgets.php';
  Functions\when('koala_beanstalk_sidebar_preview')->justReturn(true);
  $tag='<script src="https://example.test/widget.js" id="widget-js"></script>';
  $out=koala_beanstalk_location_lazy_widget_script($tag,'grw-public-main-js');
  self::assertStringContainsString('data-widget-src=',$out);
  self::assertStringNotContainsString(' src=',$out);
  self::assertStringContainsString('type="text/plain"',$out);
  self::assertSame($tag,koala_beanstalk_location_lazy_widget_script($tag,'gform_gravityforms'));
 }
 public function test_lazy_loading_has_viewport_and_manual_load_paths(): void {
  $js=file_get_contents(dirname(__DIR__).'/assets/location-lazy-widgets.js');
  self::assertStringContainsString('IntersectionObserver',$js);
  self::assertStringContainsString('300px',$js);
  self::assertStringContainsString('grw_boot',$js);
  self::assertStringContainsString('data-load-widget',$js);
 }
 public function test_inline_map_configuration_stays_executable_when_external_loader_is_deferred(): void {
  Functions\when('add_action')->justReturn();Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-lazy-widgets.php';
  Functions\when('koala_beanstalk_sidebar_preview')->justReturn(true);
  $before='<script type="text/javascript" id="zip-shape-map-loader-js-before">window.zipShapeMapRestUrl="https://example.test/wp-json/zip-shape-map/v1/boundaries";window.zipShapeMapApiKey="test-key";</script>';
  $external='<script type="text/javascript" src="https://example.test/map.js" id="zip-shape-map-loader-js"></script>';
  $after='<script id="zip-shape-map-loader-js-after">window.mapAfter=true;</script>';
  $out=koala_beanstalk_location_lazy_widget_script($before.$external.$after,'zip-shape-map-loader');
  self::assertStringContainsString($before,$out);
  self::assertStringContainsString($after,$out);
  self::assertSame(1,substr_count($out,'type="text/plain"'));
  self::assertStringContainsString('data-widget-src="https://example.test/map.js"',$out);
 }
}
