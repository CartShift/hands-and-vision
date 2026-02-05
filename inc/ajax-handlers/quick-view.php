<?php
/**
 * AJAX Quick View Handler
 * Fetches product details for a modal view
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Quick View Request
 */
function handandvision_ajax_quick_view() {
	// Verify nonce
	check_ajax_referer( 'hv_quick_view', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid product ID', 'astra' ) ) );
	}

	$product = wc_get_product( $product_id );

	if ( ! $product ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Product not found', 'astra' ) ) );
	}

	// Prepare data
	ob_start();

	// Image
	$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
	if ( ! $image_url ) {
		$image_url = wc_placeholder_img_src( 'large' );
	}

	// Artist Info
	$artist_id = get_post_meta( $product_id, '_handandvision_artist_id', true );
	$artist_name = $artist_id ? get_the_title( $artist_id ) : '';
	$artist_url = $artist_id ? get_permalink( $artist_id ) : '';

	// Render
	?>
	<div class="hv-quick-view-modal__content hv-grid-2-col">
		<div class="hv-quick-view-modal__image">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
		</div>
		<div class="hv-quick-view-modal__details">
			<?php if ( $artist_name ) : ?>
				<a href="<?php echo esc_url( $artist_url ); ?>" class="hv-quick-view-modal__artist"><?php echo esc_html( $artist_name ); ?></a>
			<?php endif; ?>

			<h2 class="hv-headline-3 hv-mb-4"><?php echo esc_html( $product->get_name() ); ?></h2>

			<div class="hv-quick-view-modal__price hv-mb-6">
				<?php echo $product->get_price_html(); ?>
			</div>

			<div class="hv-quick-view-modal__desc hv-text-body hv-mb-6">
				<?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); ?>
			</div>

			<?php if ( $product->is_in_stock() ) : ?>
				<div class="hv-quick-view-modal__actions">
					<?php
					// Simple Add to Cart
					if ( $product->is_type( 'simple' ) ) {
						echo '<a href="' . esc_url( $product->add_to_cart_url() ) . '" data-quantity="1" class="hv-btn hv-btn--primary button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr( $product->get_id() ) . '" aria-label="' . esc_attr( $product->add_to_cart_description() ) . '">' . esc_html( $product->add_to_cart_text() ) . '</a>';
					} else {
						echo '<a href="' . esc_url( get_permalink( $product_id ) ) . '" class="hv-btn hv-btn--primary">' . esc_html( handandvision_is_hebrew() ? 'צפה באפשרויות' : 'View Options' ) . '</a>';
					}
					?>
				</div>
			<?php else : ?>
				<p class="hv-text-error"><?php echo esc_html( handandvision_is_hebrew() ? 'אזל מהמלאי' : 'Out of Stock' ); ?></p>
			<?php endif; ?>

			<div class="hv-quick-view-modal__meta hv-mt-6">
				<span><?php echo esc_html( handandvision_is_hebrew() ? 'מק"ט:' : 'SKU:' ); ?> <?php echo ( $sku = $product->get_sku() ) ? $sku : 'N/A'; ?></span>
				<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
			</div>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_hv_quick_view', 'handandvision_ajax_quick_view' );
add_action( 'wp_ajax_nopriv_hv_quick_view', 'handandvision_ajax_quick_view' );
