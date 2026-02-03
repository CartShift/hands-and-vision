<?php
/**
 * Single Template: Artist
 * Premium artist profile page ("The Exhibition")
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$artist_id = get_the_ID();

// Artist Data
$portrait = get_field( 'artist_portrait', $artist_id );
$portrait_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist_id, 'full' );
$discipline = get_field( 'artist_discipline', $artist_id );
if ( empty( $discipline ) ) {
    $discipline = handandvision_is_hebrew() ? 'אמן/ית' : 'Artist';
}
$bio_raw = get_field( 'artist_biography', $artist_id ) ?: get_field( 'artist_bio', $artist_id ) ?: '';

// Socials
$social = get_field( 'artist_social', $artist_id );
if ( empty( $social ) || ! is_array( $social ) ) {
	$ig = get_field( 'artist_instagram', $artist_id );
	$fb = get_field( 'artist_facebook', $artist_id );
	$web = get_field( 'artist_website', $artist_id );
	$social = array_filter( array(
		'instagram' => $ig ?: '',
		'facebook'   => $fb ?: '',
		'website'    => $web ?: '',
	) );
}

?>

<main id="primary" class="hv-single-artist-premium">

    <!-- Cinematic Hero -->
    <section class="hv-artist-hero-premium">
        <div class="hv-container">
            <div class="hv-artist-hero-premium__grid">

                <!-- Sticky Portrait Side -->
                <div class="hv-artist-hero-premium__visual">
                    <div class="hv-sticky-portrait" style="view-transition-name: artist-portrait-<?php echo esc_attr( $artist_id ); ?>; top: calc(var(--hv-header-height, 80px) + 40px);">
                         <?php
                         $video_url = get_field('artist_video', $artist_id);
                         if ( $video_url ) : ?>
                            <video class="hv-img-cover" autoplay muted loop playsinline poster="<?php echo esc_url($portrait_url); ?>">
                                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                            </video>
                         <?php elseif ( $portrait_url ) : ?>
                            <img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php the_title_attribute(); ?>" class="hv-img-cover">
                        <?php else : ?>
                            <div class="hv-img-placeholder"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content Side -->
                <div class="hv-artist-hero-premium__content">
                    <div class="hv-artist-header hv-mb-8">
                        <span class="hv-overline hv-reveal"><?php echo esc_html( $discipline ); ?></span>
                        <h1 class="hv-headline-1 hv-reveal" style="view-transition-name: header-text;"><?php echo esc_html( get_the_title() ); ?></h1>
                    </div>

                    <div class="hv-artist-bio hv-text-body-large hv-reveal">
                         <?php
                        $bio_display = function_exists( 'handandvision_acf_display_value' )
                            ? handandvision_acf_display_value( $bio_raw, handandvision_is_hebrew() ? 'ביוגרפיה' : 'Biography', 'html' )
                            : $bio_raw;
                        echo $bio_display ? wp_kses_post( wpautop( $bio_display ) ) : '';
                        ?>
                    </div>

                    <?php
                    $social_links = array_filter( $social, function ( $url ) { return is_string( $url ) && $url !== ''; } );
                    if ( ! empty( $social_links ) ) : ?>
                    <div class="hv-artist-socials hv-mt-6 hv-reveal">
                        <?php foreach ( $social_links as $platform => $url ) : ?>
                            <a href="<?php echo esc_url( $url ); ?>" class="hv-social-link-text" target="_blank" rel="noopener"><?php echo esc_html( ucfirst( $platform ) ); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <?php
    // Gallery Items
    $artist_gallery_items = handandvision_get_artist_gallery_items( $artist_id );
    if ( ! empty( $artist_gallery_items ) ) :
    ?>
    <section class="hv-section hv-section--white hv-artist-gallery-premium">
        <div class="hv-container">
            <header class="hv-section-header hv-mb-10">
                <h2 class="hv-headline-3"><?php echo handandvision_is_hebrew() ? 'עבודות נבחרות' : 'Selected Works'; ?></h2>
            </header>

            <div class="hv-masonry-gallery">
                 <?php foreach ( $artist_gallery_items as $index => $item ) : ?>
                    <a href="<?php echo esc_url( $item['url'] ); ?>"
                       class="hv-masonry-item hv-lightbox"
                       data-gallery="artist-gallery"
                       data-caption="<?php echo esc_attr( $item['caption'] ?: $item['title'] ); ?>">
                        <img src="<?php echo esc_url( $item['url'] ); ?>"
                             alt="<?php echo esc_attr( $item['caption'] ?: $item['title'] ); ?>"
                             loading="lazy">
                        <div class="hv-masonry-overlay">
                            <span><?php echo esc_html( $item['caption'] ?: $item['title'] ); ?></span>
                        </div>
                    </a>
                 <?php endforeach; ?>
            </div>

        </div>
    </section>
    <?php endif; ?>


    <!-- Products Section -->
    <?php
    if ( class_exists( 'WooCommerce' ) ) {
        $artist_products = handandvision_get_artist_products( $artist_id );
        if ( ! empty( $artist_products ) ) :
    ?>
    <section class="hv-section hv-section--cream hv-artist-shop-premium">
        <div class="hv-container">
             <header class="hv-section-header hv-mb-10 hv-flex-between">
                <h2 class="hv-headline-3"><?php echo handandvision_is_hebrew() ? 'זמין לרכישה' : 'Available for Collection'; ?></h2>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hv-link-view-all">
                    <?php echo handandvision_is_hebrew() ? 'לחנות המלאה' : 'View Shop'; ?>
                </a>
            </header>

            <div class="hv-product-grid-premium">
                <?php foreach ( $artist_products as $product_post ) :
                    $product = wc_get_product( $product_post->ID );
                    if ( ! $product ) continue;
                ?>
                <div class="hv-product-card-minimal">
                    <a href="<?php echo esc_url( get_permalink( $product_post->ID ) ); ?>" class="hv-product-card-minimal__link">
                        <div class="hv-product-card-minimal__image">
                             <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                        </div>
                        <div class="hv-product-card-minimal__details">
                            <h3 class="hv-product-card-minimal__title"><?php echo esc_html( $product->get_name() ); ?></h3>
                            <span class="hv-product-card-minimal__price"><?php echo $product->get_price_html(); ?></span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>
    <?php endif; } ?>

</main>

<?php get_footer(); ?>
