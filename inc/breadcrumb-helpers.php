<?php
/**
 * Hand and Vision - Custom Breadcrumb System
 *
 * Smart breadcrumb navigation for all post types and templates
 *
 * @package HandAndVision
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Display Hand and Vision breadcrumbs
 *
 * @param array $args Optional configuration args
 * @return void
 */
function handandvision_breadcrumbs( $args = array() ) {
    // Check if on front page
    if ( is_front_page() ) {
        return;
    }

    $chevron = handandvision_is_hebrew()
        ? '<svg class="hv-breadcrumb-separator" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>'
        : '<svg class="hv-breadcrumb-separator" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
    $defaults = array(
        'separator' => $chevron,
        'home_label' => handandvision_is_hebrew() ? 'בית' : 'Home',
        'show_current' => true,
        'echo' => true,
    );

    $args = wp_parse_args( $args, $defaults );

    $breadcrumb = handandvision_build_breadcrumb_trail( $args );

    if ( $args['echo'] ) {
        echo $breadcrumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trail built from esc_url/esc_html and hardcoded markup
    } else {
        return $breadcrumb;
    }
}

/**
 * Build breadcrumb trail HTML
 *
 * @param array $args Configuration args
 * @return string HTML output
 */
function handandvision_build_breadcrumb_trail( $args ) {
    $items = array();
    $separator = $args['separator'];
    $schema_items = array();

    // Home link (always first)
    $home_url = home_url( '/' );
    $items[] = sprintf(
        '<a href="%s" class="hv-breadcrumb-item hv-breadcrumb-home">%s</a>',
        esc_url( $home_url ),
        esc_html( $args['home_label'] )
    );

    // Add home to schema
    $schema_items[] = array(
        '@type' => 'ListItem',
        'position' => 1,
        'name' => $args['home_label'],
        'item' => $home_url,
    );

    // Build trail based on page type
    $position = 2;
    if ( is_singular() ) {
        $trail_items = handandvision_get_singular_breadcrumb_items();
        $items = array_merge( $items, array_column( $trail_items, 'html' ) );
        foreach ( $trail_items as $trail_item ) {
            if ( isset( $trail_item['url'] ) && isset( $trail_item['name'] ) ) {
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                    'item' => $trail_item['url'],
                );
            } elseif ( isset( $trail_item['name'] ) ) {
                // Current item (no URL)
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                );
            }
        }
    } elseif ( is_post_type_archive() ) {
        $trail_items = handandvision_get_archive_breadcrumb_items();
        $items = array_merge( $items, array_column( $trail_items, 'html' ) );
        foreach ( $trail_items as $trail_item ) {
            if ( isset( $trail_item['name'] ) ) {
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                );
            }
        }
    } elseif ( is_tax() || is_category() || is_tag() ) {
        $trail_items = handandvision_get_taxonomy_breadcrumb_items();
        $items = array_merge( $items, array_column( $trail_items, 'html' ) );
        foreach ( $trail_items as $trail_item ) {
            if ( isset( $trail_item['url'] ) && isset( $trail_item['name'] ) ) {
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                    'item' => $trail_item['url'],
                );
            } elseif ( isset( $trail_item['name'] ) ) {
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                );
            }
        }
    } elseif ( is_search() ) {
        $search_term = get_search_query();
        $search_label = sprintf(
            '%s: %s',
            handandvision_is_hebrew() ? 'חיפוש' : 'Search',
            $search_term
        );
        $items[] = sprintf(
            '<span class="hv-breadcrumb-item hv-breadcrumb-current">%s</span>',
            esc_html( $search_label )
        );
        $schema_items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $search_label,
        );
    } elseif ( is_404() ) {
        $label_404 = handandvision_is_hebrew() ? 'דף לא נמצא' : '404 - Page Not Found';
        $items[] = sprintf(
            '<span class="hv-breadcrumb-item hv-breadcrumb-current">%s</span>',
            esc_html( $label_404 )
        );
        $schema_items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $label_404,
        );
    } elseif ( is_page() ) {
        $trail_items = handandvision_get_page_breadcrumb_items();
        $items = array_merge( $items, array_column( $trail_items, 'html' ) );
        foreach ( $trail_items as $trail_item ) {
            if ( isset( $trail_item['url'] ) && isset( $trail_item['name'] ) ) {
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                    'item' => $trail_item['url'],
                );
            } elseif ( isset( $trail_item['name'] ) ) {
                $schema_items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $trail_item['name'],
                );
            }
        }
    }

    // Build JSON-LD Schema
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $schema_items,
    );

    // Build final HTML
    $output = '<nav class="hv-breadcrumbs" aria-label="' . esc_attr( handandvision_is_hebrew() ? 'ניווט מסלול' : 'Breadcrumb navigation' ) . '">';
    $output .= '<div class="hv-breadcrumb-trail">';

    $total_items = count( $items );
    foreach ( $items as $index => $item ) {
        $output .= $item;

        // Add separator except after last item
        if ( $index < $total_items - 1 ) {
            $output .= $separator;
        }
    }

    $output .= '</div>';

    // Add JSON-LD Schema
    $output .= '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';

    $output .= '</nav>';

    return $output;
}

