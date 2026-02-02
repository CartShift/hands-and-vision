<?php
/**
 * Product display helpers (e.g. title with English override)
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get product title for display; uses English title when language is EN and ACF field is set.
 *
 * @param int|WC_Product $product_id_or_product Product ID or product object.
 * @return string
 */
function handandvision_product_title( $product_id_or_product ) {
	$id = is_object( $product_id_or_product ) ? $product_id_or_product->get_id() : (int) $product_id_or_product;
	if ( ! $id ) {
		return '';
	}
	$is_hebrew = function_exists( 'handandvision_is_hebrew' ) ? handandvision_is_hebrew() : true;
	if ( ! $is_hebrew && function_exists( 'get_field' ) ) {
		$en = get_field( 'product_title_en', $id );
		if ( ! empty( $en ) ) {
			return $en;
		}
	}
	return get_the_title( $id );
}
