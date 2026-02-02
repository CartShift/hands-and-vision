<?php
/**
 * ACF Field Group Configuration for Services
 * 
 * This file registers ACF fields for the Service post type.
 * Labels and instructions in Hebrew.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Field Groups for Services
 */
function handandvision_register_acf_service_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_service_fields',
        'title' => 'פרטי השירות',
        'fields' => array(
            // Translation Tab - לשונית תרגום
            array(
                'key' => 'field_service_translation_tab',
                'label' => 'תרגום לאנגלית',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_service_title_en',
                'label' => 'כותרת באנגלית (English Title)',
                'name' => 'service_title_en',
                'type' => 'text',
                'instructions' => 'כותרת השירות כפי שתופיע בגרסה האנגלית של האתר.',
            ),
            array(
                'key' => 'field_service_desc_en',
                'label' => 'תיאור קצר באנגלית (English Description)',
                'name' => 'service_desc_en',
                'type' => 'textarea',
                'rows' => 3,
                'instructions' => 'תיאור קצר כפי שיחליף את התיאור הרגיל בגרסה האנגלית.',
            ),

            // Hero Tab - לשונית באנר ראשי
            array(
                'key' => 'field_service_hero_tab',
                'label' => 'באנר ראשי',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_service_hero_image',
                'label' => 'תמונת באנר',
                'name' => 'service_hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'תמונת באנר גדולה. מומלץ: 1920x800 פיקסלים או יותר.',
            ),
            array(
                'key' => 'field_service_hero_video',
                'label' => 'סרטון באנר (אופציונלי)',
                'name' => 'service_hero_video',
                'type' => 'file',
                'return_format' => 'array',
                'mime_types' => 'mp4,webm',
                'instructions' => 'העלה סרטון MP4 או WebM. הסרטון יחליף את התמונה.',
            ),
            array(
                'key' => 'field_service_short_desc',
                'label' => 'תיאור קצר',
                'name' => 'service_short_description',
                'type' => 'textarea',
                'rows' => 3,
                'instructions' => 'תיאור קצר שמוצג בסקשן הבאנר.',
            ),
            
            // Content Tab - לשונית תוכן
            array(
                'key' => 'field_service_content_tab',
                'label' => 'תוכן השירות',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_service_full_desc',
                'label' => 'תיאור מלא',
                'name' => 'service_full_description',
                'type' => 'wysiwyg',
                'toolbar' => 'full',
                'media_upload' => 0,
                'instructions' => 'תיאור מפורט של השירות.',
            ),
            array(
                'key' => 'field_service_what_we_do',
                'label' => 'מה אנחנו עושים',
                'name' => 'service_what_we_do',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'הוסף פריט',
                'instructions' => 'נקודות תבליט המתארות מה כולל השירות.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_service_bullet_point',
                        'label' => 'נקודה',
                        'name' => 'point',
                        'type' => 'text',
                    ),
                ),
            ),
            
            // Gallery Tab - לשונית גלריה
            array(
                'key' => 'field_service_gallery_tab',
                'label' => 'גלריית השירות',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_service_gallery',
                'label' => 'תמונות גלריה',
                'name' => 'service_gallery',
                'type' => 'gallery',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'insert' => 'append',
                'instructions' => 'תמונות אוצרות המציגות את השירות. גרור כדי לסדר מחדש.',
            ),
            
            // Related Tab - לשונית תוכן קשור
            array(
                'key' => 'field_service_related_tab',
                'label' => 'תוכן קשור',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_service_related_artists',
                'label' => 'אמנים קשורים',
                'name' => 'service_related_artists',
                'type' => 'relationship',
                'post_type' => array( 'artist' ),
                'filters' => array( 'search' ),
                'elements' => array( 'featured_image' ),
                'min' => 0,
                'max' => 6,
                'return_format' => 'object',
                'instructions' => 'בחר אמנים הקשורים לשירות זה. גרור כדי לסדר מחדש.',
            ),
            
            // CTA Tab - לשונית קריאה לפעולה
            array(
                'key' => 'field_service_cta_tab',
                'label' => 'קריאה לפעולה',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_service_cta_text',
                'label' => 'טקסט כפתור CTA',
                'name' => 'service_cta_text',
                'type' => 'text',
                'default_value' => 'צרו קשר',
                'placeholder' => 'צרו קשר',
            ),
            array(
                'key' => 'field_service_cta_link',
                'label' => 'קישור CTA',
                'name' => 'service_cta_link',
                'type' => 'page_link',
                'post_type' => array( 'page' ),
                'allow_null' => 1,
                'instructions' => 'השאר ריק כדי לקשר אוטומטית לדף צור קשר.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'service',
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
add_action( 'acf/init', 'handandvision_register_acf_service_fields' );
