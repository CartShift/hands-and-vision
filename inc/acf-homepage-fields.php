<?php
/**
 * ACF Field Group Configuration for Homepage Settings
 * 
 * This file registers ACF fields for the Homepage Options page.
 * Labels and instructions in Hebrew.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Field Groups for Homepage
 */
function handandvision_register_acf_homepage_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_homepage_settings',
        'title' => 'הגדרות דף הבית',
        'fields' => array(
            // Hero Section Tab - לשונית באנר ראשי
            array(
                'key' => 'field_hero_tab',
                'label' => 'באנר ראשי',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_hero_title',
                'label' => 'כותרת',
                'name' => 'hero_title',
                'type' => 'text',
                'default_value' => 'HANDS AND VISION',
                'instructions' => 'כותרת ראשית באנגלית (לוגו/שם המותג).',
            ),
            array(
                'key' => 'field_hero_subtitle',
                'label' => 'תת-כותרת',
                'name' => 'hero_subtitle',
                'type' => 'text',
                'default_value' => 'קולקטיב אמנות',
                'instructions' => 'תת-כותרת שמופיעה מתחת לכותרת הראשית.',
            ),
            array(
                'key' => 'field_hero_image',
                'label' => 'תמונת באנר',
                'name' => 'hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'תמונת רקע לבאנר. מומלץ: 1920x1080 פיקסלים או יותר.',
            ),
            array(
                'key' => 'field_hero_video',
                'label' => 'סרטון באנר (אופציונלי)',
                'name' => 'hero_video',
                'type' => 'file',
                'return_format' => 'array',
                'mime_types' => 'mp4,webm',
                'instructions' => 'סרטון רקע. הסרטון יחליף את התמונה אם יועלה.',
            ),
            
            // Introduction Tab - לשונית מבוא
            array(
                'key' => 'field_intro_tab',
                'label' => 'מבוא',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_intro_text',
                'label' => 'טקסט מבוא',
                'name' => 'intro_text',
                'type' => 'wysiwyg',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'טקסט מבוא קצר המתאר את החברה/הקולקטיב.',
            ),
            
            // Featured Services Tab - לשונית שירותים מובילים
            array(
                'key' => 'field_services_tab',
                'label' => 'שירותים מובילים',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_featured_services',
                'label' => 'שירותים נבחרים',
                'name' => 'featured_services',
                'type' => 'relationship',
                'post_type' => array( 'service' ),
                'filters' => array( 'search' ),
                'min' => 0,
                'max' => 4,
                'return_format' => 'object',
                'instructions' => 'בחר עד 4 שירותים להצגה בדף הבית. גרור כדי לסדר מחדש.',
            ),
            
            // Featured Artists Tab - לשונית אמנים מובילים
            array(
                'key' => 'field_artists_tab',
                'label' => 'אמנים מובילים',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_featured_artists',
                'label' => 'אמנים נבחרים',
                'name' => 'featured_artists',
                'type' => 'relationship',
                'post_type' => array( 'artist' ),
                'filters' => array( 'search' ),
                'min' => 0,
                'max' => 4,
                'return_format' => 'object',
                'instructions' => 'בחר עד 4 אמנים להצגה בדף הבית. גרור כדי לסדר מחדש.',
            ),
            
            // Featured Collection Tab - לשונית קולקציה נבחרת
            array(
                'key' => 'field_collection_tab',
                'label' => 'קולקציה נבחרת',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_featured_products',
                'label' => 'מוצרים נבחרים',
                'name' => 'featured_products',
                'type' => 'relationship',
                'post_type' => array( 'product' ),
                'filters' => array( 'search' ),
                'min' => 0,
                'max' => 6,
                'return_format' => 'object',
                'instructions' => 'בחר עד 6 מוצרים להצגה בדף הבית. גרור כדי לסדר מחדש.',
            ),
            
            // Gallery Preview Tab - לשונית תצוגה מקדימה של גלריה
            array(
                'key' => 'field_gallery_tab',
                'label' => 'תצוגה מקדימה של גלריה',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_gallery_images',
                'label' => 'תמונות גלריה',
                'name' => 'gallery_images',
                'type' => 'gallery',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'min' => 0,
                'max' => 8,
                'instructions' => 'בחר עד 8 תמונות להצגה כתצוגה מקדימה של הגלריה.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_type',
                    'operator' => '==',
                    'value' => 'front_page',
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
add_action( 'acf/init', 'handandvision_register_acf_homepage_fields' );
