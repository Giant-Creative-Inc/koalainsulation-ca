<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class HeroSectionTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/data.php';
		require_once BEANSTALK_THEME_DIR . '/city-page.php';
	}

	public function test_manifest_exposes_all_hero_editorial_fields(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$expected = array(
			'hero_heading',
			'hero_introduction',
			'hero_benefit_1',
			'hero_benefit_2',
			'hero_benefit_3',
			'hero_benefit_4',
			'hero_benefit_5',
			'hero_benefit_6',
			'hero_form_heading',
			'hero_form_submit_label',
		);

		self::assertSame( $expected, array_slice( array_keys( $manifest['fields'] ), 0, 10 ) );
	}

	public function test_pattern_retains_the_hero_section_and_real_form_block(): void {
		$pattern = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertStringContainsString( 'koala-city-hero', $pattern );
		self::assertStringContainsString( '<!-- wp:koala/location-gravity-form {"formId":1} -->', $pattern );
		self::assertStringContainsString( 'koala-city-page-field-hero-form-submit-label', $pattern );
		self::assertStringContainsString( 'koala-city-residential-services', $pattern );
		self::assertStringContainsString( 'koala-city-commercial-services', $pattern );
		self::assertSame( 6, substr_count( $pattern, 'koala-city-page-field-hero-benefit-' ) );
		self::assertStringContainsString( '<!-- wp:koala/location-review-summary /-->', $pattern );
	}

	public function test_review_summary_is_cached_by_location_and_feed(): void {
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\expect( 'set_transient' )->once()->with(
			'koala_city_review_summary_v1_42_47675',
			array( 'rating' => 4.9, 'count' => 298 ),
			43200
		)->andReturn( true );
		$loads   = 0;
		$summary = koala_beanstalk_get_location_google_review_summary(
			array( 'id' => 42, 'google_review_shortcode' => '[grw id=47675]' ),
			static function () use ( &$loads ): array {
				++$loads;
				return array( 'rating' => 4.9, 'count' => 298 );
			}
		);

		self::assertSame( array( 'rating' => 4.9, 'count' => 298 ), $summary );
		self::assertSame( 1, $loads );
	}

	public function test_review_summary_cache_hit_avoids_google_feed_lookup(): void {
		Functions\when( 'get_transient' )->justReturn( array( 'rating' => 4.8, 'count' => 321 ) );
		$summary = koala_beanstalk_get_location_google_review_summary(
			array( 'id' => 42, 'google_review_shortcode' => '[grw id=47675]' ),
			static function (): array {
				throw new \RuntimeException( 'Loader must not run on a cache hit.' );
			}
		);

		self::assertSame( array( 'rating' => 4.8, 'count' => 321 ), $summary );
	}

	public function test_review_summary_renders_five_stars_and_fails_safely_without_data(): void {
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'get_template_directory_uri' )->justReturn( 'https://example.test/wp-content/themes/bricks' );
		$html = koala_beanstalk_render_location_review_summary_block(
			array(),
			'',
			null,
			array( 'rating' => 4.9, 'count' => 298 )
		);

		self::assertSame( 5, substr_count( $html, 'rating-star.svg' ) );
		self::assertStringContainsString( '4.9/5 from 298+ verified reviews', $html );
		self::assertSame( '', koala_beanstalk_render_location_review_summary_block( array(), '', null, array() ) );
	}

	public function test_review_summary_omits_only_a_trailing_zero_decimal(): void {
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'get_template_directory_uri' )->justReturn( 'https://example.test/wp-content/themes/bricks' );

		$whole      = koala_beanstalk_render_location_review_summary_block( array(), '', null, array( 'rating' => 5.0, 'count' => 5 ) );
		$fractional = koala_beanstalk_render_location_review_summary_block( array(), '', null, array( 'rating' => 4.5, 'count' => 25 ) );

		self::assertStringContainsString( '5/5 from 5+ verified reviews', $whole );
		self::assertStringNotContainsString( '5.0/5', $whole );
		self::assertStringContainsString( '4.5/5 from 25+ verified reviews', $fractional );
	}

	public function test_legacy_stored_pattern_adds_review_summary_with_the_phone_without_duplication(): void {
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'get_template_directory_uri' )->justReturn( 'https://example.test/wp-content/themes/bricks' );
		$context = array( 'phone' => '(201) 627-0087', 'phone_url' => 'tel:2016270087' );

		$legacy = koala_beanstalk_render_location_phone_block( array(), '', null, $context, array( 'rating' => 4.9, 'count' => 298 ), false, true );
		$current = koala_beanstalk_render_location_phone_block( array(), '', null, $context, array( 'rating' => 4.9, 'count' => 298 ), true, true );

		self::assertStringContainsString( 'koala-city-hero__review-summary', $legacy );
		self::assertStringNotContainsString( 'koala-city-hero__review-summary', $current );
	}

	public function test_gravity_form_uses_location_override_and_editable_submit_label(): void {
		Functions\when( 'absint' )->alias( 'intval' );
		Functions\when( 'wp_strip_all_tags' )->alias( 'strip_tags' );
		Functions\expect( 'add_filter' )->once()->with( 'gform_submit_button_27', \Mockery::type( 'Closure' ) );
		Functions\expect( 'gravity_form' )->once()->with( 27, false, false, false, null, false, 0, false )->andReturn( '<form class="gform_wrapper">Real Gravity Form</form>' );
		Functions\expect( 'remove_filter' )->once()->with( 'gform_submit_button_27', \Mockery::type( 'Closure' ) );

		$html = koala_beanstalk_render_location_gravity_form_block(
			array( 'formId' => 13 ),
			'<p>Get a FREE Quote</p>',
			null,
			array( 'gravity_form_id' => 27 ),
			true
		);

		self::assertStringContainsString( 'Real Gravity Form', $html );
		self::assertStringContainsString( 'koala-city-hero__form-body', $html );
	}

	public function test_gravity_form_falls_back_to_form_13_and_fails_safely_without_gravity_forms(): void {
		self::assertSame(
			'',
			koala_beanstalk_render_location_gravity_form_block(
				array( 'formId' => 13 ),
				'<p>Get a FREE Quote</p>',
				null,
				array( 'gravity_form_id' => 0 ),
				false
			)
		);
	}

	public function test_submit_button_filter_preserves_gravity_forms_button_semantics(): void {
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		$button = '<input type="submit" class="gform_button" value="Submit">';

		self::assertSame(
			'<input type="submit" class="gform_button" value="Get a FREE Quote">',
			koala_beanstalk_replace_gravity_form_submit_label( $button, 'Get a FREE Quote' )
		);
	}

	public function test_editorial_hero_placeholders_are_replaced_during_server_rendering(): void {
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );

		$html = koala_beanstalk_render_location_placeholders(
			'<h1>Insulation in {{service_area_name}}</h1>',
			array(
				'blockName' => 'core/heading',
				'attrs'     => array( 'metadata' => array( 'name' => 'koala-city-page-field-hero-heading' ) ),
			),
			array( 'service_area_name' => '<Kirkwood>' ),
			true
		);

		self::assertSame( '<h1>Insulation in &lt;Kirkwood&gt;</h1>', $html );
	}
}
