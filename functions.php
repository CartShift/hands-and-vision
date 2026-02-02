<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '4.12.1' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );
define( 'ASTRA_THEME_ORG_VERSION', file_exists( ASTRA_THEME_DIR . 'inc/w-org-version.php' ) );

/**
 * Hand and Vision Custom Theme Version
 */
define( 'HV_THEME_VERSION', '3.3.0' );

/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '4.12.0' );

/**
 * Load in-house compatibility.
 */
if ( ASTRA_THEME_ORG_VERSION ) {
	require_once ASTRA_THEME_DIR . 'inc/w-org-version.php';
}

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

define( 'ASTRA_WEBSITE_BASE_URL', 'https://wpastra.com' );

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';

/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
	require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/lib/docs/class-astra-docs-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-wp-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-command-palette.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/block-editor-compatibility.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/content-background.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/dark-mode.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-global-palette.php';

// Enable NPS Survey only if the starter templates version is < 4.3.7 or > 4.4.4 to prevent fatal error.
if ( ! defined( 'ASTRA_SITES_VER' ) || version_compare( ASTRA_SITES_VER, '4.3.7', '<' ) || version_compare( ASTRA_SITES_VER, '4.4.4', '>' ) ) {
	// NPS Survey Integration
	require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-notice.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-survey.php';
}

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-memory-limit-notice.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

/* Setup API */
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-learn.php';
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-api-init.php';

if ( is_admin() ) {
	/**
	 * Admin Menu Settings
	 */
	require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
	require_once ASTRA_THEME_DIR . 'admin/class-astra-admin-loader.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/astra-notices/class-astra-notices.php';
}

/**
 * Metabox additions.
 */
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-elementor-editor-settings.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/posts-structures/class-astra-post-structures.php';
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gutenberg.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/surecart/class-astra-surecart.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-starter-content.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-buddypress.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/scroll-to-top/class-astra-scroll-to-top.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

/**
 * Hand and Vision Custom Breadcrumbs
 * Smart breadcrumb system for all page types
 */
require_once ASTRA_THEME_DIR . 'inc/breadcrumb-helpers.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymous functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';

/**
 * ==========================================================================
 * HAND AND VISION - Custom Theme Additions
 * ==========================================================================
 */

if ( ! function_exists( 'get_field' ) ) {
	function get_field( $selector, $post_id = false ) {
		if ( is_admin() && current_user_can( 'activate_plugins' ) && ! has_action( 'admin_notices', 'handandvision_acf_missing_notice' ) ) {
			add_action( 'admin_notices', 'handandvision_acf_missing_notice' );
		}
		return '';
	}
	function handandvision_acf_missing_notice() {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Hand and Vision theme requires the Advanced Custom Fields (ACF) plugin. Please install and activate ACF.', 'astra' ) . '</p></div>';
	}
}

/**
 * Include ACF field configurations only if ACF is active
 */
function handandvision_load_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    $acf_files = array(
        'acf-homepage-fields.php',
        'acf-artist-fields.php',
        'acf-service-fields.php',
        'acf-gallery-fields.php',
        'acf-product-fields.php',
        'acf-contact-fields.php',
    );

    foreach ( $acf_files as $file ) {
        $file_path = ASTRA_THEME_DIR . 'inc/' . $file;
        if ( file_exists( $file_path ) ) {
            require_once $file_path;
        }
    }
}
add_action( 'acf/init', 'handandvision_load_acf_fields', 5 );

/**
 * Register Custom Post Types for Hand and Vision
 * Labels in Hebrew, slugs in English
 */
