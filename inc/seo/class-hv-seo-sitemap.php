<?php
/**
 * Hand and Vision SEO — Sitemap & Robots
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HV_SEO_Sitemap {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_filter( 'wp_sitemaps_post_types', array( $this, 'ensure_public_post_types' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'filter_sitemap_query_args' ), 10, 2 );
		add_filter( 'robots_txt', array( $this, 'enhance_robots_txt' ), 99, 2 );
	}

	public function ensure_public_post_types( $post_types ) {
		$theme_types = array( 'artist', 'service', 'gallery_item' );

		foreach ( $theme_types as $type ) {
			if ( post_type_exists( $type ) ) {
				$post_types[ $type ] = get_post_type_object( $type );
			}
		}

		if ( class_exists( 'WooCommerce' ) && post_type_exists( 'product' ) ) {
			$post_types['product'] = get_post_type_object( 'product' );
		}

		return $post_types;
	}

	public function filter_sitemap_query_args( $args, $post_type ) {
		$args['post_status'] = 'publish';

		if ( 'page' === $post_type ) {
			$exclude = $this->get_excluded_page_ids();
			if ( ! empty( $exclude ) ) {
				$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), $exclude );
			}
		}

		if ( 'product' === $post_type && taxonomy_exists( 'product_visibility' ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
					'operator' => 'NOT IN',
				),
			);
		}

		return $args;
	}

	public function enhance_robots_txt( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}

		$additions  = "\n# Hand and Vision\n";
		$additions .= "Disallow: /cart/\n";
		$additions .= "Disallow: /checkout/\n";
		$additions .= "Disallow: /my-account/\n";
		$additions .= "Disallow: /?s=\n";
		$additions .= "Disallow: /ai1wm-backups/\n";
		$additions .= "Disallow: /wp-content/ai1wm-backups/\n";

		if ( false === strpos( $output, 'Sitemap:' ) ) {
			$additions .= 'Sitemap: ' . home_url( '/wp-sitemap.xml' ) . "\n";
		}

		return $output . $additions;
	}

	private function get_excluded_page_ids() {
		$exclude = array();

		if ( class_exists( 'WooCommerce' ) ) {
			foreach ( array( 'cart', 'checkout', 'myaccount' ) as $page ) {
				$page_id = wc_get_page_id( $page );
				if ( $page_id > 0 ) {
					$exclude[] = $page_id;
				}
			}
		}

		return array_filter( array_unique( $exclude ) );
	}
}
