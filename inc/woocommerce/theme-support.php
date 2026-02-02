<?php
/**
 * WooCommerce Theme Support Configuration
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Declare WooCommerce support with modern features
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'handandvision_woocommerce_support' );

/**
 * Customize WooCommerce product columns
 *
 * @since 3.3.0
 * @return int Number of columns
 */
function handandvision_woocommerce_product_columns() {
    return 4; // 4 columns for desktop
}
add_filter( 'loop_shop_columns', 'handandvision_woocommerce_product_columns' );

/**
 * Change number of products per page
 *
 * @since 3.3.0
 * @return int Number of products
 */
function handandvision_products_per_page() {
    return 12;
}
add_filter( 'loop_shop_per_page', 'handandvision_products_per_page', 20 );

/**
 * Remove default WooCommerce related products (we have custom implementation)
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_remove_default_related_products() {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}
add_action( 'init', 'handandvision_remove_default_related_products' );

/**
 * Remove default WooCommerce product tabs (we have custom implementation)
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_remove_default_product_tabs() {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
}
add_action( 'init', 'handandvision_remove_default_product_tabs' );

/**
 * Disable default WooCommerce styles (we have custom styling)
 *
 * @since 3.3.0
 * @return array Empty array
 */
function handandvision_disable_woocommerce_default_styles() {
    return [];
}
add_filter( 'woocommerce_enqueue_styles', 'handandvision_disable_woocommerce_default_styles' );

/**
 * Customize Add to Cart button text (RTL-aware)
 *
 * @since 3.3.0
 * @return string Translated button text
 */
function handandvision_custom_add_to_cart_text() {
    $is_hebrew = handandvision_is_hebrew();
    return $is_hebrew ? 'הוסף לעגלה' : 'Add to Cart';
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'handandvision_custom_add_to_cart_text' );
add_filter( 'woocommerce_product_add_to_cart_text', 'handandvision_custom_add_to_cart_text' );
