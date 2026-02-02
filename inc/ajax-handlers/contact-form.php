<?php
/**
 * AJAX Contact Form Handler
 * Handles form submissions with nonce verification, rate limiting, and spam protection
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle contact form submission via AJAX
 * Includes rate limiting, nonce verification, and input sanitization
 *
 * @since 3.3.0
 * @return void Sends JSON response
 */
function handandvision_handle_contact_form() {
    // Rate limiting - prevent spam with IP-based throttling
    $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $rate_key = 'hv_contact_rate_' . md5( $ip_address );
    $last_submission = get_transient( $rate_key );

    if ( false !== $last_submission ) {
        wp_send_json_error( [
            'message' => handandvision_is_hebrew()
                ? 'אנא המתינו דקה לפני שליחת פנייה נוספת.'
                : 'Please wait a minute before submitting another message.'
        ] );
        return;
    }

    // Verify nonce with proper sanitization
    $nonce = isset( $_POST['hv_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['hv_contact_nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'hv_contact_action' ) ) {
        wp_send_json_error( [
            'message' => handandvision_is_hebrew()
                ? 'אימות נכשל. אנא טענו את הדף מחדש.'
                : 'Security check failed. Please refresh the page.'
        ] );
        return;
    }

    // Honeypot field check (spam protection)
    $honeypot = isset( $_POST['website'] ) ? sanitize_text_field( wp_unslash( $_POST['website'] ) ) : '';
    if ( ! empty( $honeypot ) ) {
        // Bot detected - silently fail
        wp_send_json_success( [
            'message' => handandvision_is_hebrew() ? 'ההודעה נשלחה בהצלחה!' : 'Message sent successfully!'
        ] );
        return;
    }

    // Sanitize and validate input fields
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    $subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    // Validate required fields
    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( [
            'message' => handandvision_is_hebrew() ? 'אנא מלאו את כל שדות החובה.' : 'Please fill in all required fields.'
        ] );
        return;
    }

    // Validate email format
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [
            'message' => handandvision_is_hebrew() ? 'כתובת האימייל אינה תקינה.' : 'Invalid email address.'
        ] );
        return;
    }

    // Additional validation: check message length
    if ( strlen( $message ) < 10 ) {
        wp_send_json_error( [
            'message' => handandvision_is_hebrew() ? 'ההודעה קצרה מדי.' : 'Message is too short.'
        ] );
        return;
    }

    if ( strlen( $message ) > 5000 ) {
        wp_send_json_error( [
            'message' => handandvision_is_hebrew() ? 'ההודעה ארוכה מדי.' : 'Message is too long.'
        ] );
        return;
    }

    // Get admin email from ACF or WordPress settings
    $submitted_page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    $to = get_field( 'contact_email', $submitted_page_id ) ?: get_option( 'admin_email' );

    // Subject line
    $email_subject = 'פנייה חדשה מאתר Hand & Vision: ' . sanitize_text_field( $subject );

    // Email Content - sanitize all user input
    $body = "נשלחה פנייה חדשה מהאתר:\n\n";
    $body .= "שם: " . sanitize_text_field( $name ) . "\n";
    $body .= "אימייל: " . sanitize_email( $email ) . "\n";
    if ( ! empty( $phone ) ) {
        $body .= "טלפון: " . sanitize_text_field( $phone ) . "\n";
    }
    $body .= "נושא: " . sanitize_text_field( $subject ) . "\n\n";
    $body .= "הודעה:\n" . sanitize_textarea_field( $message ) . "\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: Hand & Vision <' . sanitize_email( get_option( 'admin_email' ) ) . '>',
        'Reply-To: ' . sanitize_text_field( $name ) . ' <' . sanitize_email( $email ) . '>'
    ];

    // Send Mail
    $sent = wp_mail( $to, $email_subject, $body, $headers );

    if ( $sent ) {
        // Set rate limit - 60 seconds between submissions
        set_transient( $rate_key, time(), 60 );
        wp_send_json_success( [
            'message' => handandvision_is_hebrew() ? 'ההודעה נשלחה בהצלחה! נחזור אליכם בהקדם.' : 'Message sent successfully! We will get back to you soon.'
        ] );
    } else {
        // Log error for debugging (only in development)
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'Hand and Vision Contact Form: wp_mail() failed for ' . $email );
        }

        wp_send_json_error( [
            'message' => handandvision_is_hebrew() ? 'אירעה שגיאה בשליחת ההודעה. אנא נסו שוב מאוחר יותר.' : 'Error sending message. Please try again later.'
        ] );
    }
}
add_action( 'wp_ajax_hv_contact_form', 'handandvision_handle_contact_form' );
add_action( 'wp_ajax_nopriv_hv_contact_form', 'handandvision_handle_contact_form' );

/**
 * Auto-create Contact page if it doesn't exist
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_create_contact_page() {
    // Check if contact page already exists by slug
    $contact_page = get_page_by_path( 'contact' );

    if ( ! $contact_page ) {
        // Create the contact page
        $page_id = wp_insert_post( [
            'post_title'     => handandvision_is_hebrew() ? 'צור קשר' : 'Contact',
            'post_name'      => 'contact',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_content'   => '',
        ] );

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            // Set the page template
            update_post_meta( $page_id, '_wp_page_template', 'page-contact.php' );
        }
    } else {
        // Ensure the template is set correctly
        $current_template = get_post_meta( $contact_page->ID, '_wp_page_template', true );
        if ( $current_template !== 'page-contact.php' ) {
            update_post_meta( $contact_page->ID, '_wp_page_template', 'page-contact.php' );
        }
    }
}
add_action( 'after_switch_theme', 'handandvision_create_contact_page' );

/**
 * Ensure contact page exists (runs only once)
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_ensure_contact_page_exists() {
    if ( get_option( 'handandvision_contact_page_created' ) !== 'yes' ) {
        handandvision_create_contact_page();
        update_option( 'handandvision_contact_page_created', 'yes' );
    }
}
add_action( 'init', 'handandvision_ensure_contact_page_exists' );

/**
 * Get contact page URL
 *
 * @since 3.3.0
 * @return string Contact page URL
 */
function handandvision_get_contact_url() {
    $page = get_page_by_path( 'contact' );
    if ( $page && $page->post_status === 'publish' ) {
        return get_permalink( $page );
    }
    $pages = get_posts( [
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-contact.php',
        'post_status'    => 'publish',
    ] );
    if ( ! empty( $pages ) ) {
        return get_permalink( $pages[0] );
    }
    return home_url( '/contact' );
}
