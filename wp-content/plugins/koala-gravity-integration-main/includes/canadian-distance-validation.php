<?php
/**
 * Conservative Canadian postal-distance sanity checks.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns padded Statistics Canada census FSA envelopes.
 *
 * These envelopes only reject impossible distances. They never supply a
 * replacement distance, a franchise office coordinate, or postal ownership.
 *
 * @return array<string, array<float>> Envelopes in west, south, east, north order.
 */
function kgi_ca_fsa_bounds(): array {
	static $bounds = null;
	if ( null === $bounds ) {
		$file = dirname( __DIR__ ) . '/data/ca-fsa-bounds-2021.json';
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Bundled local JSON, never a remote URL.
		$bounds = is_readable( $file ) ? json_decode( file_get_contents( $file ), true ) : array();
		$bounds = is_array( $bounds ) ? $bounds : array();
	}
	return $bounds;
}

/**
 * Calculates a conservative lower distance bound between two postal areas.
 *
 * Uses the minimum latitude/longitude separations of padded envelopes and
 * the smallest cosine across their latitudes. The result cannot exceed the
 * shortest great-circle distance between points in the envelopes.
 *
 * @param string $origin Origin postal code.
 * @param string $target Candidate owned postal code.
 * @return float|null Lower bound in miles, or null when either area is unknown.
 */
function kgi_ca_fsa_min_distance( string $origin, string $target ): ?float {
	$bounds = kgi_ca_fsa_bounds();
	$a      = $bounds[ substr( $origin, 0, 3 ) ] ?? null;
	$b      = $bounds[ substr( $target, 0, 3 ) ] ?? null;
	if ( ! $a || ! $b ) {
		return null;
	}
	$lat_gap = deg2rad( max( 0, $a[1] - $b[3], $b[1] - $a[3] ) );
	$lon_gap = deg2rad( max( 0, $a[0] - $b[2], $b[0] - $a[2] ) );
	$cos_min = cos( deg2rad( max( abs( $a[1] ), abs( $a[3] ), abs( $b[1] ), abs( $b[3] ) ) ) );
	$value   = pow( sin( $lat_gap / 2 ), 2 ) + pow( $cos_min * sin( $lon_gap / 2 ), 2 );
	return 3958.7613 * 2 * asin( sqrt( min( 1, $value ) ) );
}

/**
 * Checks a provider distance without substituting approximate geography.
 *
 * Padded envelopes plus a two-mile tolerance deliberately allow boundary
 * changes and rounding. Unknown areas cannot establish a safe fallback.
 *
 * @param string $origin   Origin postal code.
 * @param string $target   Candidate owned postal code.
 * @param float  $distance Provider distance in miles.
 * @return bool Whether the distance is geographically plausible.
 */
function kgi_ca_radius_distance_is_plausible( string $origin, string $target, float $distance ): bool {
	$minimum = kgi_ca_fsa_min_distance( $origin, $target );
	return null !== $minimum && is_finite( $distance ) && $distance >= 0 && $distance + 2 >= $minimum;
}