function handandvision_register_post_types() {
    // Artist Post Type - אמנים
    register_post_type( 'artist', array(
        'labels' => array(
            'name'                  => 'אמנים',
            'singular_name'         => 'אמן',
            'add_new'               => 'הוסף אמן חדש',
            'add_new_item'          => 'הוסף אמן חדש',
            'edit_item'             => 'ערוך אמן',
            'new_item'              => 'אמן חדש',
            'view_item'             => 'צפה באמן',
            'view_items'            => 'צפה באמנים',
            'search_items'          => 'חפש אמנים',
            'not_found'             => 'לא נמצאו אמנים',
            'not_found_in_trash'    => 'לא נמצאו אמנים בפח',
            'all_items'             => 'כל האמנים',
            'archives'              => 'ארכיון אמנים',
            'attributes'            => 'מאפייני אמן',
            'insert_into_item'      => 'הכנס לאמן',
            'uploaded_to_this_item' => 'הועלה לאמן זה',
            'featured_image'        => 'תמונת אמן',
            'set_featured_image'    => 'הגדר תמונת אמן',
            'remove_featured_image' => 'הסר תמונת אמן',
            'use_featured_image'    => 'השתמש כתמונת אמן',
            'menu_name'             => 'אמנים',
            'filter_items_list'     => 'סנן רשימת אמנים',
            'items_list_navigation' => 'ניווט רשימת אמנים',
            'items_list'            => 'רשימת אמנים',
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'artists' ),
        'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
        'menu_icon'          => 'dashicons-groups',
        'show_in_rest'       => true,
    ) );

    // Service Post Type - שירותים
    register_post_type( 'service', array(
        'labels' => array(
            'name'                  => 'שירותים',
            'singular_name'         => 'שירות',
            'add_new'               => 'הוסף שירות חדש',
            'add_new_item'          => 'הוסף שירות חדש',
            'edit_item'             => 'ערוך שירות',
            'new_item'              => 'שירות חדש',
            'view_item'             => 'צפה בשירות',
            'view_items'            => 'צפה בשירותים',
            'search_items'          => 'חפש שירותים',
            'not_found'             => 'לא נמצאו שירותים',
            'not_found_in_trash'    => 'לא נמצאו שירותים בפח',
            'all_items'             => 'כל השירותים',
            'archives'              => 'ארכיון שירותים',
            'attributes'            => 'מאפייני שירות',
            'insert_into_item'      => 'הכנס לשירות',
            'uploaded_to_this_item' => 'הועלה לשירות זה',
            'featured_image'        => 'תמונת שירות',
            'set_featured_image'    => 'הגדר תמונת שירות',
            'remove_featured_image' => 'הסר תמונת שירות',
            'use_featured_image'    => 'השתמש כתמונת שירות',
            'menu_name'             => 'שירותים',
            'filter_items_list'     => 'סנן רשימת שירותים',
            'items_list_navigation' => 'ניווט רשימת שירותים',
            'items_list'            => 'רשימת שירותים',
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'services' ),
        'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
        'menu_icon'          => 'dashicons-art',
        'show_in_rest'       => true,
    ) );

    // Gallery Item Post Type - גלריה
    register_post_type( 'gallery_item', array(
        'labels' => array(
            'name'                  => 'גלריה',
            'singular_name'         => 'פריט גלריה',
            'add_new'               => 'הוסף פריט חדש',
            'add_new_item'          => 'הוסף פריט גלריה חדש',
            'edit_item'             => 'ערוך פריט גלריה',
            'new_item'              => 'פריט גלריה חדש',
            'view_item'             => 'צפה בפריט',
            'view_items'            => 'צפה בגלריה',
            'search_items'          => 'חפש בגלריה',
            'not_found'             => 'לא נמצאו פריטים',
            'not_found_in_trash'    => 'לא נמצאו פריטים בפח',
            'all_items'             => 'כל הפריטים',
            'archives'              => 'ארכיון הגלריה',
            'attributes'            => 'מאפייני פריט',
            'insert_into_item'      => 'הכנס לפריט',
            'uploaded_to_this_item' => 'הועלה לפריט זה',
            'featured_image'        => 'תמונת פריט',
            'set_featured_image'    => 'הגדר תמונת פריט',
            'remove_featured_image' => 'הסר תמונת פריט',
            'use_featured_image'    => 'השתמש כתמונת פריט',
            'menu_name'             => 'גלריה',
            'filter_items_list'     => 'סנן רשימת גלריה',
            'items_list_navigation' => 'ניווט רשימת גלריה',
            'items_list'            => 'רשימת גלריה',
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'gallery' ),
        'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
        'menu_icon'          => 'dashicons-format-gallery',
        'show_in_rest'       => true,
    ) );
}
add_action( 'init', 'handandvision_register_post_types' );

/**
 * Add custom image sizes for Hand and Vision
 */
function handandvision_image_sizes() {
    add_image_size( 'hv-hero', 1920, 800, true );
    add_image_size( 'hv-gallery', 600, 400, true );
    add_image_size( 'hv-artist', 400, 400, true );
    add_image_size( 'hv-product', 600, 800, true );
}
add_action( 'after_setup_theme', 'handandvision_image_sizes' );

/**
 * ============================================================================
 * WOOCOMMERCE INTEGRATION
 * ============================================================================
 */

/**
 * Declare WooCommerce support
 */
function handandvision_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'handandvision_woocommerce_support' );

/**
 * Add custom product meta box for artist relationship
 */
