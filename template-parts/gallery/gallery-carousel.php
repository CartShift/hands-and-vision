<?php
/**
 * Homepage gallery carousel section
 * Expects args['items']: array of { url, title, caption, id }
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
$args = get_query_var( 'args', array() );
$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : ( function_exists( 'handandvision_get_home_gallery_images' ) ? handandvision_get_home_gallery_images( (int) get_option( 'page_on_front' ) ?: get_the_ID() ) : array() );

if ( empty( $items ) ) {
	return;
}
?>
<section class="hv-section hv-section--dark hv-gallery-carousel-section" aria-labelledby="gallery-heading">
	<div class="hv-container">
		<header class="hv-section-header hv-text-center hv-animate">
			<span class="hv-overline hv-overline--light"><?php echo esc_html( handandvision_is_hebrew() ? 'מהאוסף' : 'From the Collection' ); ?></span>
			<h2 id="gallery-heading" class="hv-headline-2 hv-text-white"><?php echo esc_html( handandvision_is_hebrew() ? 'הגלריה' : 'Gallery' ); ?></h2>
		</header>
	</div>

	<div class="hv-gallery-carousel swiper">
		<div class="hv-gallery-carousel__controls">
            <button type="button" class="hv-gallery-carousel__btn hv-gallery-carousel__btn--prev" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'הבא' : 'Previous' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="hv-gallery-carousel__btn hv-gallery-carousel__btn--next" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'הקודם' : 'Next' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        </div>

		<div class="swiper-wrapper">
			<?php foreach ( $items as $i => $img ) :
				$title = $img['title'] ?? '';
				$caption = $img['caption'] ?? '';
				$data_caption = trim( $title . ' ' . $caption );
			?>
				<div class="hv-gallery-carousel__item swiper-slide" role="listitem">
					<a href="<?php echo esc_url( $img['url'] ); ?>" class="hv-lightbox hv-gallery-carousel__link" data-caption="<?php echo esc_attr( $data_caption ); ?>">
						<div class="hv-gallery-carousel__img-wrap">
							<?php
							if ( ! empty( $img['id'] ) ) {
								echo wp_get_attachment_image( $img['id'], 'large', false, array( 'class' => 'hv-gallery-carousel__img', 'loading' => 'lazy' ) );
							} else {
								echo '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $title ) . '" class="hv-gallery-carousel__img" loading="lazy">';
							}
							?>
						</div>
						<div class="hv-gallery-carousel__info">
							<?php if ( $title ) : ?><h3 class="hv-gallery-carousel__title"><?php echo esc_html( $title ); ?></h3><?php endif; ?>
							<?php if ( $caption ) : ?><span class="hv-gallery-carousel__artist"><?php echo esc_html( $caption ); ?></span><?php endif; ?>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
        <div class="swiper-pagination"></div>
	</div>

	<div class="hv-container hv-text-center hv-mt-8">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'gallery_item' ) ); ?>" class="hv-btn hv-btn--outline-light"><?php echo esc_html( handandvision_is_hebrew() ? 'לגלריה המלאה' : 'Full Gallery' ); ?></a>
	</div>
</section>
