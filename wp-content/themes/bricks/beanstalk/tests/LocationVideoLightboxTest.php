<?php
namespace Koala\Beanstalk\Tests;
final class LocationVideoLightboxTest extends TestCase {
 public function test_location_video_triggers_share_a_modal_instead_of_replacing_posters(): void {
  $script=file_get_contents(dirname(__DIR__).'/assets/location-hero.js');
  self::assertStringContainsString('[data-location-hero-video], [data-location-about-play]',$script);
  self::assertStringContainsString('showModal()',$script);
  self::assertStringContainsString("addEventListener('close'",$script);
  self::assertStringNotContainsString('media.replaceChildren(frame)',file_get_contents(dirname(__DIR__).'/assets/location-services.js'));
 }
 public function test_video_lightbox_has_no_branded_frame(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-video.css');
  self::assertStringContainsString('padding:44px 0 0;border:0;border-radius:0;background:transparent',$css);
  self::assertStringNotContainsString('#afd56c',$css);
  self::assertStringNotContainsString('#042e53',$css);
 }
}
