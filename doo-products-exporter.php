<?php
/*
Plugin Name: WooCommerce - Doo Products Exporter
Plugin URI: http://www.diegobenna.it
Description: Export in csv format all products of your Wordpress store
Version: 1.0
Author: Diego Benna
Author URI: http://www.diegobenna.it
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'WOO_CD_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'WOO_CD_RELPATH', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'WOO_CD_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_CD_PREFIX', 'woo_ce' );

// Turn this on to enable additional debugging options at export time
define( 'WOO_CD_DEBUG', false );

// Disable basic Store Exporter if it is activated
include_once( WOO_CD_PATH . 'common.php' );
if( defined( 'WOO_CE_PREFIX' ) == true ) {
	// Detect Store Exporter and other platform versions
	include_once( WOO_CD_PATH . 'includes/install.php' );
	woo_cd_detect_ce();
} else {
	include_once( WOO_CD_PATH . 'includes/functions.php' );
}

function woo_cd_i18n() {

	load_plugin_textdomain( 'woo_ce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}
add_action( 'init', 'woo_cd_i18n' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	// Register our install script for first time install
	include_once( WOO_CD_PATH . 'includes/install.php' );
	register_activation_hook( __FILE__, 'woo_cd_install' );
	register_deactivation_hook( __FILE__, 'woo_cd_uninstall' );

	// Initial scripts and export process
	function woo_cd_admin_init() {

		global $export, $wp_roles;

		$troubleshooting_url = '';

		// Time to tell the store owner if we were unable to disable the basic Store Exporter
		if( defined( 'WOO_CE_PREFIX' ) ) {
			// Display notice if we were unable to de-activate basic Store Exporter
			if( ( is_plugin_active( 'woocommerce-exporter/exporter.php' ) || is_plugin_active( 'woocommerce-store-exporter/exporter.php' ) ) && current_user_can( 'activate_plugins' ) ) {
				$plugins_url = esc_url( add_query_arg( '', '', 'plugins.php' ) );
				$message = sprintf( __( 'We did our best to de-activate Store Exporter for you but may have failed, please check that the basic Store Exporter is de-activated from the <a href="%s">Plugins screen</a>.', 'woo_ce' ), $plugins_url );
				woo_cd_admin_notice( $message, 'error', array( 'plugins.php', 'update-core.php' ) );
			}
		}

		// Detect if another e-Commerce platform is activated
		if( !woo_is_woo_activated() && ( woo_is_jigo_activated() || woo_is_wpsc_activated() ) ) {
			$message = sprintf( __( 'We have detected another e-Commerce Plugin than WooCommerce activated, please check that you are using Doo Product Exporter for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
			woo_cd_admin_notice( $message, 'error', 'plugins.php' );
		} else if( !woo_is_woo_activated() ) {
			$message = sprintf( __( 'We have been unable to detect the WooCommerce Plugin activated on this WordPress site, please check that you are using Doo Product Exporter for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
			woo_cd_admin_notice( $message, 'error', 'plugins.php' );
		}

		// Detect if WooCommerce Subscriptions Exporter is activated
		if( function_exists( 'wc_subs_exporter_admin_init' ) ) {
			$message = sprintf( __( 'We have detected a WooCommerce Plugin that is activated and known to conflict with Doo Product Exporter, please de-activate WooCommerce Subscriptions Exporter to resolve export issues. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
			woo_cd_admin_notice( $message, 'error', array( 'plugins.php', 'admin.php' ) );
		}

		// Displays a HTML notice where we have detected the site has moved or this is staging site
		if(
			woo_ce_get_option( 'duplicate_site_prompt', 0 )
			&& ( woo_ce_get_option( 'override_duplicate_site_prompt', 0 ) == false )
			&& ( woo_ce_get_option( 'dismiss_duplicate_site_prompt', 0 ) == false )
		) {
			$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss_duplicate_site_prompt' ) );
			$override_url = esc_url( add_query_arg( 'action', 'override_duplicate_site_prompt' ) );
			$message = __( 'It looks like this site has moved or is a duplicate site. Doo Product Exporter has disabled scheduled exports on this site to prevent duplicate scheduled exports being generated from a staging or test environment.', 'woo_ce' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Continue running scheduled exports', 'woo_ce' ) . '</a>' . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
			woo_cd_admin_notice( $message, 'error' );
		}

		add_action( 'after_plugin_row_' . WOO_CD_RELPATH, 'woo_ce_admin_plugin_row' );

		// Check the User has the view_woocommerce_reports capability
		if( current_user_can( 'view_woocommerce_reports' ) ) {
			// Load Dashboard widget for Scheduled Exports
			add_action( 'wp_dashboard_setup', 'woo_ce_dashboard_setup' );
			// Add Export Status to Orders screen
			add_filter( 'manage_edit-shop_order_columns', 'doo_exporter_admin_order_column_headers', 20 );
			add_action( 'manage_shop_order_posts_custom_column', 'doo_export_admin_order_column_content' );
			// Load Download buttons for Orders screen
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'woo_ce_styles', plugins_url( '/templates/admin/export.css', WOO_CD_RELPATH ) );
			// Add our export to CSV, XML, XLS, XLSX action buttons
			add_filter( 'woocommerce_admin_order_actions', 'woo_ce_admin_order_actions', 10, 2 );
			add_action( 'wp_ajax_woo_ce_export_order', 'woo_ce_ajax_export_order' );
			// Add Download as... options to Orders Bulk
			add_action( 'admin_footer', 'woo_ce_admin_order_bulk_actions' );
			add_action( 'load-edit.php', 'woo_ce_admin_order_process_bulk_action' );
			// Add Download as... options to Edit Order Actions
			add_action( 'woocommerce_order_actions', 'woo_ce_admin_order_single_actions' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_csv', 'woo_ce_admin_order_single_export_csv' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_xml', 'woo_ce_admin_order_single_export_xml' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_xls', 'woo_ce_admin_order_single_export_xls' );
			add_action( 'woocommerce_order_action_woo_ce_export_order_xlsx', 'woo_ce_admin_order_single_export_xlsx' );
		}

		// Check that we are on the Store Exporter screen
		$page = ( isset($_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : false );
		if( $page != strtolower( WOO_CD_PREFIX ) )
			return;

		// Process any pre-export notice confirmations
		$action = woo_get_action();
		switch( $action ) {

			// Delete all Archives
			case 'nuke_archives':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_archives' ) ) {
					if( woo_ce_nuke_archive_files() ) {
						$message = __( 'All existing Archives and their export files have been deleted from your WordPress site.', 'woo_ce' );
						woo_cd_admin_notice( $message );
					} else {
						$message = __( 'There were no existing Archives to be deleted from your WordPress site.', 'woo_ce' );
						woo_cd_admin_notice( $message, 'error' );
					}
				}
				break;

			// Prompt on Export screen when insufficient memory (less than 64M is allocated)
			case 'dismiss_memory_prompt':
				woo_ce_update_option( 'dismiss_memory_prompt', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			// Prompt on Export screen when PHP configuration option max_execution_time cannot be increased
			case 'dismiss_execution_time_prompt':
				woo_ce_update_option( 'dismiss_execution_time_prompt', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			// Prompt on Export screen when insufficient memory (less than 64M is allocated)
			case 'dismiss_php_legacy':
				woo_ce_update_option( 'dismiss_php_legacy', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			case 'dismiss_subscription_prompt':
				woo_ce_update_option( 'dismiss_subscription_prompt', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			case 'dismiss_duplicate_site_prompt':
				woo_ce_update_option( 'dismiss_duplicate_site_prompt', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			case 'dismiss_secure_archives_prompt':
				woo_ce_update_option( 'dismiss_secure_archives_prompt', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			case 'override_duplicate_site_prompt':
				woo_ce_update_option( 'override_duplicate_site_prompt', 1 );
				// Enable scheduled exports again
				woo_ce_update_option( 'enable_auto', 1 );
				$url = esc_url( add_query_arg( 'action', null ) );
				wp_redirect( $url );
				exit();
				break;

			// Move legacy archives exports to the sed-exports directory within Uploads
			case 'relocate_archived_exports':

				// Create the sed-exports directory if it hasn't been
				woo_cd_create_secure_archives_dir();

				$updated = 0;
				if( $files = woo_ce_get_archive_files() ) {
					foreach( $files as $key => $file ) {
						$filepath = get_attached_file( $file->ID );
						// Check for archived exports that have not been moved to sed-exports
						if( strpos( $filepath, 'sed-exports' ) == false ) {
							// Move the export

							// Update the Post meta key _wp_attached_file
							$attached_file = get_post_meta( $file->ID, '_wp_attached_file', true );
							if( !empty( $attached_file ) )
								$attached_file = trailingslashit( 'sed-exports' ) . basename( $attached_file );
							$updated++;
						}
					}
				}

				// Show the response
				$message = sprintf( __( 'That\'s sorted, we\'ve relocated %d export files to the newly created <code>sed-exports</code> folder within the WordPress Uploads directory. Happy exporting!', 'woo_ce' ), $updated );
				woo_cd_admin_notice_html( $message );
				break;

			// Save skip overview preference
			case 'skip_overview':
				$skip_overview = false;
				if( isset( $_POST['skip_overview'] ) )
					$skip_overview = 1;
				woo_ce_update_option( 'skip_overview', $skip_overview );

				if( $skip_overview == 1 ) {
					$url = esc_url( add_query_arg( 'tab', 'export' ) );
					wp_redirect( $url );
					exit();
				}
				break;

			// This is where the magic happens
			case 'export':

				// Make sure we play nice with other WooCommerce and WordPress exporters
				if( !isset( $_POST['woo_ce_export'] ) )
					return;

				check_admin_referer( 'manual_export', 'woo_ce_export' );

				// Set up the basic export options
				$export = new stdClass();
				$export->cron = 0;
				$export->scheduled_export = 0;
				$export->start_time = time();
				$export->idle_memory_start = woo_ce_current_memory_usage();
				$export->delete_file = woo_ce_get_option( 'delete_file', 1 );
				$export->encoding = woo_ce_get_option( 'encoding', get_option( 'blog_charset', 'UTF-8' ) );
				// Reset the Encoding if corrupted
				if( $export->encoding == '' || $export->encoding == false || $export->encoding == 'System default' ) {
					error_log( '[doo--product-exporter] Warning: Encoding export option was corrupted, defaulted to UTF-8' );
					$export->encoding = 'UTF-8';
					woo_ce_update_option( 'encoding', 'UTF-8' );
				}
				$export->delimiter = woo_ce_get_option( 'delimiter', ',' );
				// Reset the Delimiter if corrupted
				if( $export->delimiter == '' || $export->delimiter == false ) {
					error_log( '[doo--product-exporter] Warning: Delimiter export option was corrupted, defaulted to ,' );
					$export->delimiter = ',';
					woo_ce_update_option( 'delimiter', ',' );
				} else if( $export->delimiter == 'TAB' ) {
					$export->delimiter = "\t";
				}
				$export->category_separator = woo_ce_get_option( 'category_separator', '|' );
				// Reset the Category Separator if corrupted
				if( $export->category_separator == '' || $export->category_separator == false ) {
					error_log( '[doo--product-exporter] Warning: Category Separator export option was corrupted, defaulted to |' );
					$export->category_separator = '|';
					woo_ce_update_option( 'category_separator', '|' );
				}
				// Override for line break (LF) support in Category Separator
				if( $export->category_separator == 'LF' )
					$export->category_separator = "\n";
				$export->bom = woo_ce_get_option( 'bom', 1 );
				$export->escape_formatting = woo_ce_get_option( 'escape_formatting', 'all' );
				// Reset the Escape Formatting if corrupted
				if( $export->escape_formatting == '' || $export->escape_formatting == false ) {
					error_log( '[doo--product-exporter] Warning: Escape Formatting export option was corrupted, defaulted to all' );
					$export->escape_formatting = 'all';
					woo_ce_update_option( 'escape_formatting', 'all' );
				}
				$export->header_formatting = woo_ce_get_option( 'header_formatting', 1 );
				$export->date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
				// Reset the Date Format if corrupted
				if( $export->date_format == '1' || $export->date_format == '' || $export->date_format == false ) {
					error_log( '[doo--product-exporter] Warning: Date Format export option was corrupted, defaulted to d/m/Y' );
					$export->date_format = 'd/m/Y';
					woo_ce_update_option( 'date_format', 'd/m/Y' );
				}

				// Save export option changes made on the Export screen
				$export->limit_volume = ( isset( $_POST['limit_volume'] ) ? sanitize_text_field( $_POST['limit_volume'] ) : '' );
				woo_ce_update_option( 'limit_volume', $export->limit_volume );
				if( $export->limit_volume == '' )
					$export->limit_volume = -1;
				$export->offset = ( isset( $_POST['offset'] ) ? sanitize_text_field( $_POST['offset'] ) : '' );
				woo_ce_update_option( 'offset', $export->offset );
				if( $export->offset == '' )
					$export->offset = 0;
				$export->type = ( isset( $_POST['dataset'] ) ? sanitize_text_field( $_POST['dataset'] ) : false );
				if( in_array( $export->type, array( 'product', 'category', 'tag', 'brand', 'order' ) ) ) {
					$export->description_excerpt_formatting = ( isset( $_POST['description_excerpt_formatting'] ) ? absint( $_POST['description_excerpt_formatting'] ) : false );
					if( $export->description_excerpt_formatting <> woo_ce_get_option( 'description_excerpt_formatting' ) )
						woo_ce_update_option( 'variation_formatting', $export->description_excerpt_formatting );
				}
				woo_ce_update_option( 'export_format', sanitize_text_field( $_POST['export_format'] ) );

				// Set default values for all export options to be later passed onto the export process
				$export->fields = array();
				$export->fields_order = false;
				$export->export_format = woo_ce_get_option( 'export_format', 'csv' );

				// Product sorting
				$export->product_categories = false;
				$export->product_tags = false;
				$export->product_brands = false;
				$export->product_vendors = false;
				$export->product_status = false;
				$export->product_type = false;
				$export->product_stock = false;
				$export->product_language = false;
				$export->product_orderby = false;
				$export->product_order = false;
				$export->gallery_formatting = false;
				$export->gallery_unique = false;
				$export->max_product_gallery = false;
				$export->upsell_formatting = false;
				$export->crosssell_formatting = false;
				$export->variation_formatting = false;

				// Category sorting
				$export->category_language = false;
				$export->category_orderby = false;
				$export->category_order = false;

				// Tag sorting
				$export->tag_language = false;
				$export->tag_orderby = false;
				$export->tag_order = false;

				// Brand sorting
				$export->brand_orderby = false;
				$export->brand_order = false;

				// Order sorting
				$export->order_dates_filter = false;
				$export->order_dates_from = '';
				$export->order_dates_to = '';
				$export->order_dates_filter_variable = false;
				$export->order_dates_filter_variable_length = false;
				$export->order_status = false;
				$export->order_customer = false;
				$export->order_billing_country = false;
				$export->order_shipping_country = false;
				$export->order_user_roles = false;
				$export->order_product = false;
				$export->order_coupons = false;
				$export->order_category = false;
				$export->order_tag = false;
				$export->order_brand = false;
				$export->order_ids = false;
				$export->order_payment = false;
				$export->order_shipping = false;
				$export->order_items = 'combined';
				$export->order_items_types = false;
				$export->order_orderby = false;
				$export->order_order = false;
				$export->max_order_items = false;

				// User sorting
				$export->user_orderby = false;
				$export->user_order = false;

				// Coupon sorting
				$export->coupon_discount_types = false;
				$export->coupon_orderby = false;
				$export->coupon_order = false;

				// Subscription sorting
				$export->subscription_status = false;
				$export->subscription_product = false;

				// Commission sorting
				$export->commission_dates_filter = false;
				$export->commission_dates_from = '';
				$export->commission_dates_to = '';
				$export->commission_dates_filter_variable = false;
				$export->commission_dates_filter_variable_length = false;
				$export->commission_product_vendors = false;
				$export->commission_status = false;
				$export->commission_orderby = false;
				$export->commission_order = false;

				// Shipping Class sorting
				$export->shipping_class_orderby = false;
				$export->shipping_class_order = false;

				if( !empty( $export->type ) ) {
					$export->fields = ( isset( $_POST[$export->type . '_fields'] ) ? array_map( 'sanitize_text_field', $_POST[$export->type . '_fields'] ) : false );
					$export->fields_order = ( isset( $_POST[$export->type . '_fields_order'] ) ? array_map( 'absint', $_POST[$export->type . '_fields_order'] ) : false );
					woo_ce_update_option( 'last_export', $export->type );
				}
				switch( $export->type ) {

					case 'product':
						// Set up dataset specific options
						$export->product_categories = ( isset( $_POST['product_filter_category'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_category'] ) ) : false );
						$export->product_tags = ( isset( $_POST['product_filter_tag'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_tag'] ) ) : false );
						$export->product_brands = ( isset( $_POST['product_filter_brand'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_brand'] ) ) : false );
						$export->product_vendors = ( isset( $_POST['product_filter_vendor'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['product_filter_vendor'] ) ) : false );
						$export->product_status = ( isset( $_POST['product_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['product_filter_status'] ) ) : false );
						$export->product_type = ( isset( $_POST['product_filter_type'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['product_filter_type'] ) ) : false );
						$export->product_stock = ( isset( $_POST['product_filter_stock'] ) ? sanitize_text_field( $_POST['product_filter_stock'] ) : false );
						$export->product_language = ( isset( $_POST['product_filter_language'] ) ? array_map( 'sanitize_text_field', $_POST['product_filter_language'] ) : false );
						$export->product_orderby = ( isset( $_POST['product_orderby'] ) ? sanitize_text_field( $_POST['product_orderby'] ) : false );
						$export->product_order = ( isset( $_POST['product_order'] ) ? sanitize_text_field( $_POST['product_order'] ) : false );
						$export->gallery_formatting = ( isset( $_POST['product_gallery_formatting'] ) ? absint( $_POST['product_gallery_formatting'] ) : false );
						$export->gallery_unique = ( isset( $_POST['product_gallery_unique'] ) ? absint( $_POST['product_gallery_unique'] ) : false );
						$export->upsell_formatting = ( isset( $_POST['product_upsell_formatting'] ) ? absint( $_POST['product_upsell_formatting'] ) : false );
						$export->crosssell_formatting = ( isset( $_POST['product_crosssell_formatting'] ) ? absint( $_POST['product_crosssell_formatting'] ) : false );
						$export->variation_formatting = ( isset( $_POST['variation_formatting'] ) ? absint( $_POST['variation_formatting'] ) : false );

						// Save dataset export specific options
						if( $export->product_orderby <> woo_ce_get_option( 'product_orderby' ) )
							woo_ce_update_option( 'product_orderby', $export->product_orderby );
						if( $export->product_order <> woo_ce_get_option( 'product_order' ) )
							woo_ce_update_option( 'product_order', $export->product_order );
						if( $export->upsell_formatting <> woo_ce_get_option( 'upsell_formatting' ) )
							woo_ce_update_option( 'upsell_formatting', $export->upsell_formatting );
						if( $export->crosssell_formatting <> woo_ce_get_option( 'crosssell_formatting' ) )
							woo_ce_update_option( 'crosssell_formatting', $export->crosssell_formatting );
						if( $export->variation_formatting <> woo_ce_get_option( 'variation_formatting' ) )
							woo_ce_update_option( 'variation_formatting', $export->variation_formatting );
						if( $export->gallery_formatting <> woo_ce_get_option( 'gallery_formatting' ) )
							woo_ce_update_option( 'gallery_formatting', $export->gallery_formatting );
						if( $export->gallery_unique <> woo_ce_get_option( 'gallery_unique' ) )
							woo_ce_update_option( 'gallery_unique', $export->gallery_unique );
						if( isset( $_POST['max_product_gallery'] ) ) {
							$export->max_product_gallery = absint( $_POST['max_product_gallery'] );
							if( $export->max_product_gallery <> woo_ce_get_option( 'max_product_gallery' ) )
								woo_ce_update_option( 'max_product_gallery', $export->max_product_gallery );
						}
						break;

					case 'category':
						// Set up dataset specific options
						$export->category_language = ( isset( $_POST['category_filter_language'] ) ? array_map( 'sanitize_text_field', $_POST['category_filter_language'] ) : false );
						$export->category_orderby = ( isset( $_POST['category_orderby'] ) ? sanitize_text_field( $_POST['category_orderby'] ) : false );
						$export->category_order = ( isset( $_POST['category_order'] ) ? sanitize_text_field( $_POST['category_order'] ) : false );

						// Save dataset export specific options
						if( $export->category_orderby <> woo_ce_get_option( 'category_orderby' ) )
							woo_ce_update_option( 'category_orderby', $export->category_orderby );
						if( $export->category_order <> woo_ce_get_option( 'category_order' ) )
							woo_ce_update_option( 'category_order', $export->category_order );
						break;

					case 'tag':
						// Set up dataset specific options
						$export->tag_language = ( isset( $_POST['tag_filter_language'] ) ? array_map( 'sanitize_text_field', $_POST['tag_filter_language'] ) : false );
						$export->tag_orderby = ( isset( $_POST['tag_orderby'] ) ? sanitize_text_field( $_POST['tag_orderby'] ) : false );
						$export->tag_order = ( isset( $_POST['tag_order'] ) ? sanitize_text_field( $_POST['tag_order'] ) : false );

						// Save dataset export specific options
						if( $export->tag_orderby <> woo_ce_get_option( 'tag_orderby' ) )
							woo_ce_update_option( 'tag_orderby', $export->tag_orderby );
						if( $export->tag_order <> woo_ce_get_option( 'tag_order' ) )
							woo_ce_update_option( 'tag_order', $export->tag_order );
						break;

					case 'brand':
						// Set up dataset specific options
						$export->brand_orderby = ( isset( $_POST['brand_orderby'] ) ? sanitize_text_field( $_POST['brand_orderby'] ) : false );
						$export->brand_order = ( isset( $_POST['brand_order'] ) ? sanitize_text_field( $_POST['brand_order'] ) : false );

						// Save dataset export specific options
						if( $export->brand_orderby <> woo_ce_get_option( 'brand_orderby' ) )
							woo_ce_update_option( 'brand_orderby', $export->brand_orderby );
						if( $export->brand_order <> woo_ce_get_option( 'brand_order' ) )
							woo_ce_update_option( 'brand_order', $export->brand_order );
						break;

					case 'order':
						// Set up dataset specific options
						$export->order_dates_filter = ( isset( $_POST['order_dates_filter'] ) ? sanitize_text_field( $_POST['order_dates_filter'] ) : false );
						$export->order_dates_from = sanitize_text_field( $_POST['order_dates_from'] );
						$export->order_dates_to = sanitize_text_field( $_POST['order_dates_to'] );
						$export->order_dates_filter_variable = ( isset( $_POST['order_dates_filter_variable'] ) ? absint( $_POST['order_dates_filter_variable'] ) : false );
						$export->order_dates_filter_variable_length = ( isset( $_POST['order_dates_filter_variable_length'] ) ? sanitize_text_field( $_POST['order_dates_filter_variable_length'] ) : false );
						$export->order_status = ( isset( $_POST['order_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['order_filter_status'] ) ) : false );
						$export->order_customer = ( isset( $_POST['order_filter_customer'] ) ? array_map( 'absint', $_POST['order_filter_customer'] ) : false );
						$export->order_billing_country = ( isset( $_POST['order_filter_billing_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_billing_country'] ) : false );
						$export->order_shipping_country = ( isset( $_POST['order_filter_shipping_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping_country'] ) : false );
						$export->order_user_roles = ( isset( $_POST['order_filter_user_role'] ) ? woo_ce_format_user_role_filters( array_map( 'sanitize_text_field', $_POST['order_filter_user_role'] ) ) : false );
						$export->order_coupons = ( isset( $_POST['order_filter_coupon'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_coupon'] ) ) : false );
						$export->order_product = ( isset( $_POST['order_filter_product'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['order_filter_product'] ) ) : false );
						$export->order_category = ( isset( $_POST['order_filter_category'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_category'] ) ) : false );
						$export->order_tag = ( isset( $_POST['order_filter_tag'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_tag'] ) ) : false );
						$export->order_brand = ( isset( $_POST['order_filter_brand'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['order_filter_brand'] ) ) : false );
						$export->order_ids = ( isset( $_POST['order_filter_id'] ) ? sanitize_text_field( $_POST['order_filter_id'] ) : false );
						$export->order_payment = ( isset( $_POST['order_filter_payment_gateway'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_payment_gateway'] ) : false );
						$export->order_shipping = ( isset( $_POST['order_filter_shipping_method'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping_method'] ) : false );
						$export->order_orderby = ( isset( $_POST['order_orderby'] ) ? sanitize_text_field( $_POST['order_orderby'] ) : false );
						$export->order_order = ( isset( $_POST['order_order'] ) ? sanitize_text_field( $_POST['order_order'] ) : false );

						// Save dataset export specific options
						if( isset( $_POST['order_items'] ) ) {
							$export->order_items = sanitize_text_field( $_POST['order_items'] );
							if( $export->order_items <> woo_ce_get_option( 'order_items_formatting' ) )
								woo_ce_update_option( 'order_items_formatting', $export->order_items );
						}
						if( isset( $_POST['order_items_types'] ) ) {
							$export->order_items_types = array_map( 'sanitize_text_field', $_POST['order_items_types'] );
							if( $export->order_items_types <> woo_ce_get_option( 'order_items_types' ) )
								woo_ce_update_option( 'order_items_types', $export->order_items_types );
						}
						if( isset( $_POST['max_order_items'] ) ) {
							$export->max_order_items = absint( $_POST['max_order_items'] );
							if( $export->max_order_items <> woo_ce_get_option( 'max_order_items' ) )
								woo_ce_update_option( 'max_order_items', $export->max_order_items );
						}
						if( $export->order_orderby <> woo_ce_get_option( 'order_orderby' ) )
							woo_ce_update_option( 'order_orderby', $export->order_orderby );
						if( $export->order_order <> woo_ce_get_option( 'order_order' ) )
							woo_ce_update_option( 'order_order', $export->order_order );
						break;

					case 'customer':
						// Set up dataset specific options

						// Save dataset export specific options
						$export->order_status = ( isset( $_POST['customer_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['customer_filter_status'] ) ) : false );
						$export->order_user_roles = ( isset( $_POST['customer_filter_user_role'] ) ? woo_ce_format_user_role_filters( array_map( 'sanitize_text_field', $_POST['customer_filter_user_role'] ) ) : false );
						break;

					case 'user':
						// Set up dataset specific options
						$export->user_orderby = ( isset( $_POST['user_orderby'] ) ? sanitize_text_field( $_POST['user_orderby'] ) : false );
						$export->user_order = ( isset( $_POST['user_order'] ) ? sanitize_text_field( $_POST['user_order'] ) : false );

						// Save dataset export specific options
						if( $export->user_orderby <> woo_ce_get_option( 'user_orderby' ) )
							woo_ce_update_option( 'user_orderby', $export->user_orderby );
						if( $export->user_order <> woo_ce_get_option( 'user_order' ) )
							woo_ce_update_option( 'user_order', $export->user_order );
						break;

					case 'coupon':
						// Set up dataset specific options
						$export->coupon_discount_types = ( isset( $_POST['coupon_filter_discount_type'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['coupon_filter_discount_type'] ) ) : false );
						$export->coupon_orderby = ( isset( $_POST['coupon_orderby'] ) ? sanitize_text_field( $_POST['coupon_orderby'] ) : false );
						$export->coupon_order = ( isset( $_POST['coupon_order'] ) ? sanitize_text_field( $_POST['coupon_order'] ) : false );

						// Save dataset export specific options
						if( $export->coupon_orderby <> woo_ce_get_option( 'coupon_orderby' ) )
							woo_ce_update_option( 'coupon_orderby', $export->coupon_orderby );
						if( $export->coupon_order <> woo_ce_get_option( 'coupon_order' ) )
							woo_ce_update_option( 'coupon_order', $export->coupon_order );
						break;

					case 'subscription':
						// Set up dataset specific options

						// Save dataset export specific options
						$export->subscription_status = ( isset( $_POST['subscription_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['subscription_filter_status'] ) ) : false );
						$export->subscription_product = ( isset( $_POST['subscription_filter_product'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['subscription_filter_product'] ) ) : false );
						break;

					case 'attribute':
						break;

					case 'product_vendor':
						break;

					case 'commission':
						$export->commission_dates_filter = ( isset( $_POST['commission_dates_filter'] ) ? sanitize_text_field( $_POST['commission_dates_filter'] ) : false );
						$export->commission_dates_from = sanitize_text_field( $_POST['commission_dates_from'] );
						$export->commission_dates_to = sanitize_text_field( $_POST['commission_dates_to'] );
						$export->commission_dates_filter_variable = ( isset( $_POST['commission_dates_filter_variable'] ) ? absint( $_POST['commission_dates_filter_variable'] ) : false );
						$export->commission_dates_filter_variable_length = ( isset( $_POST['commission_dates_filter_variable_length'] ) ? sanitize_text_field( $_POST['commission_dates_filter_variable_length'] ) : false );
						$export->commission_product_vendors = ( isset( $_POST['commission_filter_product_vendor'] ) ? woo_ce_format_product_filters( array_map( 'absint', $_POST['commission_filter_product_vendor'] ) ) : false );
						$export->commission_status = ( isset( $_POST['commission_filter_commission_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['commission_filter_commission_status'] ) ) : false );
						$export->commission_orderby = ( isset( $_POST['commission_orderby'] ) ? sanitize_text_field( $_POST['commission_orderby'] ) : false );
						$export->commission_order = ( isset( $_POST['commission_order'] ) ? sanitize_text_field( $_POST['commission_order'] ) : false );

						// Save dataset export specific options
						if( $export->commission_orderby <> woo_ce_get_option( 'commission_orderby' ) )
							woo_ce_update_option( 'commission_orderby', $export->commission_orderby );
						if( $export->commission_order <> woo_ce_get_option( 'commission_order' ) )
							woo_ce_update_option( 'commission_order', $export->commission_order );
						break;

					case 'shipping_class':
						// Set up dataset specific options
						$export->shipping_class_orderby = ( isset( $_POST['shipping_class_orderby'] ) ? sanitize_text_field( $_POST['shipping_class_orderby'] ) : false );
						$export->shipping_class_order = ( isset( $_POST['shipping_class_order'] ) ? sanitize_text_field( $_POST['shipping_class_order'] ) : false );

						// Save dataset export specific options
						if( $export->shipping_class_orderby <> woo_ce_get_option( 'shipping_class_orderby' ) )
							woo_ce_update_option( 'shipping_class_orderby', $export->shipping_class_orderby );
						if( $export->shipping_class_order <> woo_ce_get_option( 'shipping_class_order' ) )
							woo_ce_update_option( 'shipping_class_order', $export->shipping_class_order );
						break;

				}
				if( $export->type ) {

					$timeout = 600;
					if( isset( $_POST['timeout'] ) ) {
						$timeout = absint( $_POST['timeout'] );
						if( $timeout <> woo_ce_get_option( 'timeout' ) )
							woo_ce_update_option( 'timeout', $timeout );
					}
					if( !ini_get( 'safe_mode' ) )
						@set_time_limit( $timeout );

					@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
					@ini_set( 'max_execution_time', $timeout );

					$export->args = array(
						'limit_volume' => $export->limit_volume,
						'offset' => $export->offset,
						'encoding' => $export->encoding,
						'date_format' => $export->date_format,
						'product_categories' => $export->product_categories,
						'product_tags' => $export->product_tags,
						'product_brands' => $export->product_brands,
						'product_vendors' => $export->product_vendors,
						'product_status' => $export->product_status,
						'product_type' => $export->product_type,
						'product_stock' => $export->product_stock,
						'product_language' => $export->product_language,
						'product_orderby' => $export->product_orderby,
						'product_order' => $export->product_order,
						'category_language' => $export->category_language,
						'category_orderby' => $export->category_orderby,
						'category_order' => $export->category_order,
						'tag_language' => $export->tag_language,
						'tag_orderby' => $export->tag_orderby,
						'tag_order' => $export->tag_order,
						'brand_orderby' => $export->brand_orderby,
						'brand_order' => $export->brand_order,
						'order_status' => $export->order_status,
						'order_dates_filter' => $export->order_dates_filter,
						'order_dates_from' => woo_ce_format_order_date( $export->order_dates_from ),
						'order_dates_to' => woo_ce_format_order_date( $export->order_dates_to ),
						'order_dates_filter_variable' => $export->order_dates_filter_variable,
						'order_dates_filter_variable_length' => $export->order_dates_filter_variable_length,
						'order_customer' => $export->order_customer,
						'order_billing_country' => $export->order_billing_country,
						'order_shipping_country' => $export->order_shipping_country,
						'order_user_roles' => $export->order_user_roles,
						'order_coupons' => $export->order_coupons,
						'order_product' => $export->order_product,
						'order_category' => $export->order_category,
						'order_tag' => $export->order_tag,
						'order_brand' => $export->order_brand,
						'order_ids' => $export->order_ids,
						'order_payment' => $export->order_payment,
						'order_shipping' => $export->order_shipping,
						'order_items' => $export->order_items,
						'order_items_types' => $export->order_items_types,
						'order_orderby' => $export->order_orderby,
						'order_order' => $export->order_order,
						'user_orderby' => $export->user_orderby,
						'user_order' => $export->user_order,
						'coupon_discount_types' => $export->coupon_discount_types,
						'coupon_orderby' => $export->coupon_orderby,
						'coupon_order' => $export->coupon_order,
						'subscription_status' => $export->subscription_status,
						'subscription_product' => $export->subscription_product,
						'commission_dates_filter' => $export->commission_dates_filter,
						'commission_dates_from' => woo_ce_format_order_date( $export->commission_dates_from ),
						'commission_dates_to' => woo_ce_format_order_date( $export->commission_dates_to ),
						'commission_dates_filter_variable' => $export->commission_dates_filter_variable,
						'commission_dates_filter_variable_length' => $export->commission_dates_filter_variable_length,
						'commission_product_vendors' => $export->commission_product_vendors,
						'commission_status' => $export->commission_status,
						'commission_orderby' => $export->commission_orderby,
						'commission_order' => $export->commission_order,
						'shipping_class_orderby' => $export->shipping_class_orderby,
						'shipping_class_order' => $export->shipping_class_order
					);

					if( empty( $export->fields ) ) {
						$message = __( 'No export fields were selected, please try again with at least a single export field.', 'woo_ce' );
						woo_cd_admin_notice( $message, 'error' );
						return;
					}
					woo_ce_save_fields( $export->type, $export->fields, $export->fields_order );
					unset( $export->fields_order );

					$export->filename = woo_ce_generate_filename( $export->type );

					$export->idle_memory_end = woo_ce_current_memory_usage();
					$export->end_time = time();

					// Let's spin up PHPExcel for supported Export Types and Export Formats
					if( in_array( $export->export_format, array( 'csv', 'xls', 'xlsx' ) ) ) {

						$dataset = woo_ce_export_dataset( $export->type );
						if( empty( $dataset ) ) {
							$message = __( 'No export entries were found, please try again with different export filters.', 'woo_ce' );
							woo_cd_admin_notice( $message, 'error' );
							return;
						}

						// Load up the fatal error notice if we 500, timeout or encounter a fatal PHP error
						add_action( 'shutdown', 'woo_ce_fatal_error' );

						// Check that PHPExcel is where we think it is
						if( file_exists( WOO_CD_PATH . 'classes/PHPExcel.php' ) ) {
							// Check if PHPExcel has already been loaded
							if( !class_exists( 'PHPExcel' ) ) {
								include_once( WOO_CD_PATH . 'classes/PHPExcel.php' );
							} else {
								$message = sprintf( __( 'The PHPExcel library was already loaded by another WordPress Plugin, if there\'s issues with your export file you know where to look. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
								woo_cd_admin_notice( $message, 'error' );
							}
						} else {
							$message = sprintf( __( 'We couldn\'t load the PHPExcel library <code>%s</code> within <code>%s</code>, this file should be present. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), 'PHPExcel.php', WOO_CD_PATH . 'classes/...', $troubleshooting_url );
							woo_cd_admin_notice( $message, 'error' );
							return;
						}

						$excel = new PHPExcel();
						$excel->setActiveSheetIndex( 0 );
						$excel->getActiveSheet()->setTitle( ucfirst( $export->type ) );

						$row = 1;
						// Skip headers if Heading Formatting is turned off
						if( $export->header_formatting ) {
							$col = 0;
							foreach( $export->columns as $column ) {
								$excel->getActiveSheet()->setCellValueByColumnAndRow( $col, $row, $column );
								$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( true );
								$excel->getActiveSheet()->getColumnDimensionByColumn( $col )->setAutoSize( true );
								$col++;
							}
							$row = 2;
						}
						$col = 0;
						foreach( $dataset as $data ) {
							$col = 0;
							foreach( array_keys( $export->fields ) as $field ) {
								$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getStyle()->getFont()->setBold( false );
								if( $export->encoding == 'UTF-8' ) {
									if( woo_ce_detect_value_string( ( isset( $data->$field ) ? $data->$field : null ) ) ) {
										// Treat this cell as a string
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValueExplicit( ( isset( $data->$field ) ? wp_specialchars_decode( $data->$field ) : '' ), PHPExcel_Cell_DataType::TYPE_STRING );
									} else {
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValue( ( isset( $data->$field ) ? wp_specialchars_decode( $data->$field ) : '' ) );
									}
								} else {
									// PHPExcel only deals with UTF-8 regardless of encoding type
									if( woo_ce_detect_value_string( ( isset( $data->$field ) ? $data->$field : null ) ) ) {
										// Treat this cell as a string
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValueExplicit( ( isset( $data->$field ) ? utf8_encode( wp_specialchars_decode( $data->$field ) ) : '' ), PHPExcel_Cell_DataType::TYPE_STRING );
									} else {
										$excel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->setValue( ( isset( $data->$field ) ? utf8_encode( wp_specialchars_decode( $data->$field ) ) : '' ) );
									}
								}
								$col++;
							}
							$row++;
						}
						// Override the export format to CSV if debug mode is enabled
						if( WOO_CD_DEBUG )
							$export->export_format = 'csv';
						switch( $export->export_format ) {

							case 'csv':
								woo_cd_load_phpexcel_sed_csv_writer();
								$php_excel_format = 'SED_CSV';
								$file_extension = 'csv';
								$post_mime_type = 'text/csv';
								break;

							case 'xls':
								$php_excel_format = 'Excel5';
								$file_extension = 'xls';
								$post_mime_type = 'application/vnd.ms-excel';
								break;

							case 'xlsx':
								$php_excel_format = 'Excel2007';
								$file_extension = 'xlsx';
								$post_mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
								break;

						}
						$export->filename = $export->filename . '.' . $file_extension;
						$objWriter = PHPExcel_IOFactory::createWriter( $excel, $php_excel_format );

						// Only write headers if we're not in debug mode
						if( WOO_CD_DEBUG !== true ) {

							// Print to browser
							woo_ce_generate_file_headers( $post_mime_type );
							switch( $export->export_format ) {
	
								case 'csv':
									$objWriter->setUseBOM( true );
									// Check if we're using a non-standard delimiter
									if( $export->delimiter != ',' )
										$objWriter->setDelimiter( $export->delimiter );
									break;

								case 'xlsx':
									$objWriter->setPreCalculateFormulas( false );
									break;

							}
							// Print directly to browser, do not save
							if( $export->delete_file ) {
								// The end memory usage and time is collected at the very last opportunity prior to the file header being rendered to the screen
								woo_ce_update_file_detail( $post_ID, '_woo_idle_memory_end', woo_ce_current_memory_usage() );
								woo_ce_update_file_detail( $post_ID, '_woo_end_time', time() );
								delete_option( WOO_CD_PREFIX . '_exported' );
								$objWriter->save( 'php://output' );
							} else {
								// Save to file and insert to WordPress Media
								$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
								// Check if we were given a temporary filename
								if( $temp_filename == false ) {
									$message = sprintf( __( 'We could not create a temporary export file in <code>%s</code>, ensure that WordPress can read and write files here and try again.', 'woo_ce' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
									woo_cd_admin_notice( $message, 'error' );
									wp_redirect( esc_url( add_query_arg( array( 'failed' => true, 'message' => urlencode( $message ) ) ) ) );
									exit();
								} else {
									$objWriter->save( $temp_filename );
									$bits = file_get_contents( $temp_filename );
								}
								unlink( $temp_filename );

								$post_ID = woo_ce_save_file_attachment( $export->filename, $post_mime_type );
								$upload = wp_upload_bits( $export->filename, null, $bits );
								// Check if the upload succeeded otherwise delete Post and return error notice
								if( ( $post_ID == false ) || $upload['error'] ) {
									wp_delete_attachment( $post_ID, true );
									if( isset( $upload['error'] ) )
										wp_redirect( esc_url( add_query_arg( array( 'failed' => true, 'message' => urlencode( $upload['error'] ) ) ) ) );
									else
										wp_redirect( esc_url( add_query_arg( array( 'failed' => true ) ) ) );
									return;
								}
								if( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
									$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
									wp_update_attachment_metadata( $post_ID, $attach_data );
									update_attached_file( $post_ID, $upload['file'] );
									if( !empty( $post_ID ) ) {
										woo_ce_save_file_guid( $post_ID, $export->type, $upload['url'] );
										woo_ce_save_file_details( $post_ID );
									}
								} else {
									error_log( sprintf( '[doo--product-exporter] %s: Error: %s', $export->filename, __( 'Could not load image.php within /wp-admin/includes/image.php', 'woo_ce' ) ) );
								}
								// The end memory usage and time is collected at the very last opportunity prior to the file header being rendered to the screen
								woo_ce_update_file_detail( $post_ID, '_woo_idle_memory_end', woo_ce_current_memory_usage() );
								woo_ce_update_file_detail( $post_ID, '_woo_end_time', time() );
								delete_option( WOO_CD_PREFIX . '_exported' );
								$objWriter->save( 'php://output' );

							}

							// Clean up PHPExcel
							$excel->disconnectWorksheets();
							unset( $objWriter, $excel );
							exit();

						} else {

							// Save to temporary file then dump into export log screen
							$objWriter->setUseBOM( true );
							$temp_filename = tempnam( apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ), 'tmp' );
							// Check if we were given a temporary filename
							if( $temp_filename == false ) {
								$message = sprintf( __( 'We could not create a temporary export file in <code>%s</code>, ensure that WordPress can read and write files here and try again.', 'woo_ce' ), apply_filters( 'woo_ce_sys_get_temp_dir', sys_get_temp_dir() ) );
								woo_cd_admin_notice( $message, 'error' );
							} else {
								$objWriter->save( $temp_filename );
								$bits = file_get_contents( $temp_filename );
							}
							unlink( $temp_filename );

							// Clean up PHPExcel
							$excel->disconnectWorksheets();
							unset( $objWriter, $excel );

							// Save the export contents to the WordPress Transient
							$response = set_transient( WOO_CD_PREFIX . '_debug_log', base64_encode( $bits ), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
							if( $response !== true ) {
								$message = __( 'The export contents were too large to store in a single WordPress transient, use the Volume offset / Limit volume options to reduce the size of your export and try again.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
								woo_cd_admin_notice( $message, 'error' );
							}

						}

						// Remove our fatal error notice to play nice with other Plugins
						remove_action( 'shutdown', 'woo_ce_fatal_error' );

					// Run the default engine for the XML and RSS export formats
					} else if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {

						// Check if SimpleXMLElement is present
						if( !class_exists( 'SED_SimpleXMLElement' ) ) {
							$message = sprintf( __( 'We couldn\'t load the SimpleXMLElement class, the SimpleXMLElement class is required for XML and RSS feed generation. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
							woo_cd_admin_notice( $message, 'error' );
							return;
						}

						switch( $export->export_format ) {

							case 'xml':
								$file_extension = 'xml';
								$post_mime_type = 'application/xml';
								break;

							case 'rss':
								$file_extension = 'xml';
								$post_mime_type = 'application/rss+xml';
								break;

						}
						$export->filename = $export->filename . '.' . $file_extension;

						if( $export->export_format == 'xml' ) {
							$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_xml_first_line', '<?xml version="1.0" encoding="%s"?><%s/>' ), esc_attr( $export->encoding ), esc_attr( apply_filters( 'woo_ce_export_xml_store_node', 'store' ) ) ) );
							if( woo_ce_get_option( 'xml_attribute_url', 1 ) )
								$xml->addAttribute( 'url', get_site_url() );
							if( woo_ce_get_option( 'xml_attribute_date', 1 ) )
								$xml->addAttribute( 'date', date( 'Y-m-d' ) );
							if( woo_ce_get_option( 'xml_attribute_time', 0 ) )
								$xml->addAttribute( 'time', date( 'H:i:s' ) );
							if( woo_ce_get_option( 'xml_attribute_title', 1 ) )
								$xml->addAttribute( 'name', htmlspecialchars( get_bloginfo( 'name' ) ) );
							if( woo_ce_get_option( 'xml_attribute_export', 1 ) )
								$xml->addAttribute( 'export', htmlspecialchars( $export->type ) );
							if( woo_ce_get_option( 'xml_attribute_orderby', 1 ) && isset( $export->{$export->type . '_orderby'} ) )
								$xml->addAttribute( 'orderby', $export->{$export->type . '_orderby'} );
							if( woo_ce_get_option( 'xml_attribute_order', 1 ) && isset( $export->{$export->type . '_order'} ) )
								$xml->addAttribute( 'order', $export->{$export->type . '_order'} );
							if( woo_ce_get_option( 'xml_attribute_limit', 1 ) )
								$xml->addAttribute( 'limit', $export->limit_volume );
							if( woo_ce_get_option( 'xml_attribute_offset', 1 ) )
								$xml->addAttribute( 'offset', $export->offset );
							$bits = woo_ce_export_dataset( $export->type, $xml );
						} else if( $export->export_format == 'rss' ) {
							$xml = new SED_SimpleXMLElement( sprintf( apply_filters( 'woo_ce_export_rss_first_line', '<?xml version="1.0" encoding="%s"?><rss version="2.0"%s/>' ), esc_attr( $export->encoding ), ' xmlns:g="http://base.google.com/ns/1.0"' ) );
							$child = $xml->addChild( apply_filters( 'woo_ce_export_rss_channel_node', 'channel' ) );
							$child->addChild( 'title', woo_ce_get_option( 'rss_title', '' ) );
							$child->addChild( 'link', woo_ce_get_option( 'rss_link', '' ) );
							$child->addChild( 'description', woo_ce_get_option( 'rss_description', '' ) );
							$bits = woo_ce_export_dataset( $export->type, $child );
						}

						if( empty( $bits ) ) {
							$message = __( 'No export entries were found, please try again with different export filters.', 'woo_ce' );
							woo_cd_admin_notice( $message, 'error' );
							return;
						}

						if( WOO_CD_DEBUG !== true ) {
							if( $export->delete_file ) {

								// Print directly to browser
								woo_ce_generate_file_headers( $post_mime_type );
								if( $bits = woo_ce_format_xml( $bits ) )
									echo $bits;
								exit();

							} else {

								// Save to file and insert to WordPress Media
								if( $export->filename && $bits ) {
									$post_ID = woo_ce_save_file_attachment( $export->filename, $post_mime_type );
									$bits = woo_ce_format_xml( $bits );
									$upload = wp_upload_bits( $export->filename, null, $bits );
									// Check for issues saving to WordPress Media
									if( ( $post_ID == false ) || !empty( $upload['error'] ) ) {
										wp_delete_attachment( $post_ID, true );
										if( isset( $upload['error'] ) )
											wp_redirect( esc_url( add_query_arg( array( 'failed' => true, 'message' => urlencode( $upload['error'] ) ) ) ) );
										else
											wp_redirect( esc_url( add_query_arg( array( 'failed' => true ) ) ) );
										return;
									}
									$attach_data = wp_generate_attachment_metadata( $post_ID, $upload['file'] );
									wp_update_attachment_metadata( $post_ID, $attach_data );
									update_attached_file( $post_ID, $upload['file'] );
									if( $post_ID ) {
										woo_ce_save_file_guid( $post_ID, $export->type, $upload['url'] );
										woo_ce_save_file_details( $post_ID );
									}
									$export_type = $export->type;
									unset( $export );

									// The end memory usage and time is collected at the very last opportunity prior to the XML header being rendered to the screen
									woo_ce_update_file_detail( $post_ID, '_woo_idle_memory_end', woo_ce_current_memory_usage() );
									woo_ce_update_file_detail( $post_ID, '_woo_end_time', time() );
									delete_option( WOO_CD_PREFIX . '_exported' );

									// Generate XML header
									woo_ce_generate_file_headers( $post_mime_type );
									unset( $export_type );

									// Print file contents to screen
									if( !empty( $upload['file'] ) ) {
										// Check if readfile() is disabled on this host
										$disabled = explode( ',', ini_get( 'disable_functions' ) );
										if( !in_array( 'readfile', $disabled ) ) {
											readfile( $upload['file'] );
										} else {
											// Workaround for disabled readfile on some hosts
											$fp = fopen( $upload['file'], 'rb' );
											fpassthru( $fp );
											fclose( $fp );
											unset( $fp );
										}
										unset( $disabled );
									} else {
										wp_redirect( esc_url( add_query_arg( 'failed', true ) ) );
									}
									unset( $upload );
								} else {
									wp_redirect( esc_url( add_query_arg( 'failed', true ) ) );
								}
								exit();

							}
						}

					}

				}
				break;

			// Save changes on Settings screen
			case 'save-settings':
				woo_ce_admin_save_settings();
				break;

			// Save changes on Field Editor screen
			case 'save-fields':
				$fields = ( isset( $_POST['fields'] ) ? array_filter( $_POST['fields'] ) : array() );
				$hidden = ( isset( $_POST['hidden'] ) ? array_filter( $_POST['hidden'] ) : array() );
				$export_type = ( isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '' );
				$types = array_keys( woo_ce_return_export_types() );
				// Check we are saving against a valid export type
				if( in_array( $export_type, $types ) ) {
					woo_ce_update_option( $export_type . '_labels', $fields );
					woo_ce_update_option( $export_type . '_hidden', $hidden );
					$message = __( 'Changes have been saved.', 'woo_ce' );
					woo_cd_admin_notice( $message );
				} else {
					$message = __( 'Changes could not be saved as we could not detect a valid export type.', 'woo_ce' );
					woo_cd_admin_notice( $message, 'error' );
				}
				break;

		}

	}
	add_action( 'admin_init', 'woo_cd_admin_init', 11 );

	// HTML templates and form processor for Doo Product Exporter screen
	function woo_cd_html_page() {

		global $wpdb, $export;

		$title = apply_filters( 'woo_ce_template_header', __( 'Doo Product Exporter', 'woo_ce' ) );
		doo_exporter_( $title );
		$action = woo_get_action();
		switch( $action ) {

			case 'export':
				if( WOO_CD_DEBUG ) {
					if( false === ( $export_log = get_transient( WOO_CD_PREFIX . '_debug_log' ) ) ) {
						$export_log = __( 'No export entries were found within the debug Transient, please try again with different export filters.', 'woo_ce' );
					} else {
						$export_log = base64_decode( $export_log );
					}
					delete_transient( WOO_CD_PREFIX . '_debug_log' );
					$output = '
<h3>' . sprintf( __( 'Export Details: %s', 'woo_ce' ), esc_attr( $export->filename ) ) . '</h3>
<p>' . __( 'This prints the $export global that contains the different export options and filters to help reproduce this on another instance of WordPress. Very useful for debugging blank or unexpected exports.', 'woo_ce' ) . '</p>
<textarea id="export_log">' . esc_textarea( print_r( $export, true ) ) . '</textarea>
<hr />';
					if( in_array( $export->export_format, array( 'csv', 'xls' ) ) ) {
						$output .= '
<script>
	$j(function() {
		$j(\'#export_sheet\').CSVToTable(\'\', { 
			startLine: 0';
						if( in_array( $export->export_format, array( 'xls', 'xlsx' ) ) ) {
							$output .= ',
			separator: "\t"';
						}
						$output .= '
		});
	});
</script>
<h3>' . __( 'Export', 'woo_ce' ) . '</h3>
<p>' . __( 'We use the <a href="http://code.google.com/p/jquerycsvtotable/" target="_blank"><em>CSV to Table plugin</em></a> to see first hand formatting errors or unexpected values within the export file.', 'woo_ce' ) . '</p>
<div id="export_sheet">' . esc_textarea( $export_log ) . '</div>
<p class="description">' . __( 'This jQuery plugin can fail with <code>\'Item count (#) does not match header count\'</code> notices which simply mean the number of headers detected does not match the number of cell contents.', 'woo_ce' ) . '</p>
<hr />';
					}
					$output .= '
<h3>' . __( 'Export Log', 'woo_ce' ) . '</h3>
<p>' . __( 'This prints the raw export contents and is helpful when the jQuery plugin above fails due to major formatting errors.', 'woo_ce' ) . '</p>
<textarea id="export_log" wrap="off">' . esc_textarea( $export_log ) . '</textarea>
<hr />
';
					echo $output;
				}

				woo_cd_manage_form();
				break;

			case 'update':
				// Save Custom Product Meta
				if( isset( $_POST['custom_products'] ) ) {
					$custom_products = $_POST['custom_products'];
					$custom_products = explode( "\n", trim( $custom_products ) );
					$size = count( $custom_products );
					if( !empty( $size ) ) {
						for( $i = 0; $i < $size; $i++ )
							$custom_products[$i] = sanitize_text_field( trim( $custom_products[$i] ) );
						woo_ce_update_option( 'custom_products', $custom_products );
					}
				}
				// Save Custom Attributes
				if( isset( $_POST['custom_attributes'] ) ) {
					$custom_attributes = $_POST['custom_attributes'];
					$custom_attributes = explode( "\n", trim( $custom_attributes ) );
					$size = count( $custom_attributes );
					if( !empty( $size ) ) {
						for( $i = 0; $i < $size; $i++ )
							$custom_attributes[$i] = sanitize_text_field( trim( $custom_attributes[$i] ) );
						woo_ce_update_option( 'custom_attributes', $custom_attributes );
					}
				}
				// Save Custom Product Add-ons
				if( isset( $_POST['custom_product_addons'] ) ) {
					$custom_product_addons = $_POST['custom_product_addons'];
					$custom_product_addons = explode( "\n", trim( $custom_product_addons ) );
					$size = count( $custom_product_addons );
					if( !empty( $size ) ) {
						for( $i = 0; $i < $size; $i++ )
							$custom_product_addons[$i] = sanitize_text_field( trim( $custom_product_addons[$i] ) );
						woo_ce_update_option( 'custom_product_addons', $custom_product_addons );
					}
				}
				// Save Custom Order Meta
				if( isset( $_POST['custom_orders'] ) ) {
					$custom_orders = $_POST['custom_orders'];
					$custom_orders = explode( "\n", trim( $custom_orders ) );
					$size = count( $custom_orders );
					if( $size ) {
						for( $i = 0; $i < $size; $i++ )
							$custom_orders[$i] = sanitize_text_field( trim( $custom_orders[$i] ) );
						woo_ce_update_option( 'custom_orders', $custom_orders );
					}
				}
				// Save Custom Order Item Meta
				if( isset( $_POST['custom_order_items'] ) ) {
					$custom_order_items = $_POST['custom_order_items'];
					if( !empty( $custom_order_items ) ) {
						$custom_order_items = explode( "\n", trim( $custom_order_items ) );
						$size = count( $custom_order_items );
						if( $size ) {
							for( $i = 0; $i < $size; $i++ )
								$custom_order_items[$i] = sanitize_text_field( trim( $custom_order_items[$i] ) );
							woo_ce_update_option( 'custom_order_items', $custom_order_items );
						}
					} else {
						woo_ce_update_option( 'custom_order_items', '' );
					}
				}
				// Save Custom User Meta
				if( isset( $_POST['custom_users'] ) ) {
					$custom_users = $_POST['custom_users'];
					$custom_users = explode( "\n", trim( $custom_users ) );
					$size = count( $custom_users );
					if( $size ) {
						for( $i = 0; $i < $size; $i++ )
							$custom_users[$i] = sanitize_text_field( trim( $custom_users[$i] ) );
						woo_ce_update_option( 'custom_users', $custom_users );
					}
				}
				// Save Custom Customer Meta
				if( isset( $_POST['custom_customers'] ) ) {
					$custom_customers = $_POST['custom_customers'];
					$custom_customers = explode( "\n", trim( $custom_customers ) );
					$size = count( $custom_customers );
					if( $size ) {
						for( $i = 0; $i < $size; $i++ )
							$custom_customers[$i] = sanitize_text_field( trim( $custom_customers[$i] ) );
						woo_ce_update_option( 'custom_customers', $custom_customers );
					}
				}

				$message = __( 'Custom Fields saved. You can now select those additional fields from the Export Fields list.', 'woo_ce' );
				woo_cd_admin_notice_html( $message );
				woo_cd_manage_form();
				break;

			default:
				woo_cd_manage_form();
				break;

		}
		woo_cd_template_footer();

	}

	// HTML template for Export screen
	function woo_cd_manage_form() {

		$tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false );
		// If Skip Overview is set then jump to Export screen
		if( $tab == false && woo_ce_get_option( 'skip_overview', false ) )
			$tab = 'export';

		woo_ce_fail_notices();

		include_once( WOO_CD_PATH . 'templates/admin/tabs.php' );

	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function woo_ce_cron() {

		$action = woo_get_action();
		// This is where the CRON export magic happens
		if( $action == 'woo_ce-cron' ) {

			// Check that Store Exporter is installed and activated or jump out
			if( !function_exists( 'woo_ce_get_option' ) )
				return;

			// Return silent response and record to error log if CRON support is disabled or bad secret key provided
			if( woo_ce_get_option( 'enable_cron', 0 ) == 0 ) {
				error_log( '[doo--product-exporter] Error: Failed CRON access, CRON is disabled' );
				return;
			}
			$key = ( isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '' );
			if( $key <> woo_ce_get_option( 'secret_key', '' ) ) {
				$ip_address = woo_ce_get_visitor_ip_address();
				error_log( sprintf( '[doo--product-exporter] Error: Failed CRON attempt from %s, incorrect secret key' ), $ip_address );
				return;
			}

			$gui = ( isset( $_GET['gui'] ) ? absint( $_GET['gui'] ) : 0 );
			$response = ( isset( $_GET['response'] ) ? sanitize_text_field( $_GET['response'] ) : '' );
			// Output to screen in friendly design with on-screen error responses
			if( $gui == 1 ) {
				woo_ce_cron_export( 'gui' );
			// Return export download to browser in different expected formats, uses error_log for error responses
			} else if( $gui == 0 && in_array( $response, array( 'download', 'raw', 'url', 'file', 'email', 'post', 'ftp' ) ) ) {
				switch( $response ) {

					// Return export download to browser
					case 'download':
						echo woo_ce_cron_export( 'download' );
						break;

					// Return raw download to browser
					case 'raw':
						echo woo_ce_cron_export( 'raw' );
						break;

					// Return the URI to the saved export
					case 'url':
						echo woo_ce_cron_export( 'url' );
						break;

					// Return the system path to the saved export
					case 'file':
						echo woo_ce_cron_export( 'file' );
						break;

					case 'email':
						echo woo_ce_cron_export( 'email' );
						break;

					case 'post':
						echo woo_ce_cron_export( 'post' );
						break;

					case 'ftp':
						echo woo_ce_cron_export( 'ftp' );
						break;

				}
			} else {
				// Return simple binary response
				echo absint( woo_ce_cron_export() );
			}
			exit();

		}

	}
	add_action( 'init', 'woo_ce_cron' );	

	/* End of: Storefront */

}

// Run this function within the WordPress Administration and storefront to ensure scheduled exports happen
function woo_ce_init() {

	// Check that Doo Product Exporter is installed and activated or jump out
	if( !function_exists( 'woo_ce_get_option' ) )
		return;

	// Check if scheduled exports is enabled
	if( woo_ce_get_option( 'enable_auto', 0 ) == 1 ) {

		// Add custom schedule for automated exports
		add_filter( 'cron_schedules', 'woo_ce_cron_schedules' );

		woo_ce_cron_activation();

	}

	// Check if trigger export on New Order is enabled
	if( woo_ce_get_option( 'enable_trigger_new_order', 0 ) == 1 ) {

		// There are other WordPress Actions (woocommerce_new_order, woocommerce_api_create_order) we can hook into but woocommerce_checkout_update_order_meta is the only one where we can access the Order Items
		add_action( 'woocommerce_checkout_update_order_meta', 'woo_ce_trigger_new_order_export', 10, 1 );

	}

	// Once every x minutes WP-CRON will run the automated export
	add_action( 'woo_ce_auto_export_schedule', 'woo_ce_auto_export' );

}
add_action( 'init', 'woo_ce_init', 11 );
?>