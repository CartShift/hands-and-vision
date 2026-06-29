<?php
/**
 * Maintenance Mode / Coming Soon Login
 *
 * This logic controls access to the site when in maintenance mode.
 */

// Enable in wp-config.php: define( 'HV_MAINTENANCE_MODE', true );
if ( ! defined( 'HV_MAINTENANCE_MODE' ) ) {
	define( 'HV_MAINTENANCE_MODE', false );
}

// Set in wp-config.php when maintenance is on: define( 'HV_MAINTENANCE_PASSWORD', 'your-secret' );

/**
 * Handle Maintenance Mode Redirects & Auth
 */
function handandvision_maintenance_mode() {
    if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'local' === WP_ENVIRONMENT_TYPE ) {
        return;
    }

    // 1. Check if maintenance mode is enabled
    if ( ! defined( 'HV_MAINTENANCE_MODE' ) || ! HV_MAINTENANCE_MODE ) {
        return;
    }

    // 2. Allow logged-in administrators
    if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        return;
    }

    // 3. Allow login URL and admin ajax
    if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
        return;
    }
    if ( is_admin() ) {
        // Allow access to admin if not logged in (will redirect to login)
        return;
    }

    $has_password_gate = defined( 'HV_MAINTENANCE_PASSWORD' ) && HV_MAINTENANCE_PASSWORD;

    // 4. Check for Bypass Cookie
    if ( $has_password_gate ) {
        $cookie_name = 'hv_maintenance_auth';
        $auth_hash   = wp_hash( HV_MAINTENANCE_PASSWORD );

        if ( isset( $_COOKIE[ $cookie_name ] ) && hash_equals( $auth_hash, sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) ) ) {
            return;
        }
    }

    // 5. Handle Password Submission
    $error = '';
    if ( $has_password_gate && isset( $_POST['hv_pass'] ) ) {
        $nonce = isset( $_POST['hv_maintenance_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['hv_maintenance_nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'hv_maintenance_login' ) ) {
            $error = 'Incorrect password';
        } else {
            $entered_pass = sanitize_text_field( wp_unslash( $_POST['hv_pass'] ) );
            if ( hash_equals( HV_MAINTENANCE_PASSWORD, $entered_pass ) ) {
                setcookie( $cookie_name, $auth_hash, time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
                wp_safe_redirect( home_url() );
                exit;
            }
            $error = 'Incorrect password';
        }
    }

    // 6. Render Coming Soon Page
    // We break out of the standard template hierarchy to render this standalone page
    // Using a locate_template check allows overrides, but we default to our logic here.

    // Set headers (503 Service Unavailable is standard for maintenance, but for a "private preview" 200 might be better.
    // Let's stick to 200 OK since it's a "Coming Soon" page that acts as a gate.)
    status_header( 200 );

    // Load the specialized template
    $template = locate_template( 'coming-soon.php' );

    if ( $template ) {
        // Pass error variable if needed (using global or just including)
        set_query_var( 'hv_maintenance_error', $error );
        load_template( $template );
        exit;
    } else {
        // Fallback textual message if template missing
        wp_die( '<h1>Site Under Construction</h1><p>Please check back soon.</p>', 'Coming Soon', array( 'response' => 200 ) );
    }
}
add_action( 'template_redirect', 'handandvision_maintenance_mode' );
