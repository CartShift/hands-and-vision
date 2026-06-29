<?php
/**
 * Import a .wpress backup from wp-content/ai1wm-backups/ (no browser upload).
 *
 * Usage:
 *   wp eval-file docker/wp-import-restore.php --yes [archive-filename.wpress]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( ! class_exists( 'Ai1wm_Import_Controller' ) ) {
	WP_CLI::error( 'All-in-One WP Migration is not active.' );
}

$archive = isset( $args[0] ) ? $args[0] : '';
if ( ! $archive ) {
	$backups_dir = WP_CONTENT_DIR . '/ai1wm-backups';
	$files       = glob( $backups_dir . '/*.wpress' );
	if ( empty( $files ) ) {
		WP_CLI::error( 'No .wpress file found in wp-content/ai1wm-backups/.' );
	}
	if ( count( $files ) > 1 ) {
		WP_CLI::error( 'Multiple .wpress files found. Pass the filename as an argument.' );
	}
	$archive = basename( $files[0] );
}

$backup_path = WP_CONTENT_DIR . '/ai1wm-backups/' . $archive;
if ( ! is_readable( $backup_path ) ) {
	WP_CLI::error( 'Backup not readable: ' . $backup_path );
}

$params = array(
	'archive'              => $archive,
	'ai1wm_manual_restore' => 1,
	'ai1wm_confirmed'      => 1,
	'secret_key'           => get_option( 'ai1wm_secret_key' ),
	'storage'              => wp_generate_password( 12, false ),
	'priority'             => 10,
);

WP_CLI::log( 'Importing: ' . $archive );
WP_CLI::log( 'Size: ' . size_format( filesize( $backup_path ) ) );
WP_CLI::log( 'This may take several minutes...' );

Ai1wm_Import_Controller::import( $params );

$local_url = 'http://127.0.0.1:8888';
update_option( 'siteurl', $local_url );
update_option( 'home', $local_url );
WP_CLI::runcommand(
	"search-replace 'http://localhost:8888' '{$local_url}' --all-tables",
	array( 'return' => 'all', 'parse' => 'json' )
);

$disable_plugins = array(
	'jetpack',
	'jetpack-boost',
	'optinmonster',
	'bluehost-wordpress-plugin',
	'wppusher',
	'google-analytics-for-wordpress',
);
WP_CLI::runcommand(
	'plugin deactivate ' . implode( ' ', $disable_plugins ),
	array( 'exit_error' => false )
);

WP_CLI::success( 'Import finished. Open ' . $local_url );
