<?php
/**
 * Front Page Template
 * Premium luxury art gallery homepage with video hero
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$front_page_id = (int) get_option( 'page_on_front' );
if ( ! $front_page_id ) {
	$front_page_id = get_the_ID();
}

$hero_video = get_field( 'hero_video', $front_page_id );
$hero_video_url = ( $hero_video && is_array( $hero_video ) && ! empty( $hero_video['url'] ) ) ? $hero_video['url'] : '';
$hero_poster = get_field( 'hero_poster', $front_page_id );
$hero_title = get_field( 'hero_title', $front_page_id ) ?: '';
$hero_subtitle = get_field( 'hero_subtitle', $front_page_id ) ?: '';
$intro_text = get_field( 'intro_text', $front_page_id ) ?: '';

$featured_services = get_field( 'featured_services', $front_page_id );
if ( empty( $featured_services ) ) {
	$featured_services = get_posts( array(
		'post_type'              => 'service',
		'posts_per_page'         => 20,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'post_status'            => 'publish',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	) );
}

$featured_artists = get_field( 'featured_artists', $front_page_id );
if ( empty( $featured_artists ) ) {
	$featured_artists = get_posts( array(
		'post_type'              => 'artist',
		'posts_per_page'         => 4,
		'orderby'                => 'menu_order date',
		'order'                  => 'ASC',
		'post_status'            => 'publish',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	) );
}

$gallery_items = handandvision_get_home_gallery_images( $front_page_id );

$is_hebrew = handandvision_is_hebrew();
?>

<main id="primary" class="hv-homepage hv-homepage-editorial">

    <!-- HERO SECTION WITH VIDEO -->
    <section class="hv-hero-video">
        <div class="hv-hero-video__media">
            <?php
            $poster_url = ( is_array( $hero_poster ) && isset( $hero_poster['url'] ) ) ? $hero_poster['url'] : ( is_string( $hero_poster ) ? $hero_poster : '' );
            if ( $hero_video_url ) :
            ?>
            <video id="hero-video" autoplay muted loop playsinline poster="<?php echo $poster_url ? esc_url( $poster_url ) : ''; ?>" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'וידאו רקע' : 'Background video' ); ?>">
                <source src="<?php echo esc_url( $hero_video_url ); ?>" type="video/mp4">
                <?php if ( $poster_url ) : ?>
                    <img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $hero_title ?: 'Hands and Vision' ); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
            </video>
            <?php endif; ?>
            <div class="hv-hero-video__overlay"></div>
        </div>
        <div class="hv-hero-video__content hv-hero-video__content--center">
            <?php
            $sign_svg      = handandvision_get_brand_svg_markup( 'sign', 'brand' );
            $wordmark_svg  = handandvision_get_brand_svg_markup( 'wordmark', 'brand' );
            $hero_logo_alt = $hero_title ?: 'Hands and Vision Collective';
            ?>
            <div class="hv-hero-brand">
                <?php if ( $sign_svg ) : ?>
                <div class="hv-hero-sign hv-brand-mark hv-brand-mark--sign" aria-hidden="true">
                    <?php echo $sign_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG asset. ?>
                </div>
                <?php endif; ?>
                <?php if ( $wordmark_svg ) : ?>
                <h1 class="hv-hero-wordmark hv-brand-mark hv-brand-mark--wordmark">
                    <span class="screen-reader-text"><?php echo esc_html( $hero_logo_alt ); ?></span>
                    <?php echo $wordmark_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG asset. ?>
                </h1>
                <?php else : ?>
                <h1 class="hv-display hv-hero-title-v2" dir="ltr"><?php echo esc_html( $hero_title ?: 'HANDS AND VISION' ); ?></h1>
                <?php endif; ?>
                <p class="hv-overline hv-hero-overline-v2 hv-hero-tagline" dir="ltr">Art. Design. Installations.</p>
            </div>
            <?php if ( $hero_subtitle ) : ?><p class="hv-subtitle hv-mt-0"><?php echo esc_html( $hero_subtitle ); ?></p><?php endif; ?>
            <div class="hv-hero-video__actions">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ); ?>" class="hv-btn hv-btn--primary"><?php echo esc_html( $is_hebrew ? 'קולקטיב האמנים' : 'ARTISTS COLLECTIVE' ); ?></a>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--glass"><?php echo esc_html( $is_hebrew ? 'צרו קשר' : 'Contact Us' ); ?></a>
            </div>
        </div>
        <div class="hv-hero-video__scroll">
            <span><?php echo esc_html( $is_hebrew ? 'גלול' : 'Scroll' ); ?></span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <polyline points="19 12 12 19 5 12"></polyline>
            </svg>
        </div>
    </section>

    <?php if ( $intro_text ) : ?>
    <section class="hv-section hv-section--white hv-home-intro" aria-labelledby="intro-heading">
        <div class="hv-container hv-container--narrow hv-text-center">
            <span class="hv-home-intro__eyebrow">
                <span class="hv-home-intro__label"><?php echo esc_html( $is_hebrew ? 'אודות' : 'About' ); ?></span>
            </span>
            <h2 id="intro-heading" class="hv-headline-2 hv-animate hv-home-intro__title"><?php echo esc_html( $is_hebrew ? 'אודותינו' : 'About Us' ); ?></h2>
            <span class="hv-home-intro__rule" aria-hidden="true"></span>
            <div class="hv-intro-statement hv-animate hv-home-intro__body"><?php echo wp_kses_post( $intro_text ); ?></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- SERVICES SECTION -->
    <?php
    $display_services = is_array( $featured_services ) ? $featured_services : array();
    $display_services = handandvision_sort_services_for_display( $display_services );
    ?>
    <section class="hv-section hv-section--cream hv-home-services" aria-labelledby="services-heading">
        <div class="hv-container">
            <header class="hv-section-header hv-section-header--services hv-text-center hv-animate hv-home-section-header">
                <span class="hv-home-section-header__eyebrow">
                    <span class="hv-home-section-header__label"><?php echo esc_html( $is_hebrew ? 'התמחויות' : 'Practice' ); ?></span>
                </span>
                <h2 id="services-heading" class="hv-headline-1 hv-services-main-title"><?php echo esc_html( $is_hebrew ? 'מה אנחנו עושים' : 'What We Do' ); ?></h2>
            </header>
        </div>

            <?php if ( ! empty( $display_services ) ) : ?>
            <div class="hv-services-carousel-bleed hv-carousel-bleed">
                <?php
                $services_carousel_classes = array(
                    'hv-services-carousel',
                    'swiper',
                    $is_hebrew ? 'hv-services-carousel--track-rtl' : 'hv-services-carousel--track-ltr',
                    $is_hebrew ? 'hv-services-carousel--text-rtl' : 'hv-services-carousel--text-ltr',
                );
                $services_track_dir = $is_hebrew ? 'rtl' : 'ltr';
                ?>
                <div class="<?php echo esc_attr( implode( ' ', $services_carousel_classes ) ); ?>" dir="<?php echo esc_attr( $services_track_dir ); ?>" data-hv-track-dir="<?php echo esc_attr( $services_track_dir ); ?>">
                    <div class="swiper-wrapper">
                        <?php
                        foreach ( $display_services as $i => $service_item ) :
                            if ( ! is_object( $service_item ) ) {
                                continue;
                            }
                            $s_title = get_the_title( $service_item->ID );
                            $s_desc  = get_field( 'service_short_description', $service_item->ID );
                            if ( empty( $s_desc ) ) {
                                $s_desc = get_the_excerpt( $service_item->ID );
                            }
                            if ( empty( $s_desc ) ) {
                                $s_desc = wp_trim_words( get_post_field( 'post_content', $service_item->ID ), 20 );
                            }
                            if ( ! $is_hebrew ) {
                                $en_title = get_field( 'service_title_en', $service_item->ID );
                                $en_desc  = get_field( 'service_desc_en', $service_item->ID );
                                if ( ! empty( $en_title ) ) {
                                    $s_title = $en_title;
                                }
                                if ( ! empty( $en_desc ) ) {
                                    $s_desc = $en_desc;
                                }
                            }
                            $carousel_ids  = handandvision_get_service_carousel_image_ids( $service_item->ID );
                            $carousel_urls = array();
                            foreach ( $carousel_ids as $cid ) {
                                $u = wp_get_attachment_image_url( $cid, 'medium_large' );
                                if ( $u ) {
                                    $carousel_urls[] = $u;
                                }
                            }
                            get_template_part(
                                'template-parts/card/service-carousel-card',
                                null,
                                array(
                                    'service_item'  => $service_item,
                                    'index'         => $i,
                                    'carousel_urls' => $carousel_urls,
                                    'img_id'        => ! empty( $carousel_ids ) ? $carousel_ids[0] : 0,
                                    'title'         => $s_title,
                                    'desc'          => $s_desc,
                                    'link'          => get_permalink( $service_item->ID ),
                                )
                            );
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
            <div class="hv-container hv-text-center hv-mt-8">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>" class="hv-btn hv-btn--outline"><?php echo esc_html( $is_hebrew ? 'כל השירותים' : 'All Services' ); ?></a>
            </div>
            <?php else : ?>
            <div class="hv-container">
                <p class="hv-text-center hv-muted hv-mt-4"><?php echo esc_html( $is_hebrew ? 'השירותים יוצגו כאן בקרוב.' : 'Our services will be featured here soon.' ); ?></p>
            </div>
            <?php endif; ?>
    </section>

    <?php
    get_template_part(
        'template-parts/artist-carousel',
        null,
        array(
            'is_hebrew'       => $is_hebrew,
            'section_classes' => 'hv-section hv-section--white hv-home-artists',
        )
    );
    ?>

    <?php get_template_part( 'template-parts/gallery/gallery-carousel', null, array( 'items' => $gallery_items ) ); ?>

    <section class="hv-cta-section hv-home-cta">
        <div class="hv-container hv-text-center">
            <span class="hv-home-section-header__eyebrow">
                <span class="hv-home-section-header__label"><?php echo esc_html( $is_hebrew ? 'מתחילים' : 'Begin' ); ?></span>
            </span>
            <h2 class="hv-headline-2 hv-home-cta__title"><?php echo esc_html( $is_hebrew ? 'בואו ניצור משהו יפה יחד' : 'Let’s Create Something Beautiful Together' ); ?></h2>
            <p class="hv-cta-text hv-home-cta__text"><?php echo esc_html( $is_hebrew ? 'צרו קשר לייעוץ ראשוני ללא התחייבות' : 'Contact us for a free initial consultation.' ); ?></p>
            <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--cta hv-home-cta__btn">
                <span><?php echo esc_html( $is_hebrew ? 'צרו קשר' : 'Contact Us' ); ?></span>
                <span class="hv-home-cta__arrow" aria-hidden="true"><?php echo esc_html( $is_hebrew ? '←' : '→' ); ?></span>
            </a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
