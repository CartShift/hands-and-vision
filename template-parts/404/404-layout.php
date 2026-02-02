<?php
/**
 * Template for 404 (bilingual)
 *
 * @package HandAndVision
 */

$astra_404_subtitle_tag = true === astra_check_is_structural_setup() ? 'h3' : 'div';
$title_404 = function_exists( 'handandvision_is_hebrew' ) && handandvision_is_hebrew()
	? 'נראה שהדף לא קיים'
	: "This page doesn't seem to exist.";
$subtitle_404 = function_exists( 'handandvision_is_hebrew' ) && handandvision_is_hebrew()
	? 'ייתכן שהקישור שגוי. נסו לחפש.'
	: 'It looks like the link pointing here was faulty. Maybe try searching?';
?>
<div <?php echo wp_kses_post( astra_attr( '404_page', array( 'class' => 'ast-404-layout-1' ) ) ); ?>>

	<header class="page-header"><h1 class="page-title"><?php echo esc_html( $title_404 ); ?></h1></header>

	<div class="page-content">
		<<?php echo esc_attr( $astra_404_subtitle_tag ); ?> class="page-sub-title"><?php echo esc_html( $subtitle_404 ); ?></<?php echo esc_attr( $astra_404_subtitle_tag ); ?>>
		<div class="ast-404-search"><?php the_widget( 'WP_Widget_Search' ); ?></div>
	</div>
</div>
