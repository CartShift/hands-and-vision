<?php
/**
 * Archive Template: Artists
 * Premium gallery-style artists listing
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

// Custom inline CSS removed - moved to hv-unified.css

$artists = get_posts( array(
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
));
?>

<main id="primary" class="hv-archive-page hv-artists-archive">

<?php
$is_hebrew = handandvision_is_hebrew();
get_template_part( 'template-parts/hero/page-hero', null, array(
	'overline'   => $is_hebrew ? 'הקולקטיב שלנו' : 'Our Collective',
	'title'      => $is_hebrew ? 'האמנים' : 'Artists',
	'subtitle'   => $is_hebrew
		? 'פגשו את היוצרים המוכשרים שמרכיבים את קהילת Hand & Vision - אמנים מגוונים המביאים חזון ייחודי לכל יצירה'
		: 'Meet the talented creators who make up the Hand & Vision community - diverse artists bringing a unique vision to every piece',
	'stats'      => null,
	'scroll_text'=> $is_hebrew ? 'גלול לגילוי' : 'Scroll to discover',
) );
?>

    <!-- Artists Grid -->
    <section class="hv-section hv-section--cream">
        <div class="hv-container">
            <div class="hv-artists-grid hv-stagger">
                <?php foreach ( $artists as $artist ) :
                    $portrait = get_field( 'artist_portrait', $artist->ID );
                    $image_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist->ID, 'medium' );
                    $discipline = get_field( 'artist_discipline', $artist->ID ) ?: ( handandvision_is_hebrew() ? 'אמן/ית' : 'Artist' );
                ?>
                    <article class="hv-artist-card">
                        <a href="<?php echo esc_url( get_permalink( $artist->ID ) ); ?>" class="hv-artist-card__link">
                            <div class="hv-artist-card__portrait">
                                <?php if ( $image_url ) : ?>
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $artist->post_title ); ?>" class="hv-artist-card__img" loading="lazy">
                                <?php else :
                                    $card_ph = function_exists( 'handandvision_acf_image_placeholder_html' ) ? handandvision_acf_image_placeholder_html( handandvision_is_hebrew() ? 'תמונת אמן' : 'Artist image' ) : '';
                                    echo $card_ph;
                                    if ( ! $card_ph ) : ?>
                                    <div class="hv-artist-card__placeholder" style="background: linear-gradient(135deg, hsl(<?php echo (int) rand(30, 60); ?>, 15%, 75%) 0%, hsl(<?php echo (int) rand(40, 70); ?>, 20%, 65%) 100%);"></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="hv-artist-card__info">
                                <h3 class="hv-artist-card__name"><?php echo esc_html( $artist->post_title ); ?></h3>
                                <span class="hv-artist-card__discipline"><?php echo esc_html( $discipline ); ?></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Join CTA -->
    <section class="hv-cta-premium">
        <div class="hv-container hv-container--narrow hv-text-center">
            <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'את/ה אמן/ית?' : 'Are you an Artist?'; ?></span>
            <h2 class="hv-headline-2 hv-reveal"><?php echo handandvision_is_hebrew() ? 'הצטרפו לקולקטיב' : 'Join the Collective'; ?></h2>
            <p class="hv-subtitle hv-reveal" style="margin: var(--hv-space-5) auto var(--hv-space-7); max-width: 550px;">
                <?php echo handandvision_is_hebrew()
                    ? 'אנחנו תמיד מחפשים יוצרים מוכשרים להצטרף לקהילה שלנו. שלחו לנו את הפורטפוליו שלכם ונשמח להכיר.'
                    : 'We are always looking for talented creators to join our community. Send us your portfolio and we’d love to meet.'; ?>
            </p>
            <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--primary hv-reveal"><?php echo handandvision_is_hebrew() ? 'שלחו פורטפוליו' : 'Send Portfolio'; ?></a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
