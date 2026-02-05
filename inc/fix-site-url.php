<?php
/**
 * One-time DB search-replace: old site URL -> current home URL.
 * Run once after migration from https://ggr.zmk.mybluehost.me/website_8422dc8c
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define old URL from constant or environment variable
define( 'HV_OLD_SITE_URL', defined( 'HV_OLD_SITE_URL_OVERRIDE' ) ? HV_OLD_SITE_URL_OVERRIDE : 'https://ggr.zmk.mybluehost.me/website_8422dc8c' );

/**
 * Replace old site URL with current home URL in options, posts, postmeta.
 * Call only when intended (e.g. admin with ?hv_fix_url=1). No fallbacks.
 */
function handandvision_fix_site_url_in_db() {
	$new_url = untrailingslashit( home_url( '/' ) );
	if ( untrailingslashit( HV_OLD_SITE_URL ) === $new_url ) {
		return array( 'skipped' => true, 'message' => 'Old and new URL match; no change.' );
	}
	global $wpdb;
	$old = HV_OLD_SITE_URL;
	$new = $new_url;
	$old_esc = $wpdb->esc_like( $old );
	$tables = array(
		$wpdb->options   => array( 'option_value' ),
		$wpdb->posts     => array( 'post_content', 'guid' ),
		$wpdb->postmeta  => array( 'meta_value' ),
	);
	$replaced = array();
	foreach ( $tables as $table => $columns ) {
		foreach ( $columns as $col ) {
			$affected = $wpdb->query( $wpdb->prepare(
				"UPDATE {$table} SET {$col} = REPLACE({$col}, %s, %s) WHERE {$col} LIKE %s",
				$old,
				$new,
				'%' . $old_esc . '%'
			) );
			if ( $affected > 0 ) {
				$replaced[ $table . '.' . $col ] = $affected;
			}
		}
	}
	return array( 'replaced' => $replaced, 'new_url' => $new );
}
