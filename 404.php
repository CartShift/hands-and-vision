<?php
/**
 * 404 — refined editorial template
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_hebrew = function_exists( 'handandvision_is_hebrew' ) ? handandvision_is_hebrew() : false;
?>

<main id="primary" class="hv-hero-layout hv-404-editorial">
	<section class="hv-404-section">
		<div class="hv-404-section__bg" aria-hidden="true">
			<svg class="hv-404-section__art" width="640" height="480" viewBox="0 0 640 480" fill="none" preserveAspectRatio="xMidYMid meet">
				<circle cx="260" cy="220" r="140" fill="currentColor" opacity="0.05"/>
				<circle cx="360" cy="240" r="110" fill="currentColor" opacity="0.04"/>
				<rect x="200" y="160" width="200" height="200" stroke="currentColor" stroke-width="1" stroke-opacity="0.18" transform="rotate(12 300 260)" fill="none"/>
				<path d="M40 360 Q180 200 320 360 T 600 360" stroke="currentColor" stroke-width="1" stroke-opacity="0.25" stroke-dasharray="6 8" fill="none"/>
			</svg>
		</div>

		<div class="hv-container hv-404-section__inner">
			<span class="hv-editorial-eyebrow">
				<span class="hv-editorial-eyebrow__label"><?php echo esc_html( $is_hebrew ? 'דף לא נמצא' : 'Page Not Found' ); ?></span>
			</span>

			<p class="hv-404-section__digits" aria-hidden="true">404</p>

			<h1 class="hv-404-section__title">
				<?php echo esc_html( $is_hebrew ? 'יצירה זו אינה קיימת' : 'This Piece Does Not Exist' ); ?>
			</h1>

			<p class="hv-404-section__text">
				<?php echo esc_html( $is_hebrew
					? 'נראה שהגעתם לחלל ריק בגלריה שלנו. אל דאגה, יש המון יצירות אחרות לגלות.'
					: "It seems you've reached an empty space in our gallery. Don't worry — there is plenty more art to discover." ); ?>
			</p>

			<div class="hv-404-section__actions">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hv-btn hv-btn--cta hv-editorial-cta-btn">
					<span><?php echo esc_html( $is_hebrew ? 'חזרה לדף הבית' : 'Back to Home' ); ?></span>
					<span class="hv-editorial-cta-arrow" aria-hidden="true"><?php echo $is_hebrew ? '←' : '→'; ?></span>
				</a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ?: home_url( '/' ) ); ?>" class="hv-404-section__ghost">
					<?php echo esc_html( $is_hebrew ? 'גלו את האמנים' : 'Discover Artists' ); ?>
				</a>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
