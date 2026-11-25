<?php
namespace Koala\Beanstalk\Tests;

final class CityPageSpacingTest extends TestCase {
	public function test_mobile_testimonials_section_has_no_bottom_padding(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-testimonials.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/\.koala-city-testimonials\s*\{\s*padding: 48px 16px 0;/', $mobile );
	}

	public function test_mobile_testimonials_intro_has_no_bottom_margin(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-testimonials.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/testimonials__intro\s*\{[^}]*margin-bottom: 0;/', $mobile );
	}

	public function test_mobile_why_koala_content_has_no_vertical_padding(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-why-koala.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/why-koala__content\s*\{\s*padding: 0 16px;/', $mobile );
	}

	public function test_mobile_residential_card_gap_is_12_pixels(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-residential-services.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/residential-services__items--many\s*\{[^}]*gap: 12px;/', $mobile );
	}

	public function test_mobile_hero_copy_gap_is_24_pixels(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-hero.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 820px)' ) );
		self::assertMatchesRegularExpression( '/hero__copy > \.wp-block-group__inner-container\s*\{ gap: 24px;/', $mobile );
	}

	public function test_mobile_why_koala_benefits_have_no_bottom_padding(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-why-koala.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/why-koala__benefits\s*\{[^}]*padding-bottom: 0;/', $mobile );
	}

	public function test_mobile_heading_pairs_have_no_extra_spacing(): void {
		foreach ( array( 'residential', 'commercial' ) as $section ) {
			$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-' . $section . '-services.css' );
			$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
			self::assertMatchesRegularExpression( '/services__title-block > \.wp-block-group__inner-container\s*\{[^}]*gap: 0;/', $mobile );
		}
		$faq = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-faq.css' );
		self::assertStringContainsString( "faq__heading {\n\t\tmargin-top: 0;", $faq );
	}

	public function test_mobile_faq_items_have_no_bottom_margin(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-faq.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/faq__item\s*\{[^}]*margin-bottom: 0;/', $mobile );
	}

	public function test_mobile_commercial_captions_fill_the_image_width(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-commercial-services.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/commercial-services__caption\s*\{\s*width: 100%;/', $mobile );
	}

	public function test_mobile_commercial_copy_gap_is_24_pixels(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-commercial-services.css' );
		$mobile = substr( $css, strpos( $css, '@media (max-width: 767px)' ) );
		self::assertMatchesRegularExpression( '/commercial-services__copy > \.wp-block-group__inner-container\s*\{\s*gap: 24px;/', $mobile );
	}

	public function test_sections_share_one_desktop_container_and_gutter(): void {
		$asset_path = BEANSTALK_THEME_DIR . '/assets/';
		$hero       = (string) file_get_contents( $asset_path . 'city-page-hero.css' );
		$sections   = array(
			'city-page-residential-services.css',
			'city-page-commercial-services.css',
			'city-page-why-koala.css',
			'city-page-testimonials.css',
			'city-page-faq.css',
		);

		self::assertStringContainsString( '--city-content-width: 1440px', $hero );
		self::assertStringContainsString( '--city-page-gutter: 32px', $hero );
		self::assertStringContainsString( 'max-width: var(--city-content-width)', $hero );
		self::assertStringContainsString( 'padding-inline: var(--city-page-gutter)', $hero );

		foreach ( $sections as $section ) {
			$css = (string) file_get_contents( $asset_path . $section );
			self::assertStringContainsString( 'max-width: var(--city-content-width)', $css, $section );
			self::assertStringContainsString( 'padding-inline: var(--city-page-gutter)', $css, $section );
			self::assertStringNotContainsString( 'max-width: 1312px', $css, $section );
			self::assertStringNotContainsString( 'max-width: 1422px', $css, $section );
			self::assertStringNotContainsString( 'max-width: 1504px', $css, $section );
		}
	}
}
