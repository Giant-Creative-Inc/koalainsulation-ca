<?php
namespace Koala\Beanstalk\Tests;

final class CityPageTextColorTest extends TestCase {
	public function test_city_body_text_does_not_use_the_rejected_light_gray(): void {
		foreach ( array( 'city-page-faq.css', 'city-page-commercial-services.css' ) as $file ) {
			$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/' . $file );
			self::assertStringNotContainsString( '#908e8c', strtolower( $css ), $file );
			self::assertStringContainsString( '#4d545c', strtolower( $css ), $file );
		}
	}
}
