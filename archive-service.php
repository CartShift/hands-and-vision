<?php
/**
 * Archive Template: Services
 * Premium minimalist design with elegant hero and refined service cards
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Safely get services
$services = get_posts( array(
    'post_type'      => 'service',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'post_status'    => 'publish',
) );

if ( ! $services ) {
    $services = array();
}
?>

<main id="primary" class="hv-archive-page hv-service-archive">

    <!-- Premium Hero Section -->
    <section class="hv-service-hero-archive">
        <div class="hv-service-hero-archive__inner">
            <div class="hv-container">
                <?php handandvision_breadcrumbs(); ?>
                <div class="hv-service-hero-archive__content">
                    <div class="hv-service-hero-archive__label">
                        <?php if ( handandvision_is_hebrew() ) : ?>
                            <span class="hv-fade-in">שירותים</span>
                            <span class="hv-fade-in" style="animation-delay: 0.1s;">·</span>
                            <span class="hv-fade-in" style="animation-delay: 0.2s;">אוצרות</span>
                            <span class="hv-fade-in" style="animation-delay: 0.3s;">·</span>
                            <span class="hv-fade-in" style="animation-delay: 0.4s;">ייעוץ</span>
                        <?php else : ?>
                            <span class="hv-fade-in">Services</span>
                            <span class="hv-fade-in" style="animation-delay: 0.1s;">·</span>
                            <span class="hv-fade-in" style="animation-delay: 0.2s;">Curation</span>
                            <span class="hv-fade-in" style="animation-delay: 0.3s;">·</span>
                            <span class="hv-fade-in" style="animation-delay: 0.4s;">Consultancy</span>
                        <?php endif; ?>
                    </div>
                    <h1 class="hv-service-hero-archive__title">
                        <?php echo esc_html( handandvision_is_hebrew() ? 'שירותים מקצועיים לעולם האמנות' : 'Professional Services for the Art World' ); ?>
                    </h1>
                    <p class="hv-service-hero-archive__desc">
                        <?php echo esc_html( handandvision_is_hebrew() ? 'אוצרות, ייעוץ ושירותים מותאמים אישית המשלבים מקצועיות עם תשוקה לאמנות' : 'Curation, consultancy and bespoke services combining professionalism with passion for art' ); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="hv-service-hero-archive__divider"></div>
    </section>

    <!-- Services Grid -->
    <section class="hv-services-archive-grid">
        <div class="hv-container hv-container--wide">
            <div class="hv-services-archive-grid__inner">
                <?php foreach ( $services as $i => $service ) :
                    // Safely get ACF fields
                    $service_image = function_exists( 'get_field' ) ? get_field( 'service_hero_image', $service->ID ) : false;
                    $image_url = ( is_array( $service_image ) && isset( $service_image['url'] ) ) ? $service_image['url'] : get_the_post_thumbnail_url( $service->ID, 'large' );
                    
                    $short_desc_acf = function_exists( 'get_field' ) ? get_field( 'service_short_description', $service->ID ) : '';
                    $short_desc = $short_desc_acf ?: wp_trim_words( $service->post_content, 18 );
                    
                    $service_title = $service->post_title;
                    
                    // Handle language-specific fields
                    if ( function_exists( 'handandvision_is_hebrew' ) && ! handandvision_is_hebrew() ) {
                        if ( function_exists( 'get_field' ) ) {
                            $en_title = get_field( 'service_title_en', $service->ID );
                            $en_desc  = get_field( 'service_desc_en', $service->ID );
                            if ( ! empty( $en_title ) ) $service_title = $en_title;
                            if ( ! empty( $en_desc ) ) $short_desc = $en_desc;
                        }
                    }
                ?>
                    <article class="hv-service-card-archive">
                        <a href="<?php echo esc_url( get_permalink( $service->ID ) ); ?>" class="hv-service-card-archive__link">
                            <div class="hv-service-card-archive__media">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $service_title ); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="hv-service-card-archive__placeholder"></div>
                                <?php endif; ?>
                                <div class="hv-service-card-archive__overlay">
                                    <span class="hv-service-card-archive__number"><?php printf( '%02d', $i + 1 ); ?></span>
                                </div>
                            </div>
                            <div class="hv-service-card-archive__content">
                                <h2 class="hv-service-card-archive__title"><?php echo esc_html( $service_title ); ?></h2>
                                <p class="hv-service-card-archive__desc"><?php echo esc_html( $short_desc ); ?></p>
                                <span class="hv-service-card-archive__arrow">
                                    <?php if ( handandvision_is_hebrew() ) : ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <line x1="19" y1="12" x2="5" y2="12"></line>
                                            <polyline points="12 19 5 12 12 5"></polyline>
                                        </svg>
                                    <?php else : ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Premium CTA -->
    <section class="hv-services-cta">
        <div class="hv-container hv-container--narrow">
            <div class="hv-services-cta__content">
                <span class="hv-services-cta__label">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'בואו נדבר' : 'Let's Talk' ); ?>
                </span>
                <h2 class="hv-services-cta__title">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'מוכנים להתחיל?' : 'Ready to Begin?' ); ?>
                </h2>
                <p class="hv-services-cta__desc">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'נשמח לשמוע על הפרויקט שלכם ולבנות יחד את הדרך קדימה' : 'We'd love to hear about your project and build the path forward together' ); ?>
                </p>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-services-cta__btn">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'צרו קשר' : 'Get in Touch' ); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