/**
 * Get breadcrumb items for singular posts
 *
 * @return array Breadcrumb items with name, url, and html
 */
function handandvision_get_singular_breadcrumb_items() {
    $items = array();
    $post_type = get_post_type();
    $post_type_object = get_post_type_object( $post_type );

    // Add post type archive link (if not a page)
    if ( $post_type !== 'page' && $post_type !== 'post' ) {
        $archive_link = get_post_type_archive_link( $post_type );

        if ( $archive_link ) {
            $archive_label = '';

            switch ( $post_type ) {
                case 'service':
                    $archive_label = handandvision_is_hebrew() ? 'שירותים' : 'Services';
                    break;
                case 'artist':
                    $archive_label = handandvision_is_hebrew() ? 'אמנים' : 'Artists';
                    break;
                case 'product':
                    $archive_label = handandvision_is_hebrew() ? 'חנות' : 'Shop';
                    break;
                case 'gallery_item':
                    $archive_label = handandvision_is_hebrew() ? 'גלריה' : 'Gallery';
                    break;
                default:
                    $archive_label = $post_type_object->labels->name;
            }

            $items[] = array(
                'name' => $archive_label,
                'url' => $archive_link,
                'html' => sprintf(
                    '<a href="%s" class="hv-breadcrumb-item hv-breadcrumb-archive">%s</a>',
                    esc_url( $archive_link ),
                    esc_html( $archive_label )
                ),
            );
        }
    }

    // For standard posts, add blog page link
    if ( $post_type === 'post' ) {
        $blog_page_id = get_option( 'page_for_posts' );
        if ( $blog_page_id ) {
            $blog_title = get_the_title( $blog_page_id );
            $blog_url = get_permalink( $blog_page_id );
            $items[] = array(
                'name' => $blog_title,
                'url' => $blog_url,
                'html' => sprintf(
                    '<a href="%s" class="hv-breadcrumb-item">%s</a>',
                    esc_url( $blog_url ),
                    esc_html( $blog_title )
                ),
            );
        }
    }

    // For products, add categories
    if ( $post_type === 'product' && function_exists( 'wc_get_product_category_list' ) ) {
        $categories = get_the_terms( get_the_ID(), 'product_cat' );
        if ( $categories && ! is_wp_error( $categories ) ) {
            $main_cat = array_shift( $categories );
            $cat_url = get_term_link( $main_cat );
            $items[] = array(
                'name' => $main_cat->name,
                'url' => $cat_url,
                'html' => sprintf(
                    '<a href="%s" class="hv-breadcrumb-item hv-breadcrumb-category">%s</a>',
                    esc_url( $cat_url ),
                    esc_html( $main_cat->name )
                ),
            );
        }
    }

    // Add parent pages for hierarchical post types
    if ( is_post_type_hierarchical( $post_type ) ) {
        $parent_items = handandvision_get_parent_pages( get_the_ID() );
        $items = array_merge( $items, $parent_items );
    }

    // Current page/post
    $current_title = get_the_title();
    $items[] = array(
        'name' => $current_title,
        'html' => sprintf(
            '<span class="hv-breadcrumb-item hv-breadcrumb-current" aria-current="page">%s</span>',
            esc_html( $current_title )
        ),
    );

    return $items;
}

/**
 * Get breadcrumb items for archive pages
 *
 * @return array Breadcrumb items with name, url, and html
 */
function handandvision_get_archive_breadcrumb_items() {
    $items = array();
    $post_type = get_post_type();
    $post_type_object = get_post_type_object( $post_type );

    if ( $post_type_object ) {
        $archive_label = '';

        switch ( $post_type ) {
            case 'service':
                $archive_label = handandvision_is_hebrew() ? 'שירותים' : 'Services';
                break;
            case 'artist':
                $archive_label = handandvision_is_hebrew() ? 'אמנים' : 'Artists';
                break;
            case 'product':
                $archive_label = handandvision_is_hebrew() ? 'חנות' : 'Shop';
                break;
            case 'gallery_item':
                $archive_label = handandvision_is_hebrew() ? 'גלריה' : 'Gallery';
                break;
            default:
                $archive_label = $post_type_object->labels->name;
        }

        $items[] = array(
            'name' => $archive_label,
            'html' => sprintf(
                '<span class="hv-breadcrumb-item hv-breadcrumb-current" aria-current="page">%s</span>',
                esc_html( $archive_label )
            ),
        );
    }

    return $items;
}

