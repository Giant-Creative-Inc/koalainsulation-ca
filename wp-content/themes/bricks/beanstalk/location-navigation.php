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
