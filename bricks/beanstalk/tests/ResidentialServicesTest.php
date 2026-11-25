<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class ResidentialServicesTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/data.php';
		require_once BEANSTALK_THEME_DIR . '/city-page.php';
	}

	public function test_manifest_exposes_all_residential_editorial_fields(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$expected = array(
			'residential_services_heading',
			'residential_services_introduction',
		);

		self::assertSame( '0.14.0', $manifest['version'] );
		self::assertSame( $expected, array_slice( array_keys( $manifest['fields'] ), 10, 2 ) );
		self::assertArrayNotHasKey( 'residential_services_card_1_title', $manifest['fields'] );
		self::assertSame( 'commercial_services_introduction', array_keys( $manifest['fields'] )[12] );
	}

	public function test_pattern_retains_hero_and_residential_services(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'koala-city-residential-services', $pattern );
		self::assertStringContainsString( 'koala-city-commercial-services', $pattern );
		self::assertSame( 1, substr_count( $pattern, '<!-- wp:koala/location-services /-->' ) );
		self::assertStringNotContainsString( '<!-- wp:koala/location-service-card-link', $pattern );
		self::assertStringNotContainsString( 'koala-city-page-field-residential-services-card-', $pattern );
	}

	public function test_location_services_block_is_registered_with_an_editor_preview(): void {
		$php    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/city-page.php' );
		$editor = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-editor.js' );

		self::assertStringContainsString( "'koala/location-services'", $php );
		self::assertStringContainsString( "'render_callback' => 'koala_beanstalk_render_location_services_block'", $php );
		self::assertStringContainsString( "blocks.registerBlockType( 'koala/location-services'", $editor );
	}

	public function test_location_services_render_every_related_service_in_saved_order(): void {
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'absint' )->alias( 'intval' );
		Functions\when( 'wp_get_attachment_image' )->alias(
			static fn( int $image_id, string $size, bool $icon, array $attributes ): string => '<img data-id="' . $image_id . '" alt="' . $attributes['alt'] . '">'
		);
		$services = array();
		for ( $index = 1; $index <= 7; ++$index ) {
			$services[] = array(
				'id'        => 100 + $index,
				'title'     => 'Service ' . $index,
				'url'       => 'https://example.test/service-' . $index . '/',
				'image_id'  => 200 + $index,
				'image_alt' => 'Service image ' . $index,
			);
		}

		$html = koala_beanstalk_render_location_services_block( array(), '', null, array( 'service_urls' => $services ) );

		self::assertStringContainsString( 'koala-city-residential-services__items--many', $html );
		self::assertSame( 7, substr_count( $html, 'koala-city-residential-services__card-link' ) );
		self::assertStringContainsString( 'href="https://example.test/service-1/"', $html );
		self::assertStringContainsString( 'href="https://example.test/service-7/"', $html );
		self::assertLessThan( strpos( $html, 'Service 7' ), strpos( $html, 'Service 1' ) );
		self::assertSame( '', koala_beanstalk_render_location_services_block( array(), '', null, array( 'service_urls' => array() ) ) );
	}

	public function test_service_card_urls_are_resolved_from_the_shared_context_without_queries(): void {
		$context = array(
			'service_urls' => array(
				array( 'title' => 'Spray Foam Insulation', 'url' => 'https://example.test/spray-foam-insulation/' ),
				array( 'title' => 'Air Sealing Service', 'url' => 'https://example.test/air-sealing-service/' ),
			),
		);

		self::assertSame( 'https://example.test/spray-foam-insulation/', koala_beanstalk_get_location_service_url( 'spray_foam', $context ) );
		self::assertSame( 'https://example.test/air-sealing-service/', koala_beanstalk_get_location_service_url( 'air_sealing', $context ) );
		self::assertSame( '', koala_beanstalk_get_location_service_url( 'unknown', $context ) );
	}

	public function test_service_card_link_escapes_the_location_url_and_fails_safely(): void {
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'sanitize_key' )->alias(
			static fn( string $value ): string => preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) )
		);

		$html = koala_beanstalk_render_location_service_card_link_block(
			array( 'serviceKey' => 'spray_foam' ),
			'<div class="card">Editable card</div>',
			null,
			array( 'service_urls' => array( array( 'title' => 'Spray Foam', 'url' => 'https://example.test/spray-foam/?x=<bad>' ) ) )
		);

		self::assertStringContainsString( 'href="https://example.test/spray-foam/?x=&lt;bad&gt;"', $html );
		self::assertStringContainsString( 'Editable card', $html );
		self::assertSame( '', koala_beanstalk_render_location_service_card_link_block( array( 'serviceKey' => 'unknown' ), 'content', null, array( 'service_urls' => array() ) ) );
	}

	public function test_residential_styles_define_desktop_and_mobile_card_layouts(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-residential-services.css' );

		self::assertStringContainsString( 'grid-template-columns: repeat(3, minmax(0, 1fr))', $css );
		self::assertStringContainsString( '.koala-city-residential-services__items--many', $css );
		self::assertStringContainsString( 'grid-template-columns: repeat(4, minmax(0, 1fr))', $css );
		self::assertMatchesRegularExpression( '/@media \(max-width: 1279px\)[\s\S]*__items--many[\s\S]*repeat\(3, minmax\(0, 1fr\)\)/', $css );
		self::assertMatchesRegularExpression( '/@media \(max-width: 1023px\)[\s\S]*__grid[\s\S]*__items--many[\s\S]*repeat\(2, minmax\(0, 1fr\)\)/', $css );
		self::assertStringContainsString( 'height: 220px', $css );
		self::assertStringContainsString( '@media (max-width: 767px)', $css );
		self::assertMatchesRegularExpression( '/@media \(max-width: 767px\)[\s\S]*__items--many[\s\S]*repeat\(2, minmax\(0, 1fr\)\)/', $css );
		self::assertMatchesRegularExpression( '/@media \(max-width: 479px\)[\s\S]*__grid[\s\S]*__items--many[\s\S]*grid-template-columns: 1fr/', $css );
		self::assertStringContainsString( 'height: 180px', $css );
	}

	public function test_residential_styles_are_conditionally_enqueued_with_city_page_assets(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );
		$bundle    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page.css' );

		self::assertStringContainsString( 'Source: city-page-residential-services.css', $bundle );
		self::assertStringContainsString( "'koala-beanstalk-city-page',", $bootstrap );
	}
}
