<?php
/**
 * Archive Template: Services
 * Premium gallery-style services listing
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
));
?>

<main id="primary" class="hv-archive-page hv-service-archive">

    <!-- Hero Header -->
    <section class="hv-page-hero">
        <div class="hv-container hv-text-center">
            <?php handandvision_breadcrumbs(); ?>
            <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'מה אנחנו מציעים' : 'What We Offer'; ?></span>
            <h1 class="hv-headline-1 hv-reveal"><?php echo handandvision_is_hebrew() ? 'השירותים שלנו' : 'Our Services'; ?></h1>
            <p class="hv-subtitle hv-service-intro-text hv-reveal">
                <?php echo handandvision_is_hebrew() ? 'אנו מציעים מגוון שירותים מותאמים אישית לעולם האמנות - מאוצרות ועד ייעוץ מקצועי' : 'We offer a range of personalized services for the art world - from curation to professional consultancy'; ?>
            </p>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="hv-section hv-section--cream">
        <div class="hv-container">
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

    <!-- CTA Section -->
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
