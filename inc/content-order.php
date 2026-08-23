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
 * Canonical service title aliases for homepage / archive ordering.
 *
 * @return string[][]
 */
function handandvision_get_service_display_order() {
	return array(
		array(
			'אובייקטים עיצוביים',
			'אובייקטים ועיצוב תעשייתי',
			'עיצוב תעשייתי',
			'industrial art objects',
			'industrial design',
			'industrial art',
			'design objects',
		),
		array(
			'עיצוב אובייקטים דקורטיבים לאירועים',
			'עיצוב אובייקטים דקורטיביים לאירועים',
			'עיצוב במות ודקורציה',
			'עיצוב במות',
			'decorative objects for events',
			'stage design',
			'event decor',
			'decorative event objects',
			'stage decor',
		),
		array(
			'אומנות מרחבית',
			'אמנות מרחבית',
			'אומנות אימרסיבית',
			'אמנות אימרסיבית',
			'spatial art',
			'immersive art',
			'space art',
		),
		array(
			'אומנות דיגיטלית',
			'אמנות דיגיטלית',
			'עיצוב דיגיטלי',
			'דיגיטל ארט',
			'דיגיטל ארט וגרפיקה',
			'digital art',
			'digital design',
			'digital art design',
		),
		array(
			'אומנות לייב באירועים',
			'אמנות לייב באירועים',
			'אמנות חיה באירועים',
			'live art at events',
			'live art events',
			'live art',
		),
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
 * Normalize display titles for stable multilingual matching.
 *
 * @param string $value Raw title / slug.
 * @return string
 */
function handandvision_normalize_display_key( $value ) {
	$value = is_string( $value ) ? $value : '';
	$value = handandvision_strip_dashes_from_copy( $value );
	$value = str_replace( array( '/', '\\', '-', '_', '–', '—' ), ' ', $value );
	$value = preg_replace( '/\s+/u', ' ', $value );
	return mb_strtolower( trim( $value ) );
}

/**
 * Sort post objects by title aliases; unknown titles trail in original order.
 *
 * @param WP_Post[] $posts   Post objects.
 * @param array[]   $order   Ordered title aliases.
 * @return WP_Post[]
 */
function handandvision_sort_posts_by_title_order( array $posts, array $order ) {
	if ( empty( $posts ) || empty( $order ) ) {
		return $posts;
	}

	$rank = array();
	foreach ( $order as $i => $aliases ) {
		foreach ( (array) $aliases as $title ) {
			$rank[ handandvision_normalize_display_key( $title ) ] = $i;
		}
	}

	$get_rank = static function ( $post ) use ( $rank ) {
		if ( ! is_object( $post ) || empty( $post->ID ) ) {
			return 9999;
		}

		$values = array(
			get_the_title( $post->ID ),
			get_post_field( 'post_name', $post->ID ),
		);

		if ( function_exists( 'get_field' ) ) {
			$values[] = (string) get_field( 'service_title_en', $post->ID );
		}

		foreach ( $values as $value ) {
			$key = handandvision_normalize_display_key( $value );
			if ( isset( $rank[ $key ] ) ) {
				return $rank[ $key ];
			}

			foreach ( $rank as $alias => $alias_rank ) {
				if ( '' !== $alias && '' !== $key && false !== mb_strpos( $key, $alias ) ) {
					return $alias_rank;
				}
			}
		}

		return 9999;
	};

	usort(
		$posts,
		static function ( $a, $b ) use ( $get_rank ) {
			$ta = handandvision_normalize_display_key( $a->post_title );
			$tb = handandvision_normalize_display_key( $b->post_title );
			$ra = $get_rank( $a );
			$rb = $get_rank( $b );
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
	if ( empty( $artists ) ) {
		return $artists;
	}

	$artist_order = array(
		array( 'daniel philosoph', 'daniel philosof', 'דניאל פילוסוף', '×“× ×™××œ ×¤×™×œ×•×¡×•×£' ),
		array( 'noa afriat', 'נועה אפריאט', '× ×•×¢×” ××¤×¨×™××˜' ),
		array( 'mantis', 'מנטיס', '×ž× ×˜×™×¡' ),
		array( 'rafi', 'רפי', '×¨×¤×™' ),
		array( 'borix', 'boris', 'בוריס', '×‘×•×¨×™×¡' ),
		array( 'daniel rotenberg', 'דניאל רוטנברג', '×“× ×™××œ ×¨×•×˜× ×‘×¨×’' ),
		array( 'yula', 'יולה', '×™×•×œ×”' ),
		array( 'sabres', 'סברס', '×¡×‘×¨×¡' ),
		array( 'addison', 'אדיסון', '××“×™×¡×•×Ÿ' ),
		array( 'vika', 'vika gorelik', 'ויקה', '×•×™×§×”' ),
		array( 'daniel atiya', 'דניאל עטיה', '×“× ×™××œ ×¢×˜×™×”' ),
		array( 'boaz philosoph', 'boaz philosof', 'בועז', '×‘×•×¢×–' ),
		array( 'chenka', 'chen', 'חן', '×—×Ÿ' ),
	);

	$get_rank = static function ( $artist ) use ( $artist_order ) {
		$title = is_object( $artist ) ? mb_strtolower( trim( get_the_title( $artist->ID ) ) ) : '';
		$slug  = is_object( $artist ) ? mb_strtolower( trim( get_post_field( 'post_name', $artist->ID ) ) ) : '';
		$haystack = $title . ' ' . str_replace( '-', ' ', $slug );

		foreach ( $artist_order as $rank => $aliases ) {
			foreach ( $aliases as $alias ) {
				$alias = mb_strtolower( trim( $alias ) );
				if ( '' !== $alias && false !== mb_strpos( $haystack, $alias ) ) {
					return $rank;
				}
			}
		}

		return 9999;
	};

	usort(
		$artists,
		static function ( $a, $b ) use ( $get_rank ) {
			$ra = $get_rank( $a );
			$rb = $get_rank( $b );

			if ( $ra === $rb ) {
				return strcmp(
					is_object( $a ) ? mb_strtolower( trim( get_the_title( $a->ID ) ) ) : '',
					is_object( $b ) ? mb_strtolower( trim( get_the_title( $b->ID ) ) ) : ''
				);
			}

			return $ra <=> $rb;
		}
	);

	return $artists;
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
