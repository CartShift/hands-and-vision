<?php
/**
 * Shared artists carousel.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_hebrew = isset( $args['is_hebrew'] ) ? (bool) $args['is_hebrew'] : ( function_exists( 'handandvision_is_hebrew' ) && handandvision_is_hebrew() );
$artists   = isset( $args['artists'] ) && is_array( $args['artists'] ) ? $args['artists'] : array();

if ( empty( $artists ) ) {
	$artists = get_posts(
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

$artists = is_array( $artists ) ? $artists : array();
if ( function_exists( 'handandvision_sort_artists_for_display' ) ) {
	$artists = handandvision_sort_artists_for_display( $artists );
}

foreach ( $artists as $artist_index => $artist_item ) {
	$artist_id   = is_object( $artist_item ) ? $artist_item->ID : (int) $artist_item;
	$artist_name = $artist_id ? mb_strtolower( trim( get_the_title( $artist_id ) ) ) : '';
	$artist_slug = $artist_id ? mb_strtolower( trim( get_post_field( 'post_name', $artist_id ) ) ) : '';
	$artist_key  = $artist_name . ' ' . str_replace( '-', ' ', $artist_slug );

	if (
		false !== mb_strpos( $artist_key, 'daniel philosoph' )
		|| false !== mb_strpos( $artist_key, 'daniel philosof' )
		|| false !== mb_strpos( $artist_key, 'דניאל פילוסוף' )
	) {
		unset( $artists[ $artist_index ] );
		array_unshift( $artists, $artist_item );
		$artists = array_values( $artists );
		break;
	}
}

$section_classes = isset( $args['section_classes'] ) ? (string) $args['section_classes'] : 'hv-section hv-section--white hv-home-artists';
$show_heading    = array_key_exists( 'show_heading', $args ) ? (bool) $args['show_heading'] : true;
$show_cta        = array_key_exists( 'show_cta', $args ) ? (bool) $args['show_cta'] : true;
$empty_text      = isset( $args['empty_text'] ) ? (string) $args['empty_text'] : ( $is_hebrew ? 'האמנים שלנו יוצגו כאן בקרוב.' : 'Our artists will be featured here soon.' );
$eyebrow         = isset( $args['eyebrow'] ) ? (string) $args['eyebrow'] : ( $is_hebrew ? 'הקולקטיב' : 'The Collective' );
$title           = isset( $args['title'] ) ? (string) $args['title'] : '';
?>

<section class="<?php echo esc_attr( $section_classes ); ?>">
	<?php if ( $show_heading ) : ?>
	<div class="hv-container">
		<header class="hv-section-header hv-text-center hv-animate hv-home-section-header hv-home-section-header--artists">
			<span class="hv-home-section-header__eyebrow">
				<span class="hv-home-section-header__label"><?php echo esc_html( $eyebrow ); ?></span>
			</span>
			<?php if ( $title ) : ?>
				<h2 class="hv-headline-1 hv-service-artists-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</header>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $artists ) ) : ?>
	<div class="hv-artists-showcase-bleed hv-carousel-bleed">
		<div class="hv-artists-showcase swiper">
			<div class="swiper-wrapper">
			<?php foreach ( $artists as $i => $artist_item ) :
				$artist_id = is_object( $artist_item ) ? $artist_item->ID : (int) $artist_item;
				if ( ! $artist_id ) {
					continue;
				}

				$a_name       = get_the_title( $artist_id );
				$a_quote      = function_exists( 'get_field' ) ? get_field( 'artist_quote', $artist_id ) : '';
				$a_link       = get_permalink( $artist_id );
				$a_image_html = '';
				$a_image_url  = '';
				$a_img_id     = function_exists( 'handandvision_get_artist_portrait_id' ) ? handandvision_get_artist_portrait_id( $artist_id ) : 0;

				if ( $a_img_id ) {
					$a_image_html = wp_get_attachment_image( $a_img_id, 'hv-artist', false, array( 'class' => 'hv-artist-card__img', 'loading' => 'lazy' ) );
				} else {
					$a_image_url = get_the_post_thumbnail_url( $artist_id, 'hv-artist' );
				}
				?>
				<article class="hv-artist-card swiper-slide">
					<a href="<?php echo esc_url( $a_link ); ?>" class="hv-artist-card__link" data-artist-id="<?php echo esc_attr( $artist_id ); ?>">
						<div class="hv-artist-card__portrait">
							<?php if ( $a_image_html ) : ?>
								<?php echo wp_kses_post( $a_image_html ); ?>
							<?php elseif ( $a_image_url ) : ?>
								<img src="<?php echo esc_url( $a_image_url ); ?>" alt="<?php echo esc_attr( $a_name ); ?>" class="hv-artist-card__img" loading="lazy">
							<?php else : ?>
								<div class="hv-artist-card__placeholder" style="background: linear-gradient(135deg, hsl(<?php echo (int) ( 30 + $i * 15 ); ?>, 15%, 75%) 0%, hsl(<?php echo (int) ( 40 + $i * 15 ); ?>, 20%, 65%) 100%);"></div>
							<?php endif; ?>
						</div>
						<div class="hv-artist-card__info">
							<h3 class="hv-artist-card__name"><?php echo esc_html( $a_name ); ?></h3>
							<?php if ( $a_quote ) : ?>
								<p class="hv-artist-card__quote">"<?php echo esc_html( $a_quote ); ?>"</p>
							<?php endif; ?>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php if ( $show_cta ) : ?>
	<div class="hv-container hv-text-center hv-mt-8">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ); ?>" class="hv-btn hv-btn--outline"><?php echo esc_html( $is_hebrew ? 'לכל האמנים' : 'All Artists' ); ?></a>
	</div>
	<?php endif; ?>
	<?php else : ?>
	<div class="hv-container">
		<p class="hv-text-center hv-muted hv-mt-4"><?php echo esc_html( $empty_text ); ?></p>
	</div>
	<?php endif; ?>
</section>
