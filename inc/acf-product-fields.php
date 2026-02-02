<?php
/**
 * ACF Field Group Configuration for WooCommerce Products
 * 
 * This file registers custom ACF fields for WooCommerce products.
 * Labels and instructions in Hebrew.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF Field Groups for Products
 */
function handandvision_register_acf_product_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // Only register if WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_product_fields',
        'title' => 'פרטי היצירה',
        'fields' => array(
            array(
                'key' => 'field_product_artist',
                'label' => 'אמן',
                'name' => 'product_artist',
                'type' => 'post_object',
                'post_type' => array( 'artist' ),
                'return_format' => 'object',
                'allow_null' => 0,
                'instructions' => 'בחר את האמן שיצר את היצירה.',
            ),
            array(
                'key' => 'field_product_unique',
                'label' => 'יצירה ייחודית / מקורית',
                'name' => 'product_unique',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
                'instructions' => 'הפעל אם זו יצירה ייחודית, אחד מאחד.',
            ),
            array(
                'key' => 'field_product_price_request',
                'label' => 'מחיר לפי בקשה',
                'name' => 'product_price_request',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
                'instructions' => 'הפעל כדי להציג "מחיר לפי בקשה" במקום המחיר בפועל.',
            ),
            array(
                'key' => 'field_product_medium',
                'label' => 'טכניקה / מדיום',
                'name' => 'product_medium',
                'type' => 'text',
                'placeholder' => 'לדוגמה: שמן על בד',
                'instructions' => 'הטכניקה או המדיום בו נעשתה היצירה.',
            ),
            array(
                'key' => 'field_product_dimensions',
                'label' => 'מידות',
                'name' => 'product_dimensions',
                'type' => 'text',
                'placeholder' => 'לדוגמה: 120 x 80 ס"מ',
                'instructions' => 'מידות היצירה.',
            ),
            array(
                'key' => 'field_product_year_created',
                'label' => 'שנת יצירה',
                'name' => 'product_year_created',
                'type' => 'number',
                'placeholder' => '2024',
                'instructions' => 'השנה בה נוצרה היצירה.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'product',
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
add_action( 'acf/init', 'handandvision_register_acf_product_fields' );
