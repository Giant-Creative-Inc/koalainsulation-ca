<?php
namespace Koala\Beanstalk\Tests;

final class CanadianCityPageTest extends TestCase {
	public function test_default_copy_does_not_claim_us_rebates_or_uniform_climate_and_prices(): void {
		$pattern = file_get_contents( BEANSTALK_THEME_DIR . '/patterns/city-page.php' );
		foreach ( array( 'NJ Clean Energy', 'Department of Energy', 'climate zone 4A', '$1.60', '$2,400' ) as $claim ) {
			self::assertStringNotContainsString( $claim, $pattern );
		}
	}

	public function test_province_context_and_schema_contract_are_available(): void {
		$manifest = json_decode( file_get_contents( BEANSTALK_THEME_DIR . '/pattern-manifests/city-page.json' ), true );
		self::assertArrayHasKey( 'structuredData', $manifest );
		$data = file_get_contents( BEANSTALK_THEME_DIR . '/data.php' );
		self::assertStringContainsString( '_beanstalk_structured_data_values', $data );
	}

	public function test_new_canadian_footers_do_not_advertise_us_financing(): void {
		foreach ( array( 'parts.php', 'footer.php' ) as $file ) {
			self::assertStringNotContainsString( 'Wisetack', file_get_contents( BEANSTALK_THEME_DIR . '/' . $file ) );
			self::assertStringContainsString( '*85% of homes', file_get_contents( BEANSTALK_THEME_DIR . '/' . $file ) );
			self::assertStringContainsString( '*64% of the homes energy', file_get_contents( BEANSTALK_THEME_DIR . '/' . $file ) );
		}
	}
}
