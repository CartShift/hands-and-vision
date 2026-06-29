<?php
/**
 * Single Template: Artist
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$artist_id   = get_the_ID();
$is_hebrew   = function_exists( 'handandvision_is_hebrew' ) ? handandvision_is_hebrew() : ( get_locale() === 'he_IL' );
$artist_name = get_the_title();

$portrait     = get_field( 'artist_portrait', $artist_id );
$portrait_url = ( is_array( $portrait ) && isset( $portrait['url'] ) ) ? $portrait['url'] : get_the_post_thumbnail_url( $artist_id, 'full' );
$has_portrait = ! empty( $portrait_url );
$video_url    = get_field( 'artist_video', $artist_id );

$bio_raw = get_field( 'artist_biography', $artist_id ) ?: get_field( 'artist_bio', $artist_id ) ?: '';
$bio_display = function_exists( 'handandvision_acf_display_value' )
	? handandvision_acf_display_value( $bio_raw, $is_hebrew ? 'ביוגרפיה' : 'Biography', 'html' )
	: $bio_raw;
$bio_plain   = trim( wp_strip_all_tags( (string) $bio_display ) );
$bio_excerpt = $bio_plain ? wp_trim_words( $bio_plain, 22, '…' ) : '';

$social_raw = get_field( 'artist_social', $artist_id );
if ( empty( $social_raw ) || ! is_array( $social_raw ) ) {
	$social_raw = array(
		'instagram' => get_field( 'artist_instagram', $artist_id ) ?: '',
		'facebook'  => get_field( 'artist_facebook', $artist_id ) ?: '',
		'website'   => get_field( 'artist_website', $artist_id ) ?: '',
	);
}
$social_links = array_filter( $social_raw, function ( $url ) { return is_string( $url ) && $url !== ''; } );

$artist_gallery_items = handandvision_get_artist_gallery_items( $artist_id );
$projects_count       = is_array( $artist_gallery_items ) ? count( $artist_gallery_items ) : 0;

$archive_url     = get_post_type_archive_link( 'artist' );
$initial_letter  = function_exists( 'mb_substr' ) ? mb_substr( $artist_name, 0, 1, 'UTF-8' ) : substr( $artist_name, 0, 1 );

$social_label = array(
	'instagram' => $is_hebrew ? 'אינסטגרם' : 'Instagram',
	'facebook'  => $is_hebrew ? 'פייסבוק'  : 'Facebook',
	'website'   => $is_hebrew ? 'אתר'       : 'Website',
);
?>

<main id="primary" class="hv-single-artist-premium hv-single-artist-v2 hv-artist-editorial">

	<?php
	$hero_modifier = ( $video_url || $has_portrait ) ? '' : ' hv-artist-cinema-hero--text';
	?>
	<section class="hv-artist-cinema-hero<?php echo esc_attr( $hero_modifier ); ?>" aria-labelledby="hv-artist-cinema-name">
		<div class="hv-artist-cinema-hero__media">
			<?php if ( $video_url ) : ?>
				<video class="hv-artist-cinema-hero__visual" autoplay muted loop playsinline<?php if ( $has_portrait ) : ?> poster="<?php echo esc_url( $portrait_url ); ?>"<?php endif; ?>>
					<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
				</video>
			<?php elseif ( $has_portrait ) : ?>
				<img src="<?php echo esc_url( $portrait_url ); ?>" alt="<?php echo esc_attr( $artist_name ); ?>" class="hv-artist-cinema-hero__visual">
			<?php else : ?>
				<div class="hv-artist-cinema-hero__monogram" aria-hidden="true"><span><?php echo esc_html( mb_strtoupper( $initial_letter ) ); ?></span></div>
			<?php endif; ?>
			<div class="hv-artist-cinema-hero__scrim" aria-hidden="true"></div>
		</div>

		<div class="hv-artist-cinema-hero__inner hv-container">
			<?php if ( $archive_url ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="hv-artist-cinema-hero__back">
					<span class="hv-artist-cinema-hero__back-arrow" aria-hidden="true"><?php echo $is_hebrew ? '→' : '←'; ?></span>
					<span><?php echo $is_hebrew ? 'כל האמנים' : 'All Artists'; ?></span>
				</a>
			<?php endif; ?>

			<div class="hv-artist-cinema-hero__caption">
				<span class="hv-artist-cinema-hero__overline"><?php echo $is_hebrew ? 'דיוקן אמן · יד וחזון' : 'Artist Profile · Hand &amp; Vision'; ?></span>
				<h1 id="hv-artist-cinema-name" class="hv-artist-cinema-hero__name"><?php echo esc_html( $artist_name ); ?></h1>
				<?php if ( $bio_excerpt ) : ?>
					<p class="hv-artist-cinema-hero__tagline"><?php echo esc_html( $bio_excerpt ); ?></p>
				<?php endif; ?>

				<div class="hv-artist-cinema-hero__meta">
					<?php if ( $projects_count ) : ?>
						<span class="hv-artist-cinema-hero__meta-item">
							<span class="hv-artist-cinema-hero__meta-num"><?php echo esc_html( str_pad( (string) $projects_count, 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="hv-artist-cinema-hero__meta-label"><?php echo $is_hebrew ? 'עבודות' : 'Works'; ?></span>
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $social_links ) ) : ?>
						<span class="hv-artist-cinema-hero__meta-divider" aria-hidden="true"></span>
						<span class="hv-artist-cinema-hero__socials">
							<?php foreach ( $social_links as $platform => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="hv-artist-cinema-hero__social"><?php echo esc_html( $social_label[ $platform ] ?? ucfirst( (string) $platform ) ); ?></a>
							<?php endforeach; ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<?php
			$scroll_target = $bio_plain ? '#hv-artist-bio' : ( ! empty( $artist_gallery_items ) ? '#hv-artist-works' : '' );
			if ( $scroll_target ) :
			?>
			<a href="<?php echo esc_attr( $scroll_target ); ?>" class="hv-artist-cinema-hero__scroll" aria-label="<?php echo esc_attr( $is_hebrew ? 'גלול לתוכן' : 'Scroll to content' ); ?>">
				<span class="hv-artist-cinema-hero__scroll-line" aria-hidden="true"></span>
				<span class="hv-artist-cinema-hero__scroll-text"><?php echo $is_hebrew ? 'גלול' : 'Scroll'; ?></span>
			</a>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $bio_plain || ! empty( $social_links ) ) : ?>
	<section id="hv-artist-bio" class="hv-section hv-artist-statement">
		<div class="hv-container">
			<div class="hv-artist-statement__grid">
				<aside class="hv-artist-statement__aside">
					<span class="hv-artist-section-label"><?php echo $is_hebrew ? 'ביוגרפיה' : 'Biography'; ?></span>
					<span class="hv-artist-statement__rule" aria-hidden="true"></span>
				</aside>

				<div class="hv-artist-statement__body hv-reveal">
					<span class="hv-artist-statement__watermark" aria-hidden="true"><?php echo esc_html( $initial_letter ); ?></span>
					<?php if ( $bio_plain ) : ?>
					<div class="hv-artist-bio hv-artist-statement__prose">
						<?php echo wp_kses_post( wpautop( $bio_display ) ); ?>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $social_links ) ) : ?>
						<div class="hv-artist-statement__socials">
							<span class="hv-artist-statement__socials-label"><?php echo $is_hebrew ? 'עקבו' : 'Follow'; ?></span>
							<?php foreach ( $social_links as $platform => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="hv-artist-statement__social-link"><?php echo esc_html( $social_label[ $platform ] ?? ucfirst( (string) $platform ) ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $artist_gallery_items ) ) : ?>
	<section id="hv-artist-works" class="hv-section hv-artist-projects-carousel-section hv-artist-projects-editorial">
		<div class="hv-container">
			<header class="hv-section-header hv-artist-section-header">
				<h2 class="hv-headline-3 hv-artist-section-header__title"><?php echo $is_hebrew ? 'עבודות נבחרות' : 'Selected Works'; ?></h2>
				<?php
				if ( $is_hebrew ) {
					$count_label = ( 1 === $projects_count ) ? 'עבודה אחת' : sprintf( '%d עבודות', $projects_count );
				} else {
					$count_label = sprintf( _n( '%d work', '%d works', $projects_count, 'astra' ), $projects_count );
				}
				?>
				<span class="hv-artist-section-header__count"><?php echo esc_html( $count_label ); ?></span>
			</header>
		</div>
		<div class="hv-artist-projects-carousel-bleed hv-carousel-bleed">
			<div class="hv-artist-projects-carousel swiper">
				<div class="swiper-wrapper">
					<?php $i = 0; foreach ( $artist_gallery_items as $item ) : $i++; ?>
						<article class="swiper-slide hv-artist-project-slide">
							<a href="<?php echo esc_url( $item['url'] ); ?>" class="hv-artist-project-slide__link hv-lightbox" data-gallery="artist-gallery" data-caption="<?php echo esc_attr( $item['caption'] ?: $item['title'] ); ?>">
								<div class="hv-artist-project-slide__media">
									<img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['caption'] ?: $item['title'] ); ?>" loading="lazy">
									<span class="hv-artist-project-slide__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
								</div>
								<?php if ( ! empty( $item['caption'] ) || ! empty( $item['title'] ) ) : ?>
									<p class="hv-artist-project-slide__caption"><?php echo esc_html( $item['caption'] ?: $item['title'] ); ?></p>
								<?php endif; ?>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
				<div class="swiper-pagination"></div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
	if ( class_exists( 'WooCommerce' ) ) {
		$artist_products = handandvision_get_artist_products( $artist_id );
		if ( ! empty( $artist_products ) ) :
	?>
	<section class="hv-section hv-artist-shop-premium hv-artist-shop-editorial">
		<div class="hv-container">
			<header class="hv-section-header hv-artist-section-header hv-artist-section-header--split">
				<div class="hv-artist-section-header__lead">
					<span class="hv-artist-section-label"><?php echo $is_hebrew ? 'אוסף' : 'Collection'; ?></span>
				</div>
				<h2 class="hv-headline-3 hv-artist-section-header__title"><?php echo $is_hebrew ? 'זמין לרכישה' : 'Available for Collection'; ?></h2>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hv-link-view-all hv-artist-section-header__cta">
					<?php echo $is_hebrew ? 'לחנות המלאה' : 'View Full Shop'; ?>
					<span aria-hidden="true"><?php echo $is_hebrew ? '←' : '→'; ?></span>
				</a>
			</header>
			<div class="hv-product-grid-premium">
				<?php foreach ( $artist_products as $product_post ) :
					$product = wc_get_product( $product_post->ID );
					if ( ! $product ) continue;
				?>
				<div class="hv-product-card-minimal">
					<a href="<?php echo esc_url( get_permalink( $product_post->ID ) ); ?>" class="hv-product-card-minimal__link" data-product-id="<?php echo esc_attr( (string) $product_post->ID ); ?>">
						<div class="hv-product-card-minimal__image">
							<?php echo $product->get_image('woocommerce_thumbnail'); ?>
						</div>
						<div class="hv-product-card-minimal__details">
							<h3 class="hv-product-card-minimal__title"><?php echo esc_html( $product->get_name() ); ?></h3>
							<span class="hv-product-card-minimal__price"><?php echo $product->get_price_html(); ?></span>
						</div>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; } ?>

</main>

<?php get_footer(); ?>
