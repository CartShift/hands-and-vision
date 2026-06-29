<?php
/**
 * Archive Template: Artists
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$artists = get_posts( array(
    'post_type'              => 'artist',
    'posts_per_page'         => 200,
    'orderby'                => 'menu_order',
    'order'                  => 'ASC',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
) );
$artists = handandvision_sort_artists_for_display( $artists );

$is_hebrew = handandvision_is_hebrew();
?>

<main id="primary" class="hv-hero-layout hv-archive-page hv-artists-archive-premium">

    <?php
    get_template_part( 'template-parts/hero/page-hero', null, array(
        'overline'   => $is_hebrew ? 'הקולקטיב' : 'The Collective',
        'title'      => $is_hebrew ? 'האמנים שלנו' : 'Our Artists',
        'subtitle'   => $is_hebrew
            ? 'יוצרים מהקולקטיב שלנו. לחצו על כל אמן לצפייה בעבודות.'
            : 'Creators from our collective. Tap any artist to explore their work.',
        'class'      => 'hv-hero--artists-dark',
    ) );
    ?>

    <section class="hv-section hv-section--cream hv-artists-grid-section">
        <div class="hv-container">
            <div class="hv-artist-grid-premium hv-stagger-parent">
                <?php foreach ( $artists as $index => $artist ) :
                    $image_id = handandvision_get_artist_portrait_id( $artist->ID );
                    if ( ! $image_id ) {
                        $gallery = get_field( 'artist_gallery', $artist->ID );
                        if ( is_array( $gallery ) && ! empty( $gallery ) ) {
                            $first = reset( $gallery );
                            if ( is_array( $first ) && ! empty( $first['ID'] ) ) {
                                $image_id = (int) $first['ID'];
                            } elseif ( is_numeric( $first ) ) {
                                $image_id = (int) $first;
                            }
                        }
                    }
                    $initials = '';
                    $words    = preg_split( '/\s+/', trim( wp_strip_all_tags( $artist->post_title ) ) );
                    foreach ( array_slice( $words, 0, 2 ) as $word ) {
                        $initials .= mb_substr( $word, 0, 1 );
                    }
                    $delay = ( $index % 3 ) * 0.1;
                ?>
                    <article class="hv-artist-card-premium hv-reveal" style="transition-delay: <?php echo esc_attr( $delay ); ?>s;">
                        <a href="<?php echo esc_url( get_permalink( $artist->ID ) ); ?>" class="hv-artist-card-premium__link" data-artist-id="<?php echo esc_attr( $artist->ID ); ?>">
                            <div class="hv-artist-card-premium__media-wrapper">
                                <div class="hv-artist-card-premium__media">
                                    <?php if ( $image_id ) : ?>
                                        <?php echo wp_get_attachment_image( $image_id, 'medium_large', false, array(
                                            'class'   => 'hv-artist-card-premium__img',
                                            'alt'     => esc_attr( $artist->post_title ),
                                            'loading' => 'lazy',
                                        ) ); ?>
                                    <?php else : ?>
                                        <div class="hv-artist-card-premium__placeholder" aria-hidden="true"><span><?php echo esc_html( mb_strtoupper( $initials ) ); ?></span></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="hv-artist-card-premium__info">
                                <h3 class="hv-artist-card-premium__name"><?php echo esc_html( $artist->post_title ); ?></h3>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="hv-section hv-section--cream hv-archive-artists-cta">
        <div class="hv-container hv-container--narrow hv-text-center">
            <div class="hv-artist-cta-minimal hv-reveal">
                <span class="hv-editorial-eyebrow"><span class="hv-editorial-eyebrow__label"><?php echo esc_html( $is_hebrew ? 'הצטרפו אלינו' : 'Join Us' ); ?></span></span>
                <h2 class="hv-headline-3"><?php echo esc_html( $is_hebrew ? 'אמנים המעוניינים להצטרף?' : 'Are you an Artist?' ); ?></h2>
                <p class="hv-text-body">
                    <?php echo esc_html( $is_hebrew
                        ? 'אנחנו תמיד שמחים לצרף כישרונות חדשים לקולקטיב שלנו.'
                        : 'We are always looking for new talent to join our collective.' ); ?>
                </p>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--cta hv-editorial-cta-btn">
                    <span><?php echo esc_html( $is_hebrew ? 'דברו איתנו' : 'Get in touch' ); ?></span>
                    <span class="hv-editorial-cta-arrow" aria-hidden="true"><?php echo $is_hebrew ? '←' : '→'; ?></span>
                </a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
