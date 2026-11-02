<?php
/**
 * Canadian location dependencies, verified against the Canadian database.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the Canadian quote forms without changing their routing or consent.
 *
 * @param string $slot Sidebar or popup.
 * @return int
 */
function koala_beanstalk_location_form_id( string $slot ): int {
	return 'popup' === $slot ? 3 : 1;
}

/**
 * Resolve existing Canadian attachments; the migration validates file identities.
 *
 * @param string $slot Overview, poster or play.
 * @return int
 */
function koala_beanstalk_location_legacy_media( string $slot ): int {
	return array(
		'overview' => 19916,
		'poster'   => 19628,
		'play'     => 8367,
	)[ $slot ] ?? 0;
}

/** Resolve the Canadian facts instead of shared US block defaults. */
function koala_beanstalk_location_footer_facts(): array {
	return array(
		'factOne' => '*85% of homes are underinsulated',
		'factTwo' => '*64% of the homes energy is used for heating and cooling',
	);
}