function handandvision_add_product_artist_metabox() {
    add_meta_box(
        'handandvision_product_artist',
        handandvision_is_hebrew() ? 'אמן/ית היצירה' : 'Artist',
        'handandvision_product_artist_callback',
        'product',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'handandvision_add_product_artist_metabox' );

/**
 * Display the artist selection dropdown in product edit screen
 */
function handandvision_product_artist_callback( $post ) {
    wp_nonce_field( 'handandvision_save_product_artist', 'handandvision_product_artist_nonce' );
    $selected_artist = get_post_meta( $post->ID, '_handandvision_artist_id', true );

    $artists = get_posts( array(
        'post_type'   => 'artist',
        'numberposts' => 200,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ) );

    echo '<select name="handandvision_product_artist" id="handandvision_product_artist" style="width:100%;">';
    echo '<option value="">' . ( handandvision_is_hebrew() ? '-- בחר אמן/ית --' : '-- Select Artist --' ) . '</option>';
    foreach ( $artists as $artist ) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr( $artist->ID ),
            selected( $selected_artist, $artist->ID, false ),
            esc_html( $artist->post_title )
        );
    }
    echo '</select>';
    echo '<p class="description" style="margin-top:10px;">' .
         ( handandvision_is_hebrew() ? 'בחרו את האמן/ית שיצר/ה את היצירה' : 'Select the artist who created this piece' ) .
         '</p>';
}

/**
 * Save the artist relationship when product is saved
 */
function handandvision_save_product_artist( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['handandvision_product_artist_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['handandvision_product_artist_nonce'], 'handandvision_save_product_artist' ) ) {
        return;
    }

    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $old_artist_id = get_post_meta( $post_id, '_handandvision_artist_id', true );
    if ( isset( $_POST['handandvision_product_artist'] ) ) {
        $artist_id = absint( $_POST['handandvision_product_artist'] );
        update_post_meta( $post_id, '_handandvision_artist_id', $artist_id ? $artist_id : '' );
        handandvision_invalidate_artist_products_cache( array_filter( array( $old_artist_id, $artist_id ) ) );
    } else {
        delete_post_meta( $post_id, '_handandvision_artist_id' );
        if ( $old_artist_id ) {
            handandvision_invalidate_artist_products_cache( array( $old_artist_id ) );
        }
    }
}
add_action( 'save_post_product', 'handandvision_save_product_artist' );

/**
 * Get products by artist ID (cached 1 hour, invalidated on product save)
 */
function handandvision_get_artist_products( $artist_id ) {
    if ( ! $artist_id ) {
        return array();
    }
    $cache_key = 'hv_artist_products_' . $artist_id;
    $cached = get_transient( $cache_key );
    if ( false !== $cached && is_array( $cached ) ) {
        return $cached;
    }
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'     => '_handandvision_artist_id',
                'value'   => $artist_id,
                'compare' => '=',
            ),
        ),
        'post_status'    => 'publish',
    );
    $products = get_posts( $args );
    set_transient( $cache_key, $products, HOUR_IN_SECONDS );
    return $products;
}

/**
 * Invalidate artist products cache for one or more artist IDs
 */
function handandvision_invalidate_artist_products_cache( $artist_ids ) {
    $artist_ids = array_filter( array_map( 'absint', (array) $artist_ids ) );
    foreach ( $artist_ids as $aid ) {
        delete_transient( 'hv_artist_products_' . $aid );
    }
}

/**
 * Customize WooCommerce product columns
 */
function handandvision_woocommerce_product_columns() {
    return 4; // 4 columns for desktop
}
add_filter( 'loop_shop_columns', 'handandvision_woocommerce_product_columns' );

/**
 * Change number of products per page
 */
function handandvision_products_per_page() {
    return 12;
}
add_filter( 'loop_shop_per_page', 'handandvision_products_per_page', 20 );

/**
 * Remove default WooCommerce related products (we have custom implementation)
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/**
 * Remove default WooCommerce product tabs (we have custom implementation)
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

/**
 * Disable default WooCommerce styles (we have custom styling)
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Customize Add to Cart button text
 */
function handandvision_custom_add_to_cart_text() {
    $is_hebrew = handandvision_is_hebrew();
    return $is_hebrew ? 'הוסף לעגלה' : 'Add to Cart';
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'handandvision_custom_add_to_cart_text' );
add_filter( 'woocommerce_product_add_to_cart_text', 'handandvision_custom_add_to_cart_text' );

/**
 * Add artist name to product title in cart/checkout
 */
function handandvision_add_artist_to_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
    $product_id = $cart_item['product_id'];
    $artist_id = get_post_meta( $product_id, '_handandvision_artist_id', true );

    if ( $artist_id ) {
        $artist_name = get_the_title( $artist_id );
        $is_hebrew = handandvision_is_hebrew();
        $by_text = $is_hebrew ? 'מאת' : 'by';
        $product_name = $product_name . ' <span style="font-size:0.9em;color:#888;font-weight:normal;">(' . $by_text . ' ' . esc_html( $artist_name ) . ')</span>';
    }

    return $product_name;
}
add_filter( 'woocommerce_cart_item_name', 'handandvision_add_artist_to_cart_item_name', 10, 3 );

