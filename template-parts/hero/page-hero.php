<?php
/**
 * Unified page hero – overline, title, optional subtitle.
 * Used on Services, Artists, Shop, Contact, Gallery.
 *
 * @package HandAndVision
 * @param array $args overline, title (string or array of [text, accent]), subtitle
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$args = wp_parse_args( $args ?? array(), array(
	'overline' => '',
	'title'    => '',
	'subtitle' => '',
) );
$title = $args['title'];
$title_lines = is_array( $title ) ? $title : array( array( 'text' => $title, 'accent' => false ) );
?>
<section class="hv-hero" aria-labelledby="hv-hero-title">
	<div class="hv-hero__bg">
		<div class="hv-hero__gradient"></div>
	</div>
	<div class="hv-hero__content">
		<div class="hv-container">
			<?php if ( function_exists( 'handandvision_breadcrumbs' ) ) { handandvision_breadcrumbs(); } ?>
			<div class="hv-hero__inner">
				<?php if ( $args['overline'] ) : ?>
				<div class="hv-hero__overline">
					<span class="hv-hero__line"></span>
					<span class="hv-hero__text"><?php echo esc_html( $args['overline'] ); ?></span>
					<span class="hv-hero__line"></span>
				</div>
				<?php endif; ?>
				<h1 class="hv-hero__title" id="hv-hero-title">
					<?php foreach ( $title_lines as $line ) :
						$t = is_array( $line ) ? ( $line['text'] ?? '' ) : $line;
						$accent = is_array( $line ) && ! empty( $line['accent'] );
						$class = 'hv-hero__title-line' . ( $accent ? ' hv-hero__title-line--accent' : '' );
					?><span class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $t ); ?></span><?php endforeach; ?>
				</h1>
				<?php if ( $args['subtitle'] ) : ?>
				<p class="hv-hero__subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
