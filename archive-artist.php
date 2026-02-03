<?php
/**
 * Archive Template: Artists
 * Premium gallery-style artists listing ("The Collective")
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$artists = get_posts( array(
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
));

$is_hebrew = handandvision_is_hebrew();
?>

<main id="primary" class="hv-hero-layout hv-archive-page hv-artists-archive-premium">

    <?php
    get_template_part( 'template-parts/hero/page-hero', null, array(
        'overline'   => $is_hebrew ? 'הקולקטיב' : 'The Collective',
        'title'      => $is_hebrew ? 'האמנים שלנו' : 'Our Artists',
        'subtitle'   => $is_hebrew
            ? 'נבחרת של יוצרים בעלי חזון, המשלבים אומנות מסורתית עם עיצוב עכשווי לכדי יצירה משמעותית.'
            : 'A curated selection of visionary creators, blending traditional craftsmanship with contemporary design.',
        'class'      => 'hv-hero--clean', // Cleaner hero variant
    ) );
    ?>

    <!-- Premium Artists Grid -->
    <section class="hv-section hv-section--white">
        <div class="hv-container">
            <div class="hv-artist-grid-premium hv-stagger-parent">
                <?php foreach ( $artists as $index => $artist ) :
                    $portrait = get_field( 'artist_portrait', $artist->ID );
                    $image_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist->ID, 'large' );

                    // Fallback Discipline
                    $discipline = get_field( 'artist_discipline', $artist->ID );
                    if ( ! $discipline ) {
                        $discipline = $is_hebrew ? 'אמן/ית' : 'Artist';
                    }

                    // Stagger animation delay
                    $delay = ($index % 3) * 0.1;
                ?>

                    <article class="hv-artist-card-premium hv-reveal" style="transition-delay: <?php echo esc_attr($delay); ?>s;">
                        <a href="<?php echo esc_url( get_permalink( $artist->ID ) ); ?>" class="hv-artist-card-premium__link" data-artist-id="<?php echo esc_attr( $artist->ID ); ?>">
                            <div class="hv-artist-card-premium__media-wrapper">
                                <div class="hv-artist-card-premium__media">
                                    <?php if ( $image_url ) : ?>
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $artist->post_title ); ?>" class="hv-artist-card-premium__img" loading="lazy">
                                    <?php else : ?>
                                        <div class="hv-artist-card-premium__placeholder"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="hv-artist-card-premium__overlay">
                                    <span class="hv-artist-card-premium__view-btn"><?php echo $is_hebrew ? 'צפה בפרופיל' : 'View Profile'; ?></span>
                                </div>
                            </div>

                            <div class="hv-artist-card-premium__info">
                                <h3 class="hv-artist-card-premium__name"><?php echo esc_html( $artist->post_title ); ?></h3>
                                <span class="hv-artist-card-premium__discipline"><?php echo esc_html( $discipline ); ?></span>

                                <div class="hv-artist-card-premium__excerpt-wrap">
                                    <div class="hv-artist-card-premium__excerpt">
                                        <?php
                                        $bio = get_field( 'artist_biography', $artist->ID ) ?: get_field( 'artist_bio', $artist->ID );
                                        if ( $bio ) {
                                            echo wp_trim_words( strip_tags( $bio ), 20 );
                                        }
                                        ?>
                                    </div>
                                    <button type="button" class="hv-artist-card-premium__expander" aria-expanded="false">
                                        <span class="hv-expander-text"><?php echo $is_hebrew ? 'קרא עוד' : 'Read More'; ?></span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div class="hv-artist-card-premium__full-bio" hidden>
                                        <?php echo wp_kses_post( $bio ); ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Join CTA (Minimalist) -->
    <section class="hv-section hv-section--cream">
        <div class="hv-container hv-container--narrow hv-text-center">
            <div class="hv-artist-cta-minimal hv-reveal">
                <h2 class="hv-headline-3"><?php echo $is_hebrew ? 'אמנים המעוניינים להצטרף?' : 'Are you an Artist?'; ?></h2>
                <p class="hv-text-body">
                    <?php echo $is_hebrew
                        ? 'אנחנו תמיד שמחים לצרף כישרונות חדשים לקולקטיב שלנו.'
                        : 'We are always looking for new talent to join our collective.'; ?>
                </p>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--primary-gold"><?php echo $is_hebrew ? 'דברו איתנו' : 'Get in touch'; ?></a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
