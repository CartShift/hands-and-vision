<?php
/**
 * Polylang integration — CPT registration, locale/RTL sync, Yoast cooperation.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Polylang is active and initialized.
 */
function handandvision_polylang_active(): bool {
	return function_exists( 'pll_current_language' );
}

/**
 * Register theme CPTs and products for Polylang translation.
 */
function handandvision_pll_post_types( array $post_types, bool $is_settings ): array {
	$theme_types = array(
		'artist'       => 'artist',
		'service'      => 'service',
		'gallery_item' => 'gallery_item',
		'product'      => 'product',
		'page'         => 'page',
	);

	if ( $is_settings ) {
		return array_merge( $post_types, $theme_types );
	}

	return array_merge( $post_types, $theme_types );
}
add_filter( 'pll_get_post_types', 'handandvision_pll_post_types', 10, 2 );

/**
 * Register product taxonomies for translation when WooCommerce is active.
 */
function handandvision_pll_taxonomies( array $taxonomies, bool $is_settings ): array {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $taxonomies;
	}

	$wc_taxonomies = array(
		'product_cat'        => 'product_cat',
		'product_tag'        => 'product_tag',
		'product_shipping_class' => 'product_shipping_class',
	);

	if ( $is_settings ) {
		return array_merge( $taxonomies, $wc_taxonomies );
	}

	return array_merge( $taxonomies, $wc_taxonomies );
}
add_filter( 'pll_get_taxonomies', 'handandvision_pll_taxonomies', 10, 2 );

/**
 * Align WordPress RTL with the active UI language on the frontend.
 * Fixes core -rtl.css loading when locale stays he_IL but user views EN.
 */
function handandvision_pll_filter_is_rtl( bool $is_rtl ): bool {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $is_rtl;
	}

	if ( handandvision_polylang_active() || isset( $_GET['lang'] ) || isset( $_COOKIE['hv_lang'] ) ) {
		return handandvision_is_hebrew();
	}

	return $is_rtl;
}
add_filter( 'is_rtl', 'handandvision_pll_filter_is_rtl' );

/**
 * Let Yoast defer hreflang to Polylang (avoid duplicate tags).
 */
function handandvision_pll_yoast_hreflang( bool $present ): bool {
	if ( handandvision_polylang_active() ) {
		return false;
	}
	return $present;
}
add_filter( 'wpseo_enable_hreflang', 'handandvision_pll_yoast_hreflang' );
