<?php
/**
 * The template for displaying 404 pages (not found).
 * Premium "Artistic" 404 Page
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// RTL Check
$is_hebrew = function_exists('handandvision_is_hebrew') ? handandvision_is_hebrew() : false;
?>

<main id="primary" class="hv-hero-layout" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: var(--hv-cream);">
    <div class="hv-container hv-text-center">
        <!-- Abstract Art SVG -->
        <div class="hv-404-art" style="margin: 0 auto 40px; max-width: 400px; opacity: 0; transform: translateY(20px); animation: fade-in-up 0.8s ease-out forwards;">
            <svg width="100%" height="auto" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 250Q150 50 250 250T450 250" stroke="var(--hv-lilac)" stroke-width="2" fill="none" stroke-dasharray="10 5"/>
                <circle cx="200" cy="150" r="80" fill="var(--hv-pink-soft)" style="mix-blend-mode: multiply;"/>
                <circle cx="260" cy="150" r="60" fill="var(--hv-blue-soft)" style="mix-blend-mode: multiply;"/>
                <rect x="150" y="100" width="100" height="100" stroke="var(--hv-petrol)" stroke-width="1" transform="rotate(15 200 150)"/>
            </svg>
        </div>

        <h1 class="hv-headline-1" style="font-size: clamp(4rem, 10vw, 8rem); margin-bottom: 0px; color: var(--hv-petrol); line-height: 1;">404</h1>

        <p class="hv-subtitle" style="font-size: 1.5rem; margin-bottom: 40px; color: var(--hv-slate);">
            <?php echo $is_hebrew ? 'יצירה זו אינה קיימת' : 'This Piece Does Not Exist'; ?>
        </p>

        <p class="hv-text-body" style="max-width: 500px; margin: 0 auto 40px; color: var(--hv-stone);">
            <?php echo $is_hebrew
                ? 'נראה שהגעתם לחלל ריק בגלריה שלנו. אל דאגה, יש המון יצירות אחרות לגלות.'
                : 'It seems you have found an empty space in our gallery. Don\'t worry, there is plenty more art to discover.'; ?>
        </p>

        <div class="hv-404-actions" style="display: flex; gap: 20px; justify-content: center;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hv-btn hv-btn--primary">
                <?php echo $is_hebrew ? 'חזרה לדף הבית' : 'Back to Home'; ?>
            </a>
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hv-btn hv-btn--outline">
                    <?php echo $is_hebrew ? 'ביקור בחנות' : 'Visit Shop'; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
    .hv-404-art svg {
        overflow: visible;
    }
    .hv-404-art circle {
        animation: float-art 6s infinite ease-in-out;
    }
    .hv-404-art circle:nth-child(2) {
        animation-delay: 1s;
    }
    @keyframes float-art {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>

<?php
get_footer();
