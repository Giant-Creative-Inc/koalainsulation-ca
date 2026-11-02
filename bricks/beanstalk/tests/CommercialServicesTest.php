<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class CommercialServicesTest extends TestCase {
	public function test_imported_gallery_images_receive_the_fixed_crop_styles(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-commercial-services.css' );
		self::assertStringContainsString( '.koala-city-commercial-services__gallery-item .wp-block-image {', $css );
		self::assertStringContainsString( '.koala-city-commercial-services__gallery-item .wp-block-image img {', $css );
	}
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/data.php';
		require_once BEANSTALK_THEME_DIR . '/city-page.php';
	}

	public function test_manifest_exposes_all_commercial_editorial_fields(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$expected = array(
			'commercial_services_introduction',
			'commercial_services_heading',
			'commercial_services_description',
			'commercial_services_benefit_1_title',
			'commercial_services_benefit_1_description',
			'commercial_services_benefit_2_title',
			'commercial_services_benefit_2_description',
			'commercial_services_benefit_3_title',
			'commercial_services_benefit_3_description',
			'commercial_services_image_1',
			'commercial_services_caption_1',
			'commercial_services_image_2',
			'commercial_services_caption_2',
			'commercial_services_cta_label',
		);

		self::assertSame( '0.14.0', $manifest['version'] );
		self::assertSame( $expected, array_slice( array_keys( $manifest['fields'] ), 12, 14 ) );
		foreach ( array( 'commercial_services_image_1', 'commercial_services_image_2' ) as $field_id ) {
			self::assertSame( 'image', $manifest['fields'][ $field_id ]['type'] );
			self::assertFalse( $manifest['fields'][ $field_id ]['required'] );
			self::assertSame( 'core/image', $manifest['fields'][ $field_id ]['target']['block'] );
		}
	}

	public function test_pattern_contains_commercial_section_without_later_sections(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'koala-city-commercial-services', $pattern );
		self::assertSame( 3, substr_count( $pattern, 'koala-city-commercial-services__benefit-title' ) / 2 );
		self::assertSame( 3, substr_count( $pattern, 'koala-city-commercial-services__benefit-description' ) / 2 );
		self::assertSame( 2, substr_count( $pattern, 'koala-city-commercial-services__gallery-image' ) / 2 );
		self::assertStringContainsString( '<!-- wp:koala/location-service-cta {"serviceKey":"commercial_insulation"} -->', $pattern );
	}

	public function test_commercial_cta_uses_the_shared_location_service_collection(): void {
		$context = array(
			'service_urls' => array(
				array( 'title' => 'Commercial Insulation Services', 'url' => 'https://example.test/commercial-insulation-services/' ),
			),
		);

		self::assertSame( 'https://example.test/commercial-insulation-services/', koala_beanstalk_get_location_service_url( 'commercial_insulation', $context ) );
	}

	public function test_commercial_cta_targets_the_shared_quote_form_with_an_editable_label(): void {
		Functions\when( 'sanitize_key' )->alias(
			static fn( string $value ): string => preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) )
		);
		Functions\when( 'wp_strip_all_tags' )->alias( 'strip_tags' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );

		$html = koala_beanstalk_render_location_service_cta_block(
			array( 'serviceKey' => 'commercial_insulation' ),
			'<p>Get <strong>Commercial</strong> Quote</p>',
			null,
			array( 'service_urls' => array( array( 'title' => 'Commercial', 'url' => 'https://example.test/commercial-insulation/?x=<bad>' ) ) )
		);

		self::assertStringContainsString( 'href="#city-page-quote"', $html );
		self::assertStringContainsString( 'koala-city-page__quote-cta', $html );
		self::assertStringContainsString( '>Get Commercial Quote</a>', $html );
		self::assertStringNotContainsString( 'commercial-insulation/?x=', $html );
		self::assertStringContainsString( '>Quote</a>', koala_beanstalk_render_location_service_cta_block( array( 'serviceKey' => 'commercial_insulation' ), '<p>Quote</p>', null, array( 'service_urls' => array() ) ) );
	}

	public function test_commercial_styles_match_desktop_and_mobile_structure(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-commercial-services.css' );

		self::assertStringContainsString( 'grid-template-columns: repeat(2, minmax(0, 1fr))', $css );
		self::assertStringContainsString( 'height: 360px', $css );
		self::assertStringContainsString( '@media (max-width: 767px)', $css );
		self::assertStringContainsString( 'height: 200px', $css );
	}

	public function test_commercial_assets_and_styles_are_registered(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );
		$bundle    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page.css' );

		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/commercial-accent.png' );
		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/commercial-space.webp' );
		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/commercial-new-construction.jpg' );
		self::assertStringContainsString( 'Source: city-page-commercial-services.css', $bundle );
		self::assertStringContainsString( "'koala-beanstalk-city-page',", $bootstrap );
	}

	public function test_default_commercial_images_use_optimized_formats_and_intrinsic_dimensions(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'commercial-space.webp', $pattern );
		self::assertStringNotContainsString( 'commercial-space.png', $pattern );
		self::assertStringContainsString( 'width="1280" height="853" loading="lazy" decoding="async"', $pattern );
		self::assertStringContainsString( 'commercial-new-construction.webp', $pattern );
	}
}
