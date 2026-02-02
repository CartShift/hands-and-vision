<?php
/**
 * Shop Filters (Categories + Artist)
 *
 * @package HandAndVision
 */

if ( ! function_exists( 'handandvision_is_hebrew' ) ) {
	function handandvision_is_hebrew() { return false; }
}
$is_hebrew = handandvision_is_hebrew();
$current_artist = isset( $_GET['filter_artist'] ) ? absint( $_GET['filter_artist'] ) : 0;
$current_cat_id = is_tax( 'product_cat' ) ? get_queried_object_id() : 0;

// Get Categories
$categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
) );

// Get Artists
$artists = get_posts( array(
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
) );

$shop_link = get_permalink( wc_get_page_id( 'shop' ) );
?>
<section class="hv-shop-filters-wrap">
    <div class="hv-container">
        <div class="hv-shop-filters hv-reveal">

            <!-- Categories (Tabs) -->
            <nav class="hv-shop-filters__categories" aria-label="<?php echo esc_attr( $is_hebrew ? 'ניווט קטגוריות' : 'Category navigation' ); ?>">
                <a href="<?php echo esc_url( add_query_arg( 'filter_artist', $current_artist > 0 ? $current_artist : false, $shop_link ) ); ?>"
                   class="hv-filter-btn <?php echo ( 0 === $current_cat_id && ! is_tax('product_cat') ) ? 'hv-filter-btn--active' : ''; ?>">
                    <?php echo esc_html( $is_hebrew ? 'הכל' : 'All' ); ?>
                </a>

                <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                    <?php foreach ( $categories as $cat ) :
                        $cat_link = get_term_link( $cat );
                        if ( $current_artist ) {
                            $cat_link = add_query_arg( 'filter_artist', $current_artist, $cat_link );
                        }
                    ?>
                        <a href="<?php echo esc_url( $cat_link ); ?>"
                           class="hv-filter-btn <?php echo ( $current_cat_id === $cat->term_id ) ? 'hv-filter-btn--active' : ''; ?>">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>

            <!-- Artist Filter -->
             <div class="hv-shop-filters__artist">
                <form class="hv-artist-filter-form" method="get">
                    <div class="hv-select-wrapper">
                        <select name="filter_artist" class="hv-select" onchange="this.form.submit()">
                            <option value=""><?php echo esc_html( $is_hebrew ? 'פילטור לפי אמן' : 'Filter by Artist' ); ?></option>
                            <option value="" <?php selected( $current_artist, 0 ); ?>><?php echo esc_html( $is_hebrew ? 'כל האמנים' : 'All Artists' ); ?></option>
                            <?php foreach ( $artists as $artist ) : ?>
                                <option value="<?php echo esc_attr( $artist->ID ); ?>" <?php selected( $current_artist, $artist->ID ); ?>>
                                    <?php echo esc_html( $artist->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="hv-select-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>
