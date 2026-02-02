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



?>

<main id="primary" class="site-main hv-contact-page">

<?php
get_template_part( 'hero/page-hero', null, array(
	'overline'   => $page_overline,
	'title'      => $page_title_display,
	'subtitle'   => $page_subtitle,
	'stats'      => null,
	'scroll_text'=> handandvision_is_hebrew() ? 'גלול ליצירת קשר' : 'Scroll to connect',
) );
?>

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



</main>

<?php get_footer(); ?>
