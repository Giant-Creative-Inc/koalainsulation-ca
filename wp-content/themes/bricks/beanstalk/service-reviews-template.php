<?php
/**
 * Service testimonials and project gallery.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service_reviews = koala_beanstalk_service_reviews_data( (int) get_queried_object_id() );
if ( $national_service ) {
	$service_reviews['title']   = $editorial['reviews']['title'] ?? 'Our Top Priority';
	$service_reviews['content'] = $editorial['reviews']['content'] ?? 'And don’t just take our word for it—hear from our happy clients about their experience with our professional insulation services.';
	$national_reviews           = array_filter(
		(array) get_post_meta( get_queried_object_id(), '_koala_national_service_testimonials', true ),
		static fn( $review ) => is_array( $review ) && isset( $review['quote'], $review['author'], $review['market'] )
	);
}
?>
<section id="location-reviews" class="koala-service-reviews" aria-labelledby="service-reviews-heading">
	<div class="koala-service-reviews__intro">
		<h2 id="service-reviews-heading">
			<?php if ( ! isset( $editorial['reviews'] ) || 'Our Top Priority' === $service_reviews['title'] ) : ?>
				<span>Your Satisfaction is</span>
			<?php endif; ?>
			<?php echo esc_html( $service_reviews['title'] ); ?>
		</h2>
		<?php echo wp_kses_post( wpautop( $service_reviews['content'] ) ); ?>
		<?php if ( $national_service ) : ?>
			<a href="<?php echo esc_url( home_url( '/testimonials' ) ); ?>">Read more customer testimonials</a>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $national_reviews ) ) : ?>
		<div class="koala-service-reviews__national">
			<?php foreach ( $national_reviews as $review ) : ?>
				<figure>
					<blockquote><?php echo esc_html( $review['quote'] ); ?></blockquote>
					<figcaption><?php echo esc_html( $review['author'] . ' · ' . $review['market'] ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ( $service_reviews['images'] ) : ?>
		<div class="koala-service-reviews__gallery" aria-label="Insulation project photos">
			<div class="koala-service-reviews__track" tabindex="0" aria-label="Scroll project photos">
				<?php foreach ( $service_reviews['images'] as $service_review_image ) : ?>
					<?php echo wp_get_attachment_image( $service_review_image, 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?>
				<?php endforeach; ?>
			</div>
			<div class="koala-service-reviews__controls" hidden>
				<button type="button" data-gallery-direction="-1" aria-label="Previous project photos"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/service-reviews-prev.svg' ); ?>" width="29" height="29" alt="" loading="lazy"></button>
				<button type="button" data-gallery-direction="1" aria-label="Next project photos"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/service-reviews-next.svg' ); ?>" width="29" height="29" alt="" loading="lazy"></button>
			</div>
		</div>
	<?php endif; ?>
</section>
