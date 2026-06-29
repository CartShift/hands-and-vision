<?php
/**
 * Hand and Vision SEO — JSON-LD Structured Data
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HV_SEO_Schema {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_head', array( $this, 'output_json_ld' ), 20 );

		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_structured_data_product', array( $this, 'enhance_product_schema' ), 10, 2 );
		}
	}

	public function output_json_ld() {
		$graph = array();

		$organization = $this->get_organization_schema();
		if ( $organization ) {
			$graph[] = $organization;
		}

		$website = $this->get_website_schema();
		if ( $website ) {
			$graph[] = $website;
		}

		$page_schema = $this->get_page_schema();
		if ( $page_schema ) {
			$graph[] = $page_schema;
		}

		if ( empty( $graph ) ) {
			return;
		}

		$payload = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}

	private function org_id() {
		return trailingslashit( home_url( '/' ) ) . '#organization';
	}

	private function website_id() {
		return trailingslashit( home_url( '/' ) ) . '#website';
	}

	private function get_organization_schema() {
		$logo = function_exists( 'handandvision_get_logo_url' ) ? handandvision_get_logo_url() : '';
		$name = get_bloginfo( 'name' ) ?: 'Hand and Vision';

		$schema = array(
			'@type'       => array( 'Organization', 'ArtGallery' ),
			'@id'         => $this->org_id(),
			'name'        => $name,
			'url'         => home_url( '/' ),
			'description' => $this->get_site_description(),
		);

		if ( $logo ) {
			$schema['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo,
			);
		}

		$same_as = $this->get_social_profiles();
		if ( ! empty( $same_as ) ) {
			$schema['sameAs'] = $same_as;
		}

		return $schema;
	}

	private function get_website_schema() {
		return array(
			'@type'           => 'WebSite',
			'@id'             => $this->website_id(),
			'url'             => home_url( '/' ),
			'name'            => get_bloginfo( 'name' ) ?: 'Hand and Vision',
			'description'     => $this->get_site_description(),
			'publisher'       => array( '@id' => $this->org_id() ),
			'inLanguage'      => array( 'he-IL', 'en-US' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	private function get_page_schema() {
		if ( ! is_singular() ) {
			return null;
		}

		$post_type = get_post_type();
		$post_id   = get_the_ID();

		switch ( $post_type ) {
			case 'artist':
				return $this->get_person_schema( $post_id );
			case 'service':
				return $this->get_service_schema( $post_id );
			case 'gallery_item':
				return $this->get_visual_artwork_schema( $post_id );
			case 'product':
				return null;
			default:
				return $this->get_webpage_schema( $post_id );
		}
	}

	private function get_person_schema( $post_id ) {
		$name = get_the_title( $post_id );
		$url  = get_permalink( $post_id );
		$bio  = function_exists( 'get_field' ) ? get_field( 'artist_biography', $post_id ) : '';

		$schema = array(
			'@type'       => 'Person',
			'@id'         => $url . '#person',
			'name'        => $name,
			'url'         => $url,
			'description' => $this->clean_text( $bio ?: wp_trim_words( get_post_field( 'post_content', $post_id ), 40 ) ),
		);

		$image = $this->get_artist_image( $post_id );
		if ( $image ) {
			$schema['image'] = $image;
		}

		$same_as = array_filter( array(
			function_exists( 'get_field' ) ? get_field( 'artist_instagram', $post_id ) : '',
			function_exists( 'get_field' ) ? get_field( 'artist_facebook', $post_id ) : '',
			function_exists( 'get_field' ) ? get_field( 'artist_website', $post_id ) : '',
		) );

		if ( ! empty( $same_as ) ) {
			$schema['sameAs'] = array_values( $same_as );
		}

		return $schema;
	}

	private function get_service_schema( $post_id ) {
		$name = $this->get_localized_title( $post_id );
		$url  = get_permalink( $post_id );
		$desc = get_post_field( 'post_excerpt', $post_id );

		if ( empty( $desc ) ) {
			$desc = wp_trim_words( get_post_field( 'post_content', $post_id ), 40 );
		}

		if ( ! handandvision_is_hebrew() && function_exists( 'get_field' ) ) {
			$en_desc = get_field( 'service_desc_en', $post_id );
			if ( $en_desc ) {
				$desc = $en_desc;
			}
		}

		$schema = array(
			'@type'       => 'Service',
			'@id'         => $url . '#service',
			'name'        => $name,
			'url'         => $url,
			'description' => $this->clean_text( $desc ),
			'provider'    => array( '@id' => $this->org_id() ),
		);

		$image = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( $image ) {
			$schema['image'] = $image;
		}

		return $schema;
	}

	private function get_visual_artwork_schema( $post_id ) {
		$name  = get_the_title( $post_id );
		$url   = get_permalink( $post_id );
		$image = $this->get_gallery_image( $post_id );

		$schema = array(
			'@type'   => 'VisualArtwork',
			'@id'     => $url . '#artwork',
			'name'    => $name,
			'url'     => $url,
			'creator' => $this->get_gallery_artist_ref( $post_id ),
		);

		if ( $image ) {
			$schema['image'] = $image;
		}

		$caption = function_exists( 'get_field' ) ? get_field( 'gallery_caption', $post_id ) : '';
		if ( $caption ) {
			$schema['description'] = $caption;
		}

		return array_filter( $schema );
	}

	private function get_webpage_schema( $post_id ) {
		return array(
			'@type'       => 'WebPage',
			'@id'         => get_permalink( $post_id ) . '#webpage',
			'url'         => get_permalink( $post_id ),
			'name'        => get_the_title( $post_id ),
			'description' => $this->clean_text( wp_trim_words( get_post_field( 'post_content', $post_id ), 40 ) ),
			'isPartOf'    => array( '@id' => $this->website_id() ),
		);
	}

	public function enhance_product_schema( $markup, $product ) {
		if ( ! is_array( $markup ) || ! $product instanceof WC_Product ) {
			return $markup;
		}

		$artist_id = function_exists( 'get_field' ) ? get_field( 'product_artist', $product->get_id() ) : 0;
		if ( is_object( $artist_id ) ) {
			$artist_id = $artist_id->ID;
		}

		if ( $artist_id ) {
			$artist_name = get_the_title( $artist_id );
			$markup['brand'] = array(
				'@type' => 'Brand',
				'name'  => $artist_name,
			);
			$markup['creator'] = array(
				'@type' => 'Person',
				'name'  => $artist_name,
				'url'   => get_permalink( $artist_id ),
			);
		}

		$medium = function_exists( 'get_field' ) ? get_field( 'product_medium', $product->get_id() ) : '';
		if ( $medium ) {
			$markup['material'] = $medium;
		}

		$is_unique = function_exists( 'get_field' ) ? get_field( 'product_unique', $product->get_id() ) : false;
		if ( $is_unique ) {
			$markup['itemCondition'] = 'https://schema.org/NewCondition';
		}

		return $markup;
	}

	private function get_localized_title( $post_id ) {
		if ( ! handandvision_is_hebrew() && function_exists( 'get_field' ) ) {
			$post_type = get_post_type( $post_id );
			$en_field  = $post_type . '_title_en';
			if ( 'product' === $post_type ) {
				$en_field = 'product_title_en';
			}
			$en_title = get_field( $en_field, $post_id );
			if ( $en_title ) {
				return $en_title;
			}
		}
		return get_the_title( $post_id );
	}

	private function get_artist_image( $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			$portrait = get_field( 'artist_portrait', $post_id );
			if ( is_array( $portrait ) && ! empty( $portrait['url'] ) ) {
				return $portrait['url'];
			}
		}
		return get_the_post_thumbnail_url( $post_id, 'large' ) ?: '';
	}

	private function get_gallery_image( $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			$image = get_field( 'gallery_image', $post_id );
			if ( is_array( $image ) && ! empty( $image['url'] ) ) {
				return $image['url'];
			}
		}
		return get_the_post_thumbnail_url( $post_id, 'large' ) ?: '';
	}

	private function get_gallery_artist_ref( $post_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return null;
		}

		$artist = get_field( 'gallery_artist', $post_id );
		if ( is_object( $artist ) ) {
			return array(
				'@type' => 'Person',
				'name'  => get_the_title( $artist->ID ),
				'url'   => get_permalink( $artist->ID ),
			);
		}

		return null;
	}

	private function get_site_description() {
		$tagline = get_bloginfo( 'description', 'display' );
		if ( $tagline ) {
			return $tagline;
		}

		return handandvision_is_hebrew()
			? 'קולקטיב אמנות יוקרתי — יצירות מקוריות, שירותי אמנות וגלריה.'
			: 'Premium art collective — original artworks, art services, and gallery.';
	}

	private function get_social_profiles() {
		if ( ! function_exists( 'get_field' ) ) {
			return array();
		}

		$contact_page = function_exists( 'handandvision_get_contact_url' ) ? handandvision_get_contact_url() : '';
		$contact_id   = $contact_page ? url_to_postid( $contact_page ) : 0;

		if ( ! $contact_id ) {
			return array();
		}

		return array_values( array_filter( array(
			get_field( 'social_instagram', $contact_id ),
			get_field( 'social_facebook', $contact_id ),
			get_field( 'social_linkedin', $contact_id ),
			get_field( 'social_youtube', $contact_id ),
		) ) );
	}

	private function clean_text( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = preg_replace( '/\s+/', ' ', $text );
		return trim( $text );
	}
}
