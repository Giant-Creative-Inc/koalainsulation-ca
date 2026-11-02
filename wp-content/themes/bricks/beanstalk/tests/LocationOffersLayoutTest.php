<?php
namespace Koala\Beanstalk\Tests;
final class LocationOffersLayoutTest extends TestCase {
 public function test_desktop_cards_fit_three_with_two_grid_gaps(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-community.css');
  self::assertStringContainsString('@media(min-width:992px){.koala-location-offers__card{width:calc((100% - 48px)/3)}}',$css);
 }
}
