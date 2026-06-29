<?php
/**
 * Hand and Vision - Client Admin Experience
 *
 * Single source of truth for everything the client sees in wp-admin:
 *   - Branded login screen
 *   - Welcome dashboard widget with quick actions
 *   - Streamlined admin menu (HV content first, parent-theme noise hidden)
 *   - CPT list-table thumbnails & useful columns
 *   - Cleaner dashboard (no WP news / promo widgets / BSF analytics nags)
 *   - Branded admin footer & login redirect
 *
 * All strings are bilingual (HE primary, EN fallback) via handandvision_is_hebrew().
 * Admins keep full access; the cleanup mainly targets editors/authors and visual noise.
 *
 * @package HandAndVision
 * @since 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'HV_Admin' ) ) {

class HV_Admin {

    /** @var self */
    private static $instance;

    /** Content post types managed by the client, in display order. */
    const CONTENT_TYPES = [ 'gallery_item', 'artist', 'service', 'product', 'page', 'post' ];

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Login
        add_filter( 'login_headerurl',  [ $this, 'login_header_url' ] );
        add_filter( 'login_headertext', [ $this, 'login_header_text' ] );
        add_action( 'login_enqueue_scripts', [ $this, 'login_styles' ] );

        // Admin chrome
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
        add_filter( 'admin_footer_text',     [ $this, 'admin_footer_text' ] );
        add_filter( 'update_footer',         [ $this, 'admin_footer_version' ], 11 );

        // Menu
        add_action( 'admin_menu',      [ $this, 'reorder_menu' ], 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order',      [ $this, 'menu_order' ] );
        add_action( 'admin_menu',      [ $this, 'hide_parent_theme_pages' ], 9999 );
        add_action( 'admin_bar_menu',  [ $this, 'clean_admin_bar' ], 999 );

        // Dashboard
        add_action( 'wp_dashboard_setup', [ $this, 'dashboard_widgets' ], 999 );
        add_action( 'admin_init',         [ $this, 'remove_welcome_panel' ] );

        // Notices
        add_action( 'admin_init', [ $this, 'suppress_third_party_notices' ], 1 );

        // CPT list tables
        $this->register_cpt_columns();

        // Misc UX
        add_filter( 'login_redirect', [ $this, 'login_redirect' ], 10, 3 );
        add_action( 'wp_before_admin_bar_render', [ $this, 'remove_wp_logo' ] );

        // Admin color scheme — "Hand and Vision" default
        add_action( 'admin_init',                  [ $this, 'register_color_scheme' ] );
        add_filter( 'get_user_option_admin_color', [ $this, 'default_color_scheme' ] );
        add_action( 'user_register',               [ $this, 'set_color_scheme_for_new_user' ] );
    }

    /* -------------------------------------------------------------------- */
    /*  Helpers                                                              */
    /* -------------------------------------------------------------------- */

    private function is_he() {
        return function_exists( 'handandvision_is_hebrew' ) && handandvision_is_hebrew();
    }

    private function t( $he, $en ) {
        return $this->is_he() ? $he : $en;
    }

    /* -------------------------------------------------------------------- */
    /*  Login screen                                                         */
    /* -------------------------------------------------------------------- */

    public function login_header_url() {
        return home_url( '/' );
    }

    public function login_header_text() {
        return get_bloginfo( 'name' );
    }

    public function login_styles() {
        $is_rtl = is_rtl() || $this->is_he();
        ?>
        <style>
            :root {
                --hv-bg: #0e0e10;
                --hv-fg: #f4f4f2;
                --hv-accent: #c9a96e;
                --hv-muted: rgba(244,244,242,0.6);
            }
            body.login {
                background: var(--hv-bg);
                color: var(--hv-fg);
                font-family: 'Heebo', 'Inter', system-ui, sans-serif;
                direction: <?php echo $is_rtl ? 'rtl' : 'ltr'; ?>;
            }
            .login h1 a {
                background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/hv-wordmark.svg' ); ?>');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                width: 320px;
                height: 70px;
                margin: 0 auto 24px;
            }
            .login form {
                background: #17171a;
                border: 1px solid rgba(255,255,255,0.06);
                border-radius: 4px;
                box-shadow: 0 30px 60px -20px rgba(0,0,0,0.6);
                padding: 32px;
            }
            .login form label { color: var(--hv-muted); font-weight: 300; letter-spacing: 0.04em; }
            .login form .input,
            .login input[type="text"],
            .login input[type="password"] {
                background: #0e0e10;
                border: 1px solid rgba(255,255,255,0.08);
                color: var(--hv-fg);
                box-shadow: none;
                border-radius: 2px;
            }
            .login form .input:focus,
            .login input[type="text"]:focus,
            .login input[type="password"]:focus {
                border-color: var(--hv-accent);
                box-shadow: 0 0 0 1px var(--hv-accent);
            }
            .wp-core-ui .button-primary {
                background: var(--hv-accent);
                border-color: var(--hv-accent);
                color: #0e0e10;
                text-shadow: none;
                box-shadow: none;
                font-weight: 500;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                border-radius: 2px;
            }
            .wp-core-ui .button-primary:hover,
            .wp-core-ui .button-primary:focus {
                background: #d4b985;
                border-color: #d4b985;
                color: #0e0e10;
            }
            .login #nav a, .login #backtoblog a { color: var(--hv-muted); }
            .login #nav a:hover, .login #backtoblog a:hover { color: var(--hv-accent); }
            .login .privacy-policy-page-link { display: none; }
        </style>
        <?php
    }

    public function login_redirect( $redirect_to, $requested, $user ) {
        if ( isset( $user->roles ) && is_array( $user->roles ) && ! in_array( 'administrator', $user->roles, true ) ) {
            return admin_url( 'index.php' );
        }
        return $redirect_to;
    }

    /* -------------------------------------------------------------------- */
    /*  Admin chrome                                                         */
    /* -------------------------------------------------------------------- */

    public function admin_styles() {
        $rtl = $this->is_he() ? 'direction:rtl;' : '';
        $widget_dir = $this->is_he() ? 'rtl' : 'ltr';
        ?>
        <style id="hv-admin-css">
            #adminmenu .toplevel_page_hv-dashboard .wp-menu-name { font-weight: 600; }
            #wpadminbar #wp-admin-bar-hv-site-link > .ab-item:before {
                content: "\f102"; top: 2px;
            }
            .hv-welcome {
                <?php echo $rtl; ?>
                background: linear-gradient(135deg, #17171a 0%, #2a2a2e 100%);
                color: #f4f4f2;
                padding: 28px 32px;
                margin: -12px -12px 12px;
                border-radius: 4px;
            }
            .hv-welcome h2 { color: #fff; margin: 0 0 8px; font-weight: 300; letter-spacing: 0.04em; }
            .hv-welcome p { color: rgba(244,244,242,0.7); margin: 0 0 20px; }
            .hv-welcome__grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 12px;
            }
            .hv-welcome__card {
                display: flex; align-items: center; gap: 10px;
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
                color: #f4f4f2 !important;
                padding: 14px 16px;
                border-radius: 3px;
                text-decoration: none;
                transition: all .2s ease;
            }
            .hv-welcome__card:hover {
                background: rgba(201,169,110,0.12);
                border-color: #c9a96e;
                transform: translateY(-1px);
            }
            .hv-welcome__card .dashicons { color: #c9a96e; font-size: 20px; width: 20px; height: 20px; }
            .hv-welcome__card span:last-child { font-size: 13px; letter-spacing: 0.03em; }
            /* CPT thumbnails in list tables */
            .column-hv_thumb { width: 70px; }
            .column-hv_thumb img { width: 56px; height: 56px; object-fit: cover; border-radius: 3px; display: block; }
            .column-hv_thumb .hv-thumb-placeholder {
                width: 56px; height: 56px; background: #f0f0f1; border-radius: 3px;
                display: flex; align-items: center; justify-content: center; color: #a7aaad;
            }
        </style>
        <?php
    }

    public function admin_footer_text() {
        $year = gmdate( 'Y' );
        return sprintf(
            '<span id="hv-admin-footer">%s &mdash; <a href="%s" target="_blank" rel="noopener">%s</a> &copy; %s</span>',
            esc_html( $this->t( 'מערכת ניהול תוכן', 'Content Management' ) ),
            esc_url( home_url( '/' ) ),
            esc_html( get_bloginfo( 'name' ) ),
            esc_html( $year )
        );
    }

    public function admin_footer_version( $text ) {
        if ( defined( 'HV_THEME_VERSION' ) ) {
            return 'HV ' . HV_THEME_VERSION;
        }
        return $text;
    }

    /* -------------------------------------------------------------------- */
    /*  Menu reorder & cleanup                                               */
    /* -------------------------------------------------------------------- */

    public function menu_order( $menu ) {
        if ( ! is_array( $menu ) ) {
            return $menu;
        }
        $priority = [
            'index.php',
            'edit.php?post_type=gallery_item',
            'edit.php?post_type=artist',
            'edit.php?post_type=service',
            'edit.php?post_type=product',
            'edit.php?post_type=page',
            'edit.php',
            'upload.php',
            'edit-comments.php',
        ];
        $ordered = [];
        foreach ( $priority as $slug ) {
            if ( in_array( $slug, $menu, true ) ) {
                $ordered[] = $slug;
            }
        }
        foreach ( $menu as $slug ) {
            if ( ! in_array( $slug, $ordered, true ) ) {
                $ordered[] = $slug;
            }
        }
        return $ordered;
    }

    public function reorder_menu() {
        // Rename "Posts" to something less confusing in HE
        global $menu, $submenu;
        if ( $this->is_he() && isset( $menu[5] ) ) {
            $menu[5][0] = 'יומן / חדשות';
        }
    }

    public function hide_parent_theme_pages() {
        // Only hide for non-admins; admins keep access to everything
        if ( current_user_can( 'manage_options' ) ) {
            return;
        }
        remove_menu_page( 'astra' );
        remove_menu_page( 'theme-builder-free' );
        remove_menu_page( 'astra-advanced-hook' );
        remove_submenu_page( 'themes.php', 'astra' );
    }

    public function clean_admin_bar( $wp_admin_bar ) {
        $wp_admin_bar->remove_node( 'wp-logo' );
        $wp_admin_bar->remove_node( 'comments' );
        if ( ! current_user_can( 'manage_options' ) ) {
            $wp_admin_bar->remove_node( 'updates' );
            $wp_admin_bar->remove_node( 'customize' );
        }
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        if ( is_object( $wp_admin_bar ) ) {
            $wp_admin_bar->remove_menu( 'wp-logo' );
        }
    }

    /* -------------------------------------------------------------------- */
    /*  Dashboard                                                            */
    /* -------------------------------------------------------------------- */

    public function dashboard_widgets() {
        global $wp_meta_boxes;

        // Remove WordPress default noise
        $remove = [
            'dashboard_primary',      // WordPress news
            'dashboard_secondary',
            'dashboard_quick_press',
            'dashboard_incoming_links',
            'dashboard_plugins',
            'dashboard_recent_drafts',
            'dashboard_activity',
            'dashboard_site_health',
        ];
        foreach ( $remove as $id ) {
            remove_meta_box( $id, 'dashboard', 'normal' );
            remove_meta_box( $id, 'dashboard', 'side' );
        }

        // Remove third-party promo widgets that don't help the client
        if ( isset( $wp_meta_boxes['dashboard'] ) ) {
            foreach ( $wp_meta_boxes['dashboard'] as $context => $priorities ) {
                foreach ( $priorities as $priority => $widgets ) {
                    foreach ( $widgets as $id => $_ ) {
                        if ( preg_match( '/^(astra|bsf|e_dashboard_overview|elementor|woocommerce_dashboard_recent_reviews|rg_forms_dashboard|monsterinsights|rank_math|wpforms)/i', $id ) ) {
                            remove_meta_box( $id, 'dashboard', $context );
                        }
                    }
                }
            }
        }

        wp_add_dashboard_widget(
            'hv_welcome',
            $this->t( 'ברוכים הבאים למערכת הניהול', 'Welcome to your Dashboard' ),
            [ $this, 'render_welcome_widget' ]
        );

        if ( class_exists( 'WooCommerce' ) ) {
            wp_add_dashboard_widget(
                'hv_store_snapshot',
                $this->t( 'תמונת מצב חנות', 'Store Snapshot' ),
                [ $this, 'render_store_widget' ]
            );
        }
    }

    public function remove_welcome_panel() {
        remove_action( 'welcome_panel', 'wp_welcome_panel' );
        update_user_meta( get_current_user_id(), 'show_welcome_panel', 0 );
    }

    public function render_welcome_widget() {
        $user  = wp_get_current_user();
        $name  = $user->display_name ?: $user->user_login;
        $title = $this->t( 'שלום, ', 'Hello, ' ) . esc_html( $name );

        $cards = [
            [ 'icon' => 'format-gallery', 'label' => $this->t( 'הוסף פריט לגלריה', 'Add Gallery Item' ),
              'url'  => admin_url( 'post-new.php?post_type=gallery_item' ) ],
            [ 'icon' => 'groups', 'label' => $this->t( 'הוסף אמן', 'Add Artist' ),
              'url'  => admin_url( 'post-new.php?post_type=artist' ) ],
            [ 'icon' => 'art', 'label' => $this->t( 'הוסף שירות', 'Add Service' ),
              'url'  => admin_url( 'post-new.php?post_type=service' ) ],
        ];

        if ( class_exists( 'WooCommerce' ) ) {
            $cards[] = [ 'icon' => 'cart', 'label' => $this->t( 'הוסף מוצר', 'Add Product' ),
                         'url'  => admin_url( 'post-new.php?post_type=product' ) ];
            $cards[] = [ 'icon' => 'money-alt', 'label' => $this->t( 'הזמנות', 'Orders' ),
                         'url'  => admin_url( 'edit.php?post_type=shop_order' ) ];
        }

        $cards[] = [ 'icon' => 'admin-page', 'label' => $this->t( 'ערוך עמודים', 'Edit Pages' ),
                     'url'  => admin_url( 'edit.php?post_type=page' ) ];
        $cards[] = [ 'icon' => 'admin-customizer', 'label' => $this->t( 'התאמה אישית', 'Customize' ),
                     'url'  => admin_url( 'customize.php' ) ];
        $cards[] = [ 'icon' => 'visibility', 'label' => $this->t( 'צפה באתר', 'View Site' ),
                     'url'  => home_url( '/' ) ];
        ?>
        <div class="hv-welcome">
            <h2><?php echo esc_html( $title ); ?></h2>
            <p><?php echo esc_html( $this->t(
                'בחרו פעולה מהירה למטה, או השתמשו בתפריט שמשמאל כדי לנהל את התכנים.',
                'Pick a quick action below, or use the side menu to manage your content.'
            ) ); ?></p>
            <div class="hv-welcome__grid">
                <?php foreach ( $cards as $card ) : ?>
                    <a href="<?php echo esc_url( $card['url'] ); ?>" class="hv-welcome__card">
                        <span class="dashicons dashicons-<?php echo esc_attr( $card['icon'] ); ?>"></span>
                        <span><?php echo esc_html( $card['label'] ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    public function render_store_widget() {
        if ( ! function_exists( 'wc_get_orders' ) ) {
            return;
        }
        $pending = count( wc_get_orders( [
            'limit'  => -1,
            'status' => [ 'processing', 'on-hold' ],
            'return' => 'ids',
        ] ) );
        $today = wc_get_orders( [
            'limit'        => -1,
            'date_created' => '>=' . strtotime( 'today' ),
            'return'       => 'ids',
        ] );
        $low_stock = 0;
        if ( function_exists( 'wc_get_products' ) ) {
            $low_stock = count( wc_get_products( [
                'limit'         => -1,
                'stock_status'  => 'outofstock',
                'return'        => 'ids',
            ] ) );
        }
        ?>
        <ul style="margin:0;line-height:1.9;">
            <li><strong><?php echo esc_html( count( $today ) ); ?></strong> &mdash; <?php echo esc_html( $this->t( 'הזמנות היום', 'orders today' ) ); ?></li>
            <li><strong><?php echo esc_html( $pending ); ?></strong> &mdash; <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=shop_order&post_status=wc-processing' ) ); ?>"><?php echo esc_html( $this->t( 'ממתינות לטיפול', 'awaiting processing' ) ); ?></a></li>
            <li><strong><?php echo esc_html( $low_stock ); ?></strong> &mdash; <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&stock_status=outofstock' ) ); ?>"><?php echo esc_html( $this->t( 'מוצרים אזלו מהמלאי', 'out-of-stock products' ) ); ?></a></li>
        </ul>
        <?php
    }

    /* -------------------------------------------------------------------- */
    /*  Notice suppression                                                   */
    /* -------------------------------------------------------------------- */

    public function suppress_third_party_notices() {
        if ( current_user_can( 'manage_options' ) ) {
            // Even for admins, kill the noisiest BSF analytics opt-in
            add_filter( 'bsf_core_stats', '__return_false' );
            return;
        }
        // For editors: remove ALL admin notices to keep things calm
        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' );
    }

    /* -------------------------------------------------------------------- */
    /*  CPT list-table columns                                               */
    /* -------------------------------------------------------------------- */

    private function register_cpt_columns() {
        foreach ( [ 'artist', 'service', 'gallery_item' ] as $pt ) {
            add_filter( "manage_{$pt}_posts_columns", [ $this, 'add_thumb_column' ] );
            add_action( "manage_{$pt}_posts_custom_column", [ $this, 'render_thumb_column' ], 10, 2 );
            add_filter( "manage_edit-{$pt}_sortable_columns", [ $this, 'sortable_columns' ] );
        }
    }

    public function add_thumb_column( $columns ) {
        $new = [];
        foreach ( $columns as $key => $label ) {
            if ( 'title' === $key ) {
                $new['hv_thumb'] = $this->t( 'תמונה', 'Image' );
            }
            $new[ $key ] = $label;
        }
        return $new;
    }

    public function render_thumb_column( $column, $post_id ) {
        if ( 'hv_thumb' !== $column ) {
            return;
        }
        $edit = get_edit_post_link( $post_id );
        if ( has_post_thumbnail( $post_id ) ) {
            printf(
                '<a href="%s">%s</a>',
                esc_url( $edit ),
                get_the_post_thumbnail( $post_id, [ 56, 56 ] )
            );
        } else {
            printf(
                '<a href="%s"><span class="hv-thumb-placeholder dashicons dashicons-format-image"></span></a>',
                esc_url( $edit )
            );
        }
    }

    public function sortable_columns( $columns ) {
        $columns['menu_order'] = 'menu_order';
        return $columns;
    }

    /* -------------------------------------------------------------------- */
    /*  Admin color scheme                                                   */
    /* -------------------------------------------------------------------- */

    const COLOR_SCHEME_SLUG = 'hand-and-vision';

    public function register_color_scheme() {
        wp_admin_css_color(
            self::COLOR_SCHEME_SLUG,
            $this->t( 'Hand and Vision', 'Hand and Vision' ),
            get_template_directory_uri() . '/admin/assets/css/hv-admin-color-scheme.css',
            [ '#17171a', '#1f1f23', '#c9a96e', '#f4f4f2' ],
            [ 'base' => '#d8d8d6', 'focus' => '#c9a96e', 'current' => '#17171a' ]
        );
    }

    /**
     * Force the HV scheme as the default when a user has not picked their own.
     * Users keep the ability to change it in their profile.
     */
    public function default_color_scheme( $value ) {
        if ( empty( $value ) || 'fresh' === $value ) {
            $user_id = get_current_user_id();
            if ( $user_id && ! get_user_meta( $user_id, 'admin_color', true ) ) {
                return self::COLOR_SCHEME_SLUG;
            }
        }
        return $value;
    }

    public function set_color_scheme_for_new_user( $user_id ) {
        update_user_meta( $user_id, 'admin_color', self::COLOR_SCHEME_SLUG );
    }
}

HV_Admin::get_instance();

} // class_exists
