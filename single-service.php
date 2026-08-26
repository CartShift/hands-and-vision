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

// Fallback function for language detection
if ( ! function_exists( 'handandvision_is_hebrew' ) ) {
    function handandvision_is_hebrew() {
        return false;
    }
}

get_header();

// WordPress Loop
while ( have_posts() ) : the_post();

$service_id = get_the_ID();
$service_title = get_the_title();
$is_hebrew = function_exists( 'handandvision_is_hebrew' ) ? handandvision_is_hebrew() : false;

// ACF Fields - All Protected
$hero_image = function_exists( 'get_field' ) ? get_field( 'service_hero_image', $service_id ) : false;
$hero_url = '';
if ( is_array( $hero_image ) && isset( $hero_image['url'] ) ) {
    $hero_url = $hero_image['url'];
} elseif ( has_post_thumbnail() ) {
    $hero_url = get_the_post_thumbnail_url( $service_id, 'full' );
}

$short_desc = function_exists( 'get_field' ) ? get_field( 'service_short_description', $service_id ) : '';
$full_desc = function_exists( 'get_field' ) ? get_field( 'service_full_description', $service_id ) : '';
$features = function_exists( 'get_field' ) ? get_field( 'service_what_we_do', $service_id ) : array();
$gallery = function_exists( 'get_field' ) ? get_field( 'service_gallery', $service_id ) : array();
$related_artists = function_exists( 'get_field' ) ? get_field( 'service_related_artists', $service_id ) : array();
$cta_text = function_exists( 'get_field' ) ? get_field( 'service_cta_text', $service_id ) : '';
$consultation_intro = function_exists( 'get_field' ) ? get_field( 'service_consultation_intro', $service_id ) : '';
$consultation_pairs = function_exists( 'get_field' ) ? get_field( 'service_consultation_pairs', $service_id ) : array();
$is_consultation = handandvision_is_consultation_service( $service_id );

// Validate arrays
$features = is_array( $features ) ? $features : array();
$gallery = is_array( $gallery ) ? $gallery : array();
$related_artists = is_array( $related_artists ) ? $related_artists : array();
$consultation_pairs = is_array( $consultation_pairs ) ? $consultation_pairs : array();

// Normalize gallery
$gallery_grid_items = function_exists( 'handandvision_get_service_gallery_items' )
    ? handandvision_get_service_gallery_items( $service_id )
    : array();

$service_title = handandvision_strip_dashes_from_copy( $service_title );
$short_desc = handandvision_strip_dashes_from_copy( $short_desc );
$full_desc = handandvision_strip_dashes_from_copy( $full_desc );
$is_digital_art_service = function_exists( 'handandvision_is_digital_art_service' ) && handandvision_is_digital_art_service( $service_id );
$uses_artist_sections = function_exists( 'handandvision_service_uses_artist_sections' ) && handandvision_service_uses_artist_sections( $service_id );

// English title override
if ( ! $is_hebrew && function_exists( 'get_field' ) ) {
    $en_title = get_field( 'service_title_en', $service_id );
    if ( ! empty( $en_title ) ) {
        $service_title = $en_title;
    }
}

if ( $is_digital_art_service ) {
    if ( empty( $full_desc ) ) {
        $full_desc = $is_hebrew
            ? 'אנחנו יוצרים עבודות דיגיטליות שמחברות בין דימוי, סיפור וטכנולוגיה. השירות מתאים למותגים, חללים, אירועים ופרויקטים שרוצים לבנות עולם חזותי מקורי, מדויק וזכיר.'
            : 'We create digital artworks that connect image, story and technology. This practice is built for brands, spaces, events and projects that need a distinct visual world with depth, motion and character.';
    }

    if ( empty( $features ) ) {
        $features = $is_hebrew
            ? array(
                array( 'point' => 'עיצוב דימויים דיגיטליים מקוריים למותגים, חללים ואירועים' ),
                array( 'point' => 'יצירת עולמות חזותיים, דמויות, קומפוזיציות וסדרות תוכן' ),
                array( 'point' => 'שילוב AI Art, איור, עריכה ו־Mixed Media לפי הקונספט' ),
                array( 'point' => 'התאמה למסכים, הדפסות, קמפיינים, הקרנות ותוצרים דיגיטליים' ),
            )
            : array(
                array( 'point' => 'Original digital imagery for brands, spaces and events' ),
                array( 'point' => 'Visual worlds, characters, compositions and content series' ),
                array( 'point' => 'AI art, illustration, editing and mixed media shaped around the concept' ),
                array( 'point' => 'Adaptation for screens, prints, campaigns, projections and digital formats' ),
            );
    }

}

if ( $uses_artist_sections && empty( $related_artists ) ) {
    $related_artists = get_posts(
        array(
            'post_type'              => 'artist',
            'posts_per_page'         => 20,
            'orderby'                => 'date',
            'order'                  => 'ASC',
            'post_status'            => 'publish',
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
        )
    );
}

