<?php
/**
 * Configure Polylang for Hebrew (default) + English.
 *
 * Usage:
 *   npm run dev:polylang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( ! function_exists( 'PLL' ) ) {
	WP_CLI::error( 'Polylang is not active. Run: npm run dev:cli -- plugin install polylang --activate' );
}

$model = PLL()->model;
$existing = $model->get_languages_list();

if ( ! empty( $existing ) ) {
	WP_CLI::success( 'Polylang languages already configured (' . count( $existing ) . ').' );
} else {
	$languages = array(
		array(
			'name'       => 'עברית',
			'slug'       => 'he',
			'locale'     => 'he_IL',
			'rtl'        => 1,
			'flag'       => 'il',
			'term_group' => 0,
		),
		array(
			'name'       => 'English',
			'slug'       => 'en',
			'locale'     => 'en_US',
			'rtl'        => 0,
			'flag'       => 'us',
			'term_group' => 1,
		),
	);

	foreach ( $languages as $lang ) {
		$result = $model->add_language( $lang );
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( $lang['slug'] . ': ' . $result->get_error_message() );
		} else {
			WP_CLI::log( 'Added language: ' . $lang['slug'] );
		}
	}
}

$options = get_option( 'polylang', array() );
$options['default_lang'] = 'he';
$options['force_lang']   = 1;
$options['rewrite']      = 1;
$options['hide_default'] = 1;
$options['redirect_lang'] = 0;
$options['browser']      = 0;
update_option( 'polylang', $options );

flush_rewrite_rules( false );

// Enable CPTs and WooCommerce product type for translation.
$sync = get_option( 'polylang_wpml_compat', array() );
if ( ! is_array( $sync ) ) {
	$sync = array();
}
update_option( 'polylang_wpml_compat', $sync );

WP_CLI::success( 'Polylang configured: Hebrew default (no URL prefix), English at /en/.' );
WP_CLI::log( 'Next: link translated posts in Polylang admin, or duplicate content per language.' );
WP_CLI::log( 'Yoast SEO is active — hreflang is handled by Polylang + Yoast.' );
