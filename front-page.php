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
	$featured_services = get_posts( array( 'post_type' => 'service', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'post_status' => 'publish' ) );
}

$featured_artists = get_field( 'featured_artists', $front_page_id );
if ( empty( $featured_artists ) ) {
	$featured_artists = get_posts( array( 'post_type' => 'artist', 'posts_per_page' => 4, 'orderby' => 'menu_order date', 'order' => 'ASC', 'post_status' => 'publish' ) );
}

$gallery_items = handandvision_get_home_gallery_images( $front_page_id );
?>

<main id="primary" class="hv-homepage">

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
            <span class="hv-overline hv-hero-overline-v2">
                <?php if ( handandvision_is_hebrew() ) : ?>
                    <span class="hv-word-1">אוצרות</span> · <span class="hv-word-2">אמנות</span> · <span class="hv-word-3">חזון</span>
                <?php else : ?>
                    <span class="hv-word-1">CURATION</span> · <span class="hv-word-2">ART</span> · <span class="hv-word-3">VISION</span>
                <?php endif; ?>
            </span>
            <h1 class="hv-display hv-hero-title-v2" dir="ltr"><?php echo esc_html( $hero_title ?: 'HANDS AND VISION' ); ?></h1>
            <span class="hv-hero-subtitle-v2" dir="ltr">collective</span>
            <?php if ( $hero_subtitle ) : ?><p class="hv-subtitle hv-mt-0"><?php echo esc_html( $hero_subtitle ); ?></p><?php endif; ?>
            <div class="hv-hero-video__actions">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ); ?>" class="hv-btn hv-btn--primary"><?php echo handandvision_is_hebrew() ? 'קולקטיב האמנים' : 'ARTISTS COLLECTIVE'; ?></a>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--glass"><?php echo handandvision_is_hebrew() ? 'צרו קשר' : 'Contact Us'; ?></a>
            </div>
        </div>
        <div class="hv-hero-video__scroll">
            <span><?php echo handandvision_is_hebrew() ? 'גלול' : 'Scroll'; ?></span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <polyline points="19 12 12 19 5 12"></polyline>
            </svg>
        </div>
    </section>

    <?php if ( $intro_text ) : ?>
    <section class="hv-section hv-section--white" aria-labelledby="intro-heading">
        <div class="hv-container hv-container--narrow hv-text-center">
            <h2 id="intro-heading" class="hv-headline-2 hv-animate"><?php echo esc_html( handandvision_is_hebrew() ? 'אודותינו' : 'About Us' ); ?></h2>
            <div class="hv-intro-statement hv-animate"><?php echo wp_kses_post( $intro_text ); ?></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- SERVICES SECTION -->
    <?php
    $display_services = ! empty( $featured_services ) ? $featured_services : get_posts( array( 'post_type' => 'service', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC', 'post_status' => 'publish' ) );
    $display_services = is_array( $display_services ) ? $display_services : array();
    ?>
    <section class="hv-section hv-section--cream" aria-labelledby="services-heading">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-animate">
                <span class="hv-overline"><?php echo handandvision_is_hebrew() ? 'מה אנחנו עושים' : 'What We Do'; ?></span>
                <h2 id="services-heading" class="hv-headline-2"><?php echo handandvision_is_hebrew() ? 'השירותים שלנו' : 'Our Services'; ?></h2>
            </header>

            <?php if ( ! empty( $display_services ) ) : ?>
            <div class="hv-services-carousel-bleed">
            <div class="hv-services-carousel swiper" role="group" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'שירותים' : 'Services' ); ?>">
                <div class="swiper-wrapper">
                <?php
                foreach ( $display_services as $i => $service_item ) :
                    if ( ! is_object( $service_item ) ) continue;
                    $s_title = get_the_title( $service_item->ID );
                    $s_desc  = get_field( 'service_short_description', $service_item->ID );
                    if ( empty( $s_desc ) ) $s_desc = get_the_excerpt( $service_item->ID );
                    if ( empty( $s_desc ) ) $s_desc = wp_trim_words( get_post_field( 'post_content', $service_item->ID ), 20 );
                    if ( ! handandvision_is_hebrew() ) {
                        $en_title = get_field( 'service_title_en', $service_item->ID );
                        $en_desc  = get_field( 'service_desc_en', $service_item->ID );
                        if ( ! empty( $en_title ) ) $s_title = $en_title;
                        if ( ! empty( $en_desc ) ) $s_desc = $en_desc;
                    }
                    $s_link = get_permalink( $service_item->ID );
                    $s_img_id = null;
                    $s_hero = get_field( 'service_hero_image', $service_item->ID );
                    if ( is_array( $s_hero ) && ! empty( $s_hero['ID'] ) ) $s_img_id = (int) $s_hero['ID'];
                    if ( ! $s_img_id ) $s_img_id = get_post_thumbnail_id( $service_item->ID );
                ?>
                    <article class="hv-service-card swiper-slide">
                        <a href="<?php echo esc_url( $s_link ); ?>" class="hv-service-card__link">
                            <div class="hv-service-card__thumb">
                                <?php if ( $s_img_id ) : ?>
                                    <?php echo wp_get_attachment_image( $s_img_id, 'medium_large', false, array( 'class' => 'hv-service-card__img', 'loading' => 'lazy' ) ); ?>
                                <?php else : ?>
                                    <div class="hv-service-card__placeholder" style="background: linear-gradient(135deg, hsl(<?php echo (int) ( 30 + $i * 15 ); ?>, 15%, 85%) 0%, hsl(<?php echo (int) ( 40 + $i * 15 ); ?>, 20%, 75%) 100%);"></div>
                                <?php endif; ?>
                            </div>
                            <h3 class="hv-service-card__title"><?php echo esc_html( $s_title ); ?></h3>
                            <p class="hv-service-card__desc"><?php echo esc_html( $s_desc ); ?></p>
                            <span class="hv-service-card__cta"><?php echo handandvision_is_hebrew() ? 'קראו עוד ←' : 'Read More →'; ?></span>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
            </div>
            </div>
            <div class="hv-text-center hv-mt-8">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>" class="hv-btn hv-btn--outline"><?php echo handandvision_is_hebrew() ? 'כל השירותים' : 'All Services'; ?></a>
            </div>
            <?php else : ?>
            <p class="hv-text-center hv-muted hv-mt-4"><?php echo esc_html( handandvision_is_hebrew() ? 'השירותים יוצגו כאן בקרוב.' : 'Our services will be featured here soon.' ); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- FEATURED ARTISTS SECTION -->
    <section class="hv-section hv-section--white">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-animate">
                <span class="hv-overline"><?php echo handandvision_is_hebrew() ? 'הקולקטיב' : 'The Collective'; ?></span>
                <h2 class="hv-headline-2"><?php echo handandvision_is_hebrew() ? 'האמנים שלנו' : 'Our Artists'; ?></h2>
            </header>

            <?php
            $display_artists = get_posts( array(
                'post_type'      => 'artist',
                'posts_per_page' => 20,
                'orderby'        => 'date',
                'order'          => 'ASC',
                'post_status'    => 'publish',
            ) );
            // Fallback: If for some reason get_posts returns nothing, check featured_artists
            if ( empty( $display_artists ) && ! empty( $featured_artists ) ) {
                $display_artists = $featured_artists;
            }
            $display_artists = is_array( $display_artists ) ? $display_artists : array();
            ?>
            <?php if ( ! empty( $display_artists ) ) : ?>
            <div class="hv-artists-showcase-bleed">
            <div class="hv-artists-showcase swiper">
                <div class="swiper-wrapper">
                <?php foreach ( $display_artists as $i => $artist_item ) :
                    if ( ! is_object( $artist_item ) ) continue;
                    $a_image_html = '';
                    $a_image_url = '';

                    {
                        $a_name       = get_the_title( $artist_item->ID );
                        $a_discipline = get_field( 'artist_discipline', $artist_item->ID );
                        $a_quote      = get_field( 'artist_quote', $artist_item->ID );
                        $a_link       = get_permalink( $artist_item->ID );

                        // Priority: Featured Image -> Artist Portrait -> Artist Image -> Profile Image
                        $a_img_id = get_post_thumbnail_id( $artist_item->ID );

                        if ( ! $a_img_id ) {
                             $acf_portrait = get_field( 'artist_portrait', $artist_item->ID );
                             if ( is_array($acf_portrait) ) $a_img_id = $acf_portrait['ID'];
                        }

                        if ( ! $a_img_id ) {
                             $acf_image = get_field( 'artist_image', $artist_item->ID );
                             if ( is_array($acf_image) ) $a_img_id = $acf_image['ID'];
                        }

                        if ( ! $a_img_id ) {
                             $acf_profile = get_field( 'profile_image', $artist_item->ID );
                             if ( is_array($acf_profile) ) $a_img_id = $acf_profile['ID'];
                        }

                        if ( $a_img_id ) {
                            $a_image_html = wp_get_attachment_image( $a_img_id, 'hv-artist', false, array( 'class' => 'hv-artist-card__img', 'loading' => 'lazy' ) );
                        } else {
                            // URL Fallback check (if fields return strings instead of arrays)
                            $a_image_url = get_the_post_thumbnail_url( $artist_item->ID, 'hv-artist' );
                        }

                    }
                ?>
                    <article class="hv-artist-card swiper-slide">
                        <a href="<?php echo esc_url( $a_link ); ?>" class="hv-artist-card__link" data-artist-id="<?php echo esc_attr( $artist_item->ID ); ?>">
                            <div class="hv-artist-card__portrait">
                                <?php if ( $a_image_html ) : ?>
                                    <?php echo wp_kses_post( $a_image_html ); ?>
                                <?php elseif ( $a_image_url ) : ?>
                                    <img src="<?php echo esc_url( $a_image_url ); ?>" alt="<?php echo esc_attr( $a_name ); ?>" class="hv-artist-card__img" loading="lazy">
                                <?php else : ?>
                                    <div class="hv-artist-card__placeholder" style="background: linear-gradient(135deg, hsl(<?php echo (int) ( 30 + $i * 15 ); ?>, 15%, 75%) 0%, hsl(<?php echo (int) ( 40 + $i * 15 ); ?>, 20%, 65%) 100%);"></div>
                                <?php endif; ?>
                            </div>
                            <div class="hv-artist-card__info">
                                <h3 class="hv-artist-card__name"><?php echo esc_html( $a_name ); ?></h3>
                                <span class="hv-artist-card__discipline"><?php echo esc_html( $a_discipline ); ?></span>
                                <?php if ( $a_quote ) : ?>
                                    <p class="hv-artist-card__quote">"<?php echo esc_html( $a_quote ); ?>"</p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
            </div>
            </div>
            <div class="hv-text-center hv-mt-8">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ); ?>" class="hv-btn hv-btn--outline"><?php echo handandvision_is_hebrew() ? 'לכל האמנים' : 'All Artists'; ?></a>
            </div>
            <?php else : ?>
            <p class="hv-text-center hv-muted hv-mt-4"><?php echo esc_html( handandvision_is_hebrew() ? 'האמנים שלנו יוצגו כאן בקרוב.' : 'Our artists will be featured here soon.' ); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <?php get_template_part( 'template-parts/gallery/gallery-carousel', null, array( 'items' => $gallery_items ) ); ?>

    <section class="hv-cta-section">
        <div class="hv-container hv-text-center">
            <span class="hv-overline hv-overline--gold"><?php echo handandvision_is_hebrew() ? 'מתחילים?' : 'Getting Started?'; ?></span>
            <h2 class="hv-headline-2 hv-text-white"><?php echo handandvision_is_hebrew() ? 'בואו ניצור משהו יפה יחד' : 'Let’s Create Something Beautiful Together'; ?></h2>
            <p class="hv-cta-text"><?php echo handandvision_is_hebrew() ? 'צרו קשר לייעוץ ראשוני ללא התחייבות' : 'Contact us for a free initial consultation'; ?></p>
            <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--primary-gold"><?php echo handandvision_is_hebrew() ? 'צרו קשר' : 'Contact Us'; ?></a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