/**
 * Display artist name on order confirmation
 */
function handandvision_add_artist_to_order_item_name( $item_name, $item ) {
    $product_id = $item->get_product_id();
    $artist_id = get_post_meta( $product_id, '_handandvision_artist_id', true );

    if ( $artist_id ) {
        $artist_name = get_the_title( $artist_id );
        $is_hebrew = handandvision_is_hebrew();
        $by_text = $is_hebrew ? 'מאת' : 'by';
        $item_name = $item_name . ' <span style="font-size:0.9em;color:#888;">(' . $by_text . ' ' . esc_html( $artist_name ) . ')</span>';
    }

    return $item_name;
}
add_filter( 'woocommerce_order_item_name', 'handandvision_add_artist_to_order_item_name', 10, 2 );

/**
 * Update cart count fragment for AJAX add to cart
 */
function handandvision_cart_count_fragments( $fragments ) {
    $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $has_items_class = $cart_count > 0 ? ' has-items' : '';

    $fragments['#hv-cart-count'] = '<span class="hv-cart-count' . $has_items_class . '" id="hv-cart-count">' . esc_html( $cart_count ) . '</span>';

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'handandvision_cart_count_fragments' );

/**
 * Add custom classes to cart page body
 */
function handandvision_cart_page_body_class( $classes ) {
    if ( is_cart() ) {
        $classes[] = 'hv-cart-page-wrapper';

        if ( handandvision_is_hebrew() ) {
            $classes[] = 'rtl';
        }

        // Add empty cart class
        if ( WC()->cart && WC()->cart->is_empty() ) {
            $classes[] = 'hv-cart-empty';
        }
    }

    return $classes;
}
add_filter( 'body_class', 'handandvision_cart_page_body_class' );

add_filter( 'astra_the_title_enabled', function ( $enabled ) {
	return is_cart() ? false : $enabled;
} );

/**
 * Customize cart empty message
 */
function handandvision_empty_cart_message() {
    $is_hebrew = handandvision_is_hebrew();
    $message = $is_hebrew ? 'סל הקניות שלך ריק כרגע' : 'Your cart is currently empty.';

    return '<p class="cart-empty woocommerce-info">' . esc_html( $message ) . '</p>';
}
add_filter( 'wc_empty_cart_message', 'handandvision_empty_cart_message' );

/**
 * Customize "Return to Shop" button text
 */
function handandvision_return_to_shop_text() {
    $is_hebrew = handandvision_is_hebrew();
    return $is_hebrew ? 'חזרה לחנות' : 'Return to Shop';
}
add_filter( 'woocommerce_return_to_shop_text', 'handandvision_return_to_shop_text' );

/**
 * Enable Flexible Logo Support (Prevent forced cropping)
 */
function handandvision_flexible_logo() {
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
    ) );
}
add_action( 'after_setup_theme', 'handandvision_flexible_logo', 20 ); // Priority 20 to override parent

