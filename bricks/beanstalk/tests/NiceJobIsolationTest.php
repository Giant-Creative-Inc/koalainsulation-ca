<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class NiceJobIsolationTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/bootstrap.php';
	}

	public function test_city_page_location_markup_omits_nicejob_but_keeps_other_integrations(): void {
		$markup = '<div class="nj-engage" data-position="left"></div><script src="https://cdn.nicejob.co/js/sdk.min.js?id=123" defer></script><script src="https://chat.housecallpro.com/embed.js"></script><div class="wp-gr">Google reviews</div>';
		$clean  = koala_beanstalk_filter_location_review_scripts( $markup, null, null, true );
		self::assertStringNotContainsString( 'nicejob.co', $clean );
		self::assertStringNotContainsString( 'nj-engage', $clean );
		self::assertStringContainsString( 'chat.housecallpro.com/embed.js', $clean );
		self::assertStringContainsString( 'Google reviews', $clean );
		self::assertSame( $markup, koala_beanstalk_filter_location_review_scripts( $markup, null, null, false ) );
	}

	public function test_city_pages_dequeue_the_plugin_sdk(): void {
		Functions\when( 'get_queried_object_id' )->justReturn( 42 );
		Functions\when( 'is_singular' )->justReturn( true );
		Functions\when( 'has_term' )->justReturn( true );
		Functions\when( 'beanstalk_content_engine_is_city_page_enabled' )->justReturn( true );
		$removed = array();
		Functions\when( 'wp_dequeue_script' )->alias( static function ( $handle ) use ( &$removed ): void { $removed[] = $handle; } );
		koala_beanstalk_remove_nicejob_sdk();
		self::assertSame( array( 'nicejob-sdk' ), $removed );
	}

	public function test_legacy_pages_keep_the_plugin_sdk(): void {
		Functions\when( 'get_queried_object_id' )->justReturn( 0 );
		Functions\when( 'is_singular' )->justReturn( false );
		$removed = array();
		Functions\when( 'wp_dequeue_script' )->alias( static function ( $handle ) use ( &$removed ): void { $removed[] = $handle; } );
		koala_beanstalk_remove_nicejob_sdk();
		self::assertSame( array(), $removed );
	}
}
