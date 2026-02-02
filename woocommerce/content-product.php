<?php
/**
 * The template for displaying product content within loops
 *
 * This template overrides the default WooCommerce content-product.php
 * to implement our custom Hand & Vision card design globally.
 *
 * @package HandAndVision
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$is_hebrew = handandvision_is_hebrew();

// Get artist info
$artist_id = get_post_meta( $product->get_id(), '_handandvision_artist_id', true );
$artist_name = $artist_id ? get_the_title( $artist_id ) : '';
?>

<li <?php wc_product_class( 'hv-product-card', $product ); ?>>
	<div class="hv-product-card__inner">
		<a href="<?php echo esc_url( get_permalink() ); ?>" class="hv-product-card__link">
			<div class="hv-product-card__image">
				<?php
				if ( has_post_thumbnail() ) {
					// Use large size for crisp quality on all screens
					the_post_thumbnail( 'woocommerce_thumbnail' );
				} else {
					echo '<div class="hv-product-card__placeholder"></div>';
				}
				?>

				<!-- Badges -->
				<?php if ( $product->is_on_sale() ) : ?>
					<span class="hv-product-card__badge hv-product-card__badge--sale">
						<?php echo esc_html( $is_hebrew ? 'מבצע' : 'Sale' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( ! $product->is_in_stock() ) : ?>
					<span class="hv-product-card__badge hv-product-card__badge--sold">
						<?php echo esc_html( $is_hebrew ? 'נמכר' : 'Sold' ); ?>
					</span>
				<?php endif; ?>

				<!-- Quick View Trigger (Visual Only) -->
				<span class="hv-product-card__quick-view">
					<?php echo esc_html( $is_hebrew ? 'צפייה מהירה' : 'Quick View' ); ?>
				</span>
			</div>
		</a>

		<div class="hv-product-card__content">
			<a href="<?php echo esc_url( get_permalink() ); ?>" class="hv-product-card__content-link">
				<?php if ( $artist_name ) : ?>
					<span class="hv-product-card__artist"><?php echo esc_html( $artist_name ); ?></span>
				<?php endif; ?>

				<h3 class="hv-product-card__title"><?php echo esc_html( get_the_title() ); ?></h3>

				<div class="hv-product-card__price">
					<?php echo wp_kses_post( $product->get_price_html() ); ?>
				</div>
			</a>

			<!-- Actions visible on mobile/hover -->
			<div class="hv-product-card__actions">
				<?php
				// Custom Add to Cart Button
				$args = array(); // Default args
				echo apply_filters(
					'woocommerce_loop_add_to_cart_link',
					sprintf(
						'<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $args['class'] ) ? $args['class'] : 'hv-btn hv-btn--small hv-btn--outline add_to_cart_button ajax_add_to_cart' ),
						isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : 'data-product_id="' . esc_attr( $product->get_id() ) . '" aria-label="' . esc_attr( $product->add_to_cart_description() ) . '" rel="nofollow"',
						esc_html( $product->add_to_cart_text() )
					),
					$product,
					$args
				);
				?>
			</div>
		</div>
	</div>
</li>
