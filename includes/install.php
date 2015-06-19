<?php
function woo_cd_deactivate_ce() {
	$plugins = array(
		'woocommerce-exporter/exporter.php',
		'woocommerce-store-exporter/exporter.php'
	);
	deactivate_plugins( $plugins, true );
}

function woo_cd_install() {
	woo_cd_create_options();
}

// Trigger the creation of Admin options for this Plugin
function woo_cd_create_options() {

	$prefix = 'woo_ce';
	if( !get_option( $prefix . '_export_filename' ) )
		add_option( $prefix . '_export_filename', 'export_%dataset%-%date%-%time%.csv' );
	if( !get_option( $prefix . '_delete_file' ) )
		add_option( $prefix . '_delete_file', 1 );
	if( !get_option( $prefix . '_delimiter' ) )
		add_option( $prefix . '_delimiter', ',' );
	if( !get_option( $prefix . '_category_separator' ) )
		add_option( $prefix . '_category_separator', '|' );
	if( !get_option( $prefix . '_bom' ) )
		add_option( $prefix . '_bom', 1 );
	if( !get_option( $prefix . '_encoding' ) )
		add_option( $prefix . '_encoding', get_option( 'blog_charset', 'UTF-8' ) );
	if( !get_option( $prefix . '_escape_formatting' ) )
		add_option( $prefix . '_escape_formatting', 'all' );
	if( !get_option( $prefix . '_date_format' ) )
		add_option( $prefix . '_date_format', 'd/m/Y' );

	// Generate a unique CRON secret key for each new installation
	if( !get_option( $prefix . '_secret_key' ) )
		add_option( $prefix . '_secret_key', wp_generate_password( 64, false ) );

}

function woo_cd_create_secure_archives_dir() {
	$upload_dir =  wp_upload_dir();
	$files = array(
		array(
			'base' 		=> $upload_dir['basedir'] . '/sed-exports',
			'file' 		=> '.htaccess',
			'content' 	=> 'deny from all'
		)
	);

	foreach( $files as $file ) {
		if ( wp_mkdir_p( $file['base'] ) && !file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
			if( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
				fwrite( $file_handle, $file['content'] );
				fclose( $file_handle );
			}
		}
	}

}

function woo_cd_detect_ce() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if( is_plugin_active( 'woocommerce-exporter/exporter.php' ) || is_plugin_active( 'woocommerce-store-exporter/exporter.php' ) ) {
		woo_cd_deactivate_ce();
	}
}

function woo_cd_uninstall() {
	wp_clear_scheduled_hook( 'woo_ce_auto_export_schedule' );
}
?>