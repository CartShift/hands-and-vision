<?php
/**
 * Maintenance Mode / Coming Soon Login
 *
 * This logic controls access to the site when in maintenance mode.
 */

// Configuration
// You can toggle this to false to disable maintenance mode.
define( 'HV_MAINTENANCE_MODE', true );

// Password to bypass maintenance mode
// Use a secure password.
define( 'HV_MAINTENANCE_PASSWORD', 'vision2025' );

/**
 * Handle Maintenance Mode Redirects & Auth
 */
function handandvision_maintenance_mode() {
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

    // 4. Check for Bypass Cookie
    $cookie_name = 'hv_maintenance_auth';
    $auth_hash   = md5( HV_MAINTENANCE_PASSWORD . AUTH_SALT ); // Salted hash for better security

    if ( isset( $_COOKIE[ $cookie_name ] ) && $_COOKIE[ $cookie_name ] === $auth_hash ) {
        return;
    }

    // 5. Handle Password Submission
    $error = '';
    if ( isset( $_POST['hv_pass'] ) ) {
        $entered_pass = sanitize_text_field( $_POST['hv_pass'] );
        if ( $entered_pass === HV_MAINTENANCE_PASSWORD ) {
            setcookie( $cookie_name, $auth_hash, time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN ); // 30 days
            wp_redirect( home_url() ); // Reload to clear POST and showing site
            exit;
        } else {
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
