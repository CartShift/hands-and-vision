<?php
/**
 * Gallery grid (artist/service single) – images or placeholders
 * Expects args: items, title?, subtitle?, section_class?
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
$args = get_query_var( 'args', array() );
$items = isset( $args['items'] ) ? $args['items'] : array();
if ( empty( $items ) ) {
	return;
}
$section_title = isset( $args['title'] ) ? $args['title'] : ( handandvision_is_hebrew() ? 'הגלריה' : 'Gallery' );
$section_subtitle = isset( $args['subtitle'] ) ? $args['subtitle'] : ( handandvision_is_hebrew() ? 'עבודות נבחרות' : 'Selected Works' );
$section_class = isset( $args['section_class'] ) ? $args['section_class'] : 'hv-section--cream';
?>
<section class="hv-section <?php echo esc_attr( $section_class ); ?>">
	<div class="hv-container">
		<header class="hv-section-header hv-text-center hv-mb-10">
			<span class="hv-overline hv-reveal"><?php echo esc_html( $section_subtitle ); ?></span>
			<h2 class="hv-headline-2 hv-line-draw hv-reveal"><?php echo esc_html( $section_title ); ?></h2>
		</header>
		<div class="hv-gallery-archive-grid hv-stagger">
			<?php foreach ( $items as $i => $item ) :
				if ( ! empty( $item['placeholder'] ) ) :
					$has_overlay = ! empty( $item['title'] ) || ! empty( $item['subtitle'] );
					if ( $has_overlay ) {
						$h = ( $i * 40 ) % 360;
						$h2 = ( $i * 40 + 30 ) % 360;
						$grad = "hsl($h, 8%, 82%) 0%, hsl($h2, 12%, 72%)";
					} else {
						$h = ( 40 + $i * 15 ) % 360;
						$h2 = ( 50 + $i * 15 ) % 360;
						$grad = "hsl($h, 12%, 80%) 0%, hsl($h2, 15%, 70%)";
					}
			?>
				<article class="hv-gallery-archive-item">
					<?php if ( $has_overlay ) : ?>
						<div class="hv-gallery-archive-item hv-gallery-archive-item--placeholder">
							<div class="hv-gallery-mosaic__placeholder" style="background: linear-gradient(135deg, <?php echo esc_attr( $grad ); ?>);"></div>
							<div class="hv-gallery-archive-item__overlay">
								<div>
									<h3 class="hv-gallery-archive-item__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
									<p class="hv-gallery-archive-item__artist"><?php echo esc_html( $item['subtitle'] ?? '' ); ?></p>
								</div>
							</div>
						</div>
					<?php else : ?>
						<div class="hv-gallery-mosaic__placeholder" style="background: linear-gradient(135deg, <?php echo esc_attr( $grad ); ?>);"></div>
					<?php endif; ?>
				</article>
			<?php else : ?>
				<article class="hv-gallery-archive-item">
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="hv-lightbox" data-caption="<?php echo esc_attr( $item['caption'] ?? '' ); ?>">
						<img src="<?php echo esc_url( $item['src'] ?? $item['url'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ?? '' ); ?>">
					</a>
				</article>
			<?php endif; endforeach; ?>
		</div>
	</div>
</section>
