<?php
/**
 * Gallery helpers – single source for gallery data
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function handandvision_get_home_gallery_images( $front_page_id = 0 ) {
	$front_page_id = $front_page_id ?: (int) get_option( 'page_on_front' ) ?: get_the_ID();
	$out = array();

	$manual = get_field( 'gallery_images', $front_page_id );
	if ( ! empty( $manual ) ) {
		foreach ( $manual as $img ) {
			$url = is_array( $img ) ? ( $img['url'] ?? '' ) : '';
			if ( $url ) {
				$out[] = array(
					'url'     => $url,
					'title'   => is_array( $img ) ? ( $img['title'] ?? $img['alt'] ?? '' ) : '',
					'caption' => is_array( $img ) ? ( $img['caption'] ?? '' ) : '',
				);
			}
		}
		if ( ! empty( $out ) ) {
			return $out;
		}
	}

	$posts = get_posts( array(
		'post_type'              => 'gallery_item',
		'posts_per_page'         => 12,
		'orderby'                => 'menu_order date',
		'order'                  => 'ASC',
		'post_status'            => 'publish',
		'meta_key'               => '_thumbnail_id',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	) );

	foreach ( $posts as $post_item ) {
		$img_id = get_post_thumbnail_id( $post_item->ID );
		$url = wp_get_attachment_image_url( $img_id, 'large' );
		if ( ! $url ) continue;

		$artist_raw = get_field( 'gallery_artist', $post_item->ID );
		$artist_id = is_object( $artist_raw ) ? (int) $artist_raw->ID : (int) $artist_raw;
		$caption = $artist_id ? get_the_title( $artist_id ) : ( get_field( 'gallery_caption', $post_item->ID ) ?: '' );
		$out[] = array(
			'id'      => $img_id,
			'url'     => $url,
			'title'   => get_the_title( $post_item->ID ),
			'caption' => $caption,
		);
	}

	return $out;
}

function handandvision_normalize_gallery_grid_items( $acf_gallery, $placeholder_items = array() ) {
	if ( ! empty( $acf_gallery ) ) {
		$items = array();
		foreach ( $acf_gallery as $img ) {
			$url = is_array( $img ) ? ( $img['url'] ?? '' ) : '';
			if ( ! $url ) continue;
			$items[] = array(
				'url'     => $url,
				'src'     => $img['sizes']['medium_large'] ?? $url,
				'alt'     => $img['alt'] ?? '',
				'caption' => $img['caption'] ?? '',
				'image'   => $url,
				'title'   => $img['caption'] ?? ( $img['title'] ?? '' ),
				'link'    => '',
			);
		}
		return $items;
	}
	$out = array();
	foreach ( $placeholder_items as $item ) {
		$out[] = array(
			'placeholder' => true,
			'title'       => $item['title'] ?? '',
			'subtitle'    => $item['year'] ?? $item['subtitle'] ?? '',
		);
	}
	return $out;
}

/**
 * Build service gallery items from project repeater or legacy gallery field.
 *
 * @param int $service_id Service post ID.
 * @return array[]
 */
function handandvision_get_service_gallery_items( $service_id ) {
	$project_rows = get_field( 'service_project_gallery', $service_id );
	if ( is_array( $project_rows ) && ! empty( $project_rows ) ) {
		$items = array();
		foreach ( $project_rows as $row ) {
			$image = $row['image'] ?? null;
			if ( ! is_array( $image ) || empty( $image['url'] ) ) {
				continue;
			}
			$img_id = (int) ( $image['ID'] ?? 0 );
			if ( $img_id && empty( handandvision_filter_excluded_media_ids( array( $img_id ) ) ) ) {
				continue;
			}
			$artist_obj = $row['artist'] ?? null;
			$artist_name = is_object( $artist_obj ) ? $artist_obj->post_title : '';
			$project = trim( (string) ( $row['project_title'] ?? '' ) );
			$caption_parts = array_filter( array( $artist_name, $project ) );
			$items[] = array(
				'url'         => $image['url'],
				'src'         => $image['sizes']['medium_large'] ?? $image['url'],
				'image'       => $image['url'],
				'alt'         => $image['alt'] ?? '',
				'artist_name' => $artist_name,
				'project'     => $project,
				'title'       => implode( ' · ', $caption_parts ),
				'caption'     => implode( ' · ', $caption_parts ),
				'link'        => is_object( $artist_obj ) ? get_permalink( $artist_obj->ID ) : '',
			);
		}
		if ( ! empty( $items ) ) {
			return $items;
		}
	}

	$legacy = get_field( 'service_gallery', $service_id );
	return handandvision_normalize_gallery_grid_items( is_array( $legacy ) ? $legacy : array(), array() );
}

/**
 * Get gallery items associated with a specific artist
 *
 * @param int $artist_id The artist post ID
 * @param int $limit     Maximum number of items to return (-1 for all)
 * @return array Array of gallery item data
 */
function handandvision_get_artist_gallery_items( $artist_id, $limit = -1 ) {
	if ( ! $artist_id ) {
		return array();
	}

	$items = array();

	// Query gallery items where the gallery_artist field matches this artist
	// ACF post_object can store value as ID or serialized string depending on config
	$gallery_posts = get_posts( array(
		'post_type'              => 'gallery_item',
		'posts_per_page'         => $limit > 0 ? $limit : 100,
		'orderby'                => 'menu_order date',
		'order'                  => 'DESC',
		'post_status'            => 'publish',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'meta_query'             => array(
			'relation' => 'OR',
			array(
				'key'     => 'gallery_artist',
				'value'   => $artist_id,
				'compare' => '=',
			),
			array(
				'key'     => 'gallery_artist',
				'value'   => sprintf( '"%s"', $artist_id ),
				'compare' => 'LIKE',
			),
		),
	) );

	foreach ( $gallery_posts as $post_item ) {
		// Try to get gallery_image ACF field first
		$image = get_field( 'gallery_image', $post_item->ID );

		// Fallback to featured image if no gallery_image
		if ( ! $image || empty( $image['url'] ) ) {
			$thumb_id = get_post_thumbnail_id( $post_item->ID );
			if ( $thumb_id ) {
				$image = array(
					'ID'  => $thumb_id,
					'url' => wp_get_attachment_image_url( $thumb_id, 'large' ),
				);
			}
		}

		// Skip if no image available
		if ( ! $image || empty( $image['url'] ) ) {
			continue;
		}

		$caption = get_field( 'gallery_caption', $post_item->ID );
		$year = get_field( 'gallery_year', $post_item->ID );
		$project = get_field( 'gallery_project', $post_item->ID );

		$items[] = array(
			'id'       => $post_item->ID,
			'image_id' => $image['ID'] ?? get_post_thumbnail_id( $post_item->ID ),
			'url'      => $image['url'],
			'title'    => get_the_title( $post_item->ID ),
			'caption'  => $caption ?: '',
			'year'     => $year ?: '',
			'project'  => $project ?: '',
		);
	}

	return $items;
}
