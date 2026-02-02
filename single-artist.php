<?php
/**
 * Single Template: Artist
 * Premium artist profile page
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$artist_id = get_the_ID();

// Get ACF fields with defaults
$portrait = get_field( 'artist_portrait', $artist_id );
$portrait_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist_id, 'large' );
$discipline = get_field( 'artist_discipline', $artist_id ) ?: ( handandvision_is_hebrew() ? 'אמן/ית רב-תחומי/ת' : 'Multidisciplinary Artist' );
$bio = get_field( 'artist_bio', $artist_id ) ?: '';
$gallery = get_field( 'artist_gallery', $artist_id );
$social = get_field( 'artist_social', $artist_id );
$gallery_grid_items = handandvision_normalize_gallery_grid_items( $gallery, array() );
?>

<main id="primary" class="hv-single-artist">

    <!-- Artist Hero -->
    <section class="hv-artist-hero">
        <?php handandvision_breadcrumbs(); ?>
        <div class="hv-container">
            <div class="hv-artist-hero__grid">
                <div class="hv-artist-hero__portrait hv-reveal">
                    <?php if ( $portrait_url ) : ?>
                        <img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php else : ?>
                        <div class="hv-artist-hero__placeholder"></div>
                    <?php endif; ?>
                </div>
                <div class="hv-artist-hero__content">
                    <span class="hv-overline hv-reveal"><?php echo esc_html( $discipline ); ?></span>
                    <h1 class="hv-headline-1 hv-reveal"><?php echo esc_html( get_the_title() ); ?></h1>
                    <div class="hv-artist-hero__bio hv-reveal">
                        <?php echo wp_kses_post( wpautop( $bio ) ); ?>
                    </div>
                    <?php if ( ! empty( $social ) && is_array( $social ) ) : ?>
                        <div class="hv-artist-hero__social hv-reveal">
                            <?php if ( ! empty( $social['instagram'] ) ) : ?>
                                <a href="<?php echo esc_url( $social['instagram'] ); ?>" class="hv-social-link" target="_blank" rel="noopener" aria-label="Instagram">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if ( ! empty( $social['website'] ) ) : ?>
                                <a href="<?php echo esc_url( $social['website'] ); ?>" class="hv-social-link" target="_blank" rel="noopener" aria-label="<?php echo handandvision_is_hebrew() ? 'אתר אישי' : 'Personal Website'; ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php get_template_part( 'template-parts/gallery/gallery-grid', null, array(
        'items' => $gallery_grid_items,
        'title' => handandvision_is_hebrew() ? 'הגלריה' : 'Gallery',
        'subtitle' => handandvision_is_hebrew() ? 'עבודות נבחרות' : 'Selected Works',
    ) ); ?>

    <?php
    // Get gallery items linked to this artist from the gallery_item CPT
    $artist_gallery_items = handandvision_get_artist_gallery_items( $artist_id );

    if ( ! empty( $artist_gallery_items ) ) :
    ?>
    <!-- Artist Gallery Works Section -->
    <section class="hv-section hv-section--cream hv-artist-gallery-works">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-mb-10">
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'עבודות מהגלריה' : 'Gallery Works'; ?></span>
                <h2 class="hv-headline-2 hv-line-draw hv-reveal"><?php echo handandvision_is_hebrew() ? 'יצירות מתוך הקולקטיב' : 'Works from the Collective'; ?></h2>
            </header>

            <div class="hv-artist-gallery__grid hv-stagger">
                <?php
                $total_items = count( $artist_gallery_items );
                foreach ( $artist_gallery_items as $index => $item ) :
                    // Calculate srcset for responsive images
                    $srcset = '';
                    $sizes = '(max-width: 600px) 100vw, (max-width: 1024px) 50vw, 33vw';
                    if ( ! empty( $item['image_id'] ) ) {
                        $srcset = wp_get_attachment_image_srcset( $item['image_id'] );
                    }

                    // Determine item size class for visual variety
                    $size_class = '';
                    if ( $index === 0 && $total_items > 3 ) {
                        $size_class = 'hv-artist-gallery__item--featured';
                    } elseif ( $index % 5 === 2 ) {
                        $size_class = 'hv-artist-gallery__item--wide';
                    }
                ?>
                    <article class="hv-artist-gallery__item <?php echo esc_attr( $size_class ); ?>"
                             data-index="<?php echo esc_attr( $index ); ?>"
                             style="--stagger-delay: <?php echo ( $index * 0.08 ); ?>s">
                        <a href="<?php echo esc_url( $item['url'] ); ?>"
                           class="hv-artist-gallery__link hv-lightbox"
                           data-gallery="artist-gallery"
                           data-caption="<?php echo esc_attr( $item['caption'] ?: $item['title'] ); ?><?php echo $item['year'] ? ' (' . $item['year'] . ')' : ''; ?>"
                           aria-label="<?php printf(
                               handandvision_is_hebrew() ? 'צפה בעבודה: %s' : 'View work: %s',
                               esc_attr( $item['caption'] ?: $item['title'] )
                           ); ?>">

                            <div class="hv-artist-gallery__media">
                                <img class="hv-artist-gallery__img"
                                     src="<?php echo esc_url( $item['url'] ); ?>"
                                     alt="<?php echo esc_attr( $item['caption'] ?: $item['title'] ); ?>"
                                     loading="lazy"
                                     decoding="async"
                                     <?php if ( $srcset ) : ?>srcset="<?php echo esc_attr( $srcset ); ?>"<?php endif; ?>
                                     sizes="<?php echo esc_attr( $sizes ); ?>">
                            </div>

                            <div class="hv-artist-gallery__overlay">
                                <h3 class="hv-artist-gallery__title">
                                    <?php echo esc_html( $item['caption'] ?: $item['title'] ); ?>
                                </h3>
                                <?php if ( $item['year'] || $item['project'] ) : ?>
                                    <span class="hv-artist-gallery__meta">
                                        <?php
                                        $meta_parts = array_filter( array( $item['project'], $item['year'] ) );
                                        echo esc_html( implode( ' · ', $meta_parts ) );
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <span class="hv-artist-gallery__zoom" aria-hidden="true">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    <line x1="11" y1="8" x2="11" y2="14"></line>
                                    <line x1="8" y1="11" x2="14" y2="11"></line>
                                </svg>
                            </span>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ( $total_items > 6 ) : ?>
            <div class="hv-text-center hv-mt-8 hv-reveal">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'gallery_item' ) ); ?>?artist=<?php echo esc_attr( $artist_id ); ?>"
                   class="hv-btn hv-btn--outline">
                    <?php echo handandvision_is_hebrew() ? 'לכל הגלריה' : 'View Full Gallery'; ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Artist Products Section -->
    <?php
    if ( class_exists( 'WooCommerce' ) ) {
        // Get products by this artist using the custom meta field
        $artist_products = handandvision_get_artist_products( $artist_id );

        if ( ! empty( $artist_products ) ) :
    ?>
    <section class="hv-section hv-section--white hv-artist-products">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-mb-10">
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'לרכישה' : 'For Purchase'; ?></span>
                <h2 class="hv-headline-2 hv-line-draw hv-reveal"><?php echo handandvision_is_hebrew() ? 'יצירות זמינות' : 'Available Pieces'; ?></h2>
            </header>

            <div class="hv-shop-grid hv-stagger">
                <?php foreach ( $artist_products as $product_post ) :
                    $product = wc_get_product( $product_post->ID );
                    if ( ! $product ) continue;
                ?>
                    <article class="hv-product-card">
                        <a href="<?php echo esc_url( get_permalink( $product_post->ID ) ); ?>" class="hv-product-card__link">
                            <div class="hv-product-card__image">
                                <?php
                                if ( has_post_thumbnail( $product_post->ID ) ) {
                                    echo get_the_post_thumbnail( $product_post->ID, 'woocommerce_thumbnail' );
                                } else {
                                    echo '<div class="hv-product-card__placeholder"></div>';
                                }
                                ?>
                                <?php if ( $product->is_on_sale() ) : ?>
                                    <span class="hv-product-card__badge hv-product-card__badge--sale">
                                        <?php echo esc_html( handandvision_is_hebrew() ? 'מבצע' : 'Sale' ); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ( ! $product->is_in_stock() ) : ?>
                                    <span class="hv-product-card__badge hv-product-card__badge--sold">
                                        <?php echo esc_html( handandvision_is_hebrew() ? 'נמכר' : 'Sold' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="hv-product-card__content">
                                <h3 class="hv-product-card__title"><?php echo esc_html( $product->get_name() ); ?></h3>
                                <div class="hv-product-card__price">
                                    <?php echo wp_kses_post( $product->get_price_html() ); ?>
                                </div>
                            </div>
                        </a>

                        <!-- Quick Add to Cart -->
                        <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
                            <div class="hv-product-card__actions">
                                <?php
                                // Intelligent linking: Link to product if variable/grouped, or AJAX add if simple
                                if ( $product->is_type( 'simple' ) ) {
                                    echo apply_filters(
                                        'woocommerce_loop_add_to_cart_link',
                                        sprintf(
                                            '<a href="%s" data-quantity="1" class="%s" data-product_id="%s">%s</a>',
                                            esc_url( $product->add_to_cart_url() ),
                                            esc_attr( 'hv-btn hv-btn--small hv-btn--outline add_to_cart_button ajax_add_to_cart' ),
                                            esc_attr( $product->get_id() ),
                                            esc_html( handandvision_is_hebrew() ? 'הוסף לעגלה' : 'Add to Cart' )
                                        ),
                                        $product
                                    );
                                } else {
                                     echo sprintf(
                                        '<a href="%s" class="hv-btn hv-btn--small hv-btn--outline">%s</a>',
                                        esc_url( get_permalink( $product_post->ID ) ),
                                        esc_html( handandvision_is_hebrew() ? 'צפה באפשרויות' : 'View Options' )
                                    );
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="hv-text-center hv-shop-link-wrapper hv-reveal">
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hv-btn hv-btn--outline">
                    <?php echo handandvision_is_hebrew() ? 'לחנות המלאה' : 'Visit Full Shop'; ?>
                </a>
            </div>
        </div>
    </section>
    <?php
        endif;
    }
    ?>

    <!-- CTA -->
    <section class="hv-cta-premium">
        <div class="hv-container hv-container--narrow hv-text-center">
            <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'מתעניינים?' : 'Interested?'; ?></span>
            <h2 class="hv-headline-2 hv-reveal"><?php echo handandvision_is_hebrew() ? 'צרו קשר' : 'Contact Us'; ?></h2>
            <p class="hv-subtitle hv-cta-subtitle hv-reveal">
                <?php echo handandvision_is_hebrew()
                    ? 'לפרטים נוספים על עבודות האמן/ית, זמינות לרכישה, או הזמנות מיוחדות'
                    : 'For more details on the artist\'s works, purchase availability, or special commissions'; ?>
            </p>
            <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--primary hv-reveal"><?php echo handandvision_is_hebrew() ? 'צרו קשר' : 'Contact Us'; ?></a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
