<?php
/**
 * Service helpers – icon SVGs for service cards
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function handandvision_get_service_icon_svg( $index = 0 ) {
	$index = (int) $index % 6;
	$icons = array(
		'<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="32" height="32" rx="2"/><rect x="12" y="12" width="24" height="24" rx="1"/></svg>',
		'<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="24" cy="24" r="14"/><circle cx="18" cy="20" r="3"/><circle cx="30" cy="18" r="3"/><circle cx="24" cy="28" r="3"/></svg>',
		'<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 8v32M24 8v32M34 8v32"/><path d="M10 14h28M10 24h28M10 34h28"/></svg>',
		'<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M24 8c-4 4-8 8-8 14a8 8 0 1 0 16 0c0-6-4-10-8-14Z"/><path d="M24 22v10"/></svg>',
		'<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M24 6l12 18H12L24 6z"/><path d="M24 42l12-18H12l12 18z"/></svg>',
		'<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="24" cy="24" r="10"/><circle cx="24" cy="22" r="4"/><path d="M24 32c-3 0-5 2-6 4"/></svg>',
	);
	return $icons[ $index ];
}
