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

/**
 * Detect the Digital Art service for editorial fallback copy.
 *
 * @param int $service_id Service post ID.
 * @return bool
 */
function handandvision_is_digital_art_service( $service_id ) {
	$slug  = mb_strtolower( (string) get_post_field( 'post_name', $service_id ) );
	$title = mb_strtolower( (string) get_the_title( $service_id ) );
	$key   = $slug . ' ' . $title;

	return false !== mb_strpos( $key, 'digital' )
		|| false !== mb_strpos( $key, 'דיגיטל' );
}

/**
 * Detect the object / industrial design service.
 *
 * @param int $service_id Service post ID.
 * @return bool
 */
function handandvision_is_object_design_service( $service_id ) {
	$slug  = mb_strtolower( (string) get_post_field( 'post_name', $service_id ) );
	$title = mb_strtolower( (string) get_the_title( $service_id ) );
	$key   = $slug . ' ' . $title;

	return false !== mb_strpos( $key, 'object' )
		|| false !== mb_strpos( $key, 'industrial' )
		|| false !== mb_strpos( $key, 'אובייקט' )
		|| false !== mb_strpos( $key, 'תעשייתי' );
}

/**
 * Detect the decorative event objects service.
 *
 * @param int $service_id Service post ID.
 * @return bool
 */
function handandvision_is_decorative_event_objects_service( $service_id ) {
	$slug  = mb_strtolower( (string) get_post_field( 'post_name', $service_id ) );
	$title = mb_strtolower( (string) get_the_title( $service_id ) );
	$key   = $slug . ' ' . $title;

	return ( false !== mb_strpos( $key, 'decorative' )
		|| false !== mb_strpos( $key, 'event' )
		|| false !== mb_strpos( $key, 'דקורט' )
		|| false !== mb_strpos( $key, 'אירוע' ) )
		&& ( false !== mb_strpos( $key, 'object' )
			|| false !== mb_strpos( $key, 'אובייקט' ) );
}

/**
 * Detect the live art at events service.
 *
 * @param int $service_id Service post ID.
 * @return bool
 */
function handandvision_is_live_event_art_service( $service_id ) {
	$slug  = mb_strtolower( (string) get_post_field( 'post_name', $service_id ) );
	$title = mb_strtolower( (string) get_the_title( $service_id ) );
	$key   = $slug . ' ' . $title;

	return ( false !== mb_strpos( $key, 'live' )
		|| false !== mb_strpos( $key, 'לייב' ) )
		&& ( false !== mb_strpos( $key, 'event' )
			|| false !== mb_strpos( $key, 'אירוע' ) );
}

/**
 * Detect the spatial art service.
 *
 * @param int $service_id Service post ID.
 * @return bool
 */
function handandvision_is_spatial_art_service( $service_id ) {
	$slug  = mb_strtolower( (string) get_post_field( 'post_name', $service_id ) );
	$title = mb_strtolower( (string) get_the_title( $service_id ) );
	$key   = $slug . ' ' . $title;

	return false !== mb_strpos( $key, 'spatial' )
		|| false !== mb_strpos( $key, 'mural' )
		|| false !== mb_strpos( $key, 'wall art' )
		|| false !== mb_strpos( $key, 'מרחבית' )
		|| false !== mb_strpos( $key, 'אמנות קיר' )
		|| false !== mb_strpos( $key, 'ציור בהזמנה' );
}

/**
 * Detect services that use artist-led artwork sections.
 *
 * @param int $service_id Service post ID.
 * @return bool
 */
function handandvision_service_uses_artist_sections( $service_id ) {
	return handandvision_is_digital_art_service( $service_id )
		|| handandvision_is_live_event_art_service( $service_id )
		|| handandvision_is_spatial_art_service( $service_id )
		|| handandvision_is_decorative_event_objects_service( $service_id )
		|| handandvision_is_object_design_service( $service_id );
}

/**
 * Return the artist section heading for a service.
 *
 * @param int  $service_id Service post ID.
 * @param bool $is_hebrew  Whether the current language is Hebrew.
 * @return string
 */
