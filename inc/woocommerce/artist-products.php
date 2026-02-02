<?php
/**
 * WooCommerce Artist-Product Relationship
 * Links products to artist custom post type
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom product meta box for artist relationship
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_add_product_artist_metabox() {
    add_meta_box(
        'handandvision_product_artist',
        handandvision_is_hebrew() ? 'אמן/ית היצירה' : 'Artist',
        'handandvision_product_artist_callback',
        'product',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'handandvision_add_product_artist_metabox' );

/**
 * Display the artist selection dropdown in product edit screen
 *
 * @since 3.3.0
 * @param WP_Post $post Current post object
 * @return void
 */
function handandvision_product_artist_callback( $post ) {
    wp_nonce_field( 'handandvision_save_product_artist', 'handandvision_product_artist_nonce' );
    $selected_artist = get_post_meta( $post->ID, '_handandvision_artist_id', true );

    $artists = get_posts( [
        'post_type'   => 'artist',
        'numberposts' => 200,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ] );

    echo '<select name="handandvision_product_artist" id="handandvision_product_artist" style="width:100%;">';
    echo '<option value="">' . esc_html( handandvision_is_hebrew() ? '-- בחר אמן/ית --' : '-- Select Artist --' ) . '</option>';
    foreach ( $artists as $artist ) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr( $artist->ID ),
            selected( $selected_artist, $artist->ID, false ),
            esc_html( $artist->post_title )
        );
    }
    echo '</select>';
    echo '<p class="description" style="margin-top:10px;">' .
         esc_html( handandvision_is_hebrew() ? 'בחרו את האמן/ית שיצר/ה את היצירה' : 'Select the artist who created this piece' ) .
         '</p>';
}

/**
 * Save the artist relationship when product is saved
 *
 * @since 3.3.0
 * @param int $post_id Product post ID
 * @return void
 */
function handandvision_save_product_artist( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['handandvision_product_artist_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['handandvision_product_artist_nonce'], 'handandvision_save_product_artist' ) ) {
        return;
    }

    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $old_artist_id = get_post_meta( $post_id, '_handandvision_artist_id', true );
    if ( isset( $_POST['handandvision_product_artist'] ) ) {
        $artist_id = absint( $_POST['handandvision_product_artist'] );
        update_post_meta( $post_id, '_handandvision_artist_id', $artist_id ? $artist_id : '' );
        handandvision_invalidate_artist_products_cache( array_filter( [ $old_artist_id, $artist_id ] ) );
    } else {
        delete_post_meta( $post_id, '_handandvision_artist_id' );
        if ( $old_artist_id ) {
            handandvision_invalidate_artist_products_cache( [ $old_artist_id ] );
        }
    }
}
add_action( 'save_post_product', 'handandvision_save_product_artist' );

/**
 * Get products by artist ID (cached 1 hour, invalidated on product save)
 *
 * @since 3.3.0
 * @param int $artist_id Artist post ID
 * @return array Array of WP_Post objects
 */
function handandvision_get_artist_products( $artist_id ) {
    if ( ! $artist_id ) {
        return [];
    }
    $cache_key = 'hv_artist_products_' . $artist_id;
    $cached = get_transient( $cache_key );
    if ( false !== $cached && is_array( $cached ) ) {
        return $cached;
    }
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => '_handandvision_artist_id',
                'value'   => $artist_id,
                'compare' => '=',
            ],
        ],
        'post_status'    => 'publish',
    ];
    $products = get_posts( $args );
    set_transient( $cache_key, $products, HOUR_IN_SECONDS );
    return $products;
}

/**
 * Invalidate artist products cache for one or more artist IDs
 *
 * @since 3.3.0
 * @param array $artist_ids Array of artist IDs
 * @return void
 */
function handandvision_invalidate_artist_products_cache( $artist_ids ) {
    $artist_ids = array_filter( array_map( 'absint', (array) $artist_ids ) );
    foreach ( $artist_ids as $aid ) {
        delete_transient( 'hv_artist_products_' . $aid );
    }
}
