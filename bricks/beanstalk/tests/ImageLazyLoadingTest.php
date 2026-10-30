<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class ImageLazyLoadingTest extends TestCase {
	public function test_saved_bricks_placeholders_render_with_native_lazy_loading(): void {
		$html = '<figure><img src="data:image/svg+xml,placeholder" data-src="/commercial.webp" data-srcset="/commercial.webp 950w" class="size-large bricks-lazy-hidden" width="950" height="634" loading="lazy"></figure>';
		$result = koala_beanstalk_native_block_images( $html, array( 'blockName' => 'core/image' ), true );
		self::assertStringContainsString( 'src="/commercial.webp"', $result );
		self::assertStringContainsString( 'srcset="/commercial.webp 950w"', $result );
		self::assertStringContainsString( 'loading="lazy"', $result );
		self::assertStringContainsString( 'width="950"', $result );
		self::assertStringNotContainsString( 'data-src', $result );
		self::assertStringNotContainsString( 'bricks-lazy-hidden', $result );
		self::assertSame( $html, koala_beanstalk_native_block_images( $html, array( 'blockName' => 'core/image' ), false ) );
	}
	public function test_complete_city_markup_lazily_loads_logos_reviews_and_content_images(): void {
		$html = '<header><img src="/logo.svg" fetchpriority="high"></header><main><img src="/service.jpg" loading="eager"><img src="/review.jpg"></main><footer><img src="/footer.svg"></footer>';
		$result = koala_beanstalk_lazy_page_images( $html );
		self::assertSame( 4, substr_count( $result, 'loading="lazy"' ) );
		self::assertStringNotContainsString( 'fetchpriority="high"', $result );
		self::assertStringContainsString( 'src="/review.jpg"', $result );
	}

	public function test_city_pages_keep_native_lazy_loading_when_rocket_is_active(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 56942 );
		Functions\expect( 'has_term' )->once()->with( 'areas-served', 'resources-page-type', 56942 )->andReturn( true );
		Functions\expect( 'beanstalk_content_engine_is_city_page_enabled' )->once()->with( 56942 )->andReturn( true );
		self::assertFalse( koala_beanstalk_disable_rocket_image_lazyload( true ) );
	}

	public function test_other_pages_retain_the_rocket_lazy_loading_setting(): void {
		Functions\expect( 'is_singular' )->twice()->with( 'resources-landing-pa' )->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->twice()->andReturn( 0 );
		self::assertTrue( koala_beanstalk_disable_rocket_image_lazyload( true ) );
		self::assertFalse( koala_beanstalk_disable_rocket_image_lazyload( false ) );
	}

	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/bootstrap.php';
		$api = rtrim( getenv( 'WP_CORE_DIR' ) ?: dirname( __DIR__, 4 ) . '/ca/app/public', '/' ) . '/wp-includes/html-api/';
		require_once dirname( $api ) . '/class-wp-token-map.php';
		require_once dirname( $api ) . '/utf8.php';
		require_once dirname( $api ) . '/kses.php';
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		require_once $api . 'class-wp-html-tag-processor.php';
		foreach ( glob( $api . '*.php' ) as $file ) {
			require_once $file;
		}
	}

	public function test_city_pages_restore_native_image_attributes_after_bricks_lazy_loading(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 87632 );
		Functions\expect( 'has_term' )->once()->with( 'areas-served', 'resources-page-type', 87632 )->andReturn( true );
		Functions\expect( 'beanstalk_content_engine_is_city_page_enabled' )->once()->with( 87632 )->andReturn( true );

		$attributes = koala_beanstalk_restore_native_image_attributes(
			array(
				'class'       => 'service-image bricks-lazy-hidden another-class',
				'src'         => 'data:image/svg+xml,placeholder',
				'data-src'    => 'https://example.test/service.jpg',
				'data-srcset' => 'https://example.test/service-340.jpg 340w, https://example.test/service.jpg 876w',
				'data-sizes'  => '(max-width: 876px) 100vw, 876px',
				'data-type'   => 'string',
				'loading'     => 'lazy',
			)
		);

		self::assertSame( 'https://example.test/service.jpg', $attributes['src'] );
		self::assertSame( 'https://example.test/service-340.jpg 340w, https://example.test/service.jpg 876w', $attributes['srcset'] );
		self::assertSame( '(max-width: 876px) 100vw, 876px', $attributes['sizes'] );
		self::assertSame( 'lazy', $attributes['loading'] );
		self::assertSame( 'service-image another-class', $attributes['class'] );
		self::assertArrayNotHasKey( 'data-src', $attributes );
		self::assertArrayNotHasKey( 'data-srcset', $attributes );
		self::assertArrayNotHasKey( 'data-sizes', $attributes );
		self::assertArrayNotHasKey( 'data-type', $attributes );
	}

	public function test_non_city_pages_keep_bricks_image_attributes_unchanged(): void {
		Functions\expect( 'is_singular' )->once()->with( 'resources-landing-pa' )->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 0 );
		$attributes = array(
			'class'    => 'bricks-lazy-hidden',
			'src'      => 'data:image/svg+xml,placeholder',
			'data-src' => 'https://example.test/service.jpg',
		);

		self::assertSame( $attributes, koala_beanstalk_restore_native_image_attributes( $attributes ) );
	}

	public function test_saved_default_city_image_is_upgraded_without_overriding_editorial_images(): void {
		Functions\expect( 'is_singular' )->twice()->with( 'resources-landing-pa' )->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->twice()->andReturn( 87632 );
		Functions\expect( 'has_term' )->twice()->with( 'areas-served', 'resources-page-type', 87632 )->andReturn( true );
		Functions\expect( 'beanstalk_content_engine_is_city_page_enabled' )->twice()->with( 87632 )->andReturn( true );
		$block = array(
			'blockName' => 'core/image',
			'attrs'     => array( 'metadata' => array( 'name' => 'koala-city-page-field-commercial-services-image-1' ) ),
		);

		self::assertStringContainsString(
			'commercial-space.webp',
			koala_beanstalk_optimize_default_city_images( '<img src="/commercial-space.png">', $block )
		);
		self::assertSame(
			'<img src="/editor-selected.jpg">',
			koala_beanstalk_optimize_default_city_images( '<img src="/editor-selected.jpg">', $block )
		);
	}

}
