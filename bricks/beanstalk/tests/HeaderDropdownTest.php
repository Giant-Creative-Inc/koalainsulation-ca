<?php
namespace Koala\Beanstalk\Tests;

final class HeaderDropdownTest extends TestCase {
	public function test_header_links_have_a_gold_hover_and_keyboard_focus_underline(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/header.css' );
		self::assertStringContainsString( '.beanstalk-header__menu a:hover', $css );
		self::assertStringContainsString( '.beanstalk-header__menu a:focus-visible', $css );
		self::assertStringContainsString( 'text-decoration: underline; text-decoration-color: #ffc107; text-decoration-thickness: 2px; text-underline-offset: 5px;', $css );
		self::assertStringNotContainsString( 'color: var(--beanstalk-green)', $css );
	}

	public function test_desktop_dropdown_is_bounded_and_scrollable(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/header.css' );
		self::assertMatchesRegularExpression( '/@media\s*\(min-width:\s*992px\)\s*\{.*?\.beanstalk-header__submenu\s*\{[^}]*max-height:\s*calc\(100dvh - var\(--beanstalk-header-height, 170px\) - 48px\);[^}]*overflow-y:\s*auto;/s', $css );
	}

	public function test_last_desktop_dropdown_opens_toward_the_available_space(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/header.css' );
		self::assertMatchesRegularExpression(
			'/@media\s*\(min-width:\s*992px\)\s*\{\s*\.beanstalk-header__menu\s*>\s*\.beanstalk-header__dropdown:last-child\s*>\s*\.beanstalk-header__submenu\s*\{\s*left:\s*auto;\s*right:\s*0;\s*\}/s',
			$css
		);
	}
}
