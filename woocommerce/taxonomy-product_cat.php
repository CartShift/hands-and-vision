<?php
/**
 * The Template for displaying product category archives
 * Custom Hand and Vision implementation
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'handandvision_is_hebrew' ) ) {
	function handandvision_is_hebrew() { return false; }
}
$is_hebrew = handandvision_is_hebrew();
$term = get_queried_object();
if ( ! $term || ! isset( $term->name ) ) {
    wp_safe_redirect( get_permalink( wc_get_page_id( 'shop' ) ) );
    exit;
}
?>

<main id="primary" class="hv-shop-category-archive">

    <!-- Shop Filters (Tabs) -->
    <?php get_template_part( 'template-parts/shop-filters' ); ?>

    <!-- Products Grid -->
    <section class="hv-shop-all">
        <div class="hv-container">

            <?php if ( woocommerce_product_loop() ) : ?>

            <div class="hv-shop-grid hv-stagger">
                <?php while ( have_posts() ) : the_post();
                    global $product;

                    // Get artist info if available
                    $artist_id = get_post_meta( get_the_ID(), '_handandvision_artist_id', true );
                    $artist_name = $artist_id ? get_the_title( $artist_id ) : '';
                ?>
                    <article <?php wc_product_class( 'hv-product-card', $product ); ?>>
                        <a href="<?php the_permalink(); ?>" class="hv-product-card__link">
                            <div class="hv-product-card__image">
                                <?php
                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( 'woocommerce_thumbnail' );
                                } else {
                                    echo '<div class="hv-product-card__placeholder"></div>';
                                }
                                ?>
                                <?php if ( $product->is_on_sale() ) : ?>
                                    <span class="hv-product-card__badge hv-product-card__badge--sale">
                                        <?php echo esc_html( $is_hebrew ? 'מבצע' : 'Sale' ); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ( ! $product->is_in_stock() ) : ?>
                                    <span class="hv-product-card__badge hv-product-card__badge--sold">
                                        <?php echo esc_html( $is_hebrew ? 'נמכר' : 'Sold' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="hv-product-card__content">
                                <?php if ( $artist_name ) : ?>
                                    <span class="hv-product-card__artist"><?php echo esc_html( $artist_name ); ?></span>
                                <?php endif; ?>
                                <h3 class="hv-product-card__title"><?php echo esc_html( function_exists( 'handandvision_product_title' ) ? handandvision_product_title( get_the_ID() ) : get_the_title() ); ?></h3>
                                <div class="hv-product-card__price">
                                    <?php echo wp_kses_post( $product->get_price_html() ); ?>
                                </div>
                            </div>
                        </a>

                        <!-- Quick Add to Cart -->
                        <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
                            <div class="hv-product-card__actions">
                                <?php
                                echo apply_filters(
                                    'woocommerce_loop_add_to_cart_link',
                                    sprintf(
                                        '<a href="%s" data-quantity="1" class="%s" data-product_id="%s">%s</a>',
                                        esc_url( $product->add_to_cart_url() ),
                                        esc_attr( 'hv-btn hv-btn--small hv-btn--outline add_to_cart_button ajax_add_to_cart' ),
                                        esc_attr( $product->get_id() ),
                                        esc_html( $is_hebrew ? 'הוסף לעגלה' : 'Add to Cart' )
                                    ),
                                    $product
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            if ( function_exists( 'woocommerce_pagination' ) ) {
                woocommerce_pagination();
            }
            ?>

            <?php else : ?>
                <p class="woocommerce-info hv-text-center" style="padding: var(--hv-space-10) 0;">
                    <?php echo esc_html( $is_hebrew ? 'לא נמצאו מוצרים בקטגוריה זו.' : 'No products found in this category.' ); ?>
                </p>
            <?php endif; ?>

        </div>
    </section>

    <!-- Back to Shop -->
    <section class="hv-shop-cta">
        <div class="hv-container hv-container--narrow hv-text-center">
            <h2 class="hv-headline-2 hv-reveal"><?php echo esc_html( $is_hebrew ? 'חפשו עוד יצירות' : 'Explore More Pieces' ); ?></h2>
            <p class="hv-subtitle hv-reveal" style="margin: var(--hv-space-5) auto var(--hv-space-7); max-width: 550px;">
                <?php echo esc_html( $is_hebrew
                    ? 'גלו עוד יצירות מקטגוריות שונות בחנות שלנו'
                    : 'Discover more pieces from different categories in our shop'
                ); ?>
            </p>
            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hv-btn hv-btn--primary hv-reveal">
                <?php echo esc_html( $is_hebrew ? 'לכל החנות' : 'View All Shop' ); ?>
            </a>
        </div>
    </section>

</main>

<?php
get_footer();
