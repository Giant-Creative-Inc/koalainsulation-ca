<?php
namespace Koala\Beanstalk\Tests;

final class CityPageInteractionsTest extends TestCase {
	public function test_quote_anchor_clears_the_measured_header(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-hero.css' );
		self::assertStringContainsString( 'scroll-margin-top: calc(var(--beanstalk-header-height, 170px) + 16px)', $css );
		self::assertStringContainsString( 'scroll-margin-top: calc(var(--beanstalk-header-height, 170px) + 62px)', $css );
	}

	public function test_faq_opening_closes_siblings_and_animates_with_reduced_motion_support(): void {
		$script = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-quote-attention.js' );
		$css    = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-faq.css' );
		self::assertStringContainsString( "addEventListener( 'toggle'", $script );
		self::assertStringContainsString( 'other.open = false', $script );
		self::assertStringContainsString( 'animation: koala-city-faq-reveal .2s ease-out', $css );
		self::assertMatchesRegularExpression( '/prefers-reduced-motion: reduce[\s\S]*animation: none/', $css );
	}
}