function handandvision_get_service_artist_sections_heading( $service_id, $is_hebrew ) {
	if ( handandvision_is_live_event_art_service( $service_id ) ) {
		return $is_hebrew ? 'עבודות אמנות לייב באירועים לפי אמן' : 'Live Event Art by Artist';
	}

	if ( handandvision_is_spatial_art_service( $service_id ) ) {
		return $is_hebrew ? 'עבודות אמנות מרחבית לפי אמן' : 'Spatial Art by Artist';
	}

	if ( handandvision_is_decorative_event_objects_service( $service_id ) ) {
		return $is_hebrew ? 'עבודות עיצוב אובייקטים דקורטיביים לאירועים לפי אמן' : 'Decorative Object Design for Events by Artist';
	}

	if ( handandvision_is_object_design_service( $service_id ) ) {
		return $is_hebrew ? 'עבודות אובייקטים ועיצוב תעשייתי לפי אמן' : 'Object and Industrial Design Work by Artist';
	}

	if ( handandvision_is_digital_art_service( $service_id ) ) {
		return $is_hebrew ? 'עבודות דיגיטליות לפי אמן' : 'Digital Work by Artist';
	}

	return $is_hebrew ? 'עבודות לפי אמן' : 'Work by Artist';
}

/**
 * Build artist-led project groups for a service page.
 *
 * @param int   $service_id      Service post ID.
 * @param array $related_artists Related artist posts from ACF.
 * @return array
 */
function handandvision_get_service_artist_project_groups( $service_id, $related_artists = array() ) {
	$groups       = array();
	$project_rows = function_exists( 'get_field' ) ? get_field( 'service_project_gallery', $service_id ) : array();
	$project_rows = is_array( $project_rows ) ? $project_rows : array();
	$artists_pool = is_array( $related_artists ) ? $related_artists : array();

	foreach ( $project_rows as $row ) {
		$image = $row['image'] ?? null;
		if ( ! is_array( $image ) || empty( $image['url'] ) ) {
			continue;
		}

		$artist_obj = $row['artist'] ?? null;
		$artist_id  = is_object( $artist_obj ) ? (int) $artist_obj->ID : 0;
		if ( ! $artist_id ) {
			continue;
		}

		if ( empty( $groups[ $artist_id ] ) ) {
			$groups[ $artist_id ] = handandvision_get_service_artist_group_base( $artist_id );
		}

		$groups[ $artist_id ]['projects'][] = array(
			'image'   => $image['sizes']['large'] ?? $image['url'],
			'alt'     => $image['alt'] ?? get_the_title( $artist_id ),
			'title'   => handandvision_strip_dashes_from_copy( (string) ( $row['project_title'] ?? '' ) ),
			'link'    => get_permalink( $artist_id ),
		);
	}

	if ( empty( $artists_pool ) && handandvision_service_uses_artist_sections( $service_id ) ) {
		$artists_pool = get_posts(
			array(
				'post_type'              => 'artist',
				'posts_per_page'         => 8,
				'orderby'                => 'date',
				'order'                  => 'ASC',
				'post_status'            => 'publish',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
			)
		);
	}

	foreach ( $artists_pool as $artist ) {
		$artist_id = is_object( $artist ) ? (int) $artist->ID : (int) $artist;
		if ( ! $artist_id || ! empty( $groups[ $artist_id ] ) ) {
			continue;
		}

		$groups[ $artist_id ] = handandvision_get_service_artist_group_base( $artist_id );
		$artist_gallery       = function_exists( 'handandvision_get_artist_gallery_items' ) ? handandvision_get_artist_gallery_items( $artist_id, 6 ) : array();

		foreach ( $artist_gallery as $item ) {
			$image_url = isset( $item['image'] ) ? $item['image'] : ( $item['url'] ?? '' );
			if ( ! $image_url ) {
				continue;
			}

			$groups[ $artist_id ]['projects'][] = array(
				'image' => $image_url,
				'alt'   => $item['alt'] ?? get_the_title( $artist_id ),
				'title' => handandvision_strip_dashes_from_copy( (string) ( $item['title'] ?? '' ) ),
				'link'  => get_permalink( $artist_id ),
			);
		}
	}

	return array_values(
		array_filter(
			$groups,
			function ( $group ) {
				return ! empty( $group['projects'] ) && count( $group['projects'] ) >= 3;
			}
		)
	);
}

