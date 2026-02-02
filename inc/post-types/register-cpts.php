<?php
/**
 * Register Custom Post Types for Hand and Vision
 * Labels in Hebrew, slugs in English
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Artist Post Type - אמנים
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_register_artist_post_type() {
    register_post_type( 'artist', [
        'labels' => [
            'name'                  => 'אמנים',
            'singular_name'         => 'אמן',
            'add_new'               => 'הוסף אמן חדש',
            'add_new_item'          => 'הוסף אמן חדש',
            'edit_item'             => 'ערוך אמן',
            'new_item'              => 'אמן חדש',
            'view_item'             => 'צפה באמן',
            'view_items'            => 'צפה באמנים',
            'search_items'          => 'חפש אמנים',
            'not_found'             => 'לא נמצאו אמנים',
            'not_found_in_trash'    => 'לא נמצאו אמנים בפח',
            'all_items'             => 'כל האמנים',
            'archives'              => 'ארכיון אמנים',
            'attributes'            => 'מאפייני אמן',
            'insert_into_item'      => 'הכנס לאמן',
            'uploaded_to_this_item' => 'הועלה לאמן זה',
            'featured_image'        => 'תמונת אמן',
            'set_featured_image'    => 'הגדר תמונת אמן',
            'remove_featured_image' => 'הסר תמונת אמן',
            'use_featured_image'    => 'השתמש כתמונת אמן',
            'menu_name'             => 'אמנים',
            'filter_items_list'     => 'סנן רשימת אמנים',
            'items_list_navigation' => 'ניווט רשימת אמנים',
            'items_list'            => 'רשימת אמנים',
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => [ 'slug' => 'artists' ],
        'supports'           => [ 'title', 'thumbnail', 'page-attributes' ],
        'menu_icon'          => 'dashicons-groups',
        'show_in_rest'       => true,
    ] );
}

/**
 * Register Service Post Type - שירותים
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_register_service_post_type() {
    register_post_type( 'service', [
        'labels' => [
            'name'                  => 'שירותים',
            'singular_name'         => 'שירות',
            'add_new'               => 'הוסף שירות חדש',
            'add_new_item'          => 'הוסף שירות חדש',
            'edit_item'             => 'ערוך שירות',
            'new_item'              => 'שירות חדש',
            'view_item'             => 'צפה בשירות',
            'view_items'            => 'צפה בשירותים',
            'search_items'          => 'חפש שירותים',
            'not_found'             => 'לא נמצאו שירותים',
            'not_found_in_trash'    => 'לא נמצאו שירותים בפח',
            'all_items'             => 'כל השירותים',
            'archives'              => 'ארכיון שירותים',
            'attributes'            => 'מאפייני שירות',
            'insert_into_item'      => 'הכנס לשירות',
            'uploaded_to_this_item' => 'הועלה לשירות זה',
            'featured_image'        => 'תמונת שירות',
            'set_featured_image'    => 'הגדר תמונת שירות',
            'remove_featured_image' => 'הסר תמונת שירות',
            'use_featured_image'    => 'השתמש כתמונת שירות',
            'menu_name'             => 'שירותים',
            'filter_items_list'     => 'סנן רשימת שירותים',
            'items_list_navigation' => 'ניווט רשימת שירותים',
            'items_list'            => 'רשימת שירותים',
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => [ 'slug' => 'services' ],
        'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ],
        'menu_icon'          => 'dashicons-art',
        'show_in_rest'       => true,
    ] );
}

/**
 * Register Gallery Item Post Type - גלריה
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_register_gallery_item_post_type() {
    register_post_type( 'gallery_item', [
        'labels' => [
            'name'                  => 'גלריה',
            'singular_name'         => 'פריט גלריה',
            'add_new'               => 'הוסף פריט חדש',
            'add_new_item'          => 'הוסף פריט גלריה חדש',
            'edit_item'             => 'ערוך פריט גלריה',
            'new_item'              => 'פריט גלריה חדש',
            'view_item'             => 'צפה בפריט',
            'view_items'            => 'צפה בגלריה',
            'search_items'          => 'חפש בגלריה',
            'not_found'             => 'לא נמצאו פריטים',
            'not_found_in_trash'    => 'לא נמצאו פריטים בפח',
            'all_items'             => 'כל הפריטים',
            'archives'              => 'ארכיון הגלריה',
            'attributes'            => 'מאפייני פריט',
            'insert_into_item'      => 'הכנס לפריט',
            'uploaded_to_this_item' => 'הועלה לפריט זה',
            'featured_image'        => 'תמונת פריט',
            'set_featured_image'    => 'הגדר תמונת פריט',
            'remove_featured_image' => 'הסר תמונת פריט',
            'use_featured_image'    => 'השתמש כתמונת פריט',
            'menu_name'             => 'גלריה',
            'filter_items_list'     => 'סנן רשימת גלריה',
            'items_list_navigation' => 'ניווט רשימת גלריה',
            'items_list'            => 'רשימת גלריה',
        ],
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => [ 'slug' => 'gallery' ],
        'supports'           => [ 'title', 'thumbnail', 'page-attributes' ],
        'menu_icon'          => 'dashicons-format-gallery',
        'show_in_rest'       => true,
    ] );
}

/**
 * Register all custom post types on init
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_register_post_types() {
    handandvision_register_artist_post_type();
    handandvision_register_service_post_type();
    handandvision_register_gallery_item_post_type();
}
add_action( 'init', 'handandvision_register_post_types' );
