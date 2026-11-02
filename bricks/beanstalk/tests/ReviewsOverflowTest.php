<?php
namespace Koala\Beanstalk\Tests;

final class ReviewsOverflowTest extends TestCase {
	public function test_reviews_wrapper_and_slider_can_shrink_at_tablet_widths(): void {
		$css = file_get_contents( BEANSTALK_THEME_DIR . '/assets/city-page-testimonials.css' );
		$desktop_css = explode( '@media', $css )[0];
		self::assertMatchesRegularExpression( '/\.koala-city-testimonials__content > \.wp-block-group__inner-container\s*\{[^}]*width: 100%;[^}]*min-width: 0;/s', $desktop_css );
		self::assertMatchesRegularExpression( '/\.koala-city-testimonials__widget\s*\{[^}]*max-width: 100%;[^}]*min-width: 0;/s', $desktop_css );
		self::assertMatchesRegularExpression( '/\.koala-city-testimonials__widget-space \.grw-content\s*\{[^}]*flex-shrink: 1 !important;[^}]*min-width: 0 !important;/s', $desktop_css );
	}
}
