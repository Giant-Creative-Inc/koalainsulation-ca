<?php
/**
 * Published location navigation assignments.
 *
 * @package Koala_Beanstalk
 */

/**
 * Resolve assigned pages without inventing location routes.
 *
 * @param int   $location_id Location record ID.
 * @param array $fields Relationship fields and owner/label settings.
 * @return array Published links with matching ownership and resolvable routes.
 */
function koala_beanstalk_location_assigned_links( int $location_id, array $fields = array() ): array {
	$fields = $fields ? $fields : array(
		'related_blog_page' => array( 'blog_related_location', 'Blog' ),
		'related_ho_page'   => array( 'ho_related_location', 'Homeowner Incentives' ),
		'related_faqs'      => array( '', 'FAQ' ),
		'related_rl_page'   => array( 'rl_related_location', '' ),
	);
	$links  = array();
	foreach ( $fields as $field => $settings ) {
		foreach ( (array) get_post_meta( $location_id, $field, true ) as $assigned ) {
			$id = $assigned instanceof WP_Post ? (int) $assigned->ID : (int) $assigned;
			if ( ! $id || 'publish' !== get_post_status( $id ) ) {
				continue;
			}
			$owners = $settings[0] ? array_filter( array_map( 'intval', (array) get_post_meta( $id, $settings[0], true ) ) ) : array();
			if ( $owners && ! in_array( $location_id, $owners, true ) ) {
				continue;
			}
			$url = get_permalink( $id );
			if ( ! $url || ( 'related_blog_page' === $field && (int) url_to_postid( $url ) !== $id ) ) {
				continue;
			}
			$links[ $url ] = array(
				'title' => $settings[1] ? $settings[1] : get_the_title( $id ),
				'url'   => $url,
			);
		}
	}
	return array_values( $links );
}

/**
 * Add the national partnership link once to a Resources menu.
 *
 * @param array $resources Existing resource links.
 * @return array Resource links including Mikey on Board.
 */
function koala_beanstalk_add_mikey_resource( array $resources ): array {
	$url = home_url( '/resources-landing-pa/mikey-on-board' );
	foreach ( $resources as $index => $resource ) {
		if ( rtrim( $resource['url'], '/' ) === $url ) {
			unset( $resources[ $index ] );
		}
	}

	$resources[] = array(
		'title' => 'Mikey on Board',
		'url'   => $url,
	);
	return array_values( $resources );
}

/**
 * Return the shared resource menu with each location's own routes.
 *
 * @param int $location_id Location record ID.
 * @return array Resource links in display order.
 */
function koala_beanstalk_location_resource_links( int $location_id ): array {
	$links      = koala_beanstalk_location_assigned_links( $location_id );
	$base       = rtrim( (string) get_permalink( $location_id ), '/' );
	$resources  = array(
		array(
			'title' => 'Homeowner Incentives',
			'url'   => $base . '/homeowner-incentives',
		),
		array(
			'title' => 'FAQ',
			'url'   => $base . '/faq',
		),
		array(
			'title' => 'Testimonials',
			'url'   => $base . '/testimonials',
		),
	);
	$city_pages = koala_beanstalk_get_published_city_pages( $location_id );
	$city_urls  = array_map( 'get_permalink', $city_pages );
	if ( $city_pages ) {
		array_unshift(
			$resources,
			array(
				'title' => 'Areas Served',
				'url'   => $base . '/areas-served',
			)
		);
	}
	foreach ( $links as $resource ) {
		if ( in_array( $resource['url'], $city_urls, true ) || rtrim( $resource['url'], '/' ) === $base . '/areas-served' ) {
			continue;
		}
		if ( 'Homeowner Incentives' !== $resource['title'] && 'Blog' !== $resource['title'] && 'FAQ' !== $resource['title'] ) {
			$resources[] = $resource;
		}
	}
	return koala_beanstalk_add_mikey_resource( $resources );
}
