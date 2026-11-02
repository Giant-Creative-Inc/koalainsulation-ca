<?php
namespace Koala\Beanstalk\Tests;
final class LocationButtonHoverTest extends TestCase {
 public function test_location_buttons_share_transparent_blue_hover(): void {
  foreach (array('location-hero.css','location-sidebar.css') as $file) {
   $css=file_get_contents(dirname(__DIR__).'/assets/'.$file);
   self::assertStringContainsString('background:transparent!important;color:#042e53!important;border-color:#042e53!important',$css,$file.' must provide the requested hover treatment.');
  }
 }
 public function test_booking_image_fills_a_readable_responsive_width(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-hero.css');
  self::assertStringContainsString('.koala-location-booking img{display:block;width:100%!important;max-width:100%!important;height:auto!important}',$css);
  self::assertStringContainsString('.koala-location-booking{width:160px;max-width:160px;',$css);
 }
 public function test_housecall_booking_image_has_link_pointer(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-hero.css');
  self::assertStringContainsString('.koala-location-booking .hcp-button{display:block;width:100%;cursor:pointer}',$css);
 }
 public function test_buttons_animate_and_respect_reduced_motion(): void {
  foreach (array('location-hero.css','location-sidebar.css') as $file) {
   $css=file_get_contents(dirname(__DIR__).'/assets/'.$file);
   self::assertStringContainsString('transition:background-color .2s ease,color .2s ease,border-color .2s ease,transform .2s ease',$css);
   self::assertStringContainsString('transform:translateY(-2px)',$css);
   self::assertStringContainsString('transition:none!important;transform:none!important',$css);
  }
 }
 public function test_hero_quote_has_link_pointer(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-hero.css');
  self::assertStringContainsString('.koala-location-hero__quote{cursor:pointer}',$css);
 }
}
