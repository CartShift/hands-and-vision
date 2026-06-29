<?php
/**
 * Temporary script: output all public site URLs for crawl testing.
 */
require '/var/www/html/wp-load.php';

$urls = [];

$archives = [ 'artist', 'service', 'gallery_item', 'product' ];
foreach ( $archives as $type ) {
    $link = get_post_type_archive_link( $type );
    if ( $link ) {
        $urls[] = $link;
    }
}

$types = array_merge( $archives, [ 'page' ] );
foreach ( $types as $type ) {
    $posts = get_posts(
        [
            'post_type'      => $type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]
    );
    foreach ( $posts as $id ) {
        $urls[] = get_permalink( $id );
    }
}

$urls = array_values( array_unique( array_filter( $urls ) ) );
sort( $urls );

foreach ( $urls as $url ) {
    echo $url . PHP_EOL;
}
