<?php
/**
 * Archive Template: Services
 * Elegant minimalist design with immersive light hero and showcase-style service presentation
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Check if handandvision_is_hebrew exists, if not create a fallback
if ( ! function_exists( 'handandvision_is_hebrew' ) ) {
    function handandvision_is_hebrew() {
        return false;
    }
}

$is_hebrew = function_exists( 'handandvision_is_hebrew' ) ? handandvision_is_hebrew() : false;

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

$total_services = count( $services );
?>

<main id="primary" class="hv-hero-layout hv-services-page">

<?php
get_template_part( 'template-parts/hero/page-hero', null, array(
	'overline'   => $is_hebrew ? 'שירותים מקצועיים' : 'Professional Services',
	'title'      => array(
		array( 'text' => $is_hebrew ? 'אוצרות, ייעוץ' : 'Curation, Consultancy', 'accent' => false ),
		array( 'text' => $is_hebrew ? 'ואמנות בתנועה' : '& Art in Motion', 'accent' => true ),
	),
	'subtitle'   => $is_hebrew
		? 'אנו מציעים מגוון שירותים מותאמים אישית לעולם האמנות – מאוצרות תערוכות ועד ייעוץ איסוף, הכל עם תשוקה למצוינות.'
		: 'We offer a range of bespoke services for the art world — from exhibition curation to collection advisory, all with a passion for excellence.',
) );
?>

    <!-- Services Showcase -->
    <section class="hv-services-showcase">
        <div class="hv-container hv-container--wide">

            <?php foreach ( $services as $i => $service ) :
                // Safely get ACF fields
                $service_image = function_exists( 'get_field' ) ? get_field( 'service_hero_image', $service->ID ) : false;
                $image_url = ( is_array( $service_image ) && isset( $service_image['url'] ) ) ? $service_image['url'] : get_the_post_thumbnail_url( $service->ID, 'large' );

                $short_desc_acf = function_exists( 'get_field' ) ? get_field( 'service_short_description', $service->ID ) : '';
                $short_desc = $short_desc_acf ?: wp_trim_words( $service->post_content, 25 );

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

                // Determine card style: first card is featured, then alternate grid
                $is_featured = ( $i === 0 );
                $card_class = $is_featured ? 'hv-service-showcase-card hv-service-showcase-card--featured' : 'hv-service-showcase-card';
            ?>

                <article class="<?php echo esc_attr( $card_class ); ?>" data-service-index="<?php echo esc_attr( $i + 1 ); ?>">
                    <a href="<?php echo esc_url( get_permalink( $service->ID ) ); ?>" class="hv-service-showcase-card__link">
                        <div class="hv-service-showcase-card__media">
                            <?php if ( $image_url ) : ?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $service_title ); ?>" loading="lazy">
                            <?php else :
                                $card_img_ph = function_exists( 'handandvision_acf_image_placeholder_html' ) ? handandvision_acf_image_placeholder_html( $is_hebrew ? 'תמונת באנר' : 'Hero image' ) : '';
                                echo $card_img_ph;
                                if ( ! $card_img_ph ) : ?>
                                <div class="hv-service-showcase-card__placeholder">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path d="M21 15l-5-5L5 21"/>
                                    </svg>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="hv-service-showcase-card__media-overlay"></div>
                        </div>
                        <div class="hv-service-showcase-card__content">
                            <div class="hv-service-showcase-card__header">
                                <span class="hv-service-showcase-card__number"><?php printf( '%02d', $i + 1 ); ?></span>
                                <span class="hv-service-showcase-card__tag">
                                    <?php echo esc_html( $is_hebrew ? 'שירות' : 'Service' ); ?>
                                </span>
                            </div>
                            <h2 class="hv-service-showcase-card__title"><?php echo esc_html( $service_title ); ?></h2>
                            <p class="hv-service-showcase-card__desc"><?php
                                $short_display = ( function_exists( 'handandvision_acf_display_value' ) && function_exists( 'handandvision_acf_empty' ) && handandvision_acf_empty( $short_desc ) )
                                    ? handandvision_acf_display_value( $short_desc, $is_hebrew ? 'תיאור קצר' : 'Short description', 'html' )
                                    : $short_desc;
                                echo $short_display === $short_desc ? esc_html( $short_desc ) : wp_kses_post( $short_display );
                            ?></p>
                            <div class="hv-service-showcase-card__cta">
                                <span class="hv-service-showcase-card__cta-text">
                                    <?php echo esc_html( $is_hebrew ? 'גלה עוד' : 'Discover More' ); ?>
                                </span>
                                <span class="hv-service-showcase-card__cta-arrow">
                                    <?php if ( $is_hebrew ) : ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                                        </svg>
                                    <?php else : ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                        </svg>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </article>

            <?php endforeach; ?>

        </div>
    </section>

    <!-- Premium CTA Section -->
    <section class="hv-services-cta-premium">
        <div class="hv-services-cta-premium__bg">
            <div class="hv-services-cta-premium__pattern"></div>
        </div>
        <div class="hv-container">
            <div class="hv-services-cta-premium__content">
                <div class="hv-services-cta-premium__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <span class="hv-services-cta-premium__overline">
                    <?php echo esc_html( $is_hebrew ? 'מוכנים להתחיל?' : 'Ready to Begin?' ); ?>
                </span>
                <h2 class="hv-services-cta-premium__title">
                    <?php echo esc_html( $is_hebrew ? 'בואו ניצור משהו יוצא דופן יחד' : "Let's Create Something Extraordinary Together" ); ?>
                </h2>
                <p class="hv-services-cta-premium__desc">
                    <?php echo esc_html( $is_hebrew
                        ? 'נשמח לשמוע על החזון האמנותי שלכם ולעזור להפוך אותו למציאות.'
                        : "We'd love to hear about your artistic vision and help bring it to life." ); ?>
                </p>
                <a href="<?php echo esc_url( function_exists( 'handandvision_get_contact_url' ) ? handandvision_get_contact_url() : home_url( '/contact' ) ); ?>" class="hv-services-cta-premium__btn">
                    <span><?php echo esc_html( $is_hebrew ? 'צרו קשר' : 'Get in Touch' ); ?></span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <?php if ( $is_hebrew ) : ?>
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        <?php else : ?>
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        <?php endif; ?>
                    </svg>
                </a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
