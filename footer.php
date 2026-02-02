<?php
/**
 * Custom Footer Template
 * Premium footer with navigation and info
 *
 * @package HandAndVision
 */

if ( function_exists( 'astra_content_bottom' ) ) {
	astra_content_bottom();
}
?>
	</div><!-- .site-content wrapper (ast-container or hv-full-width-wrapper) -->
	</div><!-- #content -->

<?php
if ( ! defined( 'HV_FOOTER_RENDERING' ) ) {
	define( 'HV_FOOTER_RENDERING', true );
}
if ( function_exists( 'astra_footer' ) ) {
	astra_footer();
}
$footer_tagline = handandvision_is_hebrew()
	? 'כאשר אמנות פוגשת כוונה. אנחנו מחברים בין אמנים לאספנים, בין יצירה לחלל, בין חזון למציאות.'
	: 'When art meets intention. We connect artists with collectors, creation with space, vision with reality.';
$current_year = date( 'Y' );
?>

<footer class="hv-footer" id="hv-footer">
    <div class="hv-container">
        <div class="hv-footer__grid">

            <!-- Brand Column -->
            <div class="hv-footer__brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hv-footer__logo">
                    HAND & VISION
                </a>
                <p class="hv-footer__tagline"><?php echo esc_html( $footer_tagline ); ?></p>
                <div class="hv-social-links">
                    <a href="https://www.instagram.com/handsvision_collective?igsh=MXBnaGpvcXZxeDhkbg==" class="hv-social-link hv-social-link--dark" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/share/1KcTbs4JUR/" class="hv-social-link hv-social-link--dark" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Navigation Column -->
            <div class="hv-footer__column">
                <h4 class="hv-footer__heading"><?php echo esc_html( handandvision_is_hebrew() ? 'ניווט' : 'Navigation' ); ?></h4>
                <ul class="hv-footer__links">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'דף הבית' : 'Home' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'שירותים' : 'Services' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'artist' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'אמנים' : 'Artists' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'gallery_item' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'גלריה' : 'Gallery' ); ?></a></li>
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'חנות' : 'Shop' ); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Services Column -->
            <div class="hv-footer__column">
                <h4 class="hv-footer__heading"><?php echo esc_html( handandvision_is_hebrew() ? 'שירותים' : 'Services' ); ?></h4>
                <ul class="hv-footer__links">
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'אוצרות אמנות' : 'Art Curation' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'עיצוב תערוכות' : 'Exhibition Design' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'ייעוץ אמנותי' : 'Art Consultancy' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'ייצוג אמנים' : 'Artist Representation' ); ?></a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="hv-footer__column">
                <h4 class="hv-footer__heading"><?php echo esc_html( handandvision_is_hebrew() ? 'יצירת קשר' : 'Contact' ); ?></h4>
                <ul class="hv-footer__links">
                    <li><?php echo esc_html( handandvision_is_hebrew() ? 'רחוב דיזנגוף 123' : '123 Dizengoff St' ); ?></li>
                    <li><?php echo esc_html( handandvision_is_hebrew() ? 'תל אביב, ישראל' : 'Tel Aviv, Israel' ); ?></li>
                    <li><a href="tel:03-555-1234">03-555-1234</a></li>
                    <li><a href="mailto:hello@handandvision.co.il">hello@handandvision.co.il</a></li>
                </ul>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>" class="hv-btn hv-btn--ghost hv-footer__cta hv-mt-4">
                    <?php echo esc_html( handandvision_is_hebrew() ? 'צרו קשר' : 'Get in Touch' ); ?>
                </a>
            </div>

        </div>

        <div class="hv-footer__bottom">
            <p>© <?php echo esc_html( $current_year ); ?> Hand & Vision. <?php echo esc_html( handandvision_is_hebrew() ? 'כל הזכויות שמורות.' : 'All rights reserved.' ); ?></p>
            <nav class="hv-footer__legal">
                <?php
                $privacy_url = get_privacy_policy_url();
                if ( $privacy_url ) :
                    ?>
                    <a href="<?php echo esc_url( $privacy_url ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'מדיניות פרטיות' : 'Privacy Policy' ); ?></a>
                    <span>·</span>
                <?php endif; ?>
                <a href="<?php echo esc_url( handandvision_get_contact_url() ); ?>"><?php echo esc_html( handandvision_is_hebrew() ? 'צור קשר' : 'Contact' ); ?></a>
                <span>·</span>
                <span><?php echo esc_html( handandvision_is_hebrew() ? 'תנאי שימוש' : 'Terms' ); ?></span>
                <span>·</span>
                <span><?php echo esc_html( handandvision_is_hebrew() ? 'נגישות' : 'Accessibility' ); ?></span>
            </nav>
        </div>
    </div>
</footer>

<!-- Drag to Scroll for Carousels -->
<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Reusable function for drag scrolling
        function enableDragScroll(container) {
            if (!container) return;

            let isDown = false;
            let startX;
            let scrollLeft;

            container.addEventListener('mousedown', function(e) {
                isDown = true;
                container.style.cursor = 'grabbing';
                container.style.scrollSnapType = 'none';
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
                e.preventDefault();
            });

            container.addEventListener('mouseleave', function() {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mouseup', function() {
                isDown = false;
                container.style.cursor = 'grab';
                container.style.scrollSnapType = 'x mandatory';
            });

            document.addEventListener('mouseup', function() {
                if (isDown) {
                    isDown = false;
                    container.style.cursor = 'grab';
                    container.style.scrollSnapType = 'x mandatory';
                }
            });

            container.addEventListener('mousemove', function(e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 1.5;
                container.scrollLeft = scrollLeft - walk;
            });
        }

        // Apply to artists showcase
        const artistsShowcase = document.querySelector('.hv-artists-showcase');
        enableDragScroll(artistsShowcase);

        // Apply to services grid
        const servicesGrid = document.querySelector('.hv-services-grid');
        enableDragScroll(servicesGrid);
    });
})();
</script>

<?php wp_footer(); ?>

</body>
</html>
