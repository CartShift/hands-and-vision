<?php
/**
 * Theme Support Features
 * Logo, image sizes, and general theme setup
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom image sizes for Hand and Vision
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_image_sizes() {
    add_image_size( 'hv-hero', 1920, 800, true );
    add_image_size( 'hv-gallery', 600, 400, true );
    add_image_size( 'hv-artist', 400, 400, true );
    add_image_size( 'hv-product', 600, 800, true );
}
add_action( 'after_setup_theme', 'handandvision_image_sizes' );

/**
 * Enable Flexible Logo Support (Prevent forced cropping)
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_flexible_logo() {
    add_theme_support( 'custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => [ 'site-title', 'site-description' ],
    ] );
}
add_action( 'after_setup_theme', 'handandvision_flexible_logo', 20 ); // Priority 20 to override parent

/**
 * Get safe logo URL - uses uploads directory to avoid special character issues in theme path
 *
 * @since 3.3.0
 * @return string Logo URL or empty string
 */
function handandvision_get_logo_url() {
    // First check if custom logo is set in WordPress Customizer
    if ( has_custom_logo() ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
        if ( $logo_data && isset( $logo_data[0] ) ) {
            return $logo_data[0];
        }
    }

    // Try uploads directory for hv-logo.png
    $upload_dir = wp_upload_dir();
    $logo_in_uploads = $upload_dir['basedir'] . '/hv-logo.png';
    $logo_url_in_uploads = $upload_dir['baseurl'] . '/hv-logo.png';

    if ( file_exists( $logo_in_uploads ) ) {
        return $logo_url_in_uploads;
    }

    return '';
}

/**
 * Wordmark logo URL (text only, no symbol) for hero and prominent branding.
 *
 * @return string
 */
function handandvision_get_wordmark_logo_url() {
    $upload_dir = wp_upload_dir();
    $candidates = array(
        $upload_dir['basedir'] . '/hv-wordmark.png' => $upload_dir['baseurl'] . '/hv-wordmark.png',
        $upload_dir['basedir'] . '/hv-logo-wordmark.png' => $upload_dir['baseurl'] . '/hv-logo-wordmark.png',
    );

    foreach ( $candidates as $path => $url ) {
        if ( file_exists( $path ) ) {
            return $url;
        }
    }

    $theme_path = get_stylesheet_directory() . '/assets/images/hv-wordmark.svg';
    if ( file_exists( $theme_path ) ) {
        return get_stylesheet_directory_uri() . '/assets/images/hv-wordmark.svg';
    }

    return handandvision_get_logo_url();
}

/**
 * Get container class for header.php
 * Allows full-width sections on pages and archives by replacing .ast-container
 *
 * @since 3.3.0
 * @return string Container class
 */
function handandvision_get_container_class() {
    if ( is_page() || is_archive() || is_home() || is_singular( [ 'artist', 'service', 'gallery_item', 'product' ] ) ) {
        return 'hv-full-width-wrapper';
    }
    return 'ast-container';
}

function handandvision_hero_layout_body_class( $classes ) {
    if ( is_post_type_archive( 'service' ) || is_post_type_archive( 'artist' ) || is_post_type_archive( 'product' )
        || is_post_type_archive( 'gallery_item' ) || is_page_template( 'page-contact.php' ) ) {
        $classes[] = 'hv-hero-layout-page';
    }
    return $classes;
}
add_filter( 'body_class', 'handandvision_hero_layout_body_class' );

/**
 * Whether the current request is a WooCommerce storefront page.
 *
 * @return bool
 */
function handandvision_is_woocommerce_page() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	return ( function_exists( 'is_woocommerce' ) && is_woocommerce() )
		|| ( function_exists( 'is_shop' ) && is_shop() )
		|| ( function_exists( 'is_product_category' ) && is_product_category() )
		|| ( function_exists( 'is_product_tag' ) && is_product_tag() )
		|| ( function_exists( 'is_product' ) && is_product() )
		|| ( function_exists( 'is_cart' ) && is_cart() )
		|| ( function_exists( 'is_checkout' ) && is_checkout() )
		|| ( function_exists( 'is_account_page' ) && is_account_page() );
}

/**
 * Pages that render Swiper carousels.
 *
 * @return bool
 */
function handandvision_needs_swiper_assets() {
	return is_front_page()
		|| is_singular( array( 'artist', 'service' ) )
		|| is_post_type_archive( array( 'artist', 'service', 'gallery_item' ) );
}

/**
 * Pages that use scroll parallax (homepage hero, single CPT heroes).
 *
 * @return bool
 */
function handandvision_needs_parallax_assets() {
	return is_front_page() || is_singular( array( 'artist', 'service', 'product' ) );
}