function handandvision_enqueue_custom_assets() {
    $theme_uri = get_stylesheet_directory_uri();

    wp_enqueue_style(
        'handandvision-fonts',
        'https://fonts.googleapis.com/css2?family=Heebo:wght@200;300;400;500;600;700&family=Inter:wght@300;400;500&family=Outfit:wght@200;300;400;500&display=swap',
        array(),
        null
    );

    // Unified Main CSS - everything in one file
    wp_enqueue_style(
        'hv-unified',
        $theme_uri . '/assets/css/hv-unified.css',
        array(),
        HV_THEME_VERSION
    );

    // Tell WordPress that hv-unified.css already includes RTL support
    // This prevents 404 errors when WordPress tries to find hv-unified-rtl.css
    wp_style_add_data( 'hv-unified', 'rtl', false );

    wp_enqueue_script(
        'handandvision-main',
        $theme_uri . '/assets/js/hv-main.js',
        array(),
        HV_THEME_VERSION,
        true
    );

    // UI Refinements - micro-interactions and scroll animations
    wp_enqueue_script(
        'handandvision-refinements',
        $theme_uri . '/assets/js/hv-refinements.js',
        array( 'handandvision-main' ),
        HV_THEME_VERSION,
        true
    );

    // Pass AJAX URL and localized strings to JS
    wp_localize_script( 'handandvision-main', 'hv_ajax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'hv_contact_action' ),
    ) );

    $is_hebrew = handandvision_is_hebrew();
    wp_localize_script( 'handandvision-main', 'hv_i18n', array(
        'offline_message' => $is_hebrew ? 'אין חיבור לאינטרנט. ייתכן שחלק מהתוכן לא יהיה זמין.' : 'You are offline. Some content may not be available.',
        'lightbox_label'  => $is_hebrew ? 'תצוגת תמונה' : 'Image lightbox',
        'close_label'     => $is_hebrew ? 'סגור' : 'Close',
        'menu_label'      => $is_hebrew ? 'תפריט' : 'Menu',
        'cart_label'      => $is_hebrew ? 'עגלת קניות' : 'Shopping Cart',
        'form_errors'     => array(
            'required' => $is_hebrew ? 'אנא מלאו את כל שדות החובה.' : 'Please fill in all required fields.',
            'email'    => $is_hebrew ? 'כתובת האימייל אינה תקינה.' : 'Invalid email address.',
        ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'handandvision_enqueue_custom_assets' );

/**
 * Fix Astra 404 for missing assets and redirect to our unified CSS.
 * This ensures all Astra components use our premium styles.
 */
function handandvision_astra_style_fix( $src, $handle ) {
    // Only intercept Astra's main theme styles to avoid breaking other plugins
    if ( strpos( $handle, 'astra-theme-css' ) !== false || strpos( $handle, 'astra-woocommerce' ) !== false ) {
        return get_stylesheet_directory_uri() . '/assets/css/hv-unified.css';
    }
    return $src;
}
add_filter( 'style_loader_src', 'handandvision_astra_style_fix', 20, 2 );

add_action( 'wp_enqueue_scripts', function() {
    global $wp_styles;
    if ( ! is_a( $wp_styles, 'WP_Styles' ) ) return;

    foreach ( $wp_styles->queue as $handle ) {
        if ( strpos( $handle, 'astra' ) !== false || $handle === 'hv-unified' ) {
            wp_style_add_data( $handle, 'rtl', false );
        }
    }
}, 999 );

/**
 * Get current language (HE or EN)
 */
/**
 * Initialize language cookie
 */
function handandvision_init_language_cookie() {
    if ( is_admin() || headers_sent() ) {
        return;
    }

    // if GET param is present, update the cookie
    if ( isset( $_GET['lang'] ) ) {
        $lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) );
        if ( in_array( $lang, array( 'en', 'he' ) ) ) {
            setcookie( 'hv_lang', $lang, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
            $_COOKIE['hv_lang'] = $lang; // Update global for immediate use in this request
        }
    }
}
add_action( 'init', 'handandvision_init_language_cookie' );

/**
 * Get current language (HE or EN)
 */
function handandvision_get_current_language() {
    // 1. Check URL Parameter (Highest Priority)
    if ( isset( $_GET['lang'] ) ) {
        $lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) );
        if ( 'en' === $lang || 'he' === $lang ) {
            return $lang;
        }
    }

    // 2. Check Cookie (Medium Priority)
    if ( isset( $_COOKIE['hv_lang'] ) ) {
        $lang = sanitize_text_field( wp_unslash( $_COOKIE['hv_lang'] ) );
        if ( 'en' === $lang || 'he' === $lang ) {
            return $lang;
        }
    }

    // 3. Fallback to Plugins (Polylang / WPML)
    if ( function_exists( 'pll_current_language' ) ) {
        $pll_lang = pll_current_language();
        if ( $pll_lang ) return $pll_lang;
    }
    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
        return ICL_LANGUAGE_CODE;
    }

    // 4. Default to Hebrew
    return 'he';
}

/**
 * Check if current language is Hebrew
 */
function handandvision_is_hebrew() {
    return handandvision_get_current_language() === 'he';
}

require_once ASTRA_THEME_DIR . 'inc/gallery-helpers.php';

/**
 * Set html dir and body class from current language so RTL/LTR matches viewed language.
 */
function handandvision_language_attributes( $attr ) {
    $dir = handandvision_is_hebrew() ? 'rtl' : 'ltr';
    if ( preg_match( '/\sdir="[^"]*"/', $attr ) ) {
        return preg_replace( '/\sdir="[^"]*"/', ' dir="' . $dir . '"', $attr );
    }
    return $attr . ' dir="' . $dir . '"';
}
add_filter( 'language_attributes', 'handandvision_language_attributes' );

function handandvision_body_class_rtl( $classes ) {
    $classes = array_diff( $classes, array( 'rtl', 'ltr' ) );
    $classes[] = handandvision_is_hebrew() ? 'rtl' : 'ltr';
    return $classes;
}
add_filter( 'body_class', 'handandvision_body_class_rtl' );

