<?php
namespace Koala\Beanstalk\Tests;

final class QuoteAttentionTest extends TestCase {
	public function test_quote_links_trigger_the_hero_form_when_it_enters_the_viewport(): void {
		$script = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-quote-attention.js' );

		self::assertStringContainsString( '.koala-city-page__quote-cta[href="#city-page-quote"]', $script );
		self::assertStringContainsString( 'koala-city-commercial-services__cta koala-city-page__quote-cta', (string) file_get_contents( BEANSTALK_THEME_DIR . '/city-page.php' ) );
		self::assertStringContainsString( "getElementById( 'city-page-quote' )", $script );
		self::assertStringContainsString( 'IntersectionObserver', $script );
		self::assertStringContainsString( "classList.add( 'is-quote-attention' )", $script );
		self::assertStringContainsString( "addEventListener( 'animationend'", $script );
		self::assertStringNotContainsString( "observer.observe( form );\n\t\t\treturn;", $script );
		self::assertStringNotContainsString( 'preventDefault', $script );
	}

	public function test_quote_attention_matches_the_approved_motion_and_respects_reduced_motion(): void {
		$css = (string) file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-hero.css' );

		self::assertStringContainsString( '@keyframes koala-city-form-shake', $css );
		self::assertStringContainsString( 'animation: koala-city-form-shake .65s ease', $css );
		self::assertStringContainsString( 'translateX(-9px) rotate(-.4deg)', $css );
		self::assertStringContainsString( '.koala-city-page .koala-city-hero__form.is-quote-attention', $css );
		self::assertMatchesRegularExpression(
			'/@media \(prefers-reduced-motion: reduce\)[\s\S]*\.koala-city-page \.koala-city-hero__form\.is-quote-attention[\s\S]*animation: none/',
			$css
		);
	}

	public function test_quote_attention_script_is_conditionally_loaded_with_city_page_assets(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );

		self::assertStringContainsString( "'koala-beanstalk-city-page-quote-attention'", $bootstrap );
		self::assertStringContainsString( '/beanstalk/assets/city-page-quote-attention.js', $bootstrap );
		self::assertStringContainsString( "wp_script_add_data( 'koala-beanstalk-city-page-quote-attention', 'strategy', 'defer' )", $bootstrap );
	}
}
