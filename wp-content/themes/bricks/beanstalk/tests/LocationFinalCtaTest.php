<?php
namespace Koala\Beanstalk\Tests;
final class LocationFinalCtaTest extends TestCase {
 public function test_pattern_preserves_bricks_copy_and_reuses_city_layout(): void {
  $file=dirname(__DIR__).'/patterns/location-final-cta.php';
  self::assertFileExists($file);
  $html=file_get_contents($file);
  self::assertStringContainsString('koala-city-final-cta__content',$html);
  self::assertStringContainsString('Ready to start your insulation project? Get a free quote from your local Koala Insulation team today.',$html);
  self::assertStringContainsString('<!-- wp:koala/location-hero-quote',$html);
  self::assertStringNotContainsString('get_field(',$html);
 }
 public function test_reused_raster_graphics_are_compressed_webp(): void {
  foreach(['accent','decoration'] as $name){
   $file=dirname(__DIR__).'/assets/location-final-cta/'.$name.'.webp';
   self::assertFileExists($file);self::assertSame('image/webp',getimagesize($file)['mime']);self::assertLessThan(100000,filesize($file));
  }
 }
}