/**
 * Get safe logo URL - uses uploads directory to avoid special character issues in theme path
 */
function handandvision_get_logo_url() {
    // First check if custom logo is set in WordPress Customizer
    if ( has_custom_logo() ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
        if ( $logo_data && isset( $logo_data[0] ) ) {
            return $logo_data[0];
        }
    }

    // Try uploads directory for hv-logo.png
    $upload_dir = wp_upload_dir();
    $logo_in_uploads = $upload_dir['basedir'] . '/hv-logo.png';
    $logo_url_in_uploads = $upload_dir['baseurl'] . '/hv-logo.png';

    if ( file_exists( $logo_in_uploads ) ) {
        return $logo_url_in_uploads;
    }

    return '';
}

function handandvision_get_contact_url() {
	$page = get_page_by_path( 'contact' );
	if ( $page && $page->post_status === 'publish' ) {
		return get_permalink( $page );
	}
	$pages = get_posts( array(
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-contact.php',
		'post_status'    => 'publish',
	) );
	if ( ! empty( $pages ) ) {
		return get_permalink( $pages[0] );
	}
	return home_url( '/contact' );
}

/**
 * Generate custom site header with language switcher, logo, navigation, and cart
 *
 * Uses output buffering for clean output and better testability.
 *
 * @since 3.3.0
 * @return void
 */
function handandvision_custom_header() {
    $logo_url = handandvision_get_logo_url();

    ob_start();
    ?>
    <header class="hv-header" id="hv-masthead">
        <div class="hv-header__container">
            <!-- Language Switcher (Floating Left on Desktop) -->
            <div class="hv-header__lang">
                <?php
                if ( function_exists( 'icl_get_languages' ) ) {
                    $languages = icl_get_languages( 'skip_missing=0' );
                    if ( ! empty( $languages ) ) {
                        foreach ( $languages as $l ) {
                            echo '<a href="' . esc_url( $l['url'] ) . '" class="hv-lang-link ' . ( ! empty( $l['active'] ) ? 'active' : '' ) . '">' . esc_html( strtoupper( $l['language_code'] ) ) . '</a>';
                            if ( end( $languages ) !== $l ) {
                                echo '<span class="hv-lang-sep">|</span>';
                            }
                        }
                    }
                } elseif ( function_exists( 'pll_the_languages' ) ) {
                    $languages = pll_the_languages( array( 'raw' => 1 ) );
                    if ( ! empty( $languages ) ) {
                        foreach ( $languages as $l ) {
                            echo '<a href="' . esc_url( $l['url'] ) . '" class="hv-lang-link ' . ( ! empty( $l['current_lang'] ) ? 'active' : '' ) . '">' . esc_html( strtoupper( $l['slug'] ) ) . '</a>';
                            if ( end( $languages ) !== $l ) {
                                echo '<span class="hv-lang-sep">|</span>';
                            }
                        }
                    }
                } else {
                    $current_url = ( isset( $GLOBALS['wp'] ) && is_object( $GLOBALS['wp'] ) && isset( $GLOBALS['wp']->request ) )
                        ? home_url( '/' . $GLOBALS['wp']->request )
                        : home_url( '/' );
                    $he_url = add_query_arg( 'lang', 'he', $current_url );
                    $en_url = add_query_arg( 'lang', 'en', $current_url );
                    $lang_param = isset( $_GET['lang'] ) ? sanitize_text_field( wp_unslash( $_GET['lang'] ) ) : '';
                    $current_lang = handandvision_get_current_language();
                    ?>
                    <a href="<?php echo esc_url( $he_url ); ?>" class="hv-lang-link <?php echo ( $current_lang === 'he' ) ? 'active' : ''; ?>">HE</a>
                    <span class="hv-lang-sep">|</span>
                    <a href="<?php echo esc_url( $en_url ); ?>" class="hv-lang-link <?php echo ( $current_lang === 'en' ) ? 'active' : ''; ?>">EN</a>
                    <?php
                }
                ?>
            </div>

            <!-- Centered Wrapper for Logo + Nav -->
            <div class="hv-header__center">
                <div class="hv-header__logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hv-logo-link">
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Hand and Vision" style="width: 60px; height: 60px; object-fit: contain; display: block;">
                        <?php else : ?>
                            <span class="hv-logo-text">HAND & VISION</span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hv-header__nav" id="hv-navigation">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'hv-nav-menu',
                        'container'      => false,
                        'fallback_cb'    => 'handandvision_default_menu',
                    ) );
                    ?>
                </nav>
            </div>

            <!-- Cart Icon -->
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="hv-header__cart" id="hv-header-cart" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'עגלת קניות' : 'Shopping Cart' ); ?>">
                    <svg class="hv-cart-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <?php $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
                    <span class="hv-cart-count<?php echo $cart_count > 0 ? ' has-items' : ''; ?>" id="hv-cart-count"><?php echo esc_html( $cart_count ); ?></span>
                </a>
            <?php endif; ?>

            <!-- Mobile Menu Toggle -->
            <button class="hv-header__menu-toggle" id="hv-menu-toggle" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'תפריט' : 'Menu' ); ?>">
                <span class="hv-hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>
    </header>

    <?php
    echo ob_get_clean();
}

