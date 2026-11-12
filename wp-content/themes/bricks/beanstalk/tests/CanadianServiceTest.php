<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class CanadianServiceTest extends TestCase {
	public function test_canadian_national_preview_uses_local_content_and_never_enables_production(): void {
		Functions\when( 'add_filter' )->justReturn( true );
		Functions\when( 'add_action' )->justReturn( true );
		require_once dirname( __DIR__ ) . '/service-hero.php';

		Functions\when( 'wp_parse_url' )->alias( 'parse_url' );
		$home = 'https://koala-insulation-ca.local';
		$environment = 'production';
		Functions\when( 'home_url' )->alias( static function () use ( &$home ) { return $home; } );
		Functions\when( 'wp_get_environment_type' )->alias( static function () use ( &$environment ) { return $environment; } );
		Functions\when( 'get_option' )->justReturn( true );
		$type = 'service';
		Functions\when( 'is_singular' )->alias( static function ( $candidate ) use ( &$type ) { return $candidate === $type; } );
		Functions\when( 'get_queried_object' )->justReturn( (object) array( 'post_name' => 'spray-foam-insulation' ) );
		Functions\when( 'get_query_var' )->justReturn( 'spray-foam-insulation' );
		Functions\when( 'bricks_is_builder' )->justReturn( false );

		self::assertTrue( koala_beanstalk_service_hero_preview() );
		self::assertSame( array(), koala_beanstalk_national_editorial_copy() );
		$excluded = koala_beanstalk_service_delay_exclusions( array( 'existing.js' ) );
		self::assertContains( '/beanstalk/assets/national-location.js', $excluded );
		self::assertContains( '/assets/js/custom-service.js', $excluded );
		self::assertContains( 'existing.js', $excluded );
		$type = 'location-service';
		self::assertFalse( koala_beanstalk_service_hero_preview() );
		$type = 'service';
		$home = 'https://staging.koalainsulation.com/ca/';
		self::assertFalse( koala_beanstalk_service_hero_preview() );
		$environment = 'staging';
		self::assertTrue( koala_beanstalk_service_hero_preview() );
		$home = 'https://staging.koalainsulation.com';
		self::assertFalse( koala_beanstalk_service_hero_preview() );
		$home = 'https://koalainsulation.com/ca';
		self::assertFalse( koala_beanstalk_service_hero_preview() );
		self::assertSame( array( 'existing.js' ), koala_beanstalk_service_delay_exclusions( array( 'existing.js' ) ) );
	}
}
