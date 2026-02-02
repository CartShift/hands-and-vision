<?php
/**
 * ACF Field Group Configuration for Contact Page
 * 
 * This file registers ACF fields for the Contact Page.
 * Labels and instructions in Hebrew.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Field Groups for Contact Page
 */
function handandvision_register_acf_contact_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_contact_settings',
        'title' => 'הגדרות דף צור קשר',
        'fields' => array(
            // Contact Info Tab - לשונית פרטי התקשרות
            array(
                'key' => 'field_contact_info_tab',
                'label' => 'פרטי התקשרות',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_contact_phone',
                'label' => 'מספר טלפון',
                'name' => 'contact_phone',
                'type' => 'text',
                'default_value' => '03-555-1234',
                'instructions' => 'מספר הטלפון הראשי ליצירת קשר.',
            ),
            array(
                'key' => 'field_contact_email',
                'label' => 'כתובת אימייל',
                'name' => 'contact_email',
                'type' => 'email',
                'default_value' => 'hello@handandvision.co.il',
                'instructions' => 'כתובת האימייל הראשית ליצירת קשר.',
            ),
            array(
                'key' => 'field_contact_address',
                'label' => 'כתובת',
                'name' => 'contact_address',
                'type' => 'text',
                'default_value' => 'רחוב דיזנגוף 123, תל אביב',
                'instructions' => 'כתובת פיזית של הגלריה/המשרד.',
            ),
            array(
                'key' => 'field_contact_whatsapp',
                'label' => 'מספר וואטסאפ (אופציונלי)',
                'name' => 'contact_whatsapp',
                'type' => 'text',
                'instructions' => 'מספר טלפון לוואטסאפ (כולל קידומת מדינה, לדוגמה: 972501234567).',
            ),
            
            // Business Hours Tab - לשונית שעות פעילות
            array(
                'key' => 'field_hours_tab',
                'label' => 'שעות פעילות',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_business_hours',
                'label' => 'שעות פעילות',
                'name' => 'business_hours',
                'type' => 'repeater',
                'min' => 1,
                'max' => 7,
                'layout' => 'table',
                'button_label' => 'הוסף שורה',
                'instructions' => 'הגדר את שעות הפעילות לכל יום.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_hours_line',
                        'label' => 'שורת שעות',
                        'name' => 'hours_line',
                        'type' => 'text',
                        'instructions' => 'לדוגמה: ימים א׳-ה׳: 10:00-18:00',
                    ),
                ),
            ),
            array(
                'key' => 'field_hours_note',
                'label' => 'הערה לשעות פעילות',
                'name' => 'hours_note',
                'type' => 'text',
                'default_value' => 'ביקורים בגלריה בתיאום מראש בלבד',
                'instructions' => 'הערה נוספת שתוצג מתחת לשעות הפעילות.',
            ),
            
            // Social Links Tab - לשונית רשתות חברתיות
            array(
                'key' => 'field_social_tab',
                'label' => 'רשתות חברתיות',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_social_instagram',
                'label' => 'קישור לאינסטגרם',
                'name' => 'social_instagram',
                'type' => 'url',
                'default_value' => 'https://instagram.com/handandvision',
                'instructions' => 'כתובת URL מלאה לעמוד האינסטגרם.',
            ),
            array(
                'key' => 'field_social_facebook',
                'label' => 'קישור לפייסבוק',
                'name' => 'social_facebook',
                'type' => 'url',
                'default_value' => 'https://facebook.com/handandvision',
                'instructions' => 'כתובת URL מלאה לעמוד הפייסבוק.',
            ),
            array(
                'key' => 'field_social_linkedin',
                'label' => 'קישור ללינקדאין',
                'name' => 'social_linkedin',
                'type' => 'url',
                'default_value' => 'https://linkedin.com/company/handandvision',
                'instructions' => 'כתובת URL מלאה לעמוד הלינקדאין.',
            ),
            array(
                'key' => 'field_social_youtube',
                'label' => 'קישור ליוטיוב',
                'name' => 'social_youtube',
                'type' => 'url',
                'instructions' => 'כתובת URL מלאה לערוץ היוטיוב (אופציונלי).',
            ),
            
            // Contact Form Tab - לשונית טופס קשר
            array(
                'key' => 'field_form_tab',
                'label' => 'טופס יצירת קשר',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_contact_form_shortcode',
                'label' => 'שורטקוד טופס קשר',
                'name' => 'contact_form_shortcode',
                'type' => 'text',
                'instructions' => 'הזן שורטקוד של Contact Form 7 או טופס אחר. השאר ריק לטופס ברירת מחדל.',
                'placeholder' => '[contact-form-7 id="123"]',
            ),
            array(
                'key' => 'field_form_title',
                'label' => 'כותרת הטופס',
                'name' => 'form_title',
                'type' => 'text',
                'default_value' => 'שלחו לנו הודעה',
                'instructions' => 'כותרת שמופיעה מעל הטופס.',
            ),
            
            // Map Tab - לשונית מפה
            array(
                'key' => 'field_map_tab',
                'label' => 'מפה',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_map_embed',
                'label' => 'קוד הטמעת מפה',
                'name' => 'map_embed',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => 'קוד iframe להטמעת גוגל מפות. השאר ריק להצגת placeholder למפה.',
            ),
            array(
                'key' => 'field_map_title',
                'label' => 'כותרת סקשן המפה',
                'name' => 'map_title',
                'type' => 'text',
                'default_value' => 'הגלריה שלנו',
                'instructions' => 'כותרת שמופיעה באזור המפה.',
            ),
            
            // Page Content Tab - לשונית תוכן הדף
            array(
                'key' => 'field_content_tab',
                'label' => 'תוכן הדף',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_page_overline',
                'label' => 'טקסט עליון (Overline)',
                'name' => 'page_overline',
                'type' => 'text',
                'default_value' => 'בואו נדבר',
                'instructions' => 'טקסט קטן שמופיע מעל הכותרת.',
            ),
            array(
                'key' => 'field_page_subtitle',
                'label' => 'תת-כותרת',
                'name' => 'page_subtitle',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'נשמח לשמוע מכם - בין אם מדובר בשאלה על יצירה ספציפית, ייעוץ לאוסף, או שיתוף פעולה עתידי',
                'instructions' => 'תת-כותרת שמופיעה מתחת לכותרת הראשית.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-contact.php',
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
add_action( 'acf/init', 'handandvision_register_acf_contact_fields' );
