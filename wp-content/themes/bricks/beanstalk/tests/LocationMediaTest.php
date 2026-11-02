<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationMediaTest extends TestCase {
 public function test_imported_image_ids_resolve_through_wordpress(): void {
  Functions\when('add_filter')->justReturn();Functions\when('esc_url')->alias('strval');
  require_once dirname(__DIR__).'/location-media.php';
  Functions\when('get_option')->justReturn(['location-hero/default-image-930.webp'=>123]);
  Functions\when('wp_get_attachment_url')->alias(static fn($id)=>$id===123?'https://cdn.example.test/hero.webp':false);
  self::assertSame('<img src="https://cdn.example.test/hero.webp">',koala_beanstalk_location_media_urls('<img src="/wp-content/themes/bricks/beanstalk/assets/location-hero/default-image-930.webp">'));
  self::assertSame('<img src="/other.webp">',koala_beanstalk_location_media_urls('<img src="/other.webp">'));
 }
}
