<?php
/**
 * Service card for homepage carousel.
 *
 * @package HandAndVision
 * @var array $args service_item, index, carousel_urls, img_id, title, desc, link
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item   = $args['service_item'] ?? null;
$index  = (int) ( $args['index'] ?? 0 );
$urls   = is_array( $args['carousel_urls'] ?? null ) ? $args['carousel_urls'] : array();
$img_id = (int) ( $args['img_id'] ?? 0 );
$title  = (string) ( $args['title'] ?? '' );
$desc   = (string) ( $args['desc'] ?? '' );
$link   = (string) ( $args['link'] ?? '' );

if ( ! $item || ! $link ) {
	return;
}

$has_rotate = count( $urls ) > 1;
$num        = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
$service_id = is_object( $item ) && isset( $item->ID ) ? (int) $item->ID : 0;
?>
<article class="hv-service-card swiper-slide hv-service-card--gallery"<?php echo $has_rotate ? ' data-hv-service-rotate="' . esc_attr( wp_json_encode( $urls ) ) . '"' : ''; ?>>
	<a href="<?php echo esc_url( $link ); ?>" class="hv-service-card__link"<?php echo $service_id ? ' data-service-id="' . esc_attr( (string) $service_id ) . '"' : ''; ?>>
		<figure class="hv-service-card__figure">
			<div class="hv-service-card__media">
				<span class="hv-service-card__index" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
				<?php if ( $img_id ) : ?>
					<div class="hv-service-card__rotate">
						<?php echo wp_get_attachment_image( $img_id, 'medium_large', false, array(
							'class'   => 'hv-service-card__img is-active',
							'loading' => $index < 2 ? 'eager' : 'lazy',
							'alt'     => $title,
						) ); ?>
					</div>
				<?php else : ?>
					<div class="hv-service-card__placeholder" style="background: linear-gradient(145deg, hsl(<?php echo (int) ( 32 + $index * 12 ); ?>, 18%, 88%) 0%, hsl(<?php echo (int) ( 38 + $index * 12 ); ?>, 22%, 72%) 100%);"></div>
				<?php endif; ?>
			</div>
			<figcaption class="hv-service-card__caption">
				<div class="hv-service-card__text">
					<h3 class="hv-service-card__title"><?php echo esc_html( $title ); ?></h3>
					<?php if ( $desc ) : ?>
						<p class="hv-service-card__desc"><?php echo esc_html( $desc ); ?></p>
					<?php endif; ?>
				</div>
				<span class="hv-service-card__arrow" aria-hidden="true"><?php echo handandvision_is_hebrew() ? '←' : '→'; ?></span>
			</figcaption>
		</figure>
	</a>
</article>
