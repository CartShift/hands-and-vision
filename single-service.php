<?php
/**
 * Single Template: Service
 * Premium minimalist design with elegant hero and refined content flow
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$service_id = get_the_ID();
$service_title = get_the_title();

$hero_image = get_field( 'service_hero_image', $service_id );
$hero_url = ( is_array( $hero_image ) && isset( $hero_image['url'] ) ) ? $hero_image['url'] : get_the_post_thumbnail_url( $service_id, 'full' );
$short_desc = get_field( 'service_short_description', $service_id ) ?: '';
$full_desc = get_field( 'service_full_description', $service_id ) ?: '';
$features = get_field( 'service_what_we_do', $service_id );
$gallery = get_field( 'service_gallery', $service_id );
$related_artists = get_field( 'service_related_artists', $service_id );
$cta_text = get_field( 'service_cta_text', $service_id );

$features = is_array( $features ) ? $features : array();
$gallery_grid_items = handandvision_normalize_gallery_grid_items( $gallery, array() );

if ( ! handandvision_is_hebrew() ) {
    $en_title = get_field( 'service_title_en', $service_id );
    if ( ! empty( $en_title ) ) {
        $service_title = $en_title;
    }
}
?>

<main id="primary" class="hv-single-service">

    <!-- Premium Hero Section -->
    <section class="hv-service-single-hero">
        <div class="hv-service-single-hero__bg">
            <?php if ( $hero_url ) : ?>
                <img src="<?php echo esc_url( $hero_url ); ?>" alt="<?php echo esc_attr( $service_title ); ?>">
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
                        <?php echo esc_html( handandvision_is_hebrew() ? 'שירות' : 'Service' ); ?>
                    </span>
                    <h1 class="hv-service-single-hero__title">
                        <?php echo esc_html( $service_title ); ?>
                    </h1>
                    <?php if ( $short_desc ) : ?>
                        <p class="hv-service-single-hero__subtitle">
                            <?php echo esc_html( $short_desc ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <?php if ( $full_desc ) : ?>
    <section class="hv-service-content-section">
        <div class="hv-container hv-container--narrow">
            <div class="hv-service-content-text">
                <?php echo wp_kses_post( wpautop( $full_desc ) ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Features / What We Do -->
    <?php if ( ! empty( $features ) ) : ?>
    <section class="hv-service-features-section">
        <div class="hv-container">
            <div class="hv-service-features-header">
                <span class="hv-service-features-label">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'תחומי פעילות' : 'Areas of Expertise' ); ?>
                </span>
                <h2 class="hv-service-features-title">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'מה כולל השירות' : 'What We Offer' ); ?>
                </h2>
            </div>
            <div class="hv-service-features-grid">
                <?php foreach ( $features as $i => $feature ) :
                    $feature_text = is_array( $feature ) ? ( $feature['point'] ?? '' ) : (string) $feature;
                    if ( $feature_text === '' ) continue;
                ?>
                    <div class="hv-service-feature-item">
                        <div class="hv-service-feature-number"><?php printf( '%02d', $i + 1 ); ?></div>
                        <p class="hv-service-feature-text"><?php echo esc_html( $feature_text ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Gallery -->
    <?php if ( ! empty( $gallery_grid_items ) ) : ?>
    <section class="hv-service-gallery-section">
        <div class="hv-container">
            <div class="hv-service-gallery-header">
                <span class="hv-service-gallery-label">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'פרויקטים' : 'Projects' ); ?>
                </span>
                <h2 class="hv-service-gallery-title">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'עבודות לדוגמה' : 'Selected Works' ); ?>
                </h2>
            </div>
            <div class="hv-service-gallery-grid">
                <?php foreach ( $gallery_grid_items as $item ) :
                    $image_url = $item['image'] ?? '';
                    $title = $item['title'] ?? '';
                    $link = $item['link'] ?? '';
                    if ( ! $image_url ) continue;
                ?>
                    <div class="hv-service-gallery-item">
                        <?php if ( $link ) : ?>
                            <a href="<?php echo esc_url( $link ); ?>" class="hv-service-gallery-link">
                        <?php endif; ?>
                                <div class="hv-service-gallery-media">
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                                </div>
                                <?php if ( $title ) : ?>
                                    <div class="hv-service-gallery-caption">
                                        <?php echo esc_html( $title ); ?>
                                    </div>
                                <?php endif; ?>
                        <?php if ( $link ) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Artists -->
    <?php if ( ! empty( $related_artists ) ) : ?>
    <section class="hv-service-artists-section">
        <div class="hv-container">
            <div class="hv-service-artists-header">
                <span class="hv-service-artists-label">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'שיתופי פעולה' : 'Collaborations' ); ?>
                </span>
                <h2 class="hv-service-artists-title">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'אמנים שעובדים איתנו' : 'Artists We Work With' ); ?>
                </h2>
            </div>
            <div class="hv-service-artists-grid">
                <?php foreach ( $related_artists as $artist ) :
                    $artist_id = is_object( $artist ) ? $artist->ID : $artist;
                    $artist_name = is_object( $artist ) ? $artist->post_title : get_the_title( $artist_id );
                    $portrait = get_field( 'artist_portrait', $artist_id );
                    $portrait_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist_id, 'medium' );
                ?>
                    <article class="hv-service-artist-card">
                        <a href="<?php echo esc_url( get_permalink( $artist_id ) ); ?>" class="hv-service-artist-link">
                            <div class="hv-service-artist-media">
                                <?php if ( $portrait_url ) : ?>
                                    <img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php echo esc_attr( $artist_name ); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="hv-service-artist-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <h3 class="hv-service-artist-name"><?php echo esc_html( $artist_name ); ?></h3>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="hv-service-cta-section">
        <div class="hv-container hv-container--narrow">
            <div class="hv-service-cta-content">
                <span class="hv-service-cta-label">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'מתחילים' : 'Let's Begin' ); ?>
                </span>
                <?php
                $default_cta = handandvision_is_hebrew() ? 'מוכנים להתחיל את הפרויקט?' : 'Ready to Start Your Project?';
                $display_cta = $cta_text ?: $default_cta;
                ?>
                <h2 class="hv-service-cta-title"><?php echo esc_html( $display_cta ); ?></h2>
                <p class="hv-service-cta-desc">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'נשמח לשמוע על הצרכים שלכם ולבנות יחד את הפתרון המושלם' : 'We'd love to hear about your needs and build the perfect solution together' ); ?>
                </p>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-service-cta-btn">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'צרו קשר' : 'Get in Touch' ); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