$service_artist_showcase = ( $uses_artist_sections && function_exists( 'handandvision_get_service_artist_showcase' ) )
    ? handandvision_get_service_artist_showcase( $service_id )
    : array();
$service_artist_sections_heading = function_exists( 'handandvision_get_service_artist_sections_heading' )
    ? handandvision_get_service_artist_sections_heading( $service_id, $is_hebrew )
    : ( $is_hebrew ? 'עבודות לפי אמן' : 'Work by Artist' );
?>

<main id="primary" class="hv-single-service hv-single-service-editorial <?php echo esc_attr( $is_hebrew ? 'hv-single-service--rtl' : 'hv-single-service--ltr' ); ?>">

    <!-- Hero Section -->
    <section class="hv-service-single-hero">
        <div class="hv-service-single-hero__bg">
            <?php if ( $hero_url ) : ?>
                <img src="<?php echo esc_url( $hero_url ); ?>" alt="<?php echo esc_attr( $service_title ); ?>">
                <div class="hv-service-single-hero__overlay"></div>
            <?php else :
                $hero_ph = function_exists( 'handandvision_acf_image_placeholder_html' ) ? handandvision_acf_image_placeholder_html( $is_hebrew ? 'תמונת באנר' : 'Hero image' ) : '';
                echo $hero_ph;
                if ( ! $hero_ph ) : ?>
                <div class="hv-service-single-hero__gradient"></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="hv-service-single-hero__content">
            <div class="hv-container">
                <?php if ( function_exists( 'handandvision_breadcrumbs' ) ) { handandvision_breadcrumbs(); } ?>
                <div class="hv-service-single-hero__inner">
                    <span class="hv-service-single-hero__label">
                        <?php echo esc_html( $is_hebrew ? 'שירות' : 'Service' ); ?>
                    </span>
                    <h1 class="hv-service-single-hero__title">
                        <?php echo esc_html( handandvision_strip_dashes_from_copy( $service_title ) ); ?>
                    </h1>
                    <?php
                    $short_desc_display = function_exists( 'handandvision_acf_display_value' )
                        ? handandvision_acf_display_value( $short_desc, $is_hebrew ? 'תיאור קצר' : 'Short description', 'html' )
                        : $short_desc;
                    $short_desc_display = handandvision_strip_dashes_from_copy( $short_desc_display );
                    if ( $short_desc_display ) : ?>
                        <p class="hv-service-single-hero__subtitle">
                            <?php echo $short_desc_display === $short_desc ? esc_html( $short_desc ) : wp_kses_post( $short_desc_display ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <?php
    $full_desc_display = function_exists( 'handandvision_acf_display_value' )
        ? handandvision_acf_display_value( $full_desc, $is_hebrew ? 'תיאור מלא' : 'Full description', 'html' )
        : $full_desc;
    if ( $full_desc_display ) : ?>
    <section class="hv-service-content-section">
        <div class="hv-container hv-container--narrow">
            <div class="hv-service-content-text">
                <?php echo $full_desc_display === $full_desc ? wp_kses_post( wpautop( $full_desc ) ) : wp_kses_post( wpautop( $full_desc_display ) ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Service Artist Sketch -->
    <?php if ( ! empty( $service_artist_showcase ) ) : ?>
    <section class="hv-service-sketch-section" aria-labelledby="hv-service-sketch-heading">
        <div class="hv-container">
            <header class="hv-service-sketch-header">
                <span class="hv-service-sketch-label"><?php echo esc_html( $is_hebrew ? 'יוצרים מתוך השירות' : 'Artists in This Practice' ); ?></span>
                <h2 id="hv-service-sketch-heading" class="hv-service-sketch-title"><?php echo esc_html( $service_artist_sections_heading ); ?></h2>
                <div class="hv-service-artist-jump" data-hv-service-artist-jump>
                    <button class="hv-service-artist-jump__button" type="button" aria-expanded="false" aria-controls="hv-service-artist-jump-list">
                        <span><?php echo esc_html( $is_hebrew ? 'בחר אמן' : 'Choose Artist' ); ?></span>
                    </button>
                    <div id="hv-service-artist-jump-list" class="hv-service-artist-jump__list" hidden>
                        <?php foreach ( $service_artist_showcase as $jump_index => $jump_artist ) : ?>
                            <button class="hv-service-artist-jump__option" type="button" data-target="#hv-service-artist-<?php echo esc_attr( (int) $jump_index + 1 ); ?>">
                                <?php echo esc_html( $jump_artist['name'] ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </header>

            <div class="hv-service-sketch-list">
                <?php foreach ( $service_artist_showcase as $artist_index => $artist ) : ?>
                <article id="hv-service-artist-<?php echo esc_attr( (int) $artist_index + 1 ); ?>" class="hv-service-sketch-artist">
                    <header class="hv-service-sketch-artist__header">
                        <?php if ( ! empty( $artist['link'] ) ) : ?>
                            <a class="hv-service-sketch-artist__identity" href="<?php echo esc_url( $artist['link'] ); ?>" aria-label="<?php echo esc_attr( sprintf( $is_hebrew ? 'לעמוד הפרופיל של %s' : 'View %s artist profile', $artist['name'] ) ); ?>" title="<?php echo esc_attr( sprintf( $is_hebrew ? 'לעמוד הפרופיל של %s' : 'View %s artist profile', $artist['name'] ) ); ?>">
                        <?php else : ?>
                            <div class="hv-service-sketch-artist__identity">
                        <?php endif; ?>
                                <span class="hv-service-sketch-artist__portrait">
                                    <?php if ( ! empty( $artist['portrait'] ) ) : ?>
                                        <img src="<?php echo esc_url( $artist['portrait'] ); ?>" alt="<?php echo esc_attr( $artist['name'] ); ?>" loading="lazy">
                                    <?php endif; ?>
                                </span>
                                <span class="hv-service-sketch-artist__name"><?php echo esc_html( $artist['name'] ); ?></span>
                        <?php if ( ! empty( $artist['link'] ) ) : ?>
                            </a>
                        <?php else : ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <div class="hv-service-sketch-grid" aria-label="<?php echo esc_attr( sprintf( $is_hebrew ? 'שישה מקומות לעבודות של %s' : 'Six artwork slots for %s', $artist['name'] ) ); ?>">
                        <?php foreach ( $artist['projects'] as $slot_index => $project ) : ?>
                            <?php if ( ! empty( $project['image'] ) ) : ?>
                                <?php
                                $lightbox_url = ! empty( $project['full'] ) ? $project['full'] : $project['image'];
                                $caption_text = ! empty( $project['title'] ) ? $project['title'] : $artist['name'];
                                ?>
                                <a class="hv-service-sketch-tile hv-service-sketch-tile--filled hv-lightbox" href="<?php echo esc_url( $lightbox_url ); ?>" data-caption="<?php echo esc_attr( $caption_text ); ?>" aria-label="<?php echo esc_attr( sprintf( $is_hebrew ? 'פתיחת העבודה של %s בגודל מלא' : 'Open %s artwork full size', $artist['name'] ) ); ?>">
                                    <img src="<?php echo esc_url( $project['image'] ); ?>" alt="<?php echo esc_attr( $project['alt'] ?: $artist['name'] ); ?>" loading="lazy">
                                    <?php if ( ! empty( $project['title'] ) ) : ?>
                                        <span class="hv-service-sketch-tile__caption"><?php echo esc_html( $project['title'] ); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php else : ?>
                                <div class="hv-service-sketch-tile hv-service-sketch-tile--placeholder" aria-label="<?php echo esc_attr( sprintf( $is_hebrew ? 'מקום ליצירה %02d' : 'Artwork slot %02d', (int) $slot_index + 1 ) ); ?>">
                                    <span><?php echo esc_html( sprintf( '%02d', (int) $slot_index + 1 ) ); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="hv-service-sketch-deeper">
                        <?php $deeper_text = ! empty( $artist['deeper_text'] ) ? $artist['deeper_text'] : ( $is_hebrew ? 'להעמקה' : 'Go deeper' ); ?>
                        <?php if ( ! empty( $artist['link'] ) ) : ?>
                            <a href="<?php echo esc_url( $artist['link'] ); ?>">
                        <?php else : ?>
                            <span>
                        <?php endif; ?>
                                <span class="hv-service-sketch-deeper__text"><?php echo esc_html( $deeper_text ); ?></span>
                                <span class="hv-service-sketch-deeper__arrow" aria-hidden="true">↓</span>
                        <?php if ( ! empty( $artist['link'] ) ) : ?>
                            </a>
                        <?php else : ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $is_consultation ) :
        $default_intro = $is_hebrew
            ? 'ייעוץ והכוונה זה תהליך שבו אנחנו מתרגמים את החזון שלכם לעיצוב מקורי, מדויק ואישי. כל פרויקט נבנה מהבסיס, עם הדמיות ותכנון שמראים לכם בדיוק לאן הדרך הולכת, עוד לפני שמתחילים לייצר.'
            : 'Consultation and guidance is how we translate your vision into original, precise, personal design. Every project is built from the ground up, with visualizations and planning that show you exactly where the journey leads, before production begins.';
        $intro_display = $consultation_intro ? handandvision_strip_dashes_from_copy( $consultation_intro ) : $default_intro;
    ?>
    <section class="hv-service-consultation-section">
        <div class="hv-container hv-container--narrow hv-text-center">
            <span class="hv-service-consultation-label"><?php echo esc_html( $is_hebrew ? 'הגישה שלנו' : 'Our Approach' ); ?></span>
            <h2 class="hv-service-consultation-title"><?php echo esc_html( $is_hebrew ? 'מתכנון לפרויקט מושלם' : 'From Planning to a Finished Project' ); ?></h2>
            <p class="hv-service-consultation-intro"><?php echo esc_html( $intro_display ); ?></p>
            <p class="hv-service-consultation-highlight"><?php echo esc_html( $is_hebrew ? 'עיצובים מקוריים, בהתאמה אישית לכל לקוח.' : 'Original designs, tailored personally for every client.' ); ?></p>
        </div>
        <?php if ( ! empty( $consultation_pairs ) ) : ?>
        <div class="hv-container">
            <div class="hv-consultation-pairs">
                <?php foreach ( $consultation_pairs as $pair ) :
                    $plan = $pair['planning_image'] ?? null;
                    $final = $pair['final_image'] ?? null;
                    if ( ! is_array( $plan ) || ! is_array( $final ) ) continue;
                    if ( empty( $plan['url'] ) || empty( $final['url'] ) ) continue;
                    $caption = handandvision_strip_dashes_from_copy( (string) ( $pair['caption'] ?? '' ) );
                ?>
                <article class="hv-consultation-pair">
                    <div class="hv-consultation-pair__grid">
                        <div class="hv-consultation-pair__col">
                            <span class="hv-consultation-pair__label"><?php echo esc_html( $is_hebrew ? 'תכנון / הדמיה' : 'Planning / Visualization' ); ?></span>
                            <img src="<?php echo esc_url( $plan['url'] ); ?>" alt="<?php echo esc_attr( $is_hebrew ? 'תכנון' : 'Planning' ); ?>" loading="lazy">
                        </div>
                        <div class="hv-consultation-pair__arrow" aria-hidden="true">→</div>
                        <div class="hv-consultation-pair__col">
                            <span class="hv-consultation-pair__label"><?php echo esc_html( $is_hebrew ? 'פרויקט סופי' : 'Final Project' ); ?></span>
                            <img src="<?php echo esc_url( $final['url'] ); ?>" alt="<?php echo esc_attr( $is_hebrew ? 'פרויקט סופי' : 'Final project' ); ?>" loading="lazy">
                        </div>
                    </div>
                    <?php if ( $caption ) : ?>
                        <p class="hv-consultation-pair__caption"><?php echo esc_html( $caption ); ?></p>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- Artists Carousel -->
    <?php
    get_template_part(
        'template-parts/artist-carousel',
        null,
        array(
            'is_hebrew'       => $is_hebrew,
            'section_classes' => 'hv-section hv-section--white hv-home-artists hv-service-artists-section hv-service-artists-section--carousel',
            'eyebrow'         => $is_hebrew ? 'שיתופי פעולה' : 'Collaborations',
            'title'           => $is_hebrew ? 'אמנים שעובדים איתנו' : 'Artists We Work With',
            'show_heading'    => true,
            'show_cta'        => true,
        )
    );
    ?>

    <!-- CTA Section -->
    <section class="hv-service-cta-section">
        <div class="hv-container hv-container--narrow">
            <div class="hv-service-cta-content">
                <span class="hv-service-cta-label">
                    <?php echo esc_html( $is_hebrew ? 'מתחילים' : 'Let\'s Begin' ); ?>
                </span>
                <?php
                $default_cta = $is_hebrew ? 'מוכנים להתחיל את הפרויקט?' : 'Ready to Start Your Project?';
                $display_cta = ! empty( $cta_text ) ? handandvision_strip_dashes_from_copy( $cta_text ) : $default_cta;
                if ( ! $is_hebrew && 'צרו קשר' === trim( $display_cta ) ) {
                    $display_cta = $default_cta;
                }
                ?>
                <h2 class="hv-service-cta-title"><?php echo esc_html( $display_cta ); ?></h2>
                <p class="hv-service-cta-desc">
                    <?php echo esc_html( $is_hebrew ? 'נשמח לשמוע על הצרכים שלכם ולבנות יחד את הפתרון המושלם' : 'We\'d love to hear about your needs and build the perfect solution together' ); ?>
                </p>
                <?php
                $contact_url = home_url( '/contact' );
                if ( function_exists( 'handandvision_get_contact_url' ) ) {
                    $contact_url = handandvision_get_contact_url();
                }
                ?>
                <a href="<?php echo esc_url( $contact_url ); ?>" class="hv-btn hv-btn--cta">
                    <?php echo esc_html( $is_hebrew ? 'צרו קשר' : 'Get in Touch' ); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php
endwhile; // End Loop
get_footer();
