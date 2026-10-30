<?php
namespace Koala\Beanstalk\Tests;

use Brain\Monkey\Functions;

final class SeoSchemaTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		require_once BEANSTALK_THEME_DIR . '/bootstrap.php';
	}

	public function test_invalid_page_url_is_not_used_as_the_social_image(): void {
		Functions\when( 'get_queried_object_id' )->justReturn( 42 );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'wp_get_attachment_image_url' )->justReturn( false );
		Functions\when( 'get_template_directory_uri' )->justReturn( 'https://example.test/wp-content/themes/bricks' );

		self::assertSame(
			'https://example.test/wp-content/themes/bricks/beanstalk/assets/city-page/why-koala-image.webp',
			koala_beanstalk_get_social_image_url()
		);
	}

	public function test_schema_graph_adds_connected_location_and_service_nodes_without_duplicate_types(): void {
		Functions\when( 'get_permalink' )->justReturn( 'https://example.test/city/' );
		Functions\when( 'get_queried_object_id' )->justReturn( 42 );
		Functions\when( 'get_post_field' )->justReturn( '' );
		$graph = koala_beanstalk_filter_schema_graph(
			array( array( '@type' => 'WebPage', '@id' => 'https://example.test/city/#webpage' ) ),
			null,
			array(
				'id' => 7,
				'location_name' => 'Bergen County',
				'service_area_name' => 'Ho-Ho-Kus',
				'state_name' => 'New Jersey',
				'phone' => '(201) 555-0100',
				'address' => '123 Main St, Ho-Ho-Kus, NJ 07423',
				'home_url' => 'https://example.test/bergen-county/',
				'service_urls' => array( array( 'title' => 'Attic Insulation', 'url' => 'https://example.test/attic/' ) ),
			)
		);

		$types = array_column( $graph, '@type' );
		self::assertContains( 'HomeAndConstructionBusiness', $types );
		self::assertContains( 'Service', $types );
		self::assertContains( 'OfferCatalog', $types );
		self::assertContains( 'BreadcrumbList', $types );
		self::assertSame( count( $types ), count( array_unique( $types ) ) );
		self::assertSame( 'https://example.test/bergen-county/#localbusiness', $graph[0]['about']['@id'] );
		self::assertSame( 'https://example.test/city/#breadcrumb', $graph[0]['breadcrumb']['@id'] );
	}

	public function test_faq_schema_is_derived_from_visible_details_and_uses_location_placeholders(): void {
		Functions\when( 'esc_html' )->alias( 'htmlspecialchars' );
		Functions\when( 'esc_url' )->alias( 'htmlspecialchars' );
		Functions\when( 'wp_strip_all_tags' )->alias( 'strip_tags' );
		$content = '<!-- wp:details --><details><summary>Do you serve {{service_area_name}}?</summary><p class="koala-city-faq__answer">Call {{location_phone}}.</p></details><!-- /wp:details -->';
		$items   = koala_beanstalk_get_faq_schema_items(
			$content,
			array( 'service_area_name' => 'Ho-Ho-Kus', 'phone' => '(201) 555-0100' )
		);

		self::assertSame( 'Do you serve Ho-Ho-Kus?', $items[0]['name'] );
		self::assertSame( 'Call (201) 555-0100.', $items[0]['acceptedAnswer']['text'] );
	}

	public function test_only_the_lcp_heading_font_is_preloaded(): void {
		$bootstrap = (string) file_get_contents( BEANSTALK_THEME_DIR . '/bootstrap.php' );

		self::assertStringContainsString( 'alegreya-sans--v28-normal-700.woff2', $bootstrap );
		self::assertSame( 1, substr_count( $bootstrap, 'rel="preload" as="font"' ) );
		self::assertStringNotContainsString( 'FilsonProBold.woff" rel="preload"', $bootstrap );
	}
}
