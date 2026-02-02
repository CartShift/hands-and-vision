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
 * Get container class for header.php
 * Allows full-width sections on pages and archives by replacing .ast-container
 *
 * @since 3.3.0
 * @return string Container class
 */
function handandvision_get_container_class() {
    // Pages, Archives, and Custom Post Types should be full width
    // The internal content will handle its own containment (e.g. .hv-container)
    if ( is_page() || is_archive() || is_home() || is_singular( [ 'artist', 'service', 'gallery_item', 'product' ] ) ) {
        return 'hv-full-width-wrapper';
    }

    // Default fallback (e.g. single blog posts if standard)
    return 'ast-container';
}
