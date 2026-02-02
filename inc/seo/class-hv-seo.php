<?php
/**
 * Hand and Vision SEO Module
 *
 * A lightweight, performance-focused SEO implementation for the theme.
 * Handles Meta Tags, Open Graph, Twitter Cards, and basic Schema.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class HV_SEO {

    /**
     * Instance wrapper
     */
    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Only run if no major SEO plugin is active to avoid conflicts
        if ( $this->is_seo_plugin_active() ) {
            return;
        }

        add_action( 'wp_head', array( $this, 'output_meta_tags' ), 1 );
        add_filter( 'document_title_parts', array( $this, 'optimize_title' ) );
        add_action( 'wp_head', array( $this, 'output_open_graph' ), 5 );
        add_action( 'wp_head', array( $this, 'output_twitter_cards' ), 6 );
        add_filter( 'wp_robots', array( $this, 'optimize_robots' ) );
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'ensure_alt_tags' ), 20, 2 );
    }

    /**
     * Ensure Alt Text Exists
     */
    public function ensure_alt_tags( $attr, $attachment ) {
        if ( empty( $attr['alt'] ) ) {
            $attr['alt'] = trim( strip_tags( get_the_title( $attachment->ID ) ) );
        }
        return $attr;
    }

    /**
     * Check for Yoast, RankMath, or All in One SEO
     */
    private function is_seo_plugin_active() {
        return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
    }

    /**
     * Better Title Generation
     */
    public function optimize_title( $title ) {
        if ( is_front_page() ) {
            $title['tagline'] = get_bloginfo( 'description', 'display' );
        }
        return $title;
    }

    /**
     * Smart Robots Meta
     */
    public function optimize_robots( $robots ) {
        $robots['max-snippet']       = '-1';
        $robots['max-image-preview'] = 'large';
        $robots['max-video-preview'] = '-1';
        return $robots;
    }

    /**
     * Main Meta Tags (Description, Canonical)
     */
    public function output_meta_tags() {
        // Description
        $description = $this->get_description();
        if ( $description ) {
            echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
        }

        // Canonical
        echo '<link rel="canonical" href="' . esc_url( $this->get_current_url() ) . '" />' . "\n";
    }

    /**
     * Open Graph Tags
     */
    public function output_open_graph() {
        $type = is_singular() ? 'article' : 'website';
        $locale = is_rtl() ? 'he_IL' : 'en_US';

        echo '<!-- Hand and Vision SEO (Open Graph) -->' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";
        echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $this->get_description() ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $this->get_current_url() ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";

        $image = $this->get_social_image();
        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
            echo '<meta property="og:image:width" content="1200" />' . "\n";
            echo '<meta property="og:image:height" content="630" />' . "\n";
        }

        echo '<meta property="og:updated_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
    }

    /**
     * Twitter Card Tags
     */
    public function output_twitter_cards() {
        $card_type = 'summary_large_image';

        echo '<!-- Hand and Vision SEO (Twitter) -->' . "\n";
        echo '<meta name="twitter:card" content="' . esc_attr( $card_type ) . '" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( wp_get_document_title() ) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $this->get_description() ) . '" />' . "\n";

        $image = $this->get_social_image();
        if ( $image ) {
            echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
        }
    }

    /**
     * Helper: Get current URL
     */
    private function get_current_url() {
        global $wp;
        return home_url( add_query_arg( array(), $wp->request ) );
    }

    /**
     * Helper: Get optimized description
     */
    private function get_description() {
        $description = '';

        if ( is_singular() ) {
            $post = get_post();
            if ( ! empty( $post->post_excerpt ) ) {
                $description = $post->post_excerpt;
            } else {
                $description = wp_trim_words( $post->post_content, 25 );
            }
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $description = term_description();
        } elseif ( is_post_type_archive() ) {
            $description = get_the_post_type_description();
        }

        if ( empty( $description ) && is_front_page() ) {
            $description = get_bloginfo( 'description' );
        }

        return strip_tags( trim( $description ) );
    }

    /**
     * Helper: Get social share image
     */
    private function get_social_image() {
        // 1. Featured Image
        if ( is_singular() && has_post_thumbnail() ) {
            return get_the_post_thumbnail_url( null, 'large' );
        }

        // 2. Fallback to Site Logo or Hero Poster
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            return wp_get_attachment_image_url( $logo_id, 'full' );
        }

        // 3. Fallback to ACF Global Hero Poster (if set)
        if ( function_exists('get_field') ) {
            $hero = get_field('hero_poster', 'option'); // Using 'option' if you have global settings, else check front page
            if ( ! $hero ) {
                $front_page_id = get_option( 'page_on_front' );
                $hero = get_field('hero_poster', $front_page_id);
            }

            if ( is_array($hero) && isset($hero['url']) ) return $hero['url'];
            if ( is_string($hero) && !empty($hero) ) return $hero;
        }

        return '';
    }

}

// Initialize
HV_SEO::get_instance();