/**
 * Default menu fallback
 */
function handandvision_default_menu() {
    $is_hebrew = handandvision_is_hebrew();
    ?>
    <ul class="hv-nav-menu">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $is_hebrew ? 'חזון' : 'HAND AND VISION' ); ?></a></li>
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php echo esc_html( $is_hebrew ? 'אוסף' : 'COLLECTION' ); ?></a></li>
        <?php else : ?>
            <li><a href="<?php echo esc_url( home_url( '/shop' ) ); ?>"><?php echo esc_html( $is_hebrew ? 'אוסף' : 'COLLECTION' ); ?></a></li>
        <?php endif; ?>
        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo esc_html( $is_hebrew ? 'שירותים' : 'SERVICES' ); ?></a></li>
        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ); ?>"><?php echo esc_html( $is_hebrew ? 'אמנים' : 'ARTISTS' ); ?></a></li>
        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'gallery_item' ) ); ?>"><?php echo esc_html( $is_hebrew ? 'גלריה' : 'GALLERY' ); ?></a></li>
        <li><a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>"><?php echo esc_html( $is_hebrew ? 'צור קשר' : 'CONTACT' ); ?></a></li>
    </ul>
    <?php
}

/**
 * Replace Astra header with custom header on ALL pages
 */
function handandvision_replace_header() {
    // Replace on all pages
    remove_action( 'astra_header', 'astra_header_markup' );
    add_action( 'astra_header', 'handandvision_custom_header' );
}
add_action( 'wp', 'handandvision_replace_header', 1 );

/**
 * Hide default Astra header via CSS (backup method)
 */
function handandvision_hide_astra_header() {
    ?>
    <style>
        .ast-primary-header-bar,
        .ast-desktop-header-wrap,
        .ast-mobile-header-wrap,
        #ast-desktop-header,
        #ast-mobile-header,
        .site-header-primary-section-left,
        .site-header-primary-section-right {
            display: none !important;
        }

        .hv-header {
            display: block !important;
        }
        .hv-logo-text {
            font-family: var(--hv-font-heading, inherit);
            font-weight: 300;
            font-size: 1rem;
            letter-spacing: 0.12em;
            text-decoration: none;
            color: var(--hv-text, #1a1a1a);
        }
    </style>
    <?php
}
add_action( 'wp_head', 'handandvision_hide_astra_header', 100 );


/**
 * Replace Astra footer with custom footer
 */
function handandvision_replace_footer() {
    remove_action( 'astra_footer', 'astra_footer_markup' );
    if ( class_exists( 'Astra_Builder_Footer' ) ) {
        remove_action( 'astra_footer', array( Astra_Builder_Footer::get_instance(), 'footer_markup' ), 10 );
    }
    add_action( 'astra_footer', 'handandvision_custom_footer' );
}
add_action( 'wp', 'handandvision_replace_footer', 1 );
/**
 * Rename "Home" menu item to language-aware brand title
 */
function handandvision_rename_home_menu_item( $items, $args ) {
    if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
        $home_title = handandvision_is_hebrew() ? 'חזון' : 'HAND AND VISION';
        foreach ( $items as $item ) {
            if ( $item->url === home_url( '/' ) || $item->url === home_url() ) {
                $item->title = $home_title;
            }
        }
    }
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'handandvision_rename_home_menu_item', 10, 2 );

function handandvision_custom_footer() {
	if ( defined( 'HV_FOOTER_RENDERING' ) && HV_FOOTER_RENDERING ) {
		return;
	}
	get_template_part( 'footer' );
}
/**
 * AJAX Contact Form Submission Handler
 *
 * Handles form submissions with nonce verification, rate limiting, and spam protection.
 *
 * @since 3.3.0
 * @return void Sends JSON response
 */
