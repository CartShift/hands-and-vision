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
            setcookie( 'hv_lang', $lang, [
                'expires'  => time() + 30 * DAY_IN_SECONDS,
                'path'     => COOKIEPATH ? COOKIEPATH : '/',
                'domain'   => COOKIE_DOMAIN,
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ] );
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

/**
 * Curated <title> labels per archive / page-type, language-aware. Avoids the
 * default WordPress "Archive: Artists" / "ארכיון אמנים" labels leaking into
 * the document title.
 *
 * @since 3.3.11
 * @param array $parts The document title parts.
 * @return array Filtered parts.
 */
function handandvision_get_curated_title() {
    $is_hebrew = handandvision_is_hebrew();

    if ( is_post_type_archive( 'artist' ) ) {
        return $is_hebrew ? 'האמנים שלנו' : 'Our Artists';
    }
    if ( is_post_type_archive( 'service' ) ) {
        return $is_hebrew ? 'השירותים שלנו' : 'Our Services';
    }
    if ( is_post_type_archive( 'gallery_item' ) ) {
        return $is_hebrew ? 'הגלריה' : 'Gallery';
    }
    if ( is_search() ) {
        return $is_hebrew ? 'תוצאות חיפוש' : 'Search Results';
    }
    if ( is_404() ) {
        return $is_hebrew ? 'דף לא נמצא' : 'Page Not Found';
    }
    if ( function_exists( 'is_shop' ) && is_shop() ) {
        return $is_hebrew ? 'חנות האמנות' : 'Art Gallery Shop';
    }
    return '';
}

function handandvision_document_title_parts( $parts ) {
    $curated = handandvision_get_curated_title();
    if ( $curated ) {
        $parts['title'] = $curated;
    }
    return $parts;
}
add_filter( 'document_title_parts', 'handandvision_document_title_parts', 20 );

/**
 * Override SEO-plugin generated titles for archives we control so EN switch
 * works regardless of whether Yoast / Rank Math / etc. is active.
 *
 * @since 3.3.11
 */
function handandvision_override_plugin_title( $title ) {
    $curated = handandvision_get_curated_title();
    if ( ! $curated ) {
        return $title;
    }
    $site = get_bloginfo( 'name' );
    return $site ? $curated . ' | ' . $site : $curated;
}
add_filter( 'wpseo_title', 'handandvision_override_plugin_title', 20 );
add_filter( 'rank_math/frontend/title', 'handandvision_override_plugin_title', 20 );
add_filter( 'aioseo_title', 'handandvision_override_plugin_title', 20 );

/**
 * Force the document title separator and tagline behavior to follow the active
 * UI language (not WP locale), so EN mode doesn't ship a Hebrew separator.
 *
 * @since 3.3.11
 * @param string $sep Current separator.
 * @return string Filtered separator.
 */
function handandvision_document_title_separator( $sep ) {
    return '|';
}
add_filter( 'document_title_separator', 'handandvision_document_title_separator' );
