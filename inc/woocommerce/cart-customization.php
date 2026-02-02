<?php
/**
 * WooCommerce Cart Customizations
 * Custom styling, empty messages, and cart updates
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add artist name to product title in cart/checkout
 *
 * @since 3.3.0
 * @param string $product_name Product name HTML
 * @param array  $cart_item    Cart item data
 * @param string $cart_item_key Cart item key
 * @return string Modified product name
 */
function handandvision_add_artist_to_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
    $product_id = $cart_item['product_id'];
    $artist_id = get_post_meta( $product_id, '_handandvision_artist_id', true );

    if ( $artist_id ) {
        $artist_name = get_the_title( $artist_id );
        $is_hebrew = handandvision_is_hebrew();
        $by_text = $is_hebrew ? 'מאת' : 'by';
        $product_name = $product_name . ' <span style="font-size:0.9em;color:#888;font-weight:normal;">(' . esc_html( $by_text ) . ' ' . esc_html( $artist_name ) . ')</span>';
    }

    return $product_name;
}
add_filter( 'woocommerce_cart_item_name', 'handandvision_add_artist_to_cart_item_name', 10, 3 );

/**
 * Display artist name on order confirmation
 *
 * @since 3.3.0
 * @param string         $item_name Order item name
 * @param WC_Order_Item $item      Order item object
 * @return string Modified item name
 */
function handandvision_add_artist_to_order_item_name( $item_name, $item ) {
    $product_id = $item->get_product_id();
    $artist_id = get_post_meta( $product_id, '_handandvision_artist_id', true );

    if ( $artist_id ) {
        $artist_name = get_the_title( $artist_id );
        $is_hebrew = handandvision_is_hebrew();
        $by_text = $is_hebrew ? 'מאת' : 'by';
        $item_name = $item_name . ' <span style="font-size:0.9em;color:#888;">(' . esc_html( $by_text ) . ' ' . esc_html( $artist_name ) . ')</span>';
    }

    return $item_name;
}
add_filter( 'woocommerce_order_item_name', 'handandvision_add_artist_to_order_item_name', 10, 2 );

function handandvision_header_cart_markup( $cart_count ) {
    $aria_label = handandvision_is_hebrew() ? 'עגלת קניות' : 'Shopping Cart';
    ?>
    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="hv-header__cart" id="hv-header-cart" aria-label="<?php echo esc_attr( $aria_label ); ?>">
        <svg class="hv-cart-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span class="hv-cart-count has-items" id="hv-cart-count"><?php echo esc_html( $cart_count ); ?></span>
    </a>
    <?php
}

function handandvision_cart_count_fragments( $fragments ) {
    $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ob_start();
    if ( $cart_count > 0 ) {
        handandvision_header_cart_markup( $cart_count );
    }
    $fragments['#hv-header-cart-wrap'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'handandvision_cart_count_fragments' );

/**
 * Add custom classes to cart page body
 *
 * @since 3.3.0
 * @param array $classes Existing body classes
 * @return array Modified body classes
 */
function handandvision_cart_page_body_class( $classes ) {
    if ( is_cart() ) {
        $classes[] = 'hv-cart-page-wrapper';

        if ( handandvision_is_hebrew() ) {
            $classes[] = 'rtl';
        }

        // Add empty cart class
        if ( WC()->cart && WC()->cart->is_empty() ) {
            $classes[] = 'hv-cart-empty';
        }
    }

    return $classes;
}
add_filter( 'body_class', 'handandvision_cart_page_body_class' );

/**
 * Disable page title on cart page (we use custom layout)
 *
 * @since 3.3.0
 * @param bool $enabled Whether title is enabled
 * @return bool
 */
function handandvision_disable_cart_page_title( $enabled ) {
    return is_cart() ? false : $enabled;
}
add_filter( 'astra_the_title_enabled', 'handandvision_disable_cart_page_title' );

/**
 * Customize cart empty message
 *
 * @since 3.3.0
 * @return string Empty cart message
 */
function handandvision_empty_cart_message() {
    $is_hebrew = handandvision_is_hebrew();
    $message = $is_hebrew ? 'סל הקניות שלך ריק כרגע' : 'Your cart is currently empty.';

    return '<p class="cart-empty woocommerce-info">' . esc_html( $message ) . '</p>';
}
add_filter( 'wc_empty_cart_message', 'handandvision_empty_cart_message' );

/**
 * Customize "Return to Shop" button text
 *
 * @since 3.3.0
 * @return string Translated button text
 */
function handandvision_return_to_shop_text() {
    $is_hebrew = handandvision_is_hebrew();
    return $is_hebrew ? 'חזרה לחנות' : 'Return to Shop';
}
add_filter( 'woocommerce_return_to_shop_text', 'handandvision_return_to_shop_text' );
