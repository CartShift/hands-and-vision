<?php
/**
 * ACF display helpers: show elegant "to complete" placeholders for empty essential fields.
 * Placeholders are visible only to users who can edit (reminds client when browsing).
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function handandvision_acf_empty( $value ) {
	if ( $value === null || $value === false ) {
		return true;
	}
	if ( is_string( $value ) ) {
		return trim( $value ) === '';
	}
	if ( is_array( $value ) ) {
		return count( $value ) === 0;
	}
	return false;
}

function handandvision_acf_can_show_placeholder() {
	return current_user_can( 'edit_posts' );
}

function handandvision_acf_display_value( $value, $empty_label, $format = 'html' ) {
	if ( ! handandvision_acf_empty( $value ) ) {
		return $value;
	}
	if ( ! handandvision_acf_can_show_placeholder() ) {
		return $format === 'html' ? '' : '';
	}
	$text = ( function_exists( 'handandvision_is_hebrew' ) && handandvision_is_hebrew() )
		? 'להשלים: ' . $empty_label
		: 'To complete: ' . $empty_label;
	if ( $format === 'html' ) {
		return '<span class="hv-field-placeholder">' . esc_html( $text ) . '</span>';
	}
	return $text;
}

function handandvision_acf_image_placeholder_html( $empty_label ) {
	if ( ! handandvision_acf_can_show_placeholder() ) {
		return '';
	}
	$text = ( function_exists( 'handandvision_is_hebrew' ) && handandvision_is_hebrew() )
		? 'להשלים: ' . $empty_label
		: 'To complete: ' . $empty_label;
	return '<div class="hv-field-placeholder hv-field-placeholder--image" aria-hidden="true"><span>' . esc_html( $text ) . '</span></div>';
}
