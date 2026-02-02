<?php
/**
 * Gallery Archive Template
 * Hand & Vision - Premium Gallery Experience
 *
 * @package HandAndVision
 * @version 2.0.0
 */

get_header();

// Get all artists for filtering
$artists = get_posts([
    'post_type'      => 'artist',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

// Build artist lookup array for efficient access
$artist_map = [];
foreach ($artists as $artist) {
    $artist_map[$artist->ID] = [
        'name' => $artist->post_title,
        'link' => get_permalink($artist->ID),
    ];
}

// Get gallery items
$gallery_items = new WP_Query([
    'post_type'      => 'gallery_item',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

// Calculate item counts per artist
$artist_counts = [];
$total_count = 0;
if ($gallery_items->have_posts()) {
    while ($gallery_items->have_posts()) {
        $gallery_items->the_post();
        $total_count++;
        $artist_id = get_field('gallery_artist');
        if ($artist_id) {
            $artist_counts[$artist_id] = ($artist_counts[$artist_id] ?? 0) + 1;
        }
    }
    $gallery_items->rewind_posts();
}

$is_hebrew = handandvision_is_hebrew();
?>

<main class="hv-gallery-page" id="gallery-content">

    <!-- Gallery Hero Section -->
    <section class="hv-gallery-hero" aria-labelledby="gallery-heading">
        <?php if ( function_exists( 'handandvision_breadcrumbs' ) ) handandvision_breadcrumbs(); ?>
        <div class="hv-gallery-hero__line" aria-hidden="true"></div>
        <h1 class="hv-gallery-hero__title" id="gallery-heading">
            <?php echo esc_html( $is_hebrew ? 'גלריה' : 'Gallery' ); ?>
        </h1>
        <p class="hv-gallery-hero__sub">
            <?php echo esc_html( $is_hebrew
                ? 'עבודות נבחרות מהקולקטיב שלנו'
                : 'Selected works from our collective' ); ?>
        </p>
    </section>

    <!-- Gallery Controls -->
    <section class="hv-gallery-controls" aria-label="Gallery controls">

        <!-- Filter Buttons -->
        <div class="hv-gallery-filters" role="tablist" aria-label="Filter by artist">
            <button
                class="hv-gallery-filters__btn is-active"
                data-artist-id="0"
                role="tab"
                aria-selected="true"
                aria-controls="gallery-grid"
                tabindex="0"
            >
                <span class="hv-gallery-filters__label">
                    <?php echo $is_hebrew ? 'הכל' : 'All'; ?>
                </span>
                <span class="hv-gallery-filters__count"><?php echo $total_count; ?></span>
            </button>

            <?php foreach ($artists as $artist) :
                $count = $artist_counts[$artist->ID] ?? 0;
                if ($count === 0) continue;
            ?>
                <button
                    class="hv-gallery-filters__btn"
                    data-artist-id="<?php echo esc_attr($artist->ID); ?>"
                    data-artist-name="<?php echo esc_attr($artist->post_title); ?>"
                    role="tab"
                    aria-selected="false"
                    aria-controls="gallery-grid"
                    tabindex="-1"
                >
                    <span class="hv-gallery-filters__label">
                        <?php echo esc_html($artist->post_title); ?>
                    </span>
                    <span class="hv-gallery-filters__count"><?php echo esc_html( (string) $count ); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Loading Indicator -->
        <div class="hv-gallery__loading" role="status" aria-live="polite" aria-label="Loading gallery items">
            <div class="hv-gallery__spinner" aria-hidden="true"></div>
            <span class="hv-gallery__loading-text">
                <?php echo esc_html( $is_hebrew ? 'טוען...' : 'Loading...' ); ?>
            </span>
        </div>

        <!-- Results Counter -->
        <div class="hv-gallery__meta">
            <span class="hv-gallery__counter" aria-live="polite">
                <?php printf(
                    $is_hebrew ? '%d מתוך %d' : '%d / %d',
                    $total_count,
                    $total_count
                ); ?>
            </span>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="hv-gallery-bento" aria-label="Gallery items">
        <div
            class="hv-gallery-bento__grid"
            id="gallery-grid"
            role="region"
            aria-live="polite"
            aria-label="Gallery grid"
        >
            <?php
            if ($gallery_items->have_posts()) :
                $index = 0;
                while ($gallery_items->have_posts()) : $gallery_items->the_post();
                    $index++;

                    $image = get_field('gallery_image');
                    if ( ! $image || empty( $image['url'] ) ) {
                        $thumb_id = get_post_thumbnail_id( get_the_ID() );
                        if ( $thumb_id ) {
                            $image = [
                                'ID'  => $thumb_id,
                                'url' => wp_get_attachment_image_url( $thumb_id, 'full' ),
                            ];
                        }
                    }
                    $caption = get_field('gallery_caption');
                    $artist_id = get_field('gallery_artist');
                    $year = get_field('gallery_year');
                    $project = get_field('gallery_project');

                    // Determine artist info
                    $artist_name = $artist_id && isset($artist_map[$artist_id])
                        ? $artist_map[$artist_id]['name']
                        : '';

                    // Calculate bento class
                    $bento_class = '';
                    if ($index === 1) {
                        $bento_class = 'hv-gallery-bento__item--wide';
                    } elseif ($index % 6 === 4) {
                        $bento_class = 'hv-gallery-bento__item--tall';
                    }

                    // Responsive srcset
                    $srcset = '';
                    $sizes = '';
                    if ($image) {
                        $srcset = wp_get_attachment_image_srcset($image['ID'] ?? 0);
                        $sizes = '(max-width: 600px) 100vw, (max-width: 1024px) 50vw, 25vw';
                    }
            ?>
                <article
                    class="hv-gallery-bento__item <?php echo esc_attr($bento_class); ?>"
                    data-artist-id="<?php echo esc_attr($artist_id ?? 0); ?>"
                    data-artist-name="<?php echo esc_attr($artist_name); ?>"
                    data-index="<?php echo esc_attr( (string) $index ); ?>"
                    data-full-src="<?php echo $image ? esc_url($image['url']) : ''; ?>"
                    style="--stagger-delay: <?php echo ($index * 0.05); ?>s"
                >
                    <a
                        href="<?php echo $image ? esc_url($image['url']) : '#'; ?>"
                        class="hv-gallery-bento__link"
                        data-gallery="gallery"
                        data-caption="<?php echo esc_attr($caption ?: get_the_title()); ?>"
                        aria-label="<?php printf(
                            $is_hebrew ? 'צפה ב%s מאת %s' : 'View %s by %s',
                            esc_attr($caption ?: get_the_title()),
                            esc_attr($artist_name)
                        ); ?>"
                    >
                        <div class="hv-gallery-bento__media">
                            <?php if ($image) : ?>
                                <img
                                    class="hv-gallery-bento__img"
                                    src="<?php echo esc_url($image['url']); ?>"
                                    data-full-src="<?php echo esc_url($image['url']); ?>"
                                    alt="<?php echo esc_attr($caption ?: get_the_title()); ?>"
                                    loading="lazy"
                                    decoding="async"
                                    <?php if ($srcset) : ?>srcset="<?php echo esc_attr($srcset); ?>"<?php endif; ?>
                                    <?php if ($sizes) : ?>sizes="<?php echo esc_attr($sizes); ?>"<?php endif; ?>
                                >
                            <?php else : ?>
                                <div class="hv-gallery-bento__placeholder">
                                    <span class="hv-gallery-bento__placeholder-icon" aria-hidden="true">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Hover Overlay -->
                            <div class="hv-gallery-bento__overlay" aria-hidden="true">
                                <h3 class="hv-gallery-bento__title">
                                    <?php echo esc_html($caption ?: get_the_title()); ?>
                                </h3>
                                <?php if ($artist_name) : ?>
                                    <span class="hv-gallery-bento__meta">
                                        <?php echo esc_html($artist_name); ?>
                                        <?php if ($year) : ?>
                                            <span class="hv-gallery-bento__year">, <?php echo esc_html($year); ?></span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Zoom Icon -->
                            <span class="hv-gallery-bento__zoom" aria-hidden="true">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    <line x1="11" y1="8" x2="11" y2="14"></line>
                                    <line x1="8" y1="11" x2="14" y2="11"></line>
                                </svg>
                            </span>
                        </div>
                    </a>
                </article>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <!-- Empty State -->
        <div class="hv-gallery__empty" role="status" aria-live="polite" hidden>
            <div class="hv-gallery__empty-icon" aria-hidden="true">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
            <h3 class="hv-gallery__empty-title">
                <?php echo esc_html( $is_hebrew ? 'אין תוצאות' : 'No results found' ); ?>
            </h3>
            <p class="hv-gallery__empty-text">
                <?php echo esc_html( $is_hebrew
                    ? 'נסה לבחור קטגוריה אחרת או חפש שוב.'
                    : 'Try selecting a different category or search again.' ); ?>
            </p>
            <button class="hv-gallery__empty-btn" onclick="window.location.reload()">
                <?php echo esc_html( $is_hebrew ? 'נקה סינון' : 'Clear filters' ); ?>
            </button>
        </div>
    </section>

    <!-- Gallery Footer -->
    <section class="hv-gallery-footer">
        <div class="hv-gallery-footer__content">
            <p class="hv-gallery-footer__text">
                <?php echo esc_html( $is_hebrew
                    ? sprintf( 'מציג %d יצירות מהקולקטיב', $total_count )
                    : sprintf( 'Displaying %d works from the collective', $total_count ) ); ?>
            </p>
        </div>
    </section>

</main>

<?php
// Enqueue gallery-specific assets
wp_enqueue_script(
    'hv-gallery',
    get_template_directory_uri() . '/assets/js/hv-gallery.js',
    [],
    '2.0.0',
    true
);

// Conditionally enqueue GLightbox if available
if (!wp_script_is('glightbox', 'registered')) {
    wp_enqueue_style(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css',
        [],
        '3.2.0'
    );
    wp_enqueue_script(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js',
        [],
        '3.2.0',
        true
    );
}

get_footer();
