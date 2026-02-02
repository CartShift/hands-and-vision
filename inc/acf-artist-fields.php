<?php
/**
 * ACF Field Group Configuration for Artists
 * 
 * This file registers ACF fields for the Artist post type.
 * Labels and instructions in Hebrew.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Field Groups for Artists
 */
function handandvision_register_acf_artist_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_artist_fields',
        'title' => 'פרטי האמן',
        'fields' => array(
            // Portrait Tab - לשונית דיוקן
            array(
                'key' => 'field_artist_portrait_tab',
                'label' => 'דיוקן וביוגרפיה',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_artist_portrait',
                'label' => 'תמונת דיוקן',
                'name' => 'artist_portrait',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'תמונת דיוקן מרובעת. מומלץ: 600x600 פיקסלים או יותר.',
            ),
            array(
                'key' => 'field_artist_biography',
                'label' => 'ביוגרפיה',
                'name' => 'artist_biography',
                'type' => 'wysiwyg',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'ביוגרפיה ורקע על האמן.',
            ),
            
            // Social Links Tab - לשונית קישורים חברתיים
            array(
                'key' => 'field_artist_social_tab',
                'label' => 'קישורים לרשתות חברתיות',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_artist_instagram',
                'label' => 'קישור לאינסטגרם',
                'name' => 'artist_instagram',
                'type' => 'url',
                'placeholder' => 'https://instagram.com/username',
            ),
            array(
                'key' => 'field_artist_facebook',
                'label' => 'קישור לפייסבוק',
                'name' => 'artist_facebook',
                'type' => 'url',
                'placeholder' => 'https://facebook.com/username',
            ),
            array(
                'key' => 'field_artist_website',
                'label' => 'אתר אישי',
                'name' => 'artist_website',
                'type' => 'url',
                'placeholder' => 'https://example.com',
            ),
            
            // Gallery Tab - לשונית גלריה
            array(
                'key' => 'field_artist_gallery_tab',
                'label' => 'גלריית האמן',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_artist_gallery',
                'label' => 'תמונות גלריה',
                'name' => 'artist_gallery',
                'type' => 'gallery',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'insert' => 'append',
                'instructions' => 'תמונות גלריה לא מסחריות של עבודות האמן. גרור כדי לסדר מחדש.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'artist',
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
add_action( 'acf/init', 'handandvision_register_acf_artist_fields' );