/**
 * Get breadcrumb items for taxonomy pages
 *
 * @return array Breadcrumb items with name, url, and html
 */
function handandvision_get_taxonomy_breadcrumb_items() {
    $items = array();
    $term = get_queried_object();

    if ( ! $term || is_wp_error( $term ) ) {
        return $items;
    }

    // Add post type archive link
    $post_type = '';

    if ( $term->taxonomy === 'product_cat' || $term->taxonomy === 'product_tag' ) {
        $post_type = 'product';
        $archive_label = handandvision_is_hebrew() ? 'חנות' : 'Shop';
        $archive_url = get_post_type_archive_link( $post_type );
        if ( $archive_url ) {
            $items[] = array(
                'name' => $archive_label,
                'url' => $archive_url,
                'html' => sprintf(
                    '<a href="%s" class="hv-breadcrumb-item hv-breadcrumb-archive">%s</a>',
                    esc_url( $archive_url ),
                    esc_html( $archive_label )
                ),
            );
        }
    } elseif ( $term->taxonomy === 'category' || $term->taxonomy === 'post_tag' ) {
        $blog_page_id = get_option( 'page_for_posts' );
        if ( $blog_page_id ) {
            $blog_title = get_the_title( $blog_page_id );
            $blog_url = get_permalink( $blog_page_id );
            $items[] = array(
                'name' => $blog_title,
                'url' => $blog_url,
                'html' => sprintf(
                    '<a href="%s" class="hv-breadcrumb-item">%s</a>',
                    esc_url( $blog_url ),
                    esc_html( $blog_title )
                ),
            );
        }
    }

    // Add parent terms
    if ( $term->parent ) {
        $parent_items = handandvision_get_parent_terms( $term->term_id, $term->taxonomy );
        $items = array_merge( $items, $parent_items );
    }

    // Current term
    $items[] = array(
        'name' => $term->name,
        'html' => sprintf(
            '<span class="hv-breadcrumb-item hv-breadcrumb-current" aria-current="page">%s</span>',
            esc_html( $term->name )
        ),
    );

    return $items;
}

/**
 * Get breadcrumb items for regular pages
 *
 * @return array Breadcrumb items with name, url, and html
 */
function handandvision_get_page_breadcrumb_items() {
    $items = array();

    // Add parent pages
    $parent_items = handandvision_get_parent_pages( get_the_ID() );
    $items = array_merge( $items, $parent_items );

    // Current page
    $current_title = get_the_title();
    $items[] = array(
        'name' => $current_title,
        'html' => sprintf(
            '<span class="hv-breadcrumb-item hv-breadcrumb-current" aria-current="page">%s</span>',
            esc_html( $current_title )
        ),
    );

    return $items;
}

/**
 * Get parent page breadcrumb items
 *
 * @param int $page_id Page ID
 * @return array Parent page items with name, url, and html
 */
function handandvision_get_parent_pages( $page_id ) {
    $items = array();
    $parents = array();

    // Get all parents
    $parent_id = wp_get_post_parent_id( $page_id );
    while ( $parent_id ) {
        $parents[] = $parent_id;
        $parent_id = wp_get_post_parent_id( $parent_id );
    }

    // Reverse to show from top-level down
    $parents = array_reverse( $parents );

    // Build parent links
    foreach ( $parents as $parent ) {
        $parent_title = get_the_title( $parent );
        $parent_url = get_permalink( $parent );
        $items[] = array(
            'name' => $parent_title,
            'url' => $parent_url,
            'html' => sprintf(
                '<a href="%s" class="hv-breadcrumb-item">%s</a>',
                esc_url( $parent_url ),
                esc_html( $parent_title )
            ),
        );
    }

    return $items;
}

/**
 * Get parent term breadcrumb items
 *
 * @param int    $term_id  Term ID
 * @param string $taxonomy Taxonomy name
 * @return array Parent term items with name, url, and html
 */
function handandvision_get_parent_terms( $term_id, $taxonomy ) {
    $items = array();
    $parents = array();

    // Get all parent terms
    $term = get_term( $term_id, $taxonomy );
    while ( $term && $term->parent ) {
        $term = get_term( $term->parent, $taxonomy );
        if ( $term && ! is_wp_error( $term ) ) {
            $parents[] = $term;
        }
    }

    // Reverse to show from top-level down
    $parents = array_reverse( $parents );

    // Build parent links
    foreach ( $parents as $parent ) {
        $parent_url = get_term_link( $parent );
        $items[] = array(
            'name' => $parent->name,
            'url' => $parent_url,
            'html' => sprintf(
                '<a href="%s" class="hv-breadcrumb-item">%s</a>',
                esc_url( $parent_url ),
                esc_html( $parent->name )
            ),
        );
    }

    return $items;
}
