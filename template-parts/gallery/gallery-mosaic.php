<?php
/**
 * Homepage gallery mosaic section
 * Expects args['items']: array of { url, title, caption }
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
$args = get_query_var( 'args', array() );
$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : ( function_exists( 'handandvision_get_home_gallery_images' ) ? handandvision_get_home_gallery_images( (int) get_option( 'page_on_front' ) ?: get_the_ID() ) : array() );
?>
<section class="hv-section hv-section--dark">
	<div class="hv-container">
		<header class="hv-section-header hv-text-center hv-animate">
			<span class="hv-overline hv-overline--light"><?php echo esc_html( handandvision_is_hebrew() ? 'מהאוסף' : 'From the Collection' ); ?></span>
			<h2 class="hv-headline-2 hv-text-white"><?php echo esc_html( handandvision_is_hebrew() ? 'הגלריה' : 'Gallery' ); ?></h2>
		</header>
		<?php if ( ! empty( $items ) ) : ?>
		<div class="hv-gallery-mosaic">
			<?php foreach ( $items as $i => $img ) :
				$mod = ( $i % 6 ) + 1;
				$title = $img['title'] ?? '';
				$caption = $img['caption'] ?? '';
				$data_caption = trim( $title . ' ' . $caption );
			?>
				<article class="hv-gallery-mosaic__item hv-gallery-mosaic__item--<?php echo (int) $mod; ?>">
					<a href="<?php echo esc_url( $img['url'] ); ?>" class="hv-lightbox" data-caption="<?php echo esc_attr( $data_caption ); ?>">
						<?php
						if ( ! empty( $img['id'] ) ) {
							echo wp_get_attachment_image( $img['id'], 'hv-gallery' );
						} else {
							echo '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">';
						}
						?>
						<div class="hv-gallery-mosaic__overlay">
							<h3 class="hv-gallery-mosaic__title"><?php echo esc_html( $title ); ?></h3>
							<span class="hv-gallery-mosaic__artist"><?php echo esc_html( $caption ); ?></span>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<div class="hv-text-center hv-mt-8">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'gallery_item' ) ); ?>" class="hv-btn hv-btn--outline-light"><?php echo esc_html( handandvision_is_hebrew() ? 'לגלריה המלאה' : 'Full Gallery' ); ?></a>
		</div>
	</div>
</section>
