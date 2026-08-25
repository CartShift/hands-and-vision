<?php
/**
 * Admin metabox for service artist artwork sections.
 *
 * @package HandAndVision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the service artist sections metabox.
 */
function handandvision_add_service_artist_sections_metabox() {
	add_meta_box(
		'hv_service_artist_sections',
		'עבודות לפי אמנים בעמוד השירות',
		'handandvision_render_service_artist_sections_metabox',
		'service',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_service', 'handandvision_add_service_artist_sections_metabox' );

/**
 * Enqueue admin assets for the service artist sections editor.
 *
 * @param string $hook_suffix Current admin hook.
 */
function handandvision_enqueue_service_artist_sections_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'service' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'hv-service-artist-sections-admin',
		get_stylesheet_directory_uri() . '/assets/css/hv-service-artist-sections-admin.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);
	wp_enqueue_script(
		'hv-service-artist-sections-admin',
		get_stylesheet_directory_uri() . '/assets/js/hv-service-artist-sections-admin.js',
		array( 'jquery' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'handandvision_enqueue_service_artist_sections_admin_assets' );

/**
 * Render the metabox.
 *
 * @param WP_Post $post Current service post.
 */
function handandvision_render_service_artist_sections_metabox( $post ) {
	wp_nonce_field( 'hv_save_service_artist_sections', 'hv_service_artist_sections_nonce' );

	$sections = handandvision_get_service_artist_sections_meta( $post->ID );
	$artists  = get_posts(
		array(
			'post_type'              => 'artist',
			'posts_per_page'         => -1,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	if ( empty( $sections ) ) {
		$sections = handandvision_get_service_artist_sections_editor_defaults( $post->ID );
	}

	?>
	<div class="hv-service-admin" dir="rtl">
		<p class="hv-service-admin__intro">
			כאן מעלים את התמונות שמופיעות בעמוד השירות לפי הסקיצה: בוחרים אמן/ית, מוסיפים עד 6 יצירות, ואפשר להוסיף עוד סקשנים לאמנים נוספים.
		</p>

		<div class="hv-service-admin__sections" data-hv-service-artist-sections>
			<?php
			foreach ( $sections as $index => $section ) {
				handandvision_render_service_artist_section_row( (int) $index, $section, $artists );
			}
			?>
		</div>

		<button type="button" class="button button-primary hv-service-admin__add" data-hv-add-artist-section>
			הוסף סקשן אמן
		</button>

		<script type="text/template" id="hv-service-artist-section-template">
			<?php handandvision_render_service_artist_section_row( '__index__', array(), $artists ); ?>
		</script>
	</div>
	<?php
}

/**
 * Return editor defaults that match the front-end fallback for Digital Art.
 *
 * @param int $service_id Service post ID.
 * @return array
 */
function handandvision_get_service_artist_sections_editor_defaults( $service_id ) {
	if ( ! function_exists( 'handandvision_is_digital_art_service' ) || ! handandvision_is_digital_art_service( $service_id ) ) {
		return array( array() );
	}

	$defaults = array();
	$artists  = array(
		array( 'daniel philosoph', 'daniel philosof', 'דניאל פילוסוף' ),
		array( 'noa afriat', 'noa efrati', 'נועה אפריאט', 'נועה אפרתי' ),
	);

	foreach ( $artists as $terms ) {
		$artist_id = function_exists( 'handandvision_find_artist_id_by_terms' ) ? handandvision_find_artist_id_by_terms( $terms ) : 0;

		$defaults[] = array(
			'artist_id'   => $artist_id,
			'artwork_ids' => array(),
			'deeper_text' => '',
		);
	}

	return $defaults;
}

/**
 * Render one artist section row.
 *
 * @param int|string $index   Row index.
 * @param array      $section Saved section.
 * @param array      $artists Artist posts.
 */
function handandvision_render_service_artist_section_row( $index, $section, $artists ) {
	$artist_id   = isset( $section['artist_id'] ) ? (int) $section['artist_id'] : 0;
	$artwork_ids = isset( $section['artwork_ids'] ) && is_array( $section['artwork_ids'] ) ? $section['artwork_ids'] : array();
	$deeper_text = isset( $section['deeper_text'] ) ? (string) $section['deeper_text'] : '';
	?>
	<div class="hv-service-admin-section" data-hv-artist-section>
		<div class="hv-service-admin-section__header">
			<h3>סקשן אמן</h3>
			<div class="hv-service-admin-section__actions">
				<button type="button" class="button hv-service-admin-section__move" data-hv-move-artist-section="up">
					העלה למעלה
				</button>
				<button type="button" class="button hv-service-admin-section__move" data-hv-move-artist-section="down">
					הורד למטה
				</button>
				<button type="button" class="button-link-delete hv-service-admin-section__remove" data-hv-remove-artist-section>
					הסר סקשן
				</button>
			</div>
		</div>

		<label class="hv-service-admin-field">
			<span>בחרו אמן/ית</span>
			<select name="hv_service_artist_sections[<?php echo esc_attr( $index ); ?>][artist_id]">
				<option value="">בחרו אמן/ית</option>
				<?php foreach ( $artists as $artist ) : ?>
					<option value="<?php echo esc_attr( $artist->ID ); ?>" <?php selected( $artist_id, $artist->ID ); ?>>
						<?php echo esc_html( handandvision_strip_dashes_from_copy( get_the_title( $artist->ID ) ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>

		<div class="hv-service-admin-artworks" aria-label="שש תמונות לעבודות של האמן">
			<?php for ( $slot = 0; $slot < 6; $slot++ ) : ?>
				<?php
				$attachment_id = isset( $artwork_ids[ $slot ] ) ? (int) $artwork_ids[ $slot ] : 0;
				$image_url     = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : '';
				?>
				<div class="hv-service-admin-image-slot <?php echo $image_url ? 'is-filled' : ''; ?>" data-hv-image-slot>
					<input
						type="hidden"
						name="hv_service_artist_sections[<?php echo esc_attr( $index ); ?>][artwork_ids][<?php echo esc_attr( $slot ); ?>]"
						value="<?php echo esc_attr( $attachment_id ); ?>"
						data-hv-image-id
					>
					<div class="hv-service-admin-image-slot__preview" data-hv-image-preview>
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="">
						<?php else : ?>
							<span>תמונה <?php echo esc_html( sprintf( '%02d', $slot + 1 ) ); ?></span>
						<?php endif; ?>
					</div>
					<div class="hv-service-admin-image-slot__actions">
						<button type="button" class="button" data-hv-select-image>בחרי תמונה</button>
						<button type="button" class="button-link-delete" data-hv-remove-image <?php disabled( ! $image_url ); ?>>הסירי</button>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<label class="hv-service-admin-field">
			<span>טקסט Go deeper, אופציונלי</span>
			<input
				type="text"
				name="hv_service_artist_sections[<?php echo esc_attr( $index ); ?>][deeper_text]"
				value="<?php echo esc_attr( $deeper_text ); ?>"
				placeholder="Go deeper"
			>
		</label>
	</div>
	<?php
}

/**
 * Save the service artist sections.
 *
 * @param int $post_id Post ID.
 */
function handandvision_save_service_artist_sections( $post_id ) {
	if ( ! isset( $_POST['hv_service_artist_sections_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hv_service_artist_sections_nonce'] ) ), 'hv_save_service_artist_sections' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw_sections = isset( $_POST['hv_service_artist_sections'] ) && is_array( $_POST['hv_service_artist_sections'] )
		? wp_unslash( $_POST['hv_service_artist_sections'] )
		: array();
	$sections     = array();

	foreach ( $raw_sections as $raw_section ) {
		if ( ! is_array( $raw_section ) ) {
			continue;
		}

		$artist_id = isset( $raw_section['artist_id'] ) ? (int) $raw_section['artist_id'] : 0;
		$ids       = isset( $raw_section['artwork_ids'] ) && is_array( $raw_section['artwork_ids'] )
			? array_map( 'absint', $raw_section['artwork_ids'] )
			: array();
		$ids       = array_slice( array_filter( $ids ), 0, 6 );

		if ( ! $artist_id && empty( $ids ) ) {
			continue;
		}

		$sections[] = array(
			'artist_id'    => $artist_id,
			'artwork_ids'  => $ids,
			'deeper_text'  => isset( $raw_section['deeper_text'] ) ? sanitize_text_field( $raw_section['deeper_text'] ) : '',
		);
	}

	if ( empty( $sections ) ) {
		delete_post_meta( $post_id, '_hv_service_artist_sections' );
		return;
	}

	update_post_meta( $post_id, '_hv_service_artist_sections', $sections );
}
add_action( 'save_post_service', 'handandvision_save_service_artist_sections' );