/**
 * Build the curated artist sketch requested for service pages.
 *
 * @param int $service_id Service post ID.
 * @return array
 */
function handandvision_get_service_artist_showcase( $service_id ) {
	$configured_sections = handandvision_get_service_artist_sections_meta( $service_id );
	if ( empty( $configured_sections ) ) {
		$configured_sections = function_exists( 'get_field' ) ? get_field( 'service_artist_sections', $service_id ) : array();
	}
	$configured_sections = is_array( $configured_sections ) ? $configured_sections : array();
	$showcase            = array();

	foreach ( $configured_sections as $section ) {
		$artist_obj = $section['artist'] ?? null;
		$artist_id  = isset( $section['artist_id'] ) ? (int) $section['artist_id'] : 0;
		$artist_id  = $artist_id ? $artist_id : ( is_object( $artist_obj ) ? (int) $artist_obj->ID : (int) $artist_obj );
		if ( ! $artist_id ) {
			continue;
		}

		$artworks = isset( $section['artworks'] ) && is_array( $section['artworks'] ) ? $section['artworks'] : array();
		if ( empty( $artworks ) && ! empty( $section['artwork_ids'] ) && is_array( $section['artwork_ids'] ) ) {
			$artworks = array_map(
				function ( $attachment_id ) {
					$attachment_id = (int) $attachment_id;
					if ( ! $attachment_id ) {
						return array();
					}

					$full_url  = wp_get_attachment_image_url( $attachment_id, 'full' );
					$large_url = wp_get_attachment_image_url( $attachment_id, 'large' );

					return array(
						'id'      => $attachment_id,
						'url'     => $full_url,
						'alt'     => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
						'title'   => get_the_title( $attachment_id ),
						'caption' => wp_get_attachment_caption( $attachment_id ),
						'sizes'   => array(
							'large' => $large_url ? $large_url : $full_url,
						),
					);
				},
				$section['artwork_ids']
			);
		}
		$projects = array();

		foreach ( array_slice( $artworks, 0, 6 ) as $image ) {
			if ( ! is_array( $image ) || empty( $image['url'] ) ) {
				continue;
			}

			$projects[] = array(
				'image' => $image['sizes']['large'] ?? $image['url'],
				'full'  => $image['url'],
				'alt'   => $image['alt'] ?? get_the_title( $artist_id ),
				'title' => handandvision_strip_dashes_from_copy( (string) ( $image['caption'] ?? ( $image['title'] ?? '' ) ) ),
				'link'  => get_permalink( $artist_id ),
			);
		}

		$showcase[] = array(
			'id'          => $artist_id,
			'name'        => handandvision_strip_dashes_from_copy( get_the_title( $artist_id ) ),
			'link'        => get_permalink( $artist_id ),
			'portrait'    => handandvision_get_service_artist_portrait_url( $artist_id ),
			'deeper_text' => handandvision_strip_dashes_from_copy( (string) ( $section['deeper_text'] ?? '' ) ),
			'projects'    => array_pad( array_slice( $projects, 0, 6 ), 6, array() ),
		);
	}

	if ( ! empty( $showcase ) ) {
		return $showcase;
	}

	if ( ! handandvision_is_digital_art_service( $service_id ) ) {
		if ( ! handandvision_service_uses_artist_sections( $service_id ) ) {
			return array();
		}

		$fallback_artists = function_exists( 'get_field' ) ? get_field( 'service_related_artists', $service_id ) : array();
		$fallback_artists = is_array( $fallback_artists ) ? array_filter( $fallback_artists ) : array();

		if ( empty( $fallback_artists ) ) {
			$fallback_artists = get_posts(
				array(
					'post_type'              => 'artist',
					'posts_per_page'         => 2,
					'orderby'                => 'date',
					'order'                  => 'ASC',
					'post_status'            => 'publish',
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
				)
			);
		}

		$fallback_showcase = array();

		foreach ( array_slice( $fallback_artists, 0, 12 ) as $artist ) {
			$artist_id = is_object( $artist ) ? (int) $artist->ID : (int) $artist;
			if ( ! $artist_id ) {
				continue;
			}

			$fallback_showcase[] = array(
				'id'          => $artist_id,
				'name'        => handandvision_strip_dashes_from_copy( get_the_title( $artist_id ) ),
				'link'        => get_permalink( $artist_id ),
				'portrait'    => handandvision_get_service_artist_portrait_url( $artist_id ),
				'deeper_text' => '',
				'projects'    => array_pad( array(), 6, array() ),
			);
		}

		return $fallback_showcase;
	}

	$artists = array(
		array(
			'name'  => 'Daniel Philosoph',
			'terms' => array( 'daniel philosoph', 'daniel philosof', 'דניאל פילוסוף' ),
		),
		array(
			'name'  => 'Noa Afriat',
			'terms' => array( 'noa afriat', 'noa efrati', 'נועה אפריאט', 'נועה אפרתי' ),
		),
	);

	foreach ( $artists as $index => $artist ) {
		$artist_id = handandvision_find_artist_id_by_terms( $artist['terms'] );
		$projects  = $artist_id ? handandvision_get_service_projects_for_artist( $service_id, $artist_id, 6 ) : array();

		$artists[ $index ]['id']       = $artist_id;
		$artists[ $index ]['link']     = $artist_id ? get_permalink( $artist_id ) : '';
		$artists[ $index ]['portrait'] = $artist_id ? handandvision_get_service_artist_portrait_url( $artist_id ) : '';
		$artists[ $index ]['deeper_text'] = '';
		$artists[ $index ]['projects'] = array_pad( array_slice( $projects, 0, 6 ), 6, array() );
	}

	return $artists;
}

