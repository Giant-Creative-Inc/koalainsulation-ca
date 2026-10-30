<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class FaqSectionTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/bootstrap.php';
	}

	public function test_manifest_exposes_every_faq_editorial_field(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$expected = array( 'faq_heading_prefix', 'faq_heading', 'faq_introduction', 'faq_cta_label' );
		for ( $index = 1; $index <= 5; ++$index ) {
			$expected[] = 'faq_' . $index . '_question';
			$expected[] = 'faq_' . $index . '_answer';
		}

		self::assertSame( '0.14.0', $manifest['version'] );
		self::assertSame( $expected, array_slice( array_keys( $manifest['fields'] ), 42, 14 ) );
		for ( $index = 1; $index <= 5; ++$index ) {
			self::assertSame( 'core/details', $manifest['fields'][ 'faq_' . $index . '_question' ]['target']['block'] );
			self::assertSame( 'summary', $manifest['fields'][ 'faq_' . $index . '_question' ]['target']['attribute'] );
		}
	}

	public function test_pattern_uses_five_native_details_without_javascript_or_later_sections(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'koala-city-faq', $pattern );
		self::assertSame( 5, substr_count( $pattern, '<!-- wp:details' ) );
		self::assertSame( 1, substr_count( $pattern, '<details class="wp-block-details koala-city-faq__item" open>' ) );
		self::assertSame( 4, substr_count( $pattern, '<details class="wp-block-details koala-city-faq__item"><summary>' ) );
		self::assertSame( 1, substr_count( $pattern, '"showContent":true' ) );
		self::assertSame( 4, substr_count( $pattern, '"showContent":false' ) );
		self::assertStringContainsString( '<!-- wp:koala/city-page-quote-cta -->', $pattern );
		self::assertStringNotContainsString( '<script', $pattern );
		self::assertStringNotContainsString( 'koala-city-footer', $pattern );
	}

	public function test_faq_copy_uses_only_allowlisted_location_placeholders(): void {
		$pattern      = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );
		$faq_position = strpos( $pattern, 'koala-city-faq' );
		$faq_markup   = false === $faq_position ? '' : substr( $pattern, $faq_position );

		foreach ( array( '{{service_area_name}}', '{{location_name}}', '{{state_name}}', '{{state_abbreviation}}', '{{location_phone}}' ) as $placeholder ) {
			self::assertStringContainsString( $placeholder, $faq_markup );
		}
		preg_match_all( '/{{[a-z_]+}}/', $faq_markup, $matches );
		self::assertEmpty( array_diff( array_unique( $matches[0] ), array_keys( koala_beanstalk_get_location_placeholders() ) ) );
	}

	public function test_named_details_summaries_receive_escaped_location_placeholders(): void {
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		$html = koala_beanstalk_render_location_placeholders(
			'<details><summary>Cost in {{service_area_name}}?</summary></details>',
			array(
				'blockName' => 'core/details',
				'attrs'     => array( 'metadata' => array( 'name' => 'koala-city-page-field-faq-1-question' ) ),
			),
			array( 'service_area_name' => 'Ho-Ho-Kus & North' ),
			true
		);

		self::assertStringContainsString( 'Ho-Ho-Kus &amp; North', $html );
	}

	public function test_faq_styles_match_the_desktop_and_mobile_nodes(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-faq.css' );

		self::assertStringContainsString( 'grid-template-columns: 544px minmax(0, 1fr)', $css );
		self::assertStringContainsString( 'gap: 58px', $css );
		self::assertStringContainsString( 'background-image: url("city-page/faq-chevron.svg")', $css );
		self::assertStringContainsString( '@media (max-width: 767px)', $css );
		self::assertStringContainsString( 'padding: 48px 16px', $css );
	}

	public function test_faq_schema_is_left_to_the_seo_plugin_without_duplicates(): void {
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array( '@type' => 'WebPage', 'name' => 'City page' ),
				array( '@type' => 'FAQPage', 'mainEntity' => array() ),
				array( '@type' => array( 'Thing', 'FAQPage' ) ),
			),
		);

		$filtered = koala_beanstalk_remove_faq_schema_nodes( $schema );

		self::assertCount( 1, $filtered['@graph'] );
		self::assertSame( 'WebPage', $filtered['@graph'][0]['@type'] );
		self::assertStringNotContainsString( 'FAQPage', (string) json_encode( $filtered ) );
	}

	public function test_faq_asset_and_conditional_stylesheet_are_registered(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );
		$bundle    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page.css' );

		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/faq-chevron.svg' );
		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/faq-accent.png' );
		self::assertStringContainsString( 'Source: city-page-faq.css', $bundle );
		self::assertStringContainsString( "'koala-beanstalk-city-page',", $bootstrap );
	}
}
