<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class CanadianLocationTest extends TestCase {
	public function test_canadian_location_form_ids_and_verified_media_are_separate_from_us(): void {
		$file = dirname( __DIR__ ) . '/location-country.php';
		self::assertFileExists( $file );
		require_once $file;
		self::assertSame( 1, koala_beanstalk_location_form_id( 'sidebar' ) );
		self::assertSame( 3, koala_beanstalk_location_form_id( 'popup' ) );
		self::assertSame( 19628, koala_beanstalk_location_legacy_media( 'poster' ) );
		self::assertSame( '*85% of homes are underinsulated', koala_beanstalk_location_footer_facts()['factOne'] );
	}

	public function test_canadian_path_activation_and_rest_context_require_the_migrated_allowlist(): void {
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		$file = dirname( __DIR__ ) . '/location-sidebar.php';
		self::assertFileExists( $file );
		require_once $file;
		Functions\when( 'wp_parse_url' )->alias( 'parse_url' );
		Functions\when( 'home_url' )->justReturn( 'https://koalainsulation.com/ca' );
		Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
		Functions\when( 'get_option' )->alias( static function ( $key, $default = false ) {
			return array( 'koala_beanstalk_locations_enabled' => true, 'koala_beanstalk_location_ids' => array( 20105 ) )[ $key ] ?? $default;
		} );
		self::assertTrue( koala_beanstalk_location_environment_enabled( 20105 ) );
		self::assertFalse( koala_beanstalk_location_environment_enabled( 20711 ) );
		Functions\when( 'home_url' )->justReturn( 'https://koalainsulation.com' );
		self::assertFalse( koala_beanstalk_location_environment_enabled( 20105 ) );
	}
}
