<?php
/**
 * Image Optimization
 * Preload critical images, lazy loading, and WebP support
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Preload LCP (Largest Contentful Paint) images for better performance
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_preload_critical_images() {
    // Homepage hero image
    if ( is_front_page() ) {
        $hero_poster = get_field( 'hero_poster', get_option( 'page_on_front' ) );
        if ( $hero_poster ) {
            $poster_url = is_array( $hero_poster ) ? $hero_poster['url'] : $hero_poster;
            if ( $poster_url ) {
                echo '<link rel="preload" as="image" href="' . esc_url( $poster_url ) . '" fetchpriority="high">';
            }
        }
    }

    // Archive pages - preload first featured image
    if ( is_archive() || is_post_type_archive() ) {
        global $wp_query;
        if ( $wp_query->have_posts() ) {
            $first_post = $wp_query->posts[0] ?? null;
            if ( $first_post && has_post_thumbnail( $first_post->ID ) ) {
                $thumbnail_url = get_the_post_thumbnail_url( $first_post->ID, 'large' );
                if ( $thumbnail_url ) {
                    echo '<link rel="preload" as="image" href="' . esc_url( $thumbnail_url ) . '" fetchpriority="high">';
                }
            }
        }
    }

    // Single product page - preload main product image
    if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
        $product = wc_get_product( get_the_ID() );
        if ( $product ) {
            $image_id = $product->get_image_id();
            if ( $image_id ) {
                $image_url = wp_get_attachment_image_url( $image_id, 'large' );
                if ( $image_url ) {
                    echo '<link rel="preload" as="image" href="' . esc_url( $image_url ) . '" fetchpriority="high">';
                }
            }
        }
    }

    // Single artist page - preload featured image
    if ( is_singular( 'artist' ) && has_post_thumbnail() ) {
        $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        if ( $thumbnail_url ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $thumbnail_url ) . '" fetchpriority="high">';
        }
    }
}
add_action( 'wp_head', 'handandvision_preload_critical_images', 1 );

/**
 * Add fetchpriority="high" to featured images above the fold
 *
 * @since 3.3.0
 * @param array  $attr       Image attributes
 * @param object $attachment Attachment post object
 * @param string $size       Image size
 * @return array Modified attributes
 */
function handandvision_add_fetchpriority_to_images( $attr, $attachment, $size ) {
    // Add fetchpriority to hero images and first featured image
    if ( is_front_page() || ( is_archive() && in_the_loop() && 0 === get_query_var( 'paged' ) ) ) {
        global $wp_query;
        $current_index = $wp_query->current_post ?? 0;
        if ( $current_index === 0 ) {
            $attr['fetchpriority'] = 'high';
        }
    }

    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'handandvision_add_fetchpriority_to_images', 10, 3 );

/**
 * Add lazy loading to images below the fold
 *
 * @since 3.3.0
 * @param array  $attr       Image attributes
 * @param object $attachment Attachment post object
 * @return array Modified attributes
 */
function handandvision_add_lazy_loading( $attr, $attachment ) {
    // Skip first image on archives (above the fold)
    if ( is_archive() && in_the_loop() ) {
        global $wp_query;
        $current_index = $wp_query->current_post ?? 0;
        if ( $current_index === 0 ) {
            return $attr;
        }
    }

    // Add lazy loading to all other images
    $attr['loading'] = 'lazy';
    $attr['decoding'] = 'async';

    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'handandvision_add_lazy_loading', 10, 2 );

/**
 * Add WebP support with fallback using srcset
 * WordPress 5.8+ has built-in WebP support, but this ensures proper srcset generation
 *
 * @since 3.3.0
 * @param array $sources Array of image sources
 * @return array Modified sources
 */
function handandvision_add_webp_support( $sources ) {
    if ( ! is_array( $sources ) ) {
        return $sources;
    }

    foreach ( $sources as $width => $source ) {
        // Check if WebP version exists
        $webp_url = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $source['url'] );
        $webp_path = str_replace( wp_get_upload_dir()['baseurl'], wp_get_upload_dir()['basedir'], $webp_url );

        if ( file_exists( $webp_path ) ) {
            $sources[ $width ]['url'] = $webp_url;
        }
    }

    return $sources;
}
// Note: This filter is optional if you have a WebP converter plugin
// add_filter( 'wp_calculate_image_srcset', 'handandvision_add_webp_support' );

/**
 * Enable responsive images with proper srcset for custom image sizes
 *
 * @since 3.3.0
 * @param string $html  Image HTML
 * @param int    $post_id Post ID
 * @param int    $attachment_id Attachment ID
 * @return string Modified HTML
 */
function handandvision_responsive_images( $html, $post_id, $attachment_id ) {
    // Ensure srcset is added to all images
    if ( strpos( $html, 'srcset' ) === false ) {
        $srcset = wp_get_attachment_image_srcset( $attachment_id, 'large' );
        $sizes = wp_get_attachment_image_sizes( $attachment_id, 'large' );

        if ( $srcset && $sizes ) {
            $html = preg_replace( '/<img/', '<img srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $sizes ) . '"', $html );
        }
    }

    return $html;
}
add_filter( 'post_thumbnail_html', 'handandvision_responsive_images', 10, 3 );

/**
 * Set default image quality for better performance
 *
 * @since 3.3.0
 * @param int    $quality Image quality (1-100)
 * @param string $mime_type Image MIME type
 * @return int Modified quality
 */
function handandvision_image_quality( $quality, $mime_type ) {
    // Use 85% quality for JPEGs (good balance of quality/file size)
    if ( 'image/jpeg' === $mime_type ) {
        return 85;
    }

    // Use 90% quality for WebP
    if ( 'image/webp' === $mime_type ) {
        return 90;
    }

    return $quality;
}
add_filter( 'wp_editor_set_quality', 'handandvision_image_quality', 10, 2 );
