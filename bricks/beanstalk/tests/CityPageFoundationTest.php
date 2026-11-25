<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class CityPageFoundationTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/data.php';
		require_once BEANSTALK_THEME_DIR . '/city-page.php';
	}

	public function test_metadata_names_use_the_stable_city_page_convention(): void {
		self::assertSame( 'koala-city-page-field-hero-heading', koala_beanstalk_city_field_name( 'hero.heading' ) );
		self::assertSame( 'koala-city-page-field-final-cta-button-label', koala_beanstalk_city_field_name( 'final_cta.button_label' ) );
		self::assertSame( '', koala_beanstalk_city_field_name( 'unsupported.heading' ) );
	}

	public function test_city_page_manifest_and_pattern_have_matching_stable_targets(): void {
		$manifest = json_decode( (string) file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		$pattern  = (string) file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );

		self::assertSame( 'koala/city-page', $manifest['id'] );
		self::assertSame( array( 'resources-landing-pa' ), $manifest['postTypes'] );
		self::assertSame( 'city-page.php', $manifest['template'] );
		foreach ( $manifest['fields'] as $field ) {
			self::assertSame( 1, substr_count( $pattern, '"name":"' . $field['target']['name'] . '"' ) );
		}
	}

	public function test_registers_the_city_page_pattern(): void {
		Functions\when( 'get_template_directory_uri' )->justReturn( 'https://example.test/wp-content/themes/bricks' );
		Functions\when( 'content_url' )->alias(
			static fn( string $path ): string => 'https://example.test/wp-content' . $path
		);
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\expect( 'register_block_pattern_category' )->once()->with( 'koala-city-pages', \Mockery::type( 'array' ) );
		Functions\expect( 'register_block_pattern' )->once()->with(
			'koala/city-page',
			\Mockery::on(
				static fn( array $settings ): bool => 'resources-landing-pa' === $settings['postTypes'][0]
					&& false !== strpos( $settings['content'], 'koala-city-page-field-hero-heading' )
			)
		);

		koala_beanstalk_register_city_page_pattern();
		self::assertTrue( true );
	}

	public function test_registers_the_content_engine_provider_when_available(): void {
		$provider = koala_beanstalk_get_content_engine_provider();

		self::assertSame( 'koala-beanstalk', $provider['id'] );
		self::assertSame( 'parent-theme', $provider['source_type'] );
		self::assertSame( 200, $provider['priority'] );
		self::assertDirectoryExists( $provider['pattern_directory'] );
		self::assertDirectoryExists( $provider['manifest_directory'] );
	}

	public function test_location_placeholders_are_allowlisted_and_escaped(): void {
		$context = array(
			'service_area_name' => '<Kirkwood>',
			'location_name'     => 'Central & Atlanta',
			'state_name'        => 'Georgia',
			'phone'             => '(404) 555-0100',
			'phone_url'         => 'tel:4045550100',
			'home_url'          => 'https://example.test/central-atlanta/',
		);
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );

		$result = koala_beanstalk_replace_location_placeholders(
			'Serving {{service_area_name}} from {{location_name}}. {{location_phone}} {{unknown}} {{state_name}}',
			$context
		);

		self::assertSame(
			'Serving &lt;Kirkwood&gt; from Central &amp; Atlanta. <a href="tel:4045550100">(404) 555-0100</a> {{unknown}} Georgia',
			$result
		);
		self::assertArrayHasKey( '{{location_home_url}}', koala_beanstalk_get_location_placeholders() );
	}

	public function test_location_home_placeholder_links_location_name_and_state(): void {
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );

		$result = koala_beanstalk_replace_location_placeholders(
			'Visit {{location_home_url}}.',
			array(
				'location_name' => 'Bergen County',
				'state_name'    => 'New Jersey',
				'home_url'      => 'https://example.test/bergen-county/',
			)
		);

		self::assertSame(
			'Visit <a href="https://example.test/bergen-county/">Bergen County, New Jersey</a>.',
			$result
		);
	}

	public function test_location_phone_block_is_server_rendered_and_fails_safely(): void {
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_attr' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );

		$html = koala_beanstalk_render_location_phone_block(
			array( 'label' => 'Call <now>' ),
			'',
			null,
			array(
				'phone'     => '(404) 555-0100',
				'phone_url' => 'tel:4045550100',
			)
		);

		self::assertStringContainsString( 'href="tel:4045550100"', $html );
		self::assertStringContainsString( 'Call &lt;now&gt;', $html );
		self::assertSame( '', koala_beanstalk_render_location_phone_block( array(), '', null, array() ) );
	}

	public function test_registers_the_server_rendered_location_phone_block(): void {
		Functions\when( 'get_template_directory_uri' )->justReturn( 'https://example.test/wp-content/themes/bricks' );
		Functions\expect( 'wp_register_script' )->once()->with(
			'koala-beanstalk-city-page-editor',
			'https://example.test/wp-content/themes/bricks/beanstalk/assets/city-page-editor.js',
			array( 'wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n' ),
			\Mockery::type( 'string' ),
			true
		);
		Functions\expect( 'register_block_type' )->once()->with(
			'koala/location-phone',
			\Mockery::on(
				static fn( array $settings ): bool => 'koala_beanstalk_render_location_phone_block' === $settings['render_callback']
					&& 'koala-beanstalk-city-page-editor' === $settings['editor_script']
					&& 'string' === $settings['attributes']['label']['type']
			)
		);
		Functions\expect( 'register_block_type' )->once()->with(
			'koala/location-gravity-form',
			\Mockery::on(
				static fn( array $settings ): bool => 'koala_beanstalk_render_location_gravity_form_block' === $settings['render_callback']
					&& 1 === $settings['attributes']['formId']['default']
			)
		);

		koala_beanstalk_register_city_page_blocks();
		self::assertTrue( true );
	}
}
