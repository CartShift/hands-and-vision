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
		'post_type'      => 'gallery_item',
		'posts_per_page' => 12,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'post_status'    => 'publish',
		'meta_key'       => '_thumbnail_id',
	) );

	foreach ( $posts as $post_item ) {
		$img_id = get_post_thumbnail_id( $post_item->ID );
		$url = wp_get_attachment_image_url( $img_id, 'large' );
		if ( ! $url && ! $img_id ) continue;

		$artist_id = get_field( 'gallery_artist', $post_item->ID );
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
