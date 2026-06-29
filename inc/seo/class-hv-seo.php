<?php
/**
 * Hand and Vision SEO Module
 *
 * Meta tags, Open Graph, Twitter Cards, hreflang, titles, and robots.
 * Schema and sitemap are handled by companion classes in this directory.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HV_SEO {

	private static $instance = null;

	const DESCRIPTION_MAX = 160;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function is_seo_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
	}

	public function __construct() {
		if ( self::is_seo_plugin_active() ) {
			return;
		}

		require_once __DIR__ . '/class-hv-seo-schema.php';
		require_once __DIR__ . '/class-hv-seo-sitemap.php';
		HV_SEO_Schema::get_instance();
		HV_SEO_Sitemap::get_instance();

		add_action( 'wp_head', array( $this, 'output_meta_tags' ), 1 );
		add_action( 'wp_head', array( $this, 'output_open_graph' ), 5 );
		add_action( 'wp_head', array( $this, 'output_twitter_cards' ), 6 );
		add_action( 'wp_head', array( $this, 'output_hreflang' ), 7 );

		add_filter( 'document_title_parts', array( $this, 'optimize_title' ) );
		add_filter( 'pre_get_document_title', array( $this, 'filter_document_title' ) );
		add_filter( 'get_canonical_url', array( $this, 'filter_canonical_url' ) );
		add_filter( 'wp_robots', array( $this, 'optimize_robots' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'ensure_alt_tags' ), 20, 2 );
	}

	public function ensure_alt_tags( $attr, $attachment ) {
		if ( empty( $attr['alt'] ) ) {
			$attr['alt'] = trim( wp_strip_all_tags( get_the_title( $attachment->ID ) ) );
		}
		return $attr;
	}

	public function optimize_title( $title ) {
		if ( is_front_page() ) {
			$title['tagline'] = $this->get_site_description();
			return $title;
		}

		unset( $title['tagline'] );

		if ( is_singular() ) {
			$custom = $this->get_localized_title();
			if ( $custom ) {
				$title['title'] = $custom;
			}
		} elseif ( is_post_type_archive() ) {
			$title['title'] = $this->get_archive_title();
		} elseif ( is_search() ) {
			$title['title'] = handandvision_is_hebrew()
				? 'חיפוש: ' . get_search_query()
				: 'Search: ' . get_search_query();
		}

		return $title;
	}

	public function filter_document_title( $title ) {
		if ( is_front_page() || is_singular() || is_post_type_archive() ) {
			return $title;
		}

		$site = get_bloginfo( 'name' );
		if ( $site && false === strpos( $title, $site ) ) {
			return $title . ' | ' . $site;
		}

		return $title;
	}

	public function filter_canonical_url( $canonical_url ) {
		if ( ! $canonical_url ) {
			return $canonical_url;
		}

		return remove_query_arg( array( 'lang', 'add-to-cart', 'removed_item' ), $canonical_url );
	}

	public function optimize_robots( $robots ) {
		if ( $this->should_noindex() ) {
			$robots['noindex']   = true;
			$robots['nofollow']  = true;
			unset( $robots['max-snippet'], $robots['max-image-preview'], $robots['max-video-preview'] );
			return $robots;
		}

		$robots['max-snippet']       = '-1';
		$robots['max-image-preview'] = 'large';
		$robots['max-video-preview'] = '-1';

		return $robots;
	}

	public function output_meta_tags() {
		$description = $this->get_description();
		if ( $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}

		echo '<meta name="author" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
	}

	public function output_open_graph() {
		$type   = $this->get_og_type();
		$locale = handandvision_is_hebrew() ? 'he_IL' : 'en_US';

		echo '<!-- Hand and Vision SEO (Open Graph) -->' . "\n";
		echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $this->get_description() ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $this->get_current_url() ) . '" />' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";

		$image = $this->get_social_image();
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image['url'] ) . '" />' . "\n";
			if ( ! empty( $image['width'] ) ) {
				echo '<meta property="og:image:width" content="' . esc_attr( $image['width'] ) . '" />' . "\n";
			}
			if ( ! empty( $image['height'] ) ) {
				echo '<meta property="og:image:height" content="' . esc_attr( $image['height'] ) . '" />' . "\n";
			}
			if ( ! empty( $image['alt'] ) ) {
				echo '<meta property="og:image:alt" content="' . esc_attr( $image['alt'] ) . '" />' . "\n";
			}
		}

		if ( is_singular() ) {
			echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '" />' . "\n";
			echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
			echo '<meta property="og:updated_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
		}

		if ( function_exists( 'is_product' ) && is_product() ) {
			$this->output_product_og_tags();
		}
	}

	public function output_twitter_cards() {
		echo '<!-- Hand and Vision SEO (Twitter) -->' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( wp_get_document_title() ) . '" />' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $this->get_description() ) . '" />' . "\n";

		$image = $this->get_social_image();
		if ( $image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image['url'] ) . '" />' . "\n";
			if ( ! empty( $image['alt'] ) ) {
				echo '<meta name="twitter:image:alt" content="' . esc_attr( $image['alt'] ) . '" />' . "\n";
			}
		}
	}

	public function output_hreflang() {
		if ( function_exists( 'icl_get_languages' ) || function_exists( 'pll_the_languages' ) ) {
			return;
		}

		$base_url = remove_query_arg( 'lang', $this->get_current_url() );
		$he_url   = add_query_arg( 'lang', 'he', $base_url );
		$en_url   = add_query_arg( 'lang', 'en', $base_url );

		echo '<link rel="alternate" hreflang="he" href="' . esc_url( $he_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="en" href="' . esc_url( $en_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $he_url ) . '" />' . "\n";
	}

	private function output_product_og_tags() {
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) {
			return;
		}

		echo '<meta property="product:brand" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";

		$artist_id = function_exists( 'get_field' ) ? get_field( 'product_artist', $product->get_id() ) : 0;
		if ( is_object( $artist_id ) ) {
			$artist_id = $artist_id->ID;
		}
		if ( $artist_id ) {
			echo '<meta property="product:brand" content="' . esc_attr( get_the_title( $artist_id ) ) . '" />' . "\n";
		}

		if ( $product->get_price() && ! ( function_exists( 'get_field' ) && get_field( 'product_price_request', $product->get_id() ) ) ) {
			echo '<meta property="product:price:amount" content="' . esc_attr( wc_format_decimal( $product->get_price(), wc_get_price_decimals() ) ) . '" />' . "\n";
			echo '<meta property="product:price:currency" content="' . esc_attr( get_woocommerce_currency() ) . '" />' . "\n";
		}

		if ( $product->is_in_stock() ) {
			echo '<meta property="product:availability" content="in stock" />' . "\n";
		}
	}

	private function should_noindex() {
		if ( is_404() || is_search() ) {
			return true;
		}

		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return true;
		}
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return true;
		}
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return true;
		}

		return false;
	}

	private function get_og_type() {
		if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product';
		}
		if ( is_singular() && ! is_page() ) {
			return 'article';
		}
		return 'website';
	}

	private function get_current_url() {
		if ( is_singular() ) {
			return get_permalink();
		}
		if ( is_front_page() ) {
			return home_url( '/' );
		}
		if ( is_post_type_archive() ) {
			return get_post_type_archive_link( get_query_var( 'post_type' ) );
		}
		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				return get_term_link( $term );
			}
		}
		if ( get_query_var( 'paged' ) > 1 ) {
			return get_pagenum_link( get_query_var( 'paged' ) );
		}

		global $wp;
		return home_url( add_query_arg( array(), $wp->request ) );
	}

	private function get_description() {
		$description = '';

		if ( is_singular() ) {
			$description = $this->get_singular_description();
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$description = term_description();
		} elseif ( is_post_type_archive() ) {
			$description = $this->get_archive_description();
		} elseif ( is_search() ) {
			$description = handandvision_is_hebrew()
				? 'תוצאות חיפוש עבור: ' . get_search_query()
				: 'Search results for: ' . get_search_query();
		} elseif ( is_front_page() ) {
			$description = $this->get_home_description();
		}

		if ( empty( $description ) ) {
			$description = $this->get_site_description();
		}

		return $this->truncate( wp_strip_all_tags( trim( $description ) ) );
	}

	private function get_singular_description() {
		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );

		if ( 'product' === $post_type && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$short = $product->get_short_description();
				if ( $short ) {
					return $short;
				}
			}
		}

		if ( 'artist' === $post_type && function_exists( 'get_field' ) ) {
			$bio = get_field( 'artist_biography', $post_id );
			if ( $bio ) {
				return wp_trim_words( wp_strip_all_tags( $bio ), 30 );
			}
		}

		if ( 'service' === $post_type ) {
			if ( ! handandvision_is_hebrew() && function_exists( 'get_field' ) ) {
				$en_desc = get_field( 'service_desc_en', $post_id );
				if ( $en_desc ) {
					return $en_desc;
				}
			}
		}

		if ( 'gallery_item' === $post_type && function_exists( 'get_field' ) ) {
			$caption = get_field( 'gallery_caption', $post_id );
			if ( $caption ) {
				return $caption;
			}
		}

		$post = get_post( $post_id );
		if ( ! empty( $post->post_excerpt ) ) {
			return $post->post_excerpt;
		}

		return wp_trim_words( $post->post_content, 25 );
	}

	private function get_home_description() {
		$front_page_id = (int) get_option( 'page_on_front' );
		if ( $front_page_id && function_exists( 'get_field' ) ) {
			$intro = get_field( 'intro_text', $front_page_id );
			if ( $intro ) {
				return wp_trim_words( wp_strip_all_tags( $intro ), 30 );
			}
		}

		return $this->get_site_description();
	}

	private function get_archive_description() {
		$is_hebrew = handandvision_is_hebrew();
		$post_type = get_query_var( 'post_type' );

		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		$descriptions = array(
			'artist'       => $is_hebrew
				? 'גלריית האמנים של Hand and Vision — יוצרים, צלמים ואמנים עכשוויים.'
				: 'Meet the artists of Hand and Vision — contemporary creators, photographers, and makers.',
			'service'      => $is_hebrew
				? 'שירותי אמנות וייעוץ — הפקות, אוצרות, ייעוץ לאוספים ועוד.'
				: 'Art services and consulting — production, curation, collection advisory, and more.',
			'gallery_item' => $is_hebrew
				? 'גלריית עבודות נבחרות — פרויקטים, תערוכות ויצירות מהקולקטיב.'
				: 'Selected works gallery — projects, exhibitions, and pieces from the collective.',
			'product'      => $is_hebrew
				? 'חנות יצירות מקוריות — אמנות, עיצוב ופריטים ייחודיים לאוסף.'
				: 'Shop original artworks — art, design, and unique collectible pieces.',
		);

		if ( isset( $descriptions[ $post_type ] ) ) {
			return $descriptions[ $post_type ];
		}

		$desc = get_the_post_type_description();
		return $desc ?: $this->get_site_description();
	}

	private function get_archive_title() {
		$is_hebrew = handandvision_is_hebrew();
		$post_type = get_query_var( 'post_type' );

		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		$titles = array(
			'artist'       => $is_hebrew ? 'אמנים' : 'Artists',
			'service'      => $is_hebrew ? 'שירותים' : 'Services',
			'gallery_item' => $is_hebrew ? 'גלריה' : 'Gallery',
			'product'      => $is_hebrew ? 'חנות' : 'Shop',
		);

		return $titles[ $post_type ] ?? post_type_archive_title( '', false );
	}

	private function get_localized_title( $post_id = null ) {
		$post_id = $post_id ?: get_the_ID();

		if ( ! handandvision_is_hebrew() && function_exists( 'get_field' ) ) {
			$post_type = get_post_type( $post_id );
			$map       = array(
				'product' => 'product_title_en',
				'service' => 'service_title_en',
			);
			if ( isset( $map[ $post_type ] ) ) {
				$en = get_field( $map[ $post_type ], $post_id );
				if ( $en ) {
					return $en;
				}
			}
		}

		return get_the_title( $post_id );
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

	private function get_social_image() {
		if ( is_singular() && has_post_thumbnail() ) {
			return $this->image_data_from_id( get_post_thumbnail_id() );
		}

		if ( is_singular( 'artist' ) && function_exists( 'get_field' ) ) {
			$portrait = get_field( 'artist_portrait', get_the_ID() );
			if ( is_array( $portrait ) && ! empty( $portrait['ID'] ) ) {
				return $this->image_data_from_id( $portrait['ID'], get_the_title() );
			}
		}

		if ( is_singular( 'gallery_item' ) && function_exists( 'get_field' ) ) {
			$image = get_field( 'gallery_image', get_the_ID() );
			if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
				return $this->image_data_from_id( $image['ID'], get_the_title() );
			}
		}

		if ( is_front_page() && function_exists( 'get_field' ) ) {
			$front_page_id = (int) get_option( 'page_on_front' );
			foreach ( array( 'hero_image', 'hero_poster' ) as $field ) {
				$hero = get_field( $field, $front_page_id );
				if ( is_array( $hero ) && ! empty( $hero['ID'] ) ) {
					return $this->image_data_from_id( $hero['ID'], get_bloginfo( 'name' ) );
				}
			}
		}

		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			return $this->image_data_from_id( $logo_id, get_bloginfo( 'name' ) );
		}

		return null;
	}

	private function image_data_from_id( $attachment_id, $alt = '' ) {
		$src = wp_get_attachment_image_src( $attachment_id, 'large' );
		if ( ! $src ) {
			return null;
		}

		if ( ! $alt ) {
			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			if ( ! $alt ) {
				$alt = get_the_title( $attachment_id );
			}
		}

		return array(
			'url'    => $src[0],
			'width'  => $src[1],
			'height' => $src[2],
			'alt'    => $alt,
		);
	}

	private function truncate( $text ) {
		if ( mb_strlen( $text ) <= self::DESCRIPTION_MAX ) {
			return $text;
		}

		$truncated = mb_substr( $text, 0, self::DESCRIPTION_MAX );
		$last_space = mb_strrpos( $truncated, ' ' );
		if ( false !== $last_space && $last_space > self::DESCRIPTION_MAX - 30 ) {
			$truncated = mb_substr( $truncated, 0, $last_space );
		}

		return rtrim( $truncated, '.,;:' ) . '…';
	}
}

HV_SEO::get_instance();
