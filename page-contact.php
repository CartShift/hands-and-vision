<?php
/**
 * Template Name: Contact Page
 * Premium contact page with form
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$page_id = get_the_ID();

// Page Header Info
$page_title_display = get_field( 'page_title_display', $page_id ) ?: ( handandvision_is_hebrew() ? 'צור קשר' : "Let's Talk" );
$page_overline = get_field( 'page_overline', $page_id ) ?: ( handandvision_is_hebrew() ? 'בואו נדבר' : 'Let’s Talk' );
$page_subtitle = get_field( 'page_subtitle', $page_id ) ?: ( handandvision_is_hebrew() ? 'נשמח לשמוע מכם - בין אם מדובר בשאלה על יצירה ספציפית, ייעוץ לאוסף, או שיתוף פעולה עתידי' : 'We’d love to hear from you - whether it’s a question about a specific piece, collection consultancy, or future collaboration' );

// Social Links
$social_links = array(
    'instagram' => 'https://www.instagram.com/handsvision_collective?igsh=MXBnaGpvcXZxeDhkbg==',
    'facebook'  => 'https://www.facebook.com/share/1KcTbs4JUR/',
);

?>

<main id="primary" class="site-main hv-contact-page">

    <!-- Hero Header -->
    <section class="hv-page-hero">
        <div class="hv-container hv-text-center">
            <?php handandvision_breadcrumbs(); ?>
            <?php if ( $page_overline ) : ?>
                <span class="hv-overline hv-reveal"><?php echo esc_html( $page_overline ); ?></span>
            <?php endif; ?>
            <h1 class="hv-headline-1 hv-reveal"><?php echo esc_html( $page_title_display ); ?></h1>
            <?php if ( $page_subtitle ) : ?>
                <p class="hv-subtitle hv-reveal" style="max-width: 600px; margin: var(--hv-space-6) auto 0;">
                    <?php echo esc_html( $page_subtitle ); ?>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="hv-section hv-section--white">
        <div class="hv-container">
            <div class="hv-contact-grid" style="display: block; max-width: 800px; margin: 0 auto;">

                <!-- Contact Form -->
                <div class="hv-contact-form-wrapper hv-reveal">
                    <?php
                    $form_title = get_field( 'form_title', $page_id ) ?: ( handandvision_is_hebrew() ? 'שלחו לנו הודעה' : 'Send us a message' );
                    if ( $form_title ) : ?>
                        <h2 class="hv-headline-3 hv-mb-6 hv-text-center"><?php echo esc_html( $form_title ); ?></h2>
                    <?php endif; ?>

                    <?php
                    $form_shortcode = get_field( 'contact_form_shortcode', $page_id );
                    if ( $form_shortcode ) {
                        echo do_shortcode( $form_shortcode );
                    } elseif ( shortcode_exists( 'contact-form-7' ) ) {
                        // Try to find CF7 form
                        $cf7 = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => 1 ));
                        if ( $cf7 ) {
                            echo do_shortcode( '[contact-form-7 id="' . absint( $cf7[0]->ID ) . '"]' );
                        }
                    } else {
                        // Default form fallback
                    ?>
                        <form id="hv-contact-form-ajax" class="hv-contact-form" method="post">
                            <?php wp_nonce_field( 'hv_contact_action', 'hv_contact_nonce' ); ?>
                            <input type="hidden" name="action" value="hv_contact_form">
                            <input type="hidden" name="page_id" value="<?php echo esc_attr( $page_id ); ?>">

                            <div class="hv-form-row">
                                <div class="hv-form-group">
                                    <label for="name" class="hv-form-label"><?php echo esc_html( handandvision_is_hebrew() ? 'שם מלא' : 'Full Name' ); ?> <span class="hv-required">*</span></label>
                                    <input type="text" id="name" name="name" class="hv-form-input" placeholder="<?php echo esc_attr( handandvision_is_hebrew() ? 'הכניסו את שמכם' : 'Enter your name' ); ?>" required>
                                </div>
                                <div class="hv-form-group">
                                    <label for="email" class="hv-form-label"><?php echo esc_html( handandvision_is_hebrew() ? 'אימייל' : 'Email' ); ?> <span class="hv-required">*</span></label>
                                    <input type="email" id="email" name="email" class="hv-form-input" placeholder="your@email.com" required>
                                </div>
                            </div>
                            <div class="hv-form-group">
                                <label for="phone" class="hv-form-label"><?php echo esc_html( handandvision_is_hebrew() ? 'טלפון (אופציונלי)' : 'Phone (Optional)' ); ?></label>
                                <input type="tel" id="phone" name="phone" class="hv-form-input" placeholder="050-000-0000">
                            </div>
                            <div class="hv-form-group">
                                <label for="subject" class="hv-form-label"><?php echo esc_html( handandvision_is_hebrew() ? 'נושא הפנייה' : 'Subject' ); ?></label>
                                <select id="subject" name="subject" class="hv-form-select">
                                    <option value=""><?php echo esc_html( handandvision_is_hebrew() ? 'בחרו נושא...' : 'Select a subject...' ); ?></option>
                                    <option value="inquiry"><?php echo esc_html( handandvision_is_hebrew() ? 'שאלה כללית' : 'General Inquiry' ); ?></option>
                                    <option value="artwork"><?php echo esc_html( handandvision_is_hebrew() ? 'מעוניין/ת ביצירה' : 'Interested in Artwork' ); ?></option>
                                    <option value="consult"><?php echo esc_html( handandvision_is_hebrew() ? 'ייעוץ אמנותי' : 'Artistic Consultancy' ); ?></option>
                                    <option value="exhibition"><?php echo esc_html( handandvision_is_hebrew() ? 'תערוכה / שיתוף פעולה' : 'Exhibition / Collaboration' ); ?></option>
                                    <option value="artist"><?php echo esc_html( handandvision_is_hebrew() ? 'הגשת פורטפוליו' : 'Portfolio Submission' ); ?></option>
                                    <option value="other"><?php echo esc_html( handandvision_is_hebrew() ? 'אחר' : 'Other' ); ?></option>
                                </select>
                            </div>
                            <div class="hv-form-group">
                                <label for="message" class="hv-form-label"><?php echo esc_html( handandvision_is_hebrew() ? 'ההודעה שלכם' : 'Your Message' ); ?> <span class="hv-required">*</span></label>
                                <textarea id="message" name="message" class="hv-form-textarea" rows="5" placeholder="<?php echo esc_attr( handandvision_is_hebrew() ? 'ספרו לנו במה נוכל לעזור...' : 'Tell us how we can help...' ); ?>" required></textarea>
                            </div>

                            <div class="hv-form-feedback hv-hidden" role="alert" aria-live="polite"></div>

                            <button type="submit" class="hv-btn hv-btn--primary hv-btn--lg hv-btn--submit">
                                <span class="hv-btn-text"><?php echo esc_html( handandvision_is_hebrew() ? 'שליחה' : 'Send' ); ?></span>
                                <span class="hv-btn-loader hv-hidden" aria-hidden="true">...</span>
                            </button>
                        </form>
                    <?php } ?>
                </div>

            </div>
        </div>
    </section>

    <!-- Social Section -->
    <section class="hv-contact-social-section">
        <div class="hv-container">
            <header class="hv-section-header hv-text-center">
                <span class="hv-overline hv-reveal"><?php echo handandvision_is_hebrew() ? 'הישארו מחוברים' : 'Stay Connected'; ?></span>
                <h2 class="hv-headline-3 hv-reveal"><?php echo handandvision_is_hebrew() ? 'עקבו אחרינו' : 'Follow Us'; ?></h2>
            </header>

            <div class="hv-social-group hv-reveal">
                <?php if ( $social_links['instagram'] ) : ?>
                    <a href="<?php echo esc_url( $social_links['instagram'] ); ?>" class="hv-social-link" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if ( $social_links['facebook'] ) : ?>
                    <a href="<?php echo esc_url( $social_links['facebook'] ); ?>" class="hv-social-link" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
