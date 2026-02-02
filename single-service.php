<?php
/**
 * Single Template: Service
 * Premium service detail page
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$service_id = get_the_ID();
$service_title = get_the_title();

// Get ACF fields with defaults
$hero_image = get_field( 'service_hero_image', $service_id );
$hero_url = ( is_array( $hero_image ) && isset( $hero_image['url'] ) ) ? $hero_image['url'] : get_the_post_thumbnail_url( $service_id, 'full' );
$short_desc = get_field( 'service_short_description', $service_id );
$full_desc = get_field( 'service_full_description', $service_id );
$features = get_field( 'service_features', $service_id );
$gallery = get_field( 'service_gallery', $service_id );
$related_artists = get_field( 'service_related_artists', $service_id );
$cta_text = get_field( 'service_cta_text', $service_id );

$short_desc = $short_desc ?: '';
$full_desc = $full_desc ?: '';
$features = is_array( $features ) ? $features : array();
$gallery_grid_items = handandvision_normalize_gallery_grid_items( $gallery, array() );
?>

<main id="primary" class="hv-single-service">

    <!-- Service Hero -->
    <section class="hv-service-hero">
        <div class="hv-service-hero__bg">
            <?php if ( $hero_url ) : ?>
                <img src="<?php echo esc_url( $hero_url ); ?>" alt="<?php echo esc_attr( $service_title ); ?>">
            <?php else : ?>
                <div class="hv-service-hero__gradient"></div>
            <?php endif; ?>
        </div>
        <div class="hv-service-hero__overlay"></div>
        <div class="hv-service-hero__content">
            <div class="hv-container hv-text-center">
                <?php handandvision_breadcrumbs(); ?>
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'שירות' : 'Service'; ?></span>
                <h1 class="hv-headline-1 hv-reveal"><?php echo esc_html( get_the_title() ); ?></h1>
                <p class="hv-subtitle hv-reveal" style="max-width: 600px; margin: var(--hv-space-6) auto 0; color: var(--hv-silver);">
                    <?php echo esc_html( $short_desc ); ?>
                </p>
            </div>
        </div>
    </section>

    <?php if ( $full_desc ) : ?>
    <!-- Service Description -->
    <section class="hv-section hv-section--lg hv-section--white">
        <div class="hv-container hv-container--narrow">
            <div class="hv-service-content hv-reveal">
                <?php echo wp_kses_post( wpautop( $full_desc ) ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Features -->
    <?php if ( ! empty( $features ) ) : ?>
    <section class="hv-section hv-section--cream">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-mb-10">
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'מה כולל השירות' : 'What is Included'; ?></span>
                <h2 class="hv-headline-2 hv-line-draw hv-reveal"><?php echo handandvision_is_hebrew() ? 'תחומי הפעילות' : 'Areas of Activity'; ?></h2>
            </header>

            <div class="hv-features-grid hv-stagger">
                <?php foreach ( $features as $i => $feature ) :
                    $feature_text = is_array( $feature ) ? ( $feature['feature_text'] ?? '' ) : (string) $feature;
                    if ( $feature_text === '' ) continue;
                ?>
                    <div class="hv-feature-item">
                        <span class="hv-feature-item__number"><?php printf( '%02d', $i + 1 ); ?></span>
                        <p class="hv-feature-item__text"><?php echo esc_html( $feature_text ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php get_template_part( 'template-parts/gallery/gallery-grid', null, array(
        'items' => $gallery_grid_items,
        'title' => handandvision_is_hebrew() ? 'עבודות לדוגמה' : 'Selected Works',
        'subtitle' => handandvision_is_hebrew() ? 'מהפרויקטים שלנו' : 'From Our Projects',
        'section_class' => 'hv-section--white',
    ) ); ?>

    <!-- Related Artists -->
    <?php if ( ! empty( $related_artists ) ) : ?>
    <section class="hv-section hv-section--dark">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-mb-10">
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'עובדים איתנו' : 'Working with Us'; ?></span>
                <h2 class="hv-headline-2 hv-text-white hv-line-draw hv-reveal"><?php echo handandvision_is_hebrew() ? 'אמנים בתחום' : 'Featured Artists'; ?></h2>
            </header>

            <div class="hv-artists-grid hv-stagger" style="max-width: 1000px; margin: 0 auto;">
                <?php
                if ( ! empty( $related_artists ) ) {
                    foreach ( $related_artists as $artist ) {
                        $artist_id = is_object( $artist ) ? $artist->ID : $artist;
                        $artist_name = is_object( $artist ) ? $artist->post_title : get_the_title( $artist_id );
                        $portrait = get_field( 'artist_portrait', $artist_id );
                        $portrait_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist_id, 'medium' );
                        ?>
                        <article class="hv-card hv-card--artist hv-card--dark">
                            <a href="<?php echo esc_url( get_permalink( $artist_id ) ); ?>">
                                <div class="hv-card__media">
                                    <?php if ( $portrait_url ) : ?>
                                        <img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php echo esc_attr( $artist_name ); ?>">
                                    <?php else : ?>
                                        <div class="hv-card__placeholder hv-card__placeholder--artist"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="hv-card__content">
                                    <h3 class="hv-card__title hv-text-white"><?php echo esc_html( $artist_name ); ?></h3>
                                </div>
                            </a>
                        </article>
                        <?php
                }
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="hv-cta-premium">
        <div class="hv-container hv-container--narrow hv-text-center">
            <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'מתחילים?' : 'Getting Started?'; ?></span>
            <?php
            // Determine CTA Headline
            $default_cta = handandvision_is_hebrew() ? 'בואו נדבר על הפרויקט שלכם' : "Let's Talk About Your Project";
            $display_cta = $cta_text ?: $default_cta;

            // Fix: Override "Get in Touch" content if in Hebrew mode
            if ( handandvision_is_hebrew() && strcasecmp( trim( $display_cta ), 'Get in Touch' ) === 0 ) {
                $display_cta = $default_cta;
            }
            ?>
            <h2 class="hv-headline-2 hv-reveal"><?php echo esc_html( $display_cta ); ?></h2>
            <p class="hv-subtitle hv-reveal" style="margin: var(--hv-space-5) auto var(--hv-space-7); max-width: 550px;">
                <?php echo handandvision_is_hebrew() ? 'נשמח לשמוע על הצרכים שלכם ולהציע פתרון מותאם' : 'We’d love to hear about your needs and offer a tailored solution'; ?>
            </p>
            <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--primary hv-btn--lg hv-reveal"><?php echo handandvision_is_hebrew() ? 'צרו קשר' : 'Contact Us'; ?></a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
