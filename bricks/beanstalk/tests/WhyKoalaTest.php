<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class WhyKoalaTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/data.php';
		require_once BEANSTALK_THEME_DIR . '/city-page.php';
	}

	public function test_manifest_exposes_all_why_koala_editorial_fields(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$expected = array(
			'why_koala_introduction',
			'why_koala_heading',
			'why_koala_benefit_1',
			'why_koala_benefit_2',
			'why_koala_benefit_3',
			'why_koala_benefit_4',
			'why_koala_benefit_5',
			'why_koala_image',
			'why_koala_feature_1',
			'why_koala_feature_2',
			'why_koala_feature_3',
			'why_koala_cta_label',
		);

		self::assertSame( '0.14.0', $manifest['version'] );
		self::assertSame( $expected, array_slice( array_keys( $manifest['fields'] ), 26, 12 ) );
		self::assertSame( 'image', $manifest['fields']['why_koala_image']['type'] );
		self::assertFalse( $manifest['fields']['why_koala_image']['required'] );
		self::assertSame( 'core/image', $manifest['fields']['why_koala_image']['target']['block'] );
	}

	public function test_pattern_contains_only_the_approved_why_koala_section_next(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'koala-city-why-koala', $pattern );
		self::assertSame( 5, substr_count( $pattern, 'koala-city-why-koala__benefit"' ) / 2 );
		self::assertSame( 3, substr_count( $pattern, 'koala-city-why-koala__feature-label"' ) / 2 );
		self::assertStringContainsString( '{{service_area_name}} team', $pattern );
		self::assertStringContainsString( '<!-- wp:koala/city-page-quote-cta -->', $pattern );
		self::assertStringContainsString( '<!-- wp:koala/why-koala-video -->', $pattern );
	}

	public function test_video_wrapper_lazy_loads_the_approved_vimeo_video_on_click(): void {
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );

		$html = koala_beanstalk_render_why_koala_video_block( array(), '<figure>Editable image</figure>' );

		self::assertStringContainsString( 'data-video-src="https://player.vimeo.com/video/1208167466?h=900b1374b3&amp;byline=0&amp;title=0&amp;portrait=0"', $html );
		self::assertStringContainsString( 'class="koala-city-why-koala__video-trigger"', $html );
		self::assertStringContainsString( 'class="koala-city-why-koala__video"', $html );
		self::assertStringContainsString( 'hidden', $html );
		self::assertStringContainsString( '<figure>Editable image</figure>', $html );
		self::assertStringContainsString( 'aria-label="Play the What to Expect video"', $html );
		self::assertStringNotContainsString( 'dropbox.com', $html );
		self::assertStringNotContainsString( '<iframe src=', $html );
	}

	public function test_video_loader_is_scoped_and_conditionally_enqueued(): void {
		$script    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-video.js' );
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );
		$css       = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-why-koala.css' );

		self::assertStringContainsString( '.koala-city-why-koala__video-trigger', $script );
		self::assertStringContainsString( "searchParams.set( 'autoplay', '1' )", $script );
		self::assertStringContainsString( "container.classList.add( 'is-playing' )", $script );
		self::assertStringContainsString( '/beanstalk/assets/city-page-video.js', $bootstrap );
		self::assertStringContainsString( "wp_script_add_data( 'koala-beanstalk-city-page-video', 'strategy', 'defer' )", $bootstrap );
		self::assertMatchesRegularExpression( '/is-playing[^}]+koala-city-why-koala__image[^}]+opacity:\s*0;/s', $css );
	}

	public function test_quote_cta_escapes_the_editable_label(): void {
		Functions\when( 'wp_strip_all_tags' )->alias( 'strip_tags' );
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );

		$html = koala_beanstalk_render_city_page_quote_cta_block( array(), '<p>Get <strong>FREE</strong> &amp; safe</p>' );

		self::assertSame( '<a class="koala-city-page__quote-cta koala-city-why-koala__cta" href="#city-page-quote">Get FREE &amp;amp; safe</a>', $html );
		self::assertSame( '', koala_beanstalk_render_city_page_quote_cta_block( array(), '<p></p>' ) );
	}

	public function test_every_why_koala_field_uses_a_stable_named_target(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$pattern  = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		foreach ( array_slice( $manifest['fields'], 26, 12 ) as $field ) {
			self::assertStringStartsWith( 'koala-city-page-field-why-koala-', $field['target']['name'] );
			self::assertSame( 1, substr_count( $pattern, '"name":"' . $field['target']['name'] . '"' ) );
		}
	}

	public function test_why_koala_styles_match_desktop_and_mobile_figma_structure(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-why-koala.css' );

		self::assertStringContainsString( 'grid-template-columns: 720px minmax(0, 1fr)', $css );
		self::assertStringContainsString( 'height: 442px', $css );
		self::assertStringContainsString( 'grid-template-columns: repeat(3, minmax(0, 1fr))', $css );
		self::assertStringContainsString( '@media (max-width: 767px)', $css );
		self::assertStringContainsString( 'height: 284px', $css );
		self::assertStringContainsString( 'padding: 0 16px', $css );
	}

	public function test_why_koala_exact_exported_assets_and_styles_are_registered(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );
		$bundle    = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page.css' );
		$assets    = array(
			'why-koala-accent.png',
			'why-koala-image.webp',
			'why-koala-play.svg',
			'why-koala-comfort.svg',
			'why-koala-energy.svg',
			'why-koala-eco.svg',
		);

		foreach ( $assets as $asset ) {
			self::assertFileExists( BEANSTALK_THEME_DIR . '/assets/city-page/' . $asset );
		}
		self::assertStringContainsString( 'Source: city-page-why-koala.css', $bundle );
		self::assertStringContainsString( "'koala-beanstalk-city-page',", $bootstrap );
	}

	public function test_default_why_koala_image_is_optimized_and_lazy_loaded(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'why-koala-image.webp', $pattern );
		self::assertStringNotContainsString( 'why-koala-image.jpg', $pattern );
		self::assertStringContainsString( 'width="1200" height="769" loading="lazy" decoding="async"', $pattern );
	}
}
