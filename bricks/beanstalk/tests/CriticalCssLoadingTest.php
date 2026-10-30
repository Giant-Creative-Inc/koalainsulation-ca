<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class CriticalCssLoadingTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		require_once BEANSTALK_THEME_DIR . '/bootstrap.php';
	}

	public function test_form_css_inlines_existing_rules_and_resolves_relative_assets(): void {
		$css = '.gfield{color:red;background:url("../fonts/icon.woff2")}';
		$html = koala_beanstalk_form_css_markup( '<link rel="stylesheet">', 'gravity_forms_theme_framework', $css, 'https://example.test/gravityforms/assets/css/dist/' );
		self::assertStringContainsString( '<style', $html );
		self::assertStringContainsString( '.gfield{color:red', $html );
		self::assertStringContainsString( 'https://example.test/gravityforms/assets/css/dist/../fonts/icon.woff2', $html );
		self::assertStringNotContainsString( '<link', $html );
		self::assertSame( '<link rel="stylesheet">', koala_beanstalk_form_css_markup( '<link rel="stylesheet">', 'other-plugin', $css, '' ) );
		self::assertSame( '<link rel="stylesheet">', koala_beanstalk_form_css_markup( '<link rel="stylesheet">', 'gravity_forms_theme_framework', false, '' ) );
	}

	public function test_deferred_link_replaces_existing_media_and_supports_wordpress_quotes(): void {
		$link = "<link rel='stylesheet' href='/footer.css' media='all' />";
		$html = koala_beanstalk_defer_noncritical_style( $link, 'koala-beanstalk-footer' );
		self::assertStringContainsString( 'media="print"', $html );
		self::assertStringContainsString( '<noscript>' . $link . '</noscript>', $html );
		self::assertSame( 1, preg_match_all( '/\smedia=/', substr( $html, 0, strpos( $html, '<noscript>' ) ) ) );
	}
}
