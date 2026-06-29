<?php
/**
 * Client-defined display order for services and artists.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical service titles (Hebrew) for homepage / archive ordering.
 *
 * @return string[]
 */
function handandvision_get_service_display_order() {
	return array(
		'אובייקטים ועיצוב תעשייתי',
		'עיצוב במות ודקורציה',
		'אומנות אימרסיבית',
		'אמנות לייב באירועים',
		'דיגיטל ארט וגרפיקה',
		'ייעוץ והכוונה',
	);
}

/**
 * Canonical artist names for archive / showcase ordering (RTL: first = rightmost).
 *
 * @return string[]
 */
function handandvision_get_artist_display_order() {
	return array(
		'דניאל פילוסוף',
		'נועה אפריאט',
		'מנטיס',
		'רפי',
		'בוריס',
		'דניאל רוטנברג',
		'יולה',
		'סברס',
		'אדיסון',
		'ויקה',
		'דניאל עטיה',
		'בועז',
		'חן',
	);
}

/**
 * Sort post objects by a title whitelist; unknown titles trail in original order.
 *
 * @param WP_Post[] $posts   Post objects.
 * @param string[]  $order   Ordered titles.
 * @return WP_Post[]
 */
function handandvision_sort_posts_by_title_order( array $posts, array $order ) {
	if ( empty( $posts ) || empty( $order ) ) {
		return $posts;
	}

	$rank = array();
	foreach ( $order as $i => $title ) {
		$rank[ mb_strtolower( trim( $title ) ) ] = $i;
	}

	usort(
		$posts,
		static function ( $a, $b ) use ( $rank ) {
			$ta = mb_strtolower( trim( $a->post_title ) );
			$tb = mb_strtolower( trim( $b->post_title ) );
			$ra = $rank[ $ta ] ?? 9999;
			$rb = $rank[ $tb ] ?? 9999;
			if ( $ra === $rb ) {
				return strcmp( $ta, $tb );
			}
			return $ra <=> $rb;
		}
	);

	return $posts;
}

/**
 * @param WP_Post[] $services Service posts.
 * @return WP_Post[]
 */
function handandvision_sort_services_for_display( array $services ) {
	return handandvision_sort_posts_by_title_order( $services, handandvision_get_service_display_order() );
}

/**
 * @param WP_Post[] $artists Artist posts.
 * @return WP_Post[]
 */
function handandvision_sort_artists_for_display( array $artists ) {
	return handandvision_sort_posts_by_title_order( $artists, handandvision_get_artist_display_order() );
}

/**
 * Strip em/en dashes and replace hyphen-minus used as punctuation in display copy.
 *
 * @param string $text Raw text.
 * @return string
 */
function handandvision_strip_dashes_from_copy( $text ) {
	if ( ! is_string( $text ) || $text === '' ) {
		return $text;
	}
	$text = str_replace( array( '—', '–', ' - ' ), array( ' ', ' ', ' ' ), $text );
	return preg_replace( '/\s+/u', ' ', $text );
}

/**
 * Whether a service post is the consultation offering.
 *
 * @param int $service_id Post ID.
 * @return bool
 */
function handandvision_is_consultation_service( $service_id ) {
	$title = mb_strtolower( get_the_title( $service_id ) );
	return ( false !== mb_strpos( $title, 'ייעוץ' ) )
		|| ( false !== mb_strpos( $title, 'consult' ) );
}

/**
 * Collect carousel image IDs for a service card (hero + gallery, no Ross-tagged media).
 *
 * @param int $service_id Post ID.
 * @return int[]
 */
function handandvision_get_service_carousel_image_ids( $service_id ) {
	$ids = array();

	$hero = get_field( 'service_hero_image', $service_id );
	if ( is_array( $hero ) && ! empty( $hero['ID'] ) ) {
		$ids[] = (int) $hero['ID'];
	}

	$thumb = get_post_thumbnail_id( $service_id );
	if ( $thumb ) {
		$ids[] = (int) $thumb;
	}

	$gallery = get_field( 'service_gallery', $service_id );
	if ( is_array( $gallery ) ) {
		foreach ( $gallery as $img ) {
			if ( is_array( $img ) && ! empty( $img['ID'] ) ) {
				$ids[] = (int) $img['ID'];
			}
		}
	}

	$project_gallery = get_field( 'service_project_gallery', $service_id );
	if ( is_array( $project_gallery ) ) {
		foreach ( $project_gallery as $row ) {
			if ( is_array( $row['image'] ?? null ) && ! empty( $row['image']['ID'] ) ) {
				$ids[] = (int) $row['image']['ID'];
			}
		}
	}

	$ids = array_values( array_unique( array_filter( $ids ) ) );
	return handandvision_filter_excluded_media_ids( $ids );
}

/**
 * Exclude media tagged with excluded artist names (e.g. Ross).
 *
 * @param int[] $attachment_ids Attachment IDs.
 * @return int[]
 */
function handandvision_filter_excluded_media_ids( array $attachment_ids ) {
	$blocked = array( 'ross', 'רוס' );

	return array_values(
		array_filter(
			$attachment_ids,
			static function ( $id ) use ( $blocked ) {
				$alt   = mb_strtolower( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
				$title = mb_strtolower( (string) get_the_title( $id ) );
				$hay   = $alt . ' ' . $title;
				foreach ( $blocked as $needle ) {
					if ( $needle !== '' && false !== mb_strpos( $hay, $needle ) ) {
						return false;
					}
				}
				return true;
			}
		)
	);
}
