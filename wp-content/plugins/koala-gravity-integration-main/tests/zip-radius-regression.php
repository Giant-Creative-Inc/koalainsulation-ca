<?php
/**
 * Regression checks for Canadian and U.S. ZIP-radius routing.
 *
 * Run with: php tests/zip-radius-regression.php
 *
 * @package Koala_Gravity_Integration
 */

define( 'ABSPATH', __DIR__ );
define( 'WEEK_IN_SECONDS', 604800 );

class WP_Post {
	public int $ID = 0;
}

class WP_Error {
	public function get_error_message(): string {
		return 'Fixture transport error';
	}
}

$kgi_test_response = array();
$kgi_test_url      = '';
$kgi_test_meta     = array();

function sanitize_key( string $value ): string {
	return strtolower( (string) preg_replace( '/[^a-z0-9_\-]/', '', $value ) );
}

function is_wp_error( $value ): bool {
	return $value instanceof WP_Error;
}

function wp_remote_get( string $url, array $args = array() ) {
	unset( $args );
	$GLOBALS['kgi_test_url'] = $url;
	return $GLOBALS['kgi_test_response'];
}

function wp_remote_retrieve_response_code( $response ): int {
	return (int) ( $response['status'] ?? 0 );
}

function wp_remote_retrieve_body( $response ): string {
	return (string) ( $response['body'] ?? '' );
}

function get_transient( string $key ) {
	return KGI_LOCATION_ZIP_INDEX_TRANSIENT === $key ? array() : false;
}

function set_transient( string $key, $value, int $expiration ): bool {
	unset( $key, $value, $expiration );
	return true;
}

function apply_filters( string $hook_name, $value ) {
	unset( $hook_name );
	return $value;
}

function add_query_arg( array $args, string $url ): string {
	return $url . '?' . http_build_query( $args );
}

function kgi_log( string $message, array $context = array() ): void {
	unset( $message, $context );
}

function get_option( string $name, $default = false ) {
	$options = array(
		'kgi_country'        => 'ca',
		'kgi_zipcodeapi_key' => 'fixture-key',
	);

	return $options[ $name ] ?? $default;
}

function kgi_get_field_map_for_form( int $form_id ): array {
	unset( $form_id );
	return array( 'zip' => '6' );
}

function rgar( array $array, string $key ) {
	return $array[ $key ] ?? null;
}

function gform_update_meta( int $entry_id, string $key, $value ): void {
	$GLOBALS['kgi_test_meta'][ $entry_id ][ $key ] = $value;
}

require_once dirname( __DIR__ ) . '/includes/location-resolver.php';

function kgi_test_assert_same( $expected, $actual, string $message ): void {
	if ( $expected !== $actual ) {
		fwrite( STDERR, $message . PHP_EOL . 'Expected: ' . var_export( $expected, true ) . PHP_EOL . 'Actual: ' . var_export( $actual, true ) . PHP_EOL );
		exit( 1 );
	}
}

$GLOBALS['kgi_test_response'] = array(
	'status' => 200,
	'body'   => json_encode(
		array(
			'postal_codes' => array( 'L4Z 1T9', 'L7L 4X9' ),
			'distances'    => array( 0, 19.4 ),
		)
	),
);

kgi_test_assert_same(
	array( 'L4Z1T9', 'L7L4X9' ),
	kgi_get_zip_radius_codes( 'L4Z1T9', 'ca', 'fixture-key', 575 ),
	'Canadian radius lookup must parse the expanded simple response.'
);

kgi_test_assert_same(
	true,
	str_ends_with( $GLOBALS['kgi_test_url'], '?simple=true&limit=50000' ),
	'Canadian radius lookup must request the expanded simple response.'
);

$GLOBALS['kgi_test_response'] = array(
	'status' => 200,
	'body'   => json_encode(
		array(
			'zip_codes' => array(
				array( 'zip_code' => '24018' ),
				array( 'zip_code' => '24019' ),
			),
		)
	),
);

kgi_test_assert_same(
	array( 24018, 24019 ),
	kgi_get_zip_radius_codes( '24018', 'us', 'fixture-key', 576 ),
	'U.S. radius lookup must retain its object-based ZIP response parsing.'
);

kgi_test_assert_same(
	'https://www.zipcodeapi.com/rest/fixture-key/radius.json/24018/60/mile',
	$GLOBALS['kgi_test_url'],
	'U.S. radius lookup URL must remain unchanged.'
);

$failure_fixtures = array(
	'transport'        => new WP_Error(),
	'rate_limited'     => array( 'status' => 429, 'body' => '{}' ),
	'http_503'         => array( 'status' => 503, 'body' => '{}' ),
	'invalid_response' => array( 'status' => 200, 'body' => 'not-json' ),
	'provider'         => array( 'status' => 200, 'body' => '{"error_msg":"fixture"}' ),
);

foreach ( $failure_fixtures as $expected_type => $response ) {
	kgi_clear_last_zip_api_error_type();
	$GLOBALS['kgi_test_response'] = $response;

	kgi_test_assert_same(
		null,
		kgi_zipcodeapi_get( 'https://example.test', 'radius', 'ca', 575 ),
		'Failed API responses must return null.'
	);

	kgi_test_assert_same(
		$expected_type,
		kgi_get_last_zip_api_error_type(),
		'Failed API responses must retain a specific diagnostic type.'
	);
}

$original_location                    = new WP_Post();
$original_location->ID                = 20180;
$GLOBALS['kgi_test_response']         = array( 'status' => 429, 'body' => '{}' );
$GLOBALS['kgi_test_meta'][575]        = array();

kgi_test_assert_same(
	$original_location,
	kgi_resolve_location_for_entry_zip( array( 'form_id' => 1, '6' => 'L4Z 1T9' ), $original_location, 575 ),
	'API failures must preserve the original page-derived location.'
);

kgi_test_assert_same(
	'api_error_rate_limited_original_preserved',
	$GLOBALS['kgi_test_meta'][575]['kgi_zip_routing_status'] ?? '',
	'Preserved locations must record the specific API error type.'
);

echo "ZIP radius regression checks passed.\n";
