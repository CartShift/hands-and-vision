<?php
/**
 * Unified page hero – overline, title, optional subtitle & stats.
 * Used on Services, Artists, Shop, Contact, Gallery.
 *
 * @package HandAndVision
 * @param array $args overline, title (string or array of [text, accent]), subtitle, stats [[number, label]], scroll_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$args = wp_parse_args( $args ?? array(), array(
	'overline'   => '',
	'title'      => '',
	'subtitle'   => '',
	'stats'      => null,
	'scroll_text'=> '',
) );
$title = $args['title'];
$title_lines = is_array( $title ) ? $title : array( array( 'text' => $title, 'accent' => false ) );
?>
<section class="hv-hero" aria-labelledby="hv-hero-title">
	<div class="hv-hero__bg">
		<div class="hv-hero__gradient"></div>
	</div>
	<div class="hv-hero__content">
		<?php if ( function_exists( 'handandvision_breadcrumbs' ) ) { handandvision_breadcrumbs(); } ?>
		<div class="hv-container">
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
				<?php if ( ! empty( $args['stats'] ) ) : ?>
				<div class="hv-hero__stats">
					<?php
					$n = 0;
					foreach ( $args['stats'] as $stat ) :
						$num = $stat['number'] ?? $stat[0] ?? '';
						$lab = $stat['label'] ?? $stat[1] ?? '';
						if ( $n ) : ?><div class="hv-hero__stat-divider"></div><?php endif;
						?><div class="hv-hero__stat">
							<span class="hv-hero__stat-number"><?php echo esc_html( $num ); ?></span>
							<span class="hv-hero__stat-label"><?php echo esc_html( $lab ); ?></span>
						</div>
					<?php $n++; endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php if ( $args['scroll_text'] ) : ?>
	<div class="hv-hero__scroll">
		<span><?php echo esc_html( $args['scroll_text'] ); ?></span>
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
	</div>
	<?php endif; ?>
</section>