function handandvision_handle_contact_form() {
    // Rate limiting - prevent spam
    $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $rate_key = 'hv_contact_rate_' . md5( $ip_address );
    $last_submission = get_transient( $rate_key );

    if ( false !== $last_submission ) {
        wp_send_json_error( array(
            'message' => handandvision_is_hebrew()
                ? 'אנא המתינו דקה לפני שליחת פנייה נוספת.'
                : 'Please wait a minute before submitting another message.'
        ) );
        return;
    }

    // Verify nonce with proper sanitization
    $nonce = isset( $_POST['hv_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['hv_contact_nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'hv_contact_action' ) ) {
        wp_send_json_error( array(
            'message' => handandvision_is_hebrew()
                ? 'אימות נכשל. אנא טענו את הדף מחדש.'
                : 'Security check failed. Please refresh the page.'
        ) );
        return;
    }

    $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    $subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( array( 'message' => handandvision_is_hebrew() ? 'אנא מלאו את כל שדות החובה.' : 'Please fill in all required fields.' ) );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => handandvision_is_hebrew() ? 'כתובת האימייל אינה תקינה.' : 'Invalid email address.' ) );
    }

    // Get admin email from ACF or WordPress settings
    $submitted_page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
    $to = get_field( 'contact_email', $submitted_page_id ) ?: get_option( 'admin_email' );

    // Subject line
    $email_subject = 'פנייה חדשה מאתר Hand & Vision: ' . sanitize_text_field( $subject );

    // Email Content - sanitize all user input
    $body = "נשלחה פנייה חדשה מהאתר:\n\n";
    $body .= "שם: " . sanitize_text_field( $name ) . "\n";
    $body .= "אימייל: " . sanitize_email( $email ) . "\n";
    if ( ! empty( $phone ) ) {
        $body .= "טלפון: " . sanitize_text_field( $phone ) . "\n";
    }
    $body .= "נושא: " . sanitize_text_field( $subject ) . "\n\n";
    $body .= "הודעה:\n" . sanitize_textarea_field( $message ) . "\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: Hand & Vision <' . sanitize_email( get_option( 'admin_email' ) ) . '>',
        'Reply-To: ' . sanitize_text_field( $name ) . ' <' . sanitize_email( $email ) . '>'
    );

    // Send Mail
    $sent = wp_mail( $to, $email_subject, $body, $headers );

    if ( $sent ) {
        // Set rate limit - 60 seconds between submissions
        set_transient( $rate_key, time(), 60 );
        wp_send_json_success( array( 'message' => handandvision_is_hebrew() ? 'ההודעה נשלחה בהצלחה! נחזור אליכם בהקדם.' : 'Message sent successfully! We will get back to you soon.' ) );
    } else {
        wp_send_json_error( array( 'message' => handandvision_is_hebrew() ? 'אירעה שגיאה בשליחת ההודעה. אנא נסו שוב מאוחר יותר.' : 'Error sending message. Please try again later.' ) );
    }
}
add_action( 'wp_ajax_hv_contact_form', 'handandvision_handle_contact_form' );
add_action( 'wp_ajax_nopriv_hv_contact_form', 'handandvision_handle_contact_form' );

/**
 * Auto-create Contact page if it doesn't exist
 */
function handandvision_create_contact_page() {
    // Check if contact page already exists by slug
    $contact_page = get_page_by_path( 'contact' );

    if ( ! $contact_page ) {
        // Create the contact page
        $page_id = wp_insert_post( array(
            'post_title'     => handandvision_is_hebrew() ? 'צור קשר' : 'Contact',
            'post_name'      => 'contact',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_content'   => '',
        ) );

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            // Set the page template
            update_post_meta( $page_id, '_wp_page_template', 'page-contact.php' );
        }
    } else {
        // Ensure the template is set correctly
        $current_template = get_post_meta( $contact_page->ID, '_wp_page_template', true );
        if ( $current_template !== 'page-contact.php' ) {
            update_post_meta( $contact_page->ID, '_wp_page_template', 'page-contact.php' );
        }
    }
}
add_action( 'after_switch_theme', 'handandvision_create_contact_page' );


// Also run on init to ensure page exists (only once)
function handandvision_ensure_contact_page_exists() {
    if ( get_option( 'handandvision_contact_page_created' ) !== 'yes' ) {
        handandvision_create_contact_page();
        update_option( 'handandvision_contact_page_created', 'yes' );
    }
}
add_action( 'init', 'handandvision_ensure_contact_page_exists' );

/**
 * Get container class for header.php
 * Allows full-width sections on pages and archives by replacing .ast-container
 */
function handandvision_get_container_class() {
    // Pages, Archives, and Custom Post Types should be full width
    // The internal content will handle its own containment (e.g. .hv-container)
    if ( is_page() || is_archive() || is_home() || is_singular( array( 'artist', 'service', 'gallery_item', 'product' ) ) ) {
        return 'hv-full-width-wrapper';
    }

    // Default fallback (e.g. single blog posts if standard)
    return 'ast-container';
}


