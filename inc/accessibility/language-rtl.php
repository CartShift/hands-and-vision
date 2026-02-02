<?php
/**
 * Accessibility Features
 * RTL/LTR switching and language handling
 *
 * @package HandAndVision
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize language cookie
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_init_language_cookie() {
    if ( is_admin() || headers_sent() ) {
        return;
    }

    // if GET param is present, update the cookie
    if ( isset( $_GET['lang'] ) ) {
        $lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) );
        if ( in_array( $lang, [ 'en', 'he' ], true ) ) {
            setcookie( 'hv_lang', $lang, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
            $_COOKIE['hv_lang'] = $lang; // Update global for immediate use in this request
        }
    }
}
add_action( 'init', 'handandvision_init_language_cookie' );

/**
 * Get current language (HE or EN)
 *
 * @since 3.3.0
 * @return string Language code (he or en)
 */
function handandvision_get_current_language() {
    // 1. Check URL Parameter (Highest Priority)
    if ( isset( $_GET['lang'] ) ) {
        $lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) );
        if ( 'en' === $lang || 'he' === $lang ) {
            return $lang;
        }
    }

    // 2. Check Cookie (Medium Priority)
    if ( isset( $_COOKIE['hv_lang'] ) ) {
        $lang = sanitize_text_field( wp_unslash( $_COOKIE['hv_lang'] ) );
        if ( 'en' === $lang || 'he' === $lang ) {
            return $lang;
        }
    }

    // 3. Fallback to Plugins (Polylang / WPML)
    if ( function_exists( 'pll_current_language' ) ) {
        $pll_lang = pll_current_language();
        if ( $pll_lang ) {
            return $pll_lang;
        }
    }
    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
        return ICL_LANGUAGE_CODE;
    }

    // 4. Default to Hebrew
    return 'he';
}

/**
 * Check if current language is Hebrew
 *
 * @since 3.3.0
 * @return bool True if Hebrew, false otherwise
 */
function handandvision_is_hebrew() {
    return handandvision_get_current_language() === 'he';
}

/**
 * Set html dir and body class from current language so RTL/LTR matches viewed language.
 *
 * @since 3.3.0
 * @param string $attr Language attributes
 * @return string Modified attributes
 */
function handandvision_language_attributes( $attr ) {
    $dir = handandvision_is_hebrew() ? 'rtl' : 'ltr';
    if ( preg_match( '/\sdir="[^"]*"/', $attr ) ) {
        return preg_replace( '/\sdir="[^"]*"/', ' dir="' . $dir . '"', $attr );
    }
    return $attr . ' dir="' . $dir . '"';
}
add_filter( 'language_attributes', 'handandvision_language_attributes' );

/**
 * Add RTL/LTR class to body
 *
 * @since 3.3.0
 * @param array $classes Body classes
 * @return array Modified classes
 */
function handandvision_body_class_rtl( $classes ) {
    $classes = array_diff( $classes, [ 'rtl', 'ltr' ] );
    $classes[] = handandvision_is_hebrew() ? 'rtl' : 'ltr';
    return $classes;
}
add_filter( 'body_class', 'handandvision_body_class_rtl' );
