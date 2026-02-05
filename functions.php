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
define( 'HV_THEME_VERSION', '3.3.2' );

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
 * Organized modular structure for maintainability
 */

/**
 * ACF Dependency Check - Throws error if ACF is not installed
 * No fallbacks or mock data per SSOT strategy
 */
if ( ! function_exists( 'get_field' ) ) {
	function handandvision_acf_missing_notice() {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Hand and Vision theme requires the Advanced Custom Fields (ACF) plugin. Please install and activate ACF.', 'astra' ) . '</p></div>';
	}
	add_action( 'admin_notices', 'handandvision_acf_missing_notice' );

	if ( is_admin() ) {
		wp_die(
			esc_html__( 'This theme requires Advanced Custom Fields (ACF) plugin. Please install and activate ACF to continue.', 'astra' ),
			esc_html__( 'Plugin Required', 'astra' ),
			array( 'back_link' => true )
		);
	}
}

/**
 * Include ACF field configurations only if ACF is active
 */
function handandvision_load_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    $acf_files = [
        'acf-homepage-fields.php',
        'acf-artist-fields.php',
        'acf-service-fields.php',
        'acf-gallery-fields.php',
        'acf-product-fields.php',
        'acf-contact-fields.php',
    ];

    foreach ( $acf_files as $file ) {
        $file_path = ASTRA_THEME_DIR . 'inc/' . $file;
        if ( file_exists( $file_path ) ) {
            require_once $file_path;
        }
    }
}
add_action( 'acf/init', 'handandvision_load_acf_fields', 5 );

/**
 * ==========================================================================
 * MODULAR ARCHITECTURE - Organized by Feature
 * ==========================================================================
 */

// Maintenance Mode - Gatekeeper
require_once ASTRA_THEME_DIR . 'inc/maintenance-mode.php';

// IMPORTANT: Load accessibility first - other modules depend on handandvision_is_hebrew()
require_once ASTRA_THEME_DIR . 'inc/accessibility/language-rtl.php';

// SEO Module - Lightweight Open Graph & Meta Tags
require_once ASTRA_THEME_DIR . 'inc/seo/class-hv-seo.php';

// Custom Post Types
require_once ASTRA_THEME_DIR . 'inc/post-types/register-cpts.php';

// Theme Support (before WooCommerce - provides helper functions)
require_once ASTRA_THEME_DIR . 'inc/theme-support/setup.php';
require_once ASTRA_THEME_DIR . 'inc/theme-support/image-optimization.php';

// WooCommerce Integration (depends on language functions)
if ( class_exists( 'WooCommerce' ) ) {
    require_once ASTRA_THEME_DIR . 'inc/woocommerce/theme-support.php';
    require_once ASTRA_THEME_DIR . 'inc/woocommerce/artist-products.php';
    require_once ASTRA_THEME_DIR . 'inc/woocommerce/product-helpers.php';
    require_once ASTRA_THEME_DIR . 'inc/woocommerce/cart-customization.php';
}

// AJAX Handlers (depends on language functions)
require_once ASTRA_THEME_DIR . 'inc/ajax-handlers/contact-form.php';

// Gallery Helpers
require_once ASTRA_THEME_DIR . 'inc/gallery-helpers.php';
require_once ASTRA_THEME_DIR . 'inc/acf-display-helper.php';
// Service Helpers (icon SVGs)
require_once ASTRA_THEME_DIR . 'inc/service-helpers.php';
require_once ASTRA_THEME_DIR . 'inc/fix-site-url.php';


/**
 * ==========================================================================
 * HAND AND VISION - Custom Theme Functions
 * ==========================================================================
 * Asset enqueuing, header/footer, and menu customizations
 */

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

    // Premium Store CSS - only load on WooCommerce pages
    // Use function_exists checks to avoid errors if WooCommerce is not fully loaded
    if ( class_exists( 'WooCommerce' ) ) {
        $is_wc_page = false;

        // Check if we're on any WooCommerce page
        if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_shop' ) && is_shop() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_product' ) && is_product() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_cart' ) && is_cart() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
            $is_wc_page = true;
        } elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
            $is_wc_page = true;
        }

        if ( $is_wc_page ) {
            wp_enqueue_style(
                'hv-store-premium',
                $theme_uri . '/assets/css/hv-store-premium.css',
                array( 'hv-unified' ),
                HV_THEME_VERSION
            );
            wp_style_add_data( 'hv-store-premium', 'rtl', false );
        }
    }

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

    // Drag to Scroll - DEPRECATED (Replaced by Swiper)
    // wp_enqueue_script(
    //     'handandvision-drag-scroll',
    //     $theme_uri . '/assets/js/hv-drag-scroll.js',
    //     array(),
    //     HV_THEME_VERSION,
    //     true
    // );

    // Swiper Carousel
    // Swiper Carousel (CDN)
    wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css', array(), '12.0.0' );
    wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js', array(), '12.0.0', true );
    wp_enqueue_script( 'hv-swiper-init', $theme_uri . '/assets/js/hv-swiper-init.js', array( 'swiper' ), HV_THEME_VERSION, true );
    wp_enqueue_script( 'hv-parallax', $theme_uri . '/assets/js/hv-parallax.js', array(), HV_THEME_VERSION, true );
    wp_enqueue_script( 'hv-view-transitions', $theme_uri . '/assets/js/hv-view-transitions.js', array(), HV_THEME_VERSION, true );
    wp_enqueue_script( 'hv-cart-animation', $theme_uri . '/assets/js/hv-cart-animation.js', array(), HV_THEME_VERSION, true );

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

            <?php
            if ( class_exists( 'WooCommerce' ) ) {
                $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
                ?>
                <div id="hv-header-cart-wrap"><?php
                if ( $cart_count > 0 ) {
                    handandvision_header_cart_markup( $cart_count );
                }
                ?></div>
            <?php } ?>

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

/**
 * Generate custom footer
 */
function handandvision_custom_footer() {
	if ( defined( 'HV_FOOTER_RENDERING' ) && HV_FOOTER_RENDERING ) {
		return;
	}
	get_template_part( 'footer' );
}
/**
 * One-time URL fix: replace old site URL in DB. Run as admin: ?hv_fix_url=1
 */
function handandvision_maybe_run_fix_site_url() {
	if ( ! isset( $_GET['hv_fix_url'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$result = handandvision_fix_site_url_in_db();
	if ( ! empty( $result['skipped'] ) ) {
		wp_die( esc_html( $result['message'] ), '', array( 'response' => 200 ) );
	}
	wp_die(
		'URL fix done. Replaced: ' . wp_json_encode( $result['replaced'] ) . ' New URL: ' . esc_html( $result['new_url'] ),
		'',
		array( 'response' => 200 )
	);
}
add_action( 'init', 'handandvision_maybe_run_fix_site_url', 1 );

require_once get_template_directory() . '/inc/ajax-handlers/quick-view.php';

/**
 * Get placeholder image URL
 *
 * @return string URL to placeholder image
 */
function handandvision_get_placeholder_image() {
    return get_stylesheet_directory_uri() . '/assets/images/placeholder.jpg';
}

/**
 * Inject View Transition Name for Single Product Pages
 * Targets the main gallery image.
 */
function handandvision_product_view_transition_css() {
    if ( is_product() ) {
        $product_id = get_the_ID();
        // Target: first image in the WC gallery
        echo "<style>
            .woocommerce-product-gallery__image:first-child img,
            .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:first-child,
            .hv-service-single-hero__bg img {
                view-transition-name: product-img-{$product_id};
            }
        </style>";
    }
}
add_action( 'wp_head', 'handandvision_product_view_transition_css' );