/**
 * Backwards-compatible wrapper for the Digital Art showcase function name.
 *
 * @param int $service_id Service post ID.
 * @return array
 */
function handandvision_get_digital_art_artist_showcase( $service_id ) {
	return handandvision_get_service_artist_showcase( $service_id );
}

/**
 * Return artist sections saved by the custom service editor metabox.
 *
 * @param int $service_id Service post ID.
 * @return array
 */
function handandvision_get_service_artist_sections_meta( $service_id ) {
	$sections = get_post_meta( $service_id, '_hv_service_artist_sections', true );

	if ( ! is_array( $sections ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$sections,
			function ( $section ) {
				if ( ! is_array( $section ) ) {
					return false;
				}

				$artist_id = isset( $section['artist_id'] ) ? (int) $section['artist_id'] : 0;
				$image_ids = isset( $section['artwork_ids'] ) && is_array( $section['artwork_ids'] ) ? array_filter( array_map( 'absint', $section['artwork_ids'] ) ) : array();

				return $artist_id || ! empty( $image_ids );
			}
		)
	);
}

/**
 * Find an artist by title or slug fragments.
 *
 * @param array $terms Search terms.
 * @return int
 */
function handandvision_find_artist_id_by_terms( $terms ) {
	$artist_posts = get_posts(
		array(
			'post_type'              => 'artist',
			'posts_per_page'         => 100,
			'orderby'                => 'date',
			'order'                  => 'ASC',
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $artist_posts as $artist ) {
		$key = mb_strtolower( get_the_title( $artist->ID ) . ' ' . str_replace( '-', ' ', get_post_field( 'post_name', $artist->ID ) ) );
		foreach ( $terms as $term ) {
			if ( false !== mb_strpos( $key, mb_strtolower( $term ) ) ) {
				return (int) $artist->ID;
			}
		}
	}

	return 0;
}

/**
 * Get the best portrait URL for a service artist block.
 *
 * @param int $artist_id Artist post ID.
 * @return string
 */
function handandvision_get_service_artist_portrait_url( $artist_id ) {
	$portrait_id = function_exists( 'handandvision_get_artist_portrait_id' ) ? handandvision_get_artist_portrait_id( $artist_id ) : 0;
	if ( $portrait_id ) {
		return (string) wp_get_attachment_image_url( $portrait_id, 'thumbnail' );
	}

	return (string) get_the_post_thumbnail_url( $artist_id, 'thumbnail' );
}

/**
 * Collect service-specific projects first, then artist gallery items.
 *
 * @param int $service_id Service post ID.
 * @param int $artist_id  Artist post ID.
 * @param int $limit      Maximum projects.
 * @return array
 */
function handandvision_get_service_projects_for_artist( $service_id, $artist_id, $limit = 6 ) {
	$projects     = array();
	$project_rows = function_exists( 'get_field' ) ? get_field( 'service_project_gallery', $service_id ) : array();
	$project_rows = is_array( $project_rows ) ? $project_rows : array();

	foreach ( $project_rows as $row ) {
		$artist_obj = $row['artist'] ?? null;
		$row_artist = is_object( $artist_obj ) ? (int) $artist_obj->ID : (int) $artist_obj;
		$image      = $row['image'] ?? null;

		if ( $row_artist !== (int) $artist_id || ! is_array( $image ) || empty( $image['url'] ) ) {
			continue;
		}

		$projects[] = array(
			'image' => $image['sizes']['large'] ?? $image['url'],
			'full'  => $image['url'],
			'alt'   => $image['alt'] ?? get_the_title( $artist_id ),
			'title' => handandvision_strip_dashes_from_copy( (string) ( $row['project_title'] ?? '' ) ),
			'link'  => get_permalink( $artist_id ),
		);
	}

	if ( count( $projects ) < $limit && function_exists( 'handandvision_get_artist_gallery_items' ) ) {
		$artist_gallery = handandvision_get_artist_gallery_items( $artist_id, $limit );
		foreach ( $artist_gallery as $item ) {
			$image_url = isset( $item['image'] ) ? $item['image'] : ( $item['url'] ?? '' );
			if ( ! $image_url ) {
				continue;
			}

			$projects[] = array(
				'image' => $image_url,
				'full'  => ! empty( $item['image_id'] ) ? wp_get_attachment_image_url( (int) $item['image_id'], 'full' ) : $image_url,
				'alt'   => $item['alt'] ?? get_the_title( $artist_id ),
				'title' => handandvision_strip_dashes_from_copy( (string) ( $item['title'] ?? '' ) ),
				'link'  => get_permalink( $artist_id ),
			);

			if ( count( $projects ) >= $limit ) {
				break;
			}
		}
	}

	return array_slice( $projects, 0, $limit );
}

/**
 * Return normalized artist details for the service page.
 *
 * @param int $artist_id Artist post ID.
 * @return array
 */
function handandvision_get_service_artist_group_base( $artist_id ) {
	$portrait_id = function_exists( 'handandvision_get_artist_portrait_id' ) ? handandvision_get_artist_portrait_id( $artist_id ) : 0;
	$portrait    = $portrait_id ? wp_get_attachment_image_url( $portrait_id, 'thumbnail' ) : get_the_post_thumbnail_url( $artist_id, 'thumbnail' );
	$statement   = function_exists( 'get_field' ) ? get_field( 'artist_quote', $artist_id ) : '';
	if ( ! $statement && function_exists( 'get_field' ) ) {
		$statement = get_field( 'artist_discipline', $artist_id );
	}

	return array(
		'id'        => $artist_id,
		'name'      => handandvision_strip_dashes_from_copy( get_the_title( $artist_id ) ),
		'portrait'  => $portrait,
		'statement' => handandvision_strip_dashes_from_copy( (string) $statement ),
		'link'      => get_permalink( $artist_id ),
		'projects'  => array(),
	);
}
