<?php
/**
 * The Template for displaying product archives (shop page)
 * Custom implementation: Products organized by categories
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_hebrew = handandvision_is_hebrew();
?>

<main id="primary" class="hv-shop-page">

<?php
$artist_count = wp_count_posts( 'artist' )->publish;
$product_count = wp_count_posts( 'product' )->publish;
get_template_part( 'hero/page-hero', null, array(
	'overline'   => $is_hebrew ? 'האוסף שלנו' : 'Our Collection',
	'title'      => array(
		array( 'text' => $is_hebrew ? 'חנות' : 'Art Gallery', 'accent' => false ),
		array( 'text' => $is_hebrew ? 'האמנות' : 'Shop', 'accent' => true ),
	),
	'subtitle'   => $is_hebrew
		? 'גלו יצירות אמנות ייחודיות מאמנים מובילים. כל יצירה מספרת סיפור משלה ומזמינה אתכם לחוויה אומנותית ייחודית'
		: 'Discover unique artworks from leading artists. Each piece tells its own story and invites you to a unique artistic experience',
	'stats'      => array(
		array( 'number' => ( $artist_count > 0 ? $artist_count : '50' ) . '+', 'label' => $is_hebrew ? 'אמנים' : 'Artists' ),
		array( 'number' => ( $product_count > 0 ? $product_count : '200' ) . '+', 'label' => $is_hebrew ? 'יצירות' : 'Artworks' ),
	),
	'scroll_text'=> $is_hebrew ? 'גלול לגילוי' : 'Scroll to discover',
) );
?>

    <?php
    get_template_part( 'template-parts/shop-filters' );
    ?>

    <?php
    // Get all product categories (excluding 'uncategorized')
    $product_categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_option( 'default_product_cat', 0 ) ), // Exclude default uncategorized
    ) );

    if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) :
        foreach ( $product_categories as $category ) :
            // Get products in this category
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 8,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $category->term_id,
                    ),
                ),
                'post_status'    => 'publish',
            );

            // Apply Artist Filter
            if ( ! empty( $_GET['filter_artist'] ) ) {
                $args['meta_query'] = array(
                    array(
                        'key'     => '_handandvision_artist_id',
                        'value'   => absint( $_GET['filter_artist'] ),
                        'compare' => '=',
                    ),
                );
            }

            $products = new WP_Query( $args );

            if ( $products->have_posts() ) :
    ?>

    <!-- Category Section: <?php echo esc_attr( $category->name ); ?> -->
    <section class="hv-shop-category hv-reveal">
        <div class="hv-container">
            <header class="hv-shop-category__header">
                <div class="hv-shop-category__title-wrap">
                    <h2 class="hv-headline-2 hv-reveal"><?php echo esc_html( $category->name ); ?></h2>
                    <?php if ( $category->description ) : ?>
                        <p class="hv-shop-category__description hv-reveal"><?php echo esc_html( $category->description ); ?></p>
                    <?php endif; ?>
                </div>
                <?php
                $category_link = get_term_link( $category );
                if ( is_wp_error( $category_link ) ) {
                    $category_link = get_permalink( wc_get_page_id( 'shop' ) );
                }
                if ( $products->found_posts > 8 ) : ?>
                    <a href="<?php echo esc_url( $category_link ); ?>" class="hv-btn hv-btn--outline hv-btn--small hv-reveal">
                        <?php echo esc_html( $is_hebrew ? 'צפו בכל היצירות' : 'View All' ); ?>
                    </a>
                <?php endif; ?>
            </header>

            <div class="hv-shop-grid hv-stagger">
                <?php while ( $products->have_posts() ) : $products->the_post();
                    global $product;

                    // Get artist info if available
                    $artist_id = get_post_meta( get_the_ID(), '_handandvision_artist_id', true );
                    $artist_name = $artist_id ? get_the_title( $artist_id ) : '';
                ?>
                    <article <?php wc_product_class( 'hv-product-card', $product ); ?>>
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="hv-product-card__link">
                            <div class="hv-product-card__image">
                                <?php
                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) );
                                } else {
                                    echo '<div class="hv-product-card__placeholder">';
                                    echo '<svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">';
                                    echo '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>';
                                    echo '<circle cx="8.5" cy="8.5" r="1.5"></circle>';
                                    echo '<polyline points="21 15 16 10 5 21"></polyline>';
                                    echo '</svg>';
                                    echo '</div>';
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
                                <h3 class="hv-product-card__title"><?php echo esc_html( get_the_title() ); ?></h3>
                                <div class="hv-product-card__price">
                                    <?php echo wp_kses_post( $product->get_price_html() ); ?>
                                </div>
                            </div>
                        </a>

                        <!-- Quick Add to Cart -->
                        <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
                            <div class="hv-product-card__actions">
                                <?php
                                $loop_args = array( 'quantity' => 1 );
                                if ( $product->is_type( 'simple' ) ) {
                                    echo apply_filters(
                                        'woocommerce_loop_add_to_cart_link',
                                        sprintf(
                                            '<button type="button" data-product_id="%s" class="hv-btn hv-btn--small hv-btn--outline add_to_cart_button ajax_add_to_cart" aria-label="%s">%s</button>',
                                            esc_attr( $product->get_id() ),
                                            esc_attr( $product->add_to_cart_description() ),
                                            esc_html( $is_hebrew ? 'הוסף לעגלה' : 'Add to Cart' )
                                        ),
                                        $product,
                                        $loop_args
                                    );
                                } else {
                                     echo sprintf(
                                        '<a href="%s" class="hv-btn hv-btn--small hv-btn--outline">%s</a>',
                                        esc_url( get_permalink() ),
                                        esc_html( $is_hebrew ? 'צפה באפשרויות' : 'View Options' )
                                    );
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php wp_reset_postdata(); ?>
        </div>
    </section>

    <?php
            endif; // have_posts
        endforeach; // categories
    else :
        // No categories - show all products in simple grid
    ?>

    <section class="hv-shop-all">
        <div class="hv-container">
            <div class="hv-shop-grid hv-stagger">
                <?php
                if ( woocommerce_product_loop() ) {
                    while ( have_posts() ) {
                        the_post();
                        wc_get_template_part( 'content', 'product' );
                    }
                } else {
                    echo '<p class="woocommerce-info">' . esc_html( $is_hebrew ? 'לא נמצאו מוצרים.' : 'No products found.' ) . '</p>';
                }
                ?>
            </div>

            <?php
            // Pagination
            if ( function_exists( 'woocommerce_pagination' ) ) {
                woocommerce_pagination();
            }
            ?>
        </div>
    </section>

    <?php endif; ?>

    <!-- Shop CTA -->
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
                    <?php echo esc_html( $is_hebrew ? 'מעוניינים בהזמנה מיוחדת?' : 'Interested in a Commission?' ); ?>
                </span>
                <h2 class="hv-shop-cta-premium__title">
                    <?php echo esc_html( $is_hebrew ? 'צרו איתנו קשר' : 'Get In Touch' ); ?>
                </h2>
                <p class="hv-shop-cta-premium__desc">
                    <?php echo esc_html( $is_hebrew
                        ? 'מחפשים משהו ספציפי? האמנים שלנו זמינים להזמנות מיוחדות ויצירות בהתאמה אישית.'
                        : 'Looking for something specific? Our artists are available for commissions and custom pieces.'
                    ); ?>
                </p>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-shop-cta-premium__btn">
                    <span><?php echo esc_html( $is_hebrew ? 'צרו קשר' : 'Contact Us' ); ?></span>
                </a>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
