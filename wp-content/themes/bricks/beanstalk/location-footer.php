<?php
/**
 * Server-rendered Beanstalk footer.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$beanstalk_settings = isset( $beanstalk_settings ) && is_array( $beanstalk_settings ) ? $beanstalk_settings : array();
$copy               = wp_parse_args(
	$beanstalk_settings,
	array(
		'homeLabel'          => 'Home',
		'whyKoalaLabel'      => 'Why Koala',
		'whyReinsulateLabel' => 'Why Reinsulate',
		'servicesLabel'      => 'Services',
		'resourcesLabel'     => 'Resources',
		'contactLabel'       => 'Contact Us',
		'factOne'            => '*85% of homes are underinsulated',
		'factTwo'            => '*64% of the homes energy is used for heating and cooling',
		'factThree'          => '*70% potential energy loss from poor insulation and air sealing',
		'copyrightSuffix'    => 'Koala Insulation. All rights reserved.',
		'privacyLabel'       => 'Privacy Policy',
		'termsLabel'         => 'Terms and Conditions',
	)
);
$copy               = array_merge( $copy, koala_beanstalk_location_footer_facts() );
$footer             = koala_beanstalk_get_navigation_data();
$location_preview   = function_exists( 'koala_beanstalk_sidebar_preview' ) && koala_beanstalk_sidebar_preview();
$footer_logo        = home_url( '/wp-content/uploads/2025/07/LogoC.png' );
$resources          = $location_preview ? koala_beanstalk_location_assigned_links( (int) $footer['location']->ID ) : $footer['resources'];
$why_links          = $location_preview ? koala_beanstalk_location_assigned_links(
	(int) $footer['location']->ID,
	array(
		'related_wk_page' => array( 'wk_related_location', $copy['whyKoalaLabel'] ),
		'related_wr_page' => array( 'wr_related_location', $copy['whyReinsulateLabel'] ),
	)
) : array(
	array(
		'title' => $copy['whyKoalaLabel'],
		'url'   => $footer['base'] . '/why-koala/',
	),
	array(
		'title' => $copy['whyReinsulateLabel'],
		'url'   => $footer['base'] . '/why-reinsulate/',
	),
);

?>
<footer class="beanstalk-footer">
	<div class="beanstalk-footer__inner">
		<div class="beanstalk-footer__content">
			<div class="beanstalk-footer__columns">
				<nav class="beanstalk-footer__column" aria-label="Footer navigation">
					<a class="beanstalk-footer__heading" href="<?php echo esc_url( $footer['base'] . '/' ); ?>"><?php echo esc_html( $copy['homeLabel'] ); ?></a>
					<?php foreach ( $why_links as $why_link ) : ?>
<a class="beanstalk-footer__heading" href="<?php echo esc_url( $why_link['url'] ); ?>"><?php echo esc_html( $why_link['title'] ); ?></a>
<?php endforeach; ?>
					<?php if ( $location_preview ) : ?>
						<a class="beanstalk-footer__heading" href="https://koalainsulationfranchise.ca/">Franchise</a>
						<a href="<?php echo esc_url( home_url( '/national-partnerships' ) ); ?>">National Partnerships</a>
					<?php endif; ?>
				</nav>

				<nav class="beanstalk-footer__column beanstalk-footer__services" aria-label="Footer services">
					<span class="beanstalk-footer__heading"><?php echo esc_html( $copy['servicesLabel'] ); ?></span>
					<?php foreach ( $footer['services'] as $service ) : ?>
						<a href="<?php echo esc_url( $service['url'] ); ?>"><?php echo esc_html( $service['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<nav class="beanstalk-footer__column" aria-label="Footer resources">
					<span class="beanstalk-footer__heading"><?php echo esc_html( $copy['resourcesLabel'] ); ?></span>
					<?php foreach ( $resources as $resource ) : ?>
						<a href="<?php echo esc_url( $resource['url'] ); ?>"><?php echo esc_html( $resource['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<div class="beanstalk-footer__column beanstalk-footer__contact">
					<span class="beanstalk-footer__heading"><?php echo esc_html( $copy['contactLabel'] ); ?></span>
					<?php
					if ( $footer['address'] ) :
						?>
						<address><?php echo nl2br( esc_html( $footer['address'] ) ); ?></address><?php endif; ?>
					<?php
					if ( $footer['phone'] ) :
						?>
						<a href="<?php echo esc_url( $footer['phone_url'] ); ?>"><?php echo esc_html( $footer['phone'] ); ?></a><?php endif; ?>
				</div>
			</div>

			<a class="beanstalk-footer__logo" href="<?php echo esc_url( $footer['base'] . '/' ); ?>" aria-label="<?php echo esc_attr( $footer['name'] . ' home' ); ?>">
				<img src="<?php echo esc_url( $footer_logo ); ?>" width="99" height="69" alt="Koala Insulation">
			</a>

			<div class="beanstalk-footer__facts">
				<p><a href="https://www.energystar.gov/saveathome/seal_insulate/methodology" style="color:inherit">*18% savings on costs with proper insulation</a></p>
				<p><a href="https://www.naimacanada.ca/for-homeowners/why-insulate/value-savings" style="color:inherit"><?php echo esc_html( $copy['factOne'] ); ?></a></p>
				<p><a href="https://natural-resources.canada.ca/maps-tools-publications/publications/energy-efficiency-trends-canada-1990-2013#a2" style="color:inherit"><?php echo esc_html( $copy['factTwo'] ); ?></a></p>
				<p><a href="https://www.energy.gov/sites/prod/files/2013/11/f5/insulation_guide.pdf" style="color:inherit"><?php echo esc_html( $copy['factThree'] ); ?></a></p>
			</div>
		</div>

		<div class="beanstalk-footer__bottom">
			<div class="beanstalk-footer__legal">
				<span>© <?php echo esc_html( wp_date( 'Y' ) . ' ' . $copy['copyrightSuffix'] ); ?></span>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php echo esc_html( $copy['privacyLabel'] ); ?></a>
				<a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php echo esc_html( $copy['termsLabel'] ); ?></a>
			</div>
			<nav aria-label="Social media">
				<a href="https://www.facebook.com/koalainsulationcanada">Facebook</a>
				<a href="https://www.instagram.com/koalainsulationcanada/">Instagram</a>
				<a href="https://www.linkedin.com/company/koala-insulation-canada/">LinkedIn</a>
				<a href="https://www.youtube.com/channel/UCEYUbnu82sHwLIRzsY6qtPQ">YouTube</a>
			</nav>
		</div>
	</div>
</footer>
