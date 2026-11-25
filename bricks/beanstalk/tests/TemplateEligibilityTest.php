<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class TemplateEligibilityTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/bootstrap.php';
	}

	public function test_non_resource_posts_do_not_use_beanstalk(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 0 );

		self::assertFalse( koala_is_beanstalk_area_served_page() );
	}

	public function test_unmigrated_area_served_resource_posts_keep_legacy_template(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 87632 );
		Functions\expect( 'has_term' )->once()->with( 'areas-served', 'resources-page-type', 87632 )->andReturn( true );
		Functions\expect( 'beanstalk_content_engine_is_city_page_enabled' )->once()->with( 87632 )->andReturn( false );

		self::assertFalse( koala_is_beanstalk_area_served_page() );
	}

	public function test_migrated_area_served_resource_posts_use_beanstalk(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 87632 );
		Functions\expect( 'has_term' )->once()->with( 'areas-served', 'resources-page-type', 87632 )->andReturn( true );
		Functions\expect( 'beanstalk_content_engine_is_city_page_enabled' )->once()->with( 87632 )->andReturn( true );

		self::assertTrue( koala_is_beanstalk_area_served_page() );
	}

	public function test_migrated_resource_resolved_as_attachment_uses_beanstalk(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 56942 );
		Functions\expect( 'get_post_type' )->once()->with( 56942 )->andReturn( 'resources-landing-pa' );
		Functions\expect( 'has_term' )->once()->with( 'areas-served', 'resources-page-type', 56942 )->andReturn( true );
		Functions\expect( 'beanstalk_content_engine_is_city_page_enabled' )->once()->with( 56942 )->andReturn( true );

		self::assertTrue( koala_is_beanstalk_area_served_page() );
	}
}
