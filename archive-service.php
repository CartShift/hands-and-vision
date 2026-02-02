<?php
/**
 * Archive Template: Services
 * Premium full-viewport hero + alternating service cards
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$services = get_posts( array(
    'post_type'      => 'service',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'post_status'    => 'publish',
) );
?>

<main id="primary" class="hv-archive-page hv-service-archive">

    <section class="hv-service-archive-hero">
        <div class="hv-service-archive-hero__bg"></div>
        <div class="hv-service-archive-hero__content">
            <span class="hv-overline hv-service-archive-hero__overline">
                <?php if ( handandvision_is_hebrew() ) : ?>
                    <span class="hv-word-1">אוצרות</span> · <span class="hv-word-2">ייעוץ</span> · <span class="hv-word-3">שירות</span>
                <?php else : ?>
                    <span class="hv-word-1">CURATION</span> · <span class="hv-word-2">CONSULTANCY</span> · <span class="hv-word-3">SERVICE</span>
                <?php endif; ?>
            </span>
            <h1 class="hv-display hv-service-archive-hero__title"><?php echo handandvision_is_hebrew() ? 'השירותים שלנו' : 'Our Services'; ?></h1>
            <p class="hv-service-archive-hero__subtitle">
                <?php echo handandvision_is_hebrew() ? 'מגוון שירותים מותאמים לעולם האמנות — מאוצרות ועד ייעוץ מקצועי' : 'A range of tailored services for the art world — from curation to professional consultancy'; ?>
            </p>
        </div>
        <a href="#hv-services-list" class="hv-hero-scroll" aria-label="<?php echo esc_attr( handandvision_is_hebrew() ? 'גלול לשירותים' : 'Scroll to services' ); ?>">
            <span><?php echo handandvision_is_hebrew() ? 'גלול' : 'Scroll'; ?></span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <polyline points="19 12 12 19 5 12"></polyline>
            </svg>
        </a>
    </section>

    <section id="hv-services-list" class="hv-section hv-section--cream hv-services-showcase" aria-labelledby="services-list-heading">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center hv-mb-10" id="services-list-heading">
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'מה אנחנו מציעים' : 'What We Offer'; ?></span>
                <h2 class="hv-headline-2 hv-line-draw hv-reveal"><?php echo handandvision_is_hebrew() ? 'בחרו שירות' : 'Choose a Service'; ?></h2>
            </header>
            <div class="hv-services-cards">
                <?php foreach ( $services as $i => $service ) :
                    $service_image = get_field( 'service_hero_image', $service->ID );
                    $image_url = ( is_array( $service_image ) && isset( $service_image['url'] ) ) ? $service_image['url'] : get_the_post_thumbnail_url( $service->ID, 'large' );
                    $short_desc = get_field( 'service_short_description', $service->ID ) ?: wp_trim_words( $service->post_content, 20 );
                    $service_title = $service->post_title;
                    if ( ! handandvision_is_hebrew() ) {
                        $en_title = get_field( 'service_title_en', $service->ID );
                        $en_desc  = get_field( 'service_desc_en', $service->ID );
                        if ( ! empty( $en_title ) ) $service_title = $en_title;
                        if ( ! empty( $en_desc ) ) $short_desc = $en_desc;
                    }
                ?>
                    <article class="hv-service-card">
                        <a href="<?php echo esc_url( get_permalink( $service->ID ) ); ?>" class="hv-service-card__link">
                            <div class="hv-service-card__media">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $service_title ); ?>">
                                <?php else : ?>
                                    <div class="hv-service-card__placeholder hv-service-card__placeholder--<?php echo (int) ( $i % 4 ) + 1; ?>"></div>
                                <?php endif; ?>
                                <div class="hv-service-card__overlay">
                                    <span class="hv-service-card__number"><?php printf( '%02d', $i + 1 ); ?></span>
                                </div>
                            </div>
                            <div class="hv-service-card__content">
                                <h2 class="hv-service-card__title"><?php echo esc_html( $service_title ); ?></h2>
                                <p class="hv-service-card__desc"><?php echo esc_html( $short_desc ); ?></p>
                                <span class="hv-service-card__btn"><?php echo esc_html( handandvision_is_hebrew() ? 'פרטים נוספים ←' : 'Learn More →' ); ?></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="hv-cta-premium">
        <div class="hv-container hv-container--narrow hv-text-center">
            <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'מתחילים' : 'Getting Started'; ?></span>
            <h2 class="hv-headline-2 hv-reveal"><?php echo handandvision_is_hebrew() ? 'מוכנים לשוחח?' : 'Ready to Talk?'; ?></h2>
            <p class="hv-subtitle hv-cta-subtitle hv-reveal">
                <?php echo handandvision_is_hebrew() ? 'נשמח לשמוע על הפרויקט שלכם ולראות איך נוכל לעזור' : 'We’d love to hear about your project and see how we can help'; ?>
            </p>
            <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--primary-gold hv-reveal"><?php echo handandvision_is_hebrew() ? 'צרו קשר' : 'Contact Us'; ?></a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
