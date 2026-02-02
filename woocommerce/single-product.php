<?php
/**
 * The Template for displaying single products
 * Custom Hand and Vision implementation
 *
 * @package HandAndVision
 * @since 3.3.0
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'handandvision_is_hebrew' ) ) {
	function handandvision_is_hebrew() { return false; }
}
$is_hebrew = handandvision_is_hebrew();

while ( have_posts() ) :
	the_post();

	// Ensure we have a valid product object
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}

	// Get artist info
	$artist_id = get_post_meta( get_the_ID(), '_handandvision_artist_id', true );
	$artist_name = $artist_id ? get_the_title( $artist_id ) : '';
	$artist_url = $artist_id ? get_permalink( $artist_id ) : '';

	// Required for WooCommerce hooks
	do_action( 'woocommerce_before_single_product' );
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

<main id="primary" class="hv-single-product">

    <!-- Product Hero -->
    <?php
    $hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    ?>
    <section class="hv-service-single-hero">
        <div class="hv-service-single-hero__bg">
            <?php if ( $hero_bg ) : ?>
                <img src="<?php echo esc_url( $hero_bg ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; filter: blur(20px) brightness(0.7); transform: scale(1.1);">
                <div class="hv-service-single-hero__overlay"></div>
            <?php else : ?>
                <div class="hv-service-single-hero__gradient"></div>
            <?php endif; ?>
        </div>
        <div class="hv-service-single-hero__content">
            <div class="hv-container">
                <?php handandvision_breadcrumbs(); ?>
                <div class="hv-service-single-hero__inner">
                    <span class="hv-service-single-hero__label">
                        <?php echo esc_html( $is_hebrew ? 'יצירת אמנות' : 'Artwork' ); ?>
                    </span>
                    <h1 class="hv-service-single-hero__title">
                        <?php echo esc_html( function_exists( 'handandvision_product_title' ) ? handandvision_product_title( get_the_ID() ) : get_the_title() ); ?>
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Details -->
    <section class="hv-product-main">
        <div class="hv-container">
            <div class="hv-product-layout">

                <!-- Product Gallery -->
                <div class="hv-product-gallery hv-reveal">
                    <?php
                    /**
                     * Hook: woocommerce_before_single_product_summary.
                     *
                     * @hooked woocommerce_show_product_sale_flash - 10
                     * @hooked woocommerce_show_product_images - 20
                     */
                    if ( has_post_thumbnail() ) {
                        do_action( 'woocommerce_before_single_product_summary' );
                    } else {
                        // Fallback placeholder if no image
                        echo '<div class="woocommerce-product-gallery woocommerce-product-gallery--without-images">';
                        echo '<div class="hv-product-card__placeholder" style="aspect-ratio: 3/4; min-height: 500px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5f5f5 0%, #e5e5e5 100%); color: #999; font-size: 1rem;">';
                        echo '<svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">';
                        echo '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>';
                        echo '<circle cx="8.5" cy="8.5" r="1.5"></circle>';
                        echo '<polyline points="21 15 16 10 5 21"></polyline>';
                        echo '</svg>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <!-- Product Info -->
                <div class="hv-product-info">

                    <?php if ( $artist_name ) : ?>
                        <div class="hv-product-artist hv-reveal">
                            <span class="hv-overline"><?php echo esc_html( $is_hebrew ? 'יצירה של' : 'Created by' ); ?></span>
                            <?php if ( $artist_url ) : ?>
                                <a href="<?php echo esc_url( $artist_url ); ?>" class="hv-product-artist__link">
                                    <h3><?php echo esc_html( $artist_name ); ?></h3>
                                </a>
                            <?php else : ?>
                                <h3><?php echo esc_html( $artist_name ); ?></h3>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Title moved to Hero -->

                    <div class="hv-product-price hv-reveal">
                        <?php echo wp_kses_post( $product->get_price_html() ); ?>
                    </div>

                    <?php if ( $product->get_short_description() ) : ?>
                        <div class="hv-product-excerpt hv-reveal">
                            <?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="hv-product-meta hv-reveal">
                        <?php
                        /**
                         * Hook: woocommerce_single_product_summary.
                         *
                         * @hooked woocommerce_template_single_add_to_cart - 30
                         * @hooked woocommerce_template_single_meta - 40
                         */
                        do_action( 'woocommerce_single_product_summary' );
                        ?>
                    </div>

                    <?php if ( ! $product->is_in_stock() ) : ?>
                        <div class="hv-product-status hv-product-status--sold hv-reveal">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            <?php echo esc_html( $is_hebrew ? 'יצירה זו נמכרה' : 'This piece has been sold' ); ?>
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </section>

    <!-- Product Description & Details -->
    <?php
    $has_description = $product->get_description();
    $weight = $product->get_weight();
    $dimensions = $product->get_dimensions( false );
    $categories = wp_get_post_terms( get_the_ID(), 'product_cat' );
    $has_details = $weight || $dimensions['length'] || ! empty( $categories );

    if ( $has_description || $has_details ) :
    ?>
    <section class="hv-product-description">
        <div class="hv-container hv-container--narrow">
            <div class="hv-product-tabs">
                <?php if ( $has_description ) : ?>
                <div class="hv-product-tab">
                    <h2 class="hv-headline-3 hv-reveal"><?php echo esc_html( $is_hebrew ? 'אודות היצירה' : 'About This Piece' ); ?></h2>
                    <div class="hv-product-content hv-reveal">
                        <?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $has_details ) : ?>
                <div class="hv-product-tab hv-product-specifications">
                    <h2 class="hv-headline-3 hv-reveal"><?php echo esc_html( $is_hebrew ? 'פרטים טכניים' : 'Technical Details' ); ?></h2>
                    <div class="hv-specifications-grid hv-reveal">
                        <?php if ( $dimensions['length'] ) : ?>
                            <div class="hv-spec-item">
                                <span class="hv-spec-label"><?php echo esc_html( $is_hebrew ? 'מידות' : 'Dimensions' ); ?></span>
                                <span class="hv-spec-value"><?php echo esc_html( $product->get_dimensions() ); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ( $weight ) : ?>
                            <div class="hv-spec-item">
                                <span class="hv-spec-label"><?php echo esc_html( $is_hebrew ? 'משקל' : 'Weight' ); ?></span>
                                <span class="hv-spec-value"><?php echo esc_html( wc_format_weight( $weight ) ); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $categories ) ) : ?>
                            <div class="hv-spec-item">
                                <span class="hv-spec-label"><?php echo esc_html( $is_hebrew ? 'קטגוריה' : 'Category' ); ?></span>
                                <span class="hv-spec-value">
                                    <?php echo esc_html( implode( ', ', wp_list_pluck( $categories, 'name' ) ) ); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ( $product->get_sku() ) : ?>
                            <div class="hv-spec-item">
                                <span class="hv-spec-label"><?php echo esc_html( $is_hebrew ? 'מק"ט' : 'SKU' ); ?></span>
                                <span class="hv-spec-value"><?php echo esc_html( $product->get_sku() ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Products or Artist's Other Works -->
    <?php
    $related_limit = 4;
    $artist_products = array();

    if ( $artist_id ) {
        $artist_products = handandvision_get_artist_products( $artist_id );
        // Remove current product from the list using strict comparison
        $artist_products = array_filter( $artist_products, function( $p ) {
            return (int) $p->ID !== get_the_ID();
        } );

        if ( ! empty( $artist_products ) && count( $artist_products ) > 0 ) {
            $artist_products = array_slice( $artist_products, 0, $related_limit );
    ?>
    <section class="hv-related-products">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center">
                <h2 class="hv-headline-2 hv-reveal">
                    <?php echo esc_html( $is_hebrew ? 'עוד יצירות של ' . $artist_name : 'More by ' . $artist_name ); ?>
                </h2>
            </header>

            <div class="hv-shop-grid hv-stagger">
                <?php foreach ( $artist_products as $related_product_post ) :
                    $related_product = wc_get_product( $related_product_post->ID );
                    if ( ! $related_product ) continue;
                ?>
                    <article class="hv-product-card">
                        <a href="<?php echo esc_url( get_permalink( $related_product_post->ID ) ); ?>" class="hv-product-card__link">
                            <div class="hv-product-card__image">
                                <?php echo get_the_post_thumbnail( $related_product_post->ID, 'woocommerce_thumbnail' ); ?>
                            </div>
                            <div class="hv-product-card__content">
                                <h3 class="hv-product-card__title"><?php echo esc_html( function_exists( 'handandvision_product_title' ) ? handandvision_product_title( $related_product ) : $related_product->get_name() ); ?></h3>
                                <div class="hv-product-card__price">
                                    <?php echo wp_kses_post( $related_product->get_price_html() ); ?>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
        }
    }

    if ( count( $artist_products ) < 2 ) {
        $related_ids = wc_get_related_products( get_the_ID(), $related_limit );
        if ( ! empty( $related_ids ) ) :
    ?>
    <section class="hv-related-products">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center">
                <h2 class="hv-headline-2 hv-reveal">
                    <?php echo esc_html( $is_hebrew ? 'יצירות דומות' : 'Related Pieces' ); ?>
                </h2>
            </header>

            <div class="hv-shop-grid hv-stagger">
                <?php foreach ( $related_ids as $related_id ) :
                    $related_product = wc_get_product( $related_id );
                    if ( ! $related_product ) continue;

                    $related_artist_id = get_post_meta( $related_id, '_handandvision_artist_id', true );
                    $related_artist_name = $related_artist_id ? get_the_title( $related_artist_id ) : '';
                ?>
                    <article class="hv-product-card">
                        <a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>" class="hv-product-card__link">
                            <div class="hv-product-card__image">
                                <?php echo get_the_post_thumbnail( $related_id, 'woocommerce_thumbnail' ); ?>
                            </div>
                            <div class="hv-product-card__content">
                                <?php if ( $related_artist_name ) : ?>
                                    <span class="hv-product-card__artist"><?php echo esc_html( $related_artist_name ); ?></span>
                                <?php endif; ?>
                                <h3 class="hv-product-card__title"><?php echo esc_html( function_exists( 'handandvision_product_title' ) ? handandvision_product_title( $related_product ) : $related_product->get_name() ); ?></h3>
                                <div class="hv-product-card__price">
                                    <?php echo wp_kses_post( $related_product->get_price_html() ); ?>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
        endif;
    }
    ?>

    <!-- Shop Premium CTA -->
    <section class="hv-shop-cta-premium">
        <div class="hv-shop-cta-premium__bg">
            <div class="hv-shop-cta-premium__pattern"></div>
        </div>
        <div class="hv-container">
            <div class="hv-shop-cta-premium__content">
                <div class="hv-shop-cta-premium__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <span class="hv-shop-cta-premium__overline">
                    <?php echo esc_html( $is_hebrew ? 'שאלות?' : 'Questions?' ); ?>
                </span>
                <h2 class="hv-shop-cta-premium__title">
                    <?php echo esc_html( $is_hebrew ? 'נשמח לעזור' : 'We\'re Here to Help' ); ?>
                </h2>
                <p class="hv-shop-cta-premium__desc">
                    <?php echo esc_html( $is_hebrew
                        ? 'יש לכם שאלות על היצירה? רוצים לדעת עוד על האמן/ית? צרו איתנו קשר.'
                        : 'Have questions about this piece? Want to know more about the artist? Get in touch.'
                    ); ?>
                </p>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-shop-cta-premium__btn">
                    <span><?php echo esc_html( $is_hebrew ? 'צרו קשר' : 'Contact Us' ); ?></span>
                </a>
            </div>
        </div>
    </section>

</main>

</div><!-- #product-<?php the_ID(); ?> -->

<?php
	// Required for WooCommerce hooks
	do_action( 'woocommerce_after_single_product' );
endwhile; // End of the loop.

get_footer();
