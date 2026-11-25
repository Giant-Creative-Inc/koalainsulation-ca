<?php
namespace Koala\Beanstalk\Tests;

final class FinalCtaTest extends TestCase {
	public function test_manifest_exposes_every_final_cta_editorial_field(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$expected = array(
			'final_cta_heading',
			'final_cta_supporting_copy',
			'final_cta_button_label',
			'final_cta_decorative_image',
		);

		self::assertSame( '0.14.0', $manifest['version'] );
		self::assertSame( $expected, array_slice( array_keys( $manifest['fields'] ), 56 ) );
		self::assertSame( 'image', $manifest['fields']['final_cta_decorative_image']['type'] );
		self::assertFalse( $manifest['fields']['final_cta_decorative_image']['required'] );
		self::assertSame( 'core/image', $manifest['fields']['final_cta_decorative_image']['target']['block'] );
	}

	public function test_pattern_contains_only_the_approved_final_cta_after_faq(): void {
		$pattern   = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );
		$faq       = strpos( $pattern, 'koala-city-faq' );
		$final_cta = strpos( $pattern, 'koala-city-final-cta' );

		self::assertNotFalse( $faq );
		self::assertNotFalse( $final_cta );
		self::assertGreaterThan( $faq, $final_cta );
		self::assertStringContainsString( '{{service_area_name}}', substr( $pattern, $final_cta ) );
		self::assertStringContainsString( '<!-- wp:koala/city-page-quote-cta -->', substr( $pattern, $final_cta ) );
		self::assertStringContainsString( 'alt=""', substr( $pattern, $final_cta ) );
		self::assertStringNotContainsString( 'koala-city-footer', $pattern );
	}

	public function test_final_cta_fields_have_unique_stable_named_targets(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$pattern  = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		foreach ( array_slice( $manifest['fields'], 57 ) as $field ) {
			self::assertStringStartsWith( 'koala-city-page-field-final-cta-', $field['target']['name'] );
			self::assertSame( 1, substr_count( $pattern, '"name":"' . $field['target']['name'] . '"' ) );
		}
	}

	public function test_final_cta_styles_match_desktop_and_mobile_figma(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-final-cta.css' );

		self::assertStringContainsString( 'linear-gradient(90deg, #93c845 0%, #74abda 100%)', $css );
		self::assertStringContainsString( 'border-radius: 200px 200px 0 0', $css );
		self::assertStringContainsString( 'padding: 128px 32px 84px', $css );
		self::assertStringContainsString( '@media (max-width: 767px)', $css );
		self::assertStringContainsString( 'border-radius: 100px 100px 0 0', $css );
		self::assertStringContainsString( 'padding: 64px 16px', $css );
	}

	public function test_exact_assets_and_conditional_stylesheet_are_registered(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );
		$bundle    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page.css' );

		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/final-cta-accent.png' );
		self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/final-cta-decoration.png' );
		self::assertStringContainsString( 'Source: city-page-final-cta.css', $bundle );
		self::assertStringContainsString( "'koala-beanstalk-city-page',", $bootstrap );
	}
}
