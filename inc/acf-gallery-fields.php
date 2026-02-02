<?php
/**
 * ACF Field Group Configuration for Gallery Items
 * 
 * This file registers ACF fields for the Gallery Item post type.
 * Labels and instructions in Hebrew.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Field Groups for Gallery Items
 */
function handandvision_register_acf_gallery_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_gallery_item_fields',
        'title' => 'פרטי פריט הגלריה',
        'fields' => array(
            array(
                'key' => 'field_gallery_caption',
                'label' => 'כיתוב',
                'name' => 'gallery_caption',
                'type' => 'text',
                'instructions' => 'כיתוב אופציונלי לפריט גלריה זה (אמן / פרויקט / שנה).',
                'placeholder' => 'לדוגמה: שם האמן, 2024',
            ),
            array(
                'key' => 'field_gallery_artist',
                'label' => 'אמן קשור',
                'name' => 'gallery_artist',
                'type' => 'post_object',
                'post_type' => array( 'artist' ),
                'return_format' => 'object',
                'allow_null' => 1,
                'instructions' => 'ניתן לקשר פריט גלריה זה לאמן באופן אופציונלי.',
            ),
            array(
                'key' => 'field_gallery_year',
                'label' => 'שנה',
                'name' => 'gallery_year',
                'type' => 'number',
                'placeholder' => '2024',
                'instructions' => 'שנת הצילום או היצירה.',
            ),
            array(
                'key' => 'field_gallery_project',
                'label' => 'שם הפרויקט',
                'name' => 'gallery_project',
                'type' => 'text',
                'placeholder' => 'שם הפרויקט או האירוע',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'gallery_item',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ) );
}
add_action( 'acf/init', 'handandvision_register_acf_gallery_fields' );
