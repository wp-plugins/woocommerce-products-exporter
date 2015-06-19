<?php
include_once( WOO_CD_PATH . 'includes/product.php' );
include_once( WOO_CD_PATH . 'includes/category.php' );
include_once( WOO_CD_PATH . 'includes/tag.php' );
include_once( WOO_CD_PATH . 'includes/brand.php' );
include_once( WOO_CD_PATH . 'includes/order.php' );
include_once( WOO_CD_PATH . 'includes/customer.php' );
include_once( WOO_CD_PATH . 'includes/user.php' );
include_once( WOO_CD_PATH . 'includes/coupon.php' );
include_once( WOO_CD_PATH . 'includes/subscription.php' );
include_once( WOO_CD_PATH . 'includes/product_vendor.php' );
include_once( WOO_CD_PATH . 'includes/commission.php' );
include_once( WOO_CD_PATH . 'includes/shipping_class.php' );
include_once( WOO_CD_PATH . 'includes/cron.php' );

// Check if we are using PHP 5.3 and above
if( version_compare( phpversion(), '5.3' ) >= 0 )
	include_once( WOO_CD_PATH . 'includes/legacy.php' );
include_once( WOO_CD_PATH . 'includes/formatting.php' );

include_once( WOO_CD_PATH . 'includes/export-csv.php' );
include_once( WOO_CD_PATH . 'includes/export-xml.php' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( WOO_CD_PATH . 'includes/admin.php' );
	include_once( WOO_CD_PATH . 'includes/settings.php' );

	// Displays a HTML notice when a WordPress or Store Exporter error is encountered
	function woo_ce_fail_notices() {

		$troubleshooting_url = '';

		// If the failed flag is set then prepare for an error notice
		if( isset( $_GET['failed'] ) ) {
			$message = '';
			if( isset( $_GET['message'] ) )
				$message = urldecode( $_GET['message'] );
			if( $message )
				$message = sprintf( __( 'A WordPress or server error caused the exporter to fail, the exporter was provided with a reason: <em>%s</em>', 'woo_ce' ), $message ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			else
				$message = __( 'A WordPress or server error caused the exporter to fail, no reason was provided, please get in touch so we can reproduce and resolve this with you.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			woo_cd_admin_notice_html( $message, 'error' );
		}

		// Displays a HTML notice where the memory allocated to WordPress falls below 64MB
		if( !woo_ce_get_option( 'dismiss_execution_time_prompt', 0 ) ) {
			$max_execution_time = absint( ini_get( 'max_execution_time' ) );
			$response = ini_set( 'max_execution_time', 120 );
			if( $response == false || ( $response != $max_execution_time ) ) {
				$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss_execution_time_prompt' ) );
				$message = sprintf( __( 'We could not override the PHP configuration option <code>max_execution_time</code>, this will limit the size of possible exports. See: <a href="%s" target="_blank">Increasing PHP max_execution_time configuration option</a>', 'woo_ce' ), $troubleshooting_url ) . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
				woo_cd_admin_notice_html( $message, 'error' );
			}
			ini_set( 'max_execution_time', $max_execution_time );
		}

		// Displays a HTML notice where the memory allocated to WordPress falls below 64MB
		if( !woo_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = absint( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss_memory_prompt' ) );
				$message = sprintf( __( 'We recommend setting memory to at least %dMB, your site has only %dMB allocated to it. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'woo_ce' ), $minimum_memory_limit, $memory_limit, $troubleshooting_url ) . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
				woo_cd_admin_notice_html( $message, 'error' );
			}
		}

		// Displays a HTML notice if PHP 5.2 is installed
		if( version_compare( phpversion(), '5.3', '<' ) && !woo_ce_get_option( 'dismiss_php_legacy', 0 ) ) {
			$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss_php_legacy' ) );
			$message = sprintf( __( 'Your PHP version (%s) is not supported and is very much out of date, since 2010 all users are strongly encouraged to upgrade to PHP 5.3+ and above. Contact your hosting provider to make this happen. See: <a href="%s" target="_blank">Migrating from PHP 5.2 to 5.3</a>', 'woo_ce' ), phpversion(), $troubleshooting_url ) . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
			woo_cd_admin_notice_html( $message, 'error' );
		}

		// Displays HTML notice if there are more than 2500 Subscriptions
		if( !woo_ce_get_option( 'dismiss_subscription_prompt', 0 ) ) {
			if( class_exists( 'WC_Subscriptions' ) ) {
				if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
					// Does this store have roughly more than 3000 Subscriptions
					if( WC_Subscriptions::is_large_site() ) {
						$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss_subscription_prompt' ) );
						$message = __( 'We\'ve detected the <em>is_large_site</em> flag has been set within WooCommerce Subscriptions. Please get in touch if exports are incomplete as we need to spin up an alternative export process to export Subscriptions from large stores.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)' . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
						woo_cd_admin_notice_html( $message, 'error' );
					}
				}
			}
		}

		// If the export failed the WordPress Transient will still exist
		if( get_transient( WOO_CD_PREFIX . '_running' ) ) {
			$message = __( 'A WordPress or server error caused the exporter to fail with a blank screen, this is usually isolated to a memory or timeout issue, please get in touch so we can reproduce and resolve this.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			woo_cd_admin_notice_html( $message, 'error' );
			delete_transient( WOO_CD_PREFIX . '_running' );
		}

		// If the woo_cd_exported WordPress Option exists then an Order export failed and we should roll back changes
		if( woo_ce_get_option( 'exported', false ) ) {
			$orders = woo_ce_get_option( 'exported', false );
			if( !empty( $orders ) ) {
				foreach( $orders as $order_id ) {
					// Remove the export flag
					delete_post_meta( $order_id, '_woo_cd_exported' );
					// Add an additional Order Note
					$order = woo_ce_get_order_wc_data( $order_id );
					$note = __( 'Order export flag was cleared due to a failed export.', 'woo_ce' );
					$order->add_order_note( $note );
					unset( $order );
				}
			}
			unset( $orders, $order_id );
			$message = __( 'It looks like a previous Orders export failed before it could complete, we have removed the exported flag assigned to those Orders so they are not excluded from your next export using <em>Filter Orders by Order Date</em> > <em>Since last export</em>.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			woo_cd_admin_notice_html( $message );
			delete_option( WOO_CD_PREFIX . '_exported' );
		}

/*
		// If the sed-exports folder within Uploads does not exist
		// @mod - Will work on the next minor release
		$upload_dir =  wp_upload_dir();
		if( !file_exists( $upload_dir['basedir'] . '/sed-exports/.htaccess' ) && woo_ce_get_option( 'dismiss_secure_archives_prompt', false ) == false ) {
			$dismiss_url = esc_url( add_query_arg( 'action', 'dismiss_secure_archives_prompt' ) );
			$action_url = esc_url( add_query_arg( 'action', 'relocate_archived_exports' ) );
			$message = __( 'It looks like your exports are out in the open, let\'s move them to a secure location within the WordPres Uploads directory.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)' . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>' . '<br /><br /><a href="' . $action_url . '" class="button-primary">' . __( 'Re-locate archived exports', 'woo_ce' ) . '</a>';
			woo_cd_admin_notice_html( $message, 'error' );
		}
*/

	}

	// Saves the state of Export fields for next export
	function woo_ce_save_fields( $type = '', $fields = array(), $sorting = array() ) {

		// Default fields
		if( $fields == false && !is_array( $fields ) )
			$fields = array();
		$types = array_keys( woo_ce_return_export_types() );
		if( in_array( $type, $types ) && !empty( $fields ) ) {
			woo_ce_update_option( $type . '_fields', array_map( 'sanitize_text_field', $fields ) );
			woo_ce_update_option( $type . '_sorting', array_map( 'absint', $sorting ) );
		}

	}

	// Returns number of an Export type prior to export, used on Store Exporter screen
	function woo_ce_return_count( $export_type = '', $args = array() ) {

		global $wpdb;

		$count_sql = null;
		$woocommerce_version = woo_get_woo_version();

		switch( $export_type ) {

			case 'product':
				$post_type = array( 'product', 'product_variation' );
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'fields' => 'ids',
					'suppress_filters' => 1
				);
				$count_query = new WP_Query( $args );
				$count = $count_query->found_posts;
				break;

			case 'category':
				$term_taxonomy = 'product_cat';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'tag':
				$term_taxonomy = 'product_tag';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'brand':
				$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'order':
				$post_type = 'shop_order';
				// Check if this is a WooCommerce 2.2+ instance (new Post Status)
				if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
					$post_status = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_ce_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
				else
					$post_status = apply_filters( 'woo_ce_order_post_status', woo_ce_post_statuses() );
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'post_status' => $post_status,
					'fields' => 'ids'
				);
				$count_query = new WP_Query( $args );
				$count = $count_query->found_posts;
				break;

			case 'customer':
				if( $users = woo_ce_return_count( 'user' ) > 1000 ) {
					$count = sprintf( '~%s+', 1000 );
				} else {
					$post_type = 'shop_order';
					$args = array(
						'post_type' => $post_type,
						'posts_per_page' => -1,
						'fields' => 'ids'
					);
					// Check if this is a WooCommerce 2.2+ instance (new Post Status)
					if( version_compare( $woocommerce_version, '2.2' ) >= 0 ) {
						$args['post_status'] = apply_filters( 'woo_ce_customer_post_status', array( 'wc-pending', 'wc-on-hold', 'wc-processing', 'wc-completed' ) );
					} else {
						$args['post_status'] = apply_filters( 'woo_ce_customer_post_status', woo_ce_post_statuses() );
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'shop_order_status',
								'field' => 'slug',
								'terms' => array( 'pending', 'on-hold', 'processing', 'completed' )
							),
						);
					}
					$order_ids = new WP_Query( $args );
					$count = $order_ids->found_posts;
					if( $count > 100 ) {
						$count = sprintf( '~%s', $count );
					} else {
						$customers = array();
						if( $order_ids->posts ) {
							foreach( $order_ids->posts as $order_id ) {
								$email = get_post_meta( $order_id, '_billing_email', true );
								if( !in_array( $email, $customers ) )
									$customers[$order_id] = $email;
								unset( $email );
							}
							$count = count( $customers );
						}
					}
				}
/*
				if( false ) {
					$orders = get_posts( $args );
					if( $orders ) {
						$customers = array();
						foreach( $orders as $order ) {
							$order->email = get_post_meta( $order->ID, '_billing_email', true );
							if( empty( $order->email ) ) {
								if( $order->user_id = get_post_meta( $order->ID, '_customer_user', true ) ) {
									$user = get_userdata( $order->user_id );
									if( $user )
										$order->email = $user->user_email;
									unset( $user );
								} else {
									$order->email = '-';
								}
							}
							if( !in_array( $order->email, $customers ) ) {
								$customers[$order->ID] = $order->email;
								$count++;
							}
						}
						unset( $orders, $order );
					}
				}
*/
				break;

			case 'user':
				if( $users = count_users() )
					$count = $users['total_users'];
				break;

			case 'coupon':
				$post_type = 'shop_coupon';
				if( post_type_exists( $post_type ) )
					$count = wp_count_posts( $post_type );
				break;

			case 'subscription':
				$count = 0;
				// Check that WooCommerce Subscriptions exists
				if( class_exists( 'WC_Subscriptions' ) ) {
					if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
						// Does this store have roughly more than 3000 Subscriptions
						if( false === WC_Subscriptions::is_large_site() ) {
							if( class_exists( 'WC_Subscriptions_Manager' ) ) {
								// Check that the get_all_users_subscriptions() function exists
								if( method_exists( 'WC_Subscriptions_Manager', 'get_all_users_subscriptions' ) ) {
									if( $subscriptions = WC_Subscriptions_Manager::get_all_users_subscriptions() ) {
										foreach( $subscriptions as $key => $user_subscription ) {
											if( !empty( $user_subscription ) ) {
												foreach( $user_subscription as $subscription )
													$count++;
											}
										}
										unset( $subscriptions, $subscription, $user_subscription );
									}
								}
							}
						} else {
							if( method_exists( 'WC_Subscriptions', 'get_total_subscription_count' ) )
								$count = WC_Subscriptions::get_total_subscription_count();
							else
								$count = "~2500";
						}
					}
				}
				break;

			case 'product_vendor':
				$term_taxonomy = 'shop_vendor';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'commission':
				$post_type = 'shop_commission';
				if( post_type_exists( $post_type ) )
					$count = wp_count_posts( $post_type );
				break;

			case 'shipping_class':
				$term_taxonomy = 'product_shipping_class';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'attribute':
				$attributes = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
				$count = count( $attributes );
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				if( is_object( $count ) ) {
					$count = (array)$count;
					$count = (int)array_sum( $count );
				}
				return $count;
			} else {
				if( $count_sql )
					$count = $wpdb->get_var( $count_sql );
				else
					$count = 0;
			}
			return $count;
		} else {
			return 0;
		}

	}

	// In-line display of export file and export details when viewed via WordPress Media screen
	function woo_ce_read_export_file( $post = false ) {

		if( empty( $post ) ) {
			if( isset( $_GET['post'] ) )
				$post = get_post( $_GET['post'] );
		}

		if( $post->post_type != 'attachment' )
			return false;

		// Check if the Post matches one of our Post Mime Types
		if( !in_array( $post->post_mime_type, array( 'text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/xml', 'application/rss+xml' ) ) )
			return false;

		$filepath = get_attached_file( $post->ID );

		// We can only read CSV and XML file types, the others are encoded
		if( in_array( $post->post_mime_type, array( 'text/csv', 'application/xml', 'application/rss+xml' ) ) ) {

			$contents = __( 'No export entries were found, please try again with different export filters.', 'woo_ce' );
			if( file_exists( $filepath ) ) {
				$contents = file_get_contents( $filepath );
			} else {
				// This resets the _wp_attached_file Post meta key to the correct value
				update_attached_file( $post->ID, $post->guid );
				// Try grabbing the file contents again
				$filepath = get_attached_file( $post->ID );
				if( file_exists( $filepath ) ) {
					$handle = fopen( $filepath, "r" );
					$contents = stream_get_contents( $handle );
					fclose( $handle );
				}
			}
			if( !empty( $contents ) )
				include_once( WOO_CD_PATH . 'templates/admin/media-csv_file.php' );

		}

		// We can still show the Export Details for any supported Post Mime Type
		$export_type = get_post_meta( $post->ID, '_woo_export_type', true );
		$columns = get_post_meta( $post->ID, '_woo_columns', true );
		$rows = get_post_meta( $post->ID, '_woo_rows', true );
		$start_time = get_post_meta( $post->ID, '_woo_start_time', true );
		$end_time = get_post_meta( $post->ID, '_woo_end_time', true );
		$idle_memory_start = get_post_meta( $post->ID, '_woo_idle_memory_start', true );
		$data_memory_start = get_post_meta( $post->ID, '_woo_data_memory_start', true );
		$data_memory_end = get_post_meta( $post->ID, '_woo_data_memory_end', true );
		$idle_memory_end = get_post_meta( $post->ID, '_woo_idle_memory_end', true );

		include_once( WOO_CD_PATH . 'templates/admin/media-export_details.php' );

	}
	add_action( 'edit_form_after_editor', 'woo_ce_read_export_file' );

	// Returns label of Export type slug used on Store Exporter screen
	function woo_ce_export_type_label( $export_type = '', $echo = false ) {

		$output = '';
		if( !empty( $export_type ) ) {
			$export_types = woo_ce_return_export_types();
			if( array_key_exists( $export_type, $export_types ) )
				$output = $export_types[$export_type];
		}
		if( $echo )
			echo $output;
		else
			return $output;

	}

	// Returns a list of archived exports
	function woo_ce_get_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array( 'text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/xml', 'application/rss+xml' ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1
		);
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( !empty( $filter ) )
				$args['meta_value'] = $filter;
		}
		$files = get_posts( $args );
		return $files;

	}

	function woo_ce_nuke_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array( 'text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/xml', 'application/rss+xml' ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$post_query = new WP_Query( $args );
		if( !empty( $post_query->found_posts ) ) {
			foreach( $post_query->posts as $post )
				wp_delete_attachment( $post, true );
			return true;
		}

	}

	// Returns an archived export with additional details
	function woo_ce_get_archive_file( $file = '' ) {

		$wp_upload_dir = wp_upload_dir();
		$file->export_type = get_post_meta( $file->ID, '_woo_export_type', true );
		$file->export_type_label = woo_ce_export_type_label( $file->export_type );
		if( empty( $file->export_type ) )
			$file->export_type = __( 'Unassigned', 'woo_ce' );
		if( empty( $file->guid ) )
			$file->guid = $wp_upload_dir['url'] . '/' . basename( $file->post_title );
		$file->post_mime_type = get_post_mime_type( $file->ID );
		if( !$file->post_mime_type )
			$file->post_mime_type = __( 'N/A', 'woo_ce' );
		$file->media_icon = wp_get_attachment_image( $file->ID, array( 80, 60 ), true );
		if( $author_name = get_user_by( 'id', $file->post_author ) )
			$file->post_author_name = $author_name->display_name;
		$file->post_date = woo_ce_format_archive_date( $file->ID );
		unset( $author_name, $t_time, $time );
		return $file;

	}

	// HTML template for displaying the current export type filter on the Archives screen
	function woo_ce_archives_quicklink_current( $current = '' ) {

		$output = '';
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( $filter == $current )
				$output = ' class="current"';
		} else if( $current == 'all' ) {
			$output = ' class="current"';
		}
		echo $output;

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_archives_quicklink_count( $type = '' ) {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'post_status' => 'any',
			'fields' => 'ids'
		);
		if( !empty( $type ) )
			$args['meta_value'] = $type;
		$post_query = new WP_Query( $args );
		return absint( $post_query->found_posts );

	}

	/* End of: WordPress Administration */

}

// Export process for CSV file
function woo_ce_export_dataset( $export_type = null, &$output = null ) {

	global $export;

	$separator = $export->delimiter;
	$line_ending = woo_ce_get_line_ending();
	$export->columns = array();
	$export->total_rows = 0;
	$export->total_columns = 0;

	$troubleshooting_url = '';

	set_transient( WOO_CD_PREFIX . '_running', time(), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );

	// Load up the fatal error notice if we 500 Internal Server Error (memory), hit a server timeout or encounter a fatal PHP error
	add_action( 'shutdown', 'woo_ce_fatal_error' );

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	switch( $export_type ) {

		// Products
		case 'product':
			$fields = woo_ce_get_product_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_product_field( $key );
			}
			if( $export->gallery_unique ) {
				$export->fields = woo_ce_unique_product_gallery_fields( $export->fields );
				$export->columns = woo_ce_unique_product_gallery_columns( $export->columns, $export->fields );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $products = woo_ce_get_products( $export->args ) ) {
				$export->total_rows = count( $products );
				// XML export
				if( $export->export_format == 'xml' ) {
					if( !empty( $export->fields ) ) {
						foreach( $products as $product ) {
							$child = $output->addChild( apply_filters( 'woo_ce_export_xml_product_node', sanitize_key( $export_type ) ) );
							$product = woo_ce_get_product_data( $product, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $product->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $product->$field, $export_type, $field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( woo_ce_sanitize_xml_string( $product->$field ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $product->$field ) ) );
									}
								}
							}
						}
					}
				} else if( $export->export_format == 'rss' ) {
					// RSS export
					if( !empty( $export->fields ) ) {
						foreach( $products as $product ) {
							$child = $output->addChild( 'item' );
							$product = woo_ce_get_product_data( $product, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $field ) {
								if( isset( $product->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $product->$field ) )
											$child->addChild( sanitize_key( $field ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $product->$field ) ) );
										else
											$child->addChild( sanitize_key( $field ), esc_html( woo_ce_sanitize_xml_string( $product->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $products as $key => $product ) {
						$products[$key] = woo_ce_get_product_data( $product, $export->args, array_keys( $export->fields ) );
					}
					$output = $products;
				}
				unset( $products, $product );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Categories
		case 'category':
			$fields = woo_ce_get_category_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_category_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$category_args = array(
				'orderby' => ( isset( $export->args['category_orderby'] ) ? $export->args['category_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['category_order'] ) ? $export->args['category_order'] : 'ASC' ),
			);
			if( $categories = woo_ce_get_product_categories( $category_args ) ) {
				$export->total_rows = count( $categories );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $categories as $category ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_category_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $category->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $category->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $category->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $category->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $categories;
				}
				unset( $categories, $category );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Tags
		case 'tag':
			$fields = woo_ce_get_tag_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_tag_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$tag_args = array(
				'orderby' => ( isset( $export->args['tag_orderby'] ) ? $export->args['tag_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['tag_order'] ) ? $export->args['tag_order'] : 'ASC' ),
			);
			if( $tags = woo_ce_get_product_tags( $tag_args ) ) {
				$export->total_rows = count( $tags );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $tags as $tag ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_tag_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $tag->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $tag->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $tag->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $tag->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $tags;
				}
				unset( $tags, $tag );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Brands
		case 'brand':
			$fields = woo_ce_get_brand_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_brand_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$brand_args = array(
				'orderby' => ( isset( $export->args['brand_orderby'] ) ? $export->args['brand_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['brand_order'] ) ? $export->args['brand_order'] : 'ASC' ),
			);
			if( $brands = woo_ce_get_product_brands( $brand_args ) ) {
				$export->total_rows = count( $brands );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $brands as $brand ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_brand_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $brand->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $brand->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $brand->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $brand->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $brands;
				}
				unset( $brands, $brand );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Orders
		case 'order':
			$fields = woo_ce_get_order_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				// Do not apply Field Editor changes to the unique Order Items Formatting rule
				if( $export->args['order_items'] == 'unique' )
					remove_filter( 'woo_ce_order_fields', 'woo_ce_override_order_field_labels', 11 );
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_order_field( $key );
				// Do not apply Field Editor changes to the unique Order Items Formatting rule
				if( $export->args['order_items'] == 'unique' )
					add_filter( 'woo_ce_order_fields', 'woo_ce_override_order_field_labels', 11 );
			}
			if( $export->args['order_items'] == 'unique' ) {
				$export->fields = woo_ce_unique_order_item_fields( $export->fields );
				$export->columns = woo_ce_unique_order_item_columns( $export->columns, $export->fields );
			}
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $orders = woo_ce_get_orders( 'order', $export->args ) ) {
				$export->total_columns = $size = count( $export->columns );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $orders as $order ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_order_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$child->addAttribute( 'id', $order );
							$order = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
							if( in_array( $export->args['order_items'], array( 'combined', 'unique' ) ) ) {
								// Order items formatting: SPECK-IPHONE|INCASE-NANO|-
								foreach( array_keys( $export->fields ) as $key => $field ) {
									if( isset( $order->$field ) ) {
										if( !is_array( $field ) ) {
											if( woo_ce_is_xml_cdata( $order->$field ) )
												$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
											else
												$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
										}
									}
								}
							} else if( $export->args['order_items'] == 'individual' ) {
								// Order items formatting: SPECK-IPHONE<br />INCASE-NANO<br />-
								if( !empty( $order->order_items ) ) {
									$order->order_items_product_id = '';
									$order->order_items_variation_id = '';
									$order->order_items_sku = '';
									$order->order_items_name = '';
									$order->order_items_variation = '';
									$order->order_items_description = '';
									$order->order_items_excerpt = '';
									$order->order_items_tax_class = '';
									$order->order_items_quantity = '';
									$order->order_items_total = '';
									$order->order_items_subtotal = '';
									$order->order_items_rrp = '';
									$order->order_items_stock = '';
									$order->order_items_tax = '';
									$order->order_items_tax_subtotal = '';
									$order->order_items_type = '';
									$order->order_items_category = '';
									$order->order_items_tag = '';
									$order->order_items_total_sales = '';
									$order->order_items_weight = '';
									$order->order_items_total_weight = '';
									foreach( $order->order_items as $order_item ) {
										// Add Order Item weight to Shipping Weight
										if( $order_item->total_weight != '' )
											$order->shipping_weight = $order->shipping_weight + $order_item->total_weight;
										$order->order_items_product_id = $order_item->product_id;
										$order->order_items_variation_id = $order_item->variation_id;
										if( empty( $order_item->sku ) )
											$order_item->sku = '';
										$order->order_items_sku = $order_item->sku;
										$order->order_items_name = $order_item->name;
										$order->order_items_variation = $order_item->variation;
										$order->order_items_description = woo_ce_format_description_excerpt( $order_item->description );
										$order->order_items_excerpt = woo_ce_format_description_excerpt( $order_item->excerpt );
										$order->order_items_tax_class = $order_item->tax_class;
										$order->total_quantity += $order_item->quantity;
										$order->order_items_quantity = $order_item->quantity;
										$order->order_items_total = $order_item->total;
										$order->order_items_subtotal = $order_item->subtotal;
										$order->order_items_rrp = $order_item->rrp;
										$order->order_items_stock = $order_item->stock;
										$order->order_items_tax = $order_item->tax;
										$order->order_items_tax_subtotal = $order_item->tax_subtotal;
										$order->order_items_type = $order_item->type;
										$order->order_items_category = $order_item->category;
										$order->order_items_tag = $order_item->tag;
										$order->order_items_total_sales = $order_item->total_sales;
										$order->order_items_weight = $order_item->weight;
										$order->order_items_total_weight = $order_item->total_weight;
										$order = apply_filters( 'woo_ce_order_items_individual', $order, $order_item );
										foreach( array_keys( $export->fields ) as $key => $field ) {
											if( isset( $order->$field ) ) {
												if( !is_array( $field ) ) {
													if( woo_ce_is_xml_cdata( $order->$field ) )
														$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
													else
														$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $order->$field ) ) );
												}
											}
										}
									}
									unset( $order->order_items );
								}
							}
						}
					}
				} else {
					// PHPExcel export
					if( $export->args['order_items'] == 'individual' )
						$output = array();
					foreach( $orders as $order ) {
						if( in_array( $export->args['order_items'], array( 'combined', 'unique' ) ) ) {
							// Order items formatting: SPECK-IPHONE|INCASE-NANO|-
							$output[] = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
						} else if( $export->args['order_items'] == 'individual' ) {
							// Order items formatting: SPECK-IPHONE<br />INCASE-NANO<br />-
							$order = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
							if( !empty( $order->order_items ) ) {
								foreach( $order->order_items as $order_item ) {
									// Add Order Item weight to Shipping Weight
									if( $order_item->total_weight != '' )
										$order->shipping_weight = $order->shipping_weight + $order_item->total_weight;
									$order->order_items_product_id = $order_item->product_id;
									$order->order_items_variation_id = $order_item->variation_id;
									if( empty( $order_item->sku ) )
										$order_item->sku = '';
									$order->order_items_sku = $order_item->sku;
									$order->order_items_name = $order_item->name;
									$order->order_items_variation = $order_item->variation;
									$order->order_items_description = $order_item->description;
									$order->order_items_excerpt = $order_item->excerpt;
									$order->order_items_tax_class = $order_item->tax_class;
									$order->total_quantity += $order_item->quantity;
									$order->order_items_quantity = $order_item->quantity;
									$order->order_items_total = $order_item->total;
									$order->order_items_subtotal = $order_item->subtotal;
									$order->order_items_rrp = $order_item->rrp;
									$order->order_items_stock = $order_item->stock;
									$order->order_items_tax = $order_item->tax;
									$order->order_items_tax_subtotal = $order_item->tax_subtotal;
									$order->order_items_type = $order_item->type;
									$order->order_items_category = $order_item->category;
									$order->order_items_tag = $order_item->tag;
									$order->order_items_total_sales = $order_item->total_sales;
									$order->order_items_weight = $order_item->weight;
									$order->order_items_total_weight = $order_item->total_weight;
									$order = apply_filters( 'woo_ce_order_items_individual', $order, $order_item );
									// This fixes the Order Items for this Order Items Formatting rule
									$output[] = (object)(array)$order;
								}
							}
						}
					}
				}
				unset( $orders, $order );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Customers
		case 'customer':
			$fields = woo_ce_get_customer_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_customer_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $customers = woo_ce_get_orders( 'customer', $export->args ) ) {
				$export->total_rows = count( $customers );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $customers as $customer ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_customer_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $customer->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $customer->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $customer->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $customer->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $customers;
				}
				unset( $customers, $customer );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Users
		case 'user':
			$fields = woo_ce_get_user_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_user_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $users = woo_ce_get_users( $export->args ) ) {
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					$export->total_rows = count( $users );
					if( !empty( $export->fields ) ) {
						foreach( $users as $user ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_user_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$user = woo_ce_get_user_data( $user, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $user->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $user->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $user->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $user->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $users as $key => $user )
						$users[$key] = woo_ce_get_user_data( $user, $export->args, array_keys( $export->fields ) );
					$output = $users;
				}
				unset( $users, $user );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Coupons
		case 'coupon':
			$fields = woo_ce_get_coupon_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_coupon_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $coupons = woo_ce_get_coupons( $export->args ) ) {
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					$export->total_rows = count( $coupons );
					if( !empty( $export->fields ) ) {
						foreach( $coupons as $coupon ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_coupon_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$coupon = woo_ce_get_coupon_data( $coupon, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $coupon->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $coupon->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $coupon->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $coupon->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $coupons as $key => $coupon ) {
						$coupons[$key] = woo_ce_get_coupon_data( $coupon, $export->args, array_keys( $export->fields ) );
					}
					$output = $coupons;
				}
				unset( $coupons, $coupon );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Subscriptions
		case 'subscription':
			$fields = woo_ce_get_subscription_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_subscription_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $subscriptions = woo_ce_get_subscriptions( $export->args ) ) {
				$export->total_rows = count( $subscriptions );
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					if( !empty( $export->fields ) ) {
						foreach( $subscriptions as $subscription ) {
							if( $export->export_format == 'xml' )
							$child = $output->addChild( apply_filters( 'woo_ce_export_xml_subscription_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $subscription->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $subscription->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $subscription->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $subscription->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $subscriptions;
				}
				unset( $subscriptions, $subscription );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Product Vendors
		case 'product_vendor':
			$fields = woo_ce_get_product_vendor_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_product_vendor_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $product_vendors = woo_ce_get_product_vendors( $export->args ) ) {
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					$export->total_rows = count( $product_vendors );
					if( !empty( $export->fields ) ) {
						foreach( $product_vendors as $product_vendor ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_product_vendor_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$product_vendor = woo_ce_get_product_vendor_data( $product_vendor, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $product_vendor->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $product_vendor->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $product_vendor->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $product_vendor->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $product_vendors as $key => $product_vendor ) {
						$product_vendors[$key] = woo_ce_get_product_vendor_data( $product_vendor, $export->args, array_keys( $export->fields ) );
					}
					$output = $product_vendors;
				}
				unset( $product_vendors, $product_vendor );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Commissions
		case 'commission':
			$fields = woo_ce_get_commission_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_commission_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $commissions = woo_ce_get_commissions( $export->args ) ) {
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					$export->total_rows = count( $commissions );
					if( !empty( $export->fields ) ) {
						foreach( $commissions as $commission ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_commission_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							$commission = woo_ce_get_commission_data( $commission, $export->args, array_keys( $export->fields ) );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $commission->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $commission->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $commission->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $commission->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					foreach( $commissions as $key => $commission ) {
						$commissions[$key] = woo_ce_get_commission_data( $commission, $export->args, array_keys( $export->fields ) );
					}
					$output = $commissions;
				}
				unset( $commissions, $commission );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Shipping Classes
		case 'shipping_class':
			$fields = woo_ce_get_shipping_class_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_shipping_class_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $shipping_classes = woo_ce_get_shipping_classes( $export->args ) ) {
				// XML, RSS export
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {
					$export->total_rows = count( $shipping_classes );
					if( !empty( $export->fields ) ) {
						foreach( $shipping_classes as $shipping_class ) {
							if( $export->export_format == 'xml' )
								$child = $output->addChild( apply_filters( 'woo_ce_export_xml_shipping_class_node', sanitize_key( $export_type ) ) );
							else if( $export->export_format == 'rss' )
								$child = $output->addChild( 'item' );
							foreach( array_keys( $export->fields ) as $key => $field ) {
								if( isset( $shipping_class->$field ) ) {
									if( !is_array( $field ) ) {
										if( woo_ce_is_xml_cdata( $shipping_class->$field ) )
											$child->addChild( sanitize_key( $export->columns[$key] ) )->addCData( esc_html( woo_ce_sanitize_xml_string( $shipping_class->$field ) ) );
										else
											$child->addChild( sanitize_key( $export->columns[$key] ), esc_html( woo_ce_sanitize_xml_string( $shipping_class->$field ) ) );
									}
								}
							}
						}
					}
				} else {
					// PHPExcel export
					$output = $shipping_classes;
				}
				unset( $shipping_classes, $shipping_class );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Remove our fatal error notice so not to conflict with the CRON or scheduled export engine	
	remove_action( 'shutdown', 'woo_ce_fatal_error' );

	// Export completed successfully
	delete_transient( WOO_CD_PREFIX . '_running' );

	// Check if we're using PHPExcel or generic export engine
	if( WOO_CD_DEBUG || in_array( $export->export_format, array( 'xml', 'rss' ) ) ) {

		// Check that the export file is populated, export columns have been assigned and rows counted
		if( !empty( $output ) && $export->total_rows && $export->total_columns ) {
			if( WOO_CD_DEBUG && !in_array( $export->export_format, array( 'csv', 'xls', 'xlsx' ) ) && ( !$export->cron && !$export->scheduled_export ) ) {
				if( in_array( $export->export_format, array( 'xml', 'rss' ) ) )
					$output = woo_ce_format_xml( $output );
				$response = set_transient( WOO_CD_PREFIX . '_debug_log', base64_encode( $output ), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
				if( $response !== true ) {
					$message = __( 'The export contents were too large to store in a single WordPress transient, use the Volume offset / Limit volume options to reduce the size of your export and try again.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
					if( function_exists( 'woo_cd_admin_notice' ) )
						woo_cd_admin_notice( $message, 'error' );
					else
						error_log( sprintf( '[doo-product-exporter] woo_ce_export_dataset() - %s', $message ) );
					return;
				} else {
					return true;
				}
			} else {
				return $output;
			}
		}

	} else {
		return $output;
	}

}

function woo_ce_fatal_error() {

	global $export;

	$troubleshooting_url = '';

	$error = error_get_last();
	if( $error !== null ) {
		$message = '';
		$notice = sprintf( __( '', 'woo_ce' ), $troubleshooting_url, $error['message'], $error['file'], $error['line'] );
		if ( substr( $error['message'], 0, 22 ) === 'Maximum execution time' ) {
			$message = __( 'The server\'s maximum execution time is too low to complete this export. This is commonly due to a low timeout limit set by your hosting provider or PHP Safe Mode being enabled.', 'woo_ce' );
		} elseif ( substr( $error['message'], 0, 19 ) === 'Allowed memory size' ) {
			$message = __( 'The server\'s maximum memory size is too low to complete this export. Consider increasing available memory to WordPress or reducing the size of your export.', 'woo_ce' );
		} else if( $error['type'] === E_ERROR ) {
			$message = __( 'A fatal PHP error was encountered during the export process, we couldn\'t detect or diagnose it further.', 'woo_ce' );
		}
		if( !empty( $message ) ) {

			// Save a record to the PHP error log
			error_log( sprintf( __( '[doo-product-exporter] Fatal error: %s - PHP response: %s in %s on line %s', 'woo_ce' ), $message, $error['message'], $error['file'], $error['line'] ) );

			// Only display the message if this is a manual export
			if( ( !$export->cron && !$export->scheduled_export ) ) {
				$output = '<div id="message" class="error"><p>' . sprintf( __( '<strong>[doo-product-exporter]</strong> An unexpected error occurred. %s', 'woo_ce' ), $message . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)' ) . '</p><p>' . $notice . '</p></div>';
				echo $output;
			}

		}
	}

}

// List of Export types used on Store Exporter screen
function woo_ce_return_export_types() {

	$types = array(
		'product' => __( 'Products', 'woo_ce' ),
		'category' => __( 'Categories', 'woo_ce' ),
		'tag' => __( 'Tags', 'woo_ce' ),
		'brand' => __( 'Brands', 'woo_ce' ),
		'order' => __( 'Orders', 'woo_ce' ),
		'customer' => __( 'Customers', 'woo_ce' ),
		'user' => __( 'Users', 'woo_ce' ),
		'coupon' => __( 'Coupons', 'woo_ce' ),
		'subscription' => __( 'Subscriptions', 'woo_ce' ),
		'product_vendor' => __( 'Product Vendors', 'woo_ce' ),
		'commission' => __( 'Commission', 'woo_ce' ),
		'shipping_class' => __( 'Shipping Classes', 'woo_ce' )
		// 'attribute' => __( 'Attributes', 'woo_ce' )
	);
	$types = apply_filters( 'woo_ce_export_types', $types );
	return $types;

}

function woo_ce_generate_file_headers( $post_mime_type = 'text/csv' ) {

	global $export;

	header( sprintf( 'Content-Type: %s; charset=%s', esc_attr( $post_mime_type ), esc_attr( $export->encoding ) ) );
	header( sprintf( 'Content-Disposition: attachment; filename="%s"', $export->filename ) );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );
	header( sprintf( 'Content-Encoding: %s', esc_attr( $export->encoding ) ) );

}

// Function to generate filename of export file based on the Export type
function woo_ce_generate_filename( $export_type = '', $override = '' ) {

	// Check if a fixed filename hasn't been provided
	if( !empty( $override ) ) {
		$filename = $override;
	} else {
		// Get the filename from WordPress options
		$filename = woo_ce_get_option( 'export_filename', '%store_name%-export_%dataset%-%date%-%time%' );
		// Check for empty filename
		if( empty( $filename ) )
			$filename = '%store_name%-export_%dataset%-%date%-%time%';

		// Strip file extensions if present
		$filename = str_replace( array( '.xml', '.xls', '.csv' ), '', $filename );
		if( ( strpos( $filename, '.xml' ) !== false ) || ( strpos( $filename, '.xls' ) !== false ) || ( strpos( $filename, '.csv' ) !== false ) )
			$filename = str_replace( array( '.xml', '.xls', '.csv' ), '', $filename );

	}

	// Populate the available tags
	$date = date( 'Y_m_d' );
	$time = date( 'H_i_s' );
	$store_name = sanitize_title( get_bloginfo( 'name' ) );

	// Switch out the tags for filled values
	$filename = str_replace( '%dataset%', $export_type, $filename );
	$filename = str_replace( '%date%', $date, $filename );
	$filename = str_replace( '%time%', $time, $filename );
	$filename = str_replace( '%store_name%', $store_name, $filename );

	return $filename;

}

// Returns the Post object of the export file saved as an attachment to the WordPress Media library
function woo_ce_save_file_attachment( $filename = '', $post_mime_type = 'text/csv' ) {

	if( !empty( $filename ) ) {
		$post_type = 'woo-export';
		$args = array(
			'post_title' => $filename,
			'post_type' => $post_type,
			'post_mime_type' => $post_mime_type
		);
		$post_ID = wp_insert_attachment( $args, $filename );
		if( is_wp_error( $post_ID ) )
			error_log( sprintf( '[doo-product-exporter] save_file_attachment() - $s: %s', $filename, $result->get_error_message() ) );
		else
			return $post_ID;
	}

}

// Updates the GUID of the export file attachment to match the correct file URL
function woo_ce_save_file_guid( $post_ID, $export_type, $upload_url = '' ) {

	add_post_meta( $post_ID, '_woo_export_type', $export_type );
	if( !empty( $upload_url ) ) {
		$args = array(
			'ID' => $post_ID,
			'guid' => $upload_url
		);
		wp_update_post( $args );
	}

}

// Save critical export details against the archived export
function woo_ce_save_file_details( $post_ID ) {

	global $export;

	add_post_meta( $post_ID, '_woo_start_time', $export->start_time );
	add_post_meta( $post_ID, '_woo_idle_memory_start', $export->idle_memory_start );
	add_post_meta( $post_ID, '_woo_columns', $export->total_columns );
	add_post_meta( $post_ID, '_woo_rows', $export->total_rows );
	add_post_meta( $post_ID, '_woo_data_memory_start', $export->data_memory_start );
	add_post_meta( $post_ID, '_woo_data_memory_end', $export->data_memory_end );

}

// Update detail of existing archived export
function woo_ce_update_file_detail( $post_ID, $detail, $value ) {

	if( strstr( $detail, '_woo_' ) !== false )
		update_post_meta( $post_ID, $detail, $value );

}

// Returns a list of allowed Export type statuses, can be overridden on a per-Export type basis
function woo_ce_post_statuses( $extra_status = array(), $override = false ) {

	$output = array(
		'publish',
		'pending',
		'draft',
		'future',
		'private',
		'trash'
	);
	if( $override ) {
		$output = $extra_status;
	} else {
		if( $extra_status )
			$output = array_merge( $output, $extra_status );
	}
	return $output;

}

function woo_ce_return_mime_type_extension( $mime_type, $search_by = 'extension' ) {

	$mime_types = array(
		'csv' => 'text/csv',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xml' => 'application/xml',
		'rss' => 'application/rss+xml'
	);
	if( $search_by == 'extension' ) {
		if( isset( $mime_types[$mime_type] ) )
			return $mime_types[$mime_type];
	} else if( $search_by == 'mime_type' ) {
		if( $key = array_search( $mime_type, $mime_types ) )
			return strtoupper( $key );
	}

}

function woo_ce_add_missing_mime_type( $mime_types = array() ) {

	// Add CSV mime type if it has been removed
	if( !isset( $mime_types['csv'] ) )
		$mime_types['csv'] = 'text/csv';
	// Add XLS mime type if it has been removed
	if( !isset( $mime_types['xls'] ) )
		$mime_types['xls'] = 'application/vnd.ms-excel';
	// Add XLSX mime type if it has been removed
	if( !isset( $mime_types['xlsx'] ) )
		$mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	// Add XML mime type if it has been removed
	if( !isset( $mime_types['xml'] ) )
		$mime_types['xml'] = 'application/xml';
	// Add RSS mime type if it has been removed
	if( !isset( $mime_types['rss'] ) )
		$mime_types['rss'] = 'application/rss+xml';
	return $mime_types;

}
add_filter( 'upload_mimes', 'woo_ce_add_missing_mime_type', 10, 1 );

if( !function_exists( 'woo_ce_sort_fields' ) ) {
	function woo_ce_sort_fields( $key ) {

		return $key;

	}
}

function woo_ce_next_scheduled_export() {

	// Check that WordPress has set up a wp_cron task for our export
	if( wp_next_scheduled( 'woo_ce_auto_export_schedule' ) ) {
		$cron = ( function_exists( '_get_cron_array' ) ? _get_cron_array() : array() );
		if( !empty( $cron ) ) {
			foreach( $cron as $timestamp => $cronhooks ) {
				foreach ( (array) $cronhooks as $hook => $events ) {
					if( $hook == 'woo_ce_auto_export_schedule' )
						return human_time_diff( current_time( 'timestamp', 1 ), $timestamp );
				}
			}
		}
		unset( $cron );
	}

}

// Add Store Export to filter types on the WordPress Media screen
function woo_ce_add_post_mime_type( $post_mime_types = array() ) {

	$post_mime_types['text/csv'] = array( __( 'Store Exports (CSV)', 'woo_ce' ), __( 'Manage Store Exports (CSV)', 'woo_ce' ), _n_noop( 'Store Export - CSV <span class="count">(%s)</span>', 'Store Exports - CSV <span class="count">(%s)</span>' ) );
	$post_mime_types['application/vnd.ms-excel'] = array( __( 'Store Exports (Excel 2003)', 'woo_ce' ), __( 'Manage Store Exports (Excel 2003)', 'woo_ce' ), _n_noop( 'Store Export - Excel 2003 <span class="count">(%s)</span>', 'Store Exports - Excel 2003 <span class="count">(%s)</span>' ) );
	$post_mime_types['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] = array( __( 'Store Exports (Excel 2007)', 'woo_ce' ), __( 'Manage Store Exports (Excel 2007)', 'woo_ce' ), _n_noop( 'Store Export - Excel 2007 <span class="count">(%s)</span>', 'Store Exports - Excel 2007 <span class="count">(%s)</span>' ) );
	$post_mime_types['application/xml'] = array( __( 'Store Exports (XML)', 'woo_ce' ), __( 'Manage Store Exports (XML)', 'woo_ce' ), _n_noop( 'Store Export - XML <span class="count">(%s)</span>', 'Store Exports - XML <span class="count">(%s)</span>' ) );
	$post_mime_types['application/rss+xml'] = array( __( 'Store Exports (RSS)', 'woo_ce' ), __( 'Manage Store Exports (RSS)', 'woo_ce' ), _n_noop( 'Store Export - RSS <span class="count">(%s)</span>', 'Store Exports - RSS <span class="count">(%s)</span>' ) );
	return $post_mime_types;

}
add_filter( 'post_mime_types', 'woo_ce_add_post_mime_type' );

function woo_ce_current_memory_usage() {

	$output = '';
	if( function_exists( 'memory_get_usage' ) )
		$output = round( memory_get_usage( true ) / 1024 / 1024, 2 );
	return $output;

}

function woo_ce_get_start_of_week_day() {

	global $wp_locale;

	$output = 'Monday';
	$start_of_week = get_option( 'start_of_week', 0 );
	for( $day_index = 0; $day_index <= 6; $day_index++ ) {
		if( $start_of_week == $day_index ) {
			$output = $wp_locale->get_weekday( $day_index );
			break;
		}
	}
	return $output;

}

// Provided by Pippin Williamson, mentioned on WP Beginner (http://www.wpbeginner.com/wp-tutorials/how-to-display-a-users-ip-address-in-wordpress/)
function woo_ce_get_visitor_ip_address() {

	if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters( 'woo_ce_get_visitor_ip_address', $ip );

}

function woo_ce_format_ip_address( $ip = '' ) {

	// Check if the IP Address is just a loopback
	if( in_array( $ip, array( '::1', '127.0.0.1', 'localhost' ) ) )
		$ip = '';
	return $ip;

}

function woo_ce_get_line_ending() {

	$output = "\n";
	$line_ending_formatting = woo_ce_get_option( 'line_ending_formatting', 'windows' );
	if( $line_ending_formatting == false || $line_ending_formatting == '' ) {
		error_log( '[doo-product-exporter] Line ending formatting export option was corrupted, defaulted to windows' );
		$line_ending_formatting = 'windows';
		woo_ce_update_option( 'line_ending_formatting', 'windows' );
	}
	switch( $line_ending_formatting ) {

		case 'windows':
			$output = "\n";
			break;

		case 'mac':
			$output = "\r";
			break;

		case 'unix':
			$output = "\n";
			break;

	}
	return $output;

}

function woo_ce_is_wpml_activated() {

	if( defined( 'ICL_LANGUAGE_CODE' ) )
		return true;

}

function woo_ce_error_get_last_message() {

	$output = '-';
	if( function_exists( 'error_get_last' ) ) {
		$last_error = error_get_last();
		if( isset( $last_error ) && isset( $last_error['message'] ) ) {
			$output = $last_error['message'];
		}
		unset( $last_error );
	}
	return $output;

}

function woo_ce_get_option( $option = null, $default = false, $allow_empty = false ) {

	$output = false;
	if( $option !== null ) {
		$separator = '_';
		$output = get_option( WOO_CD_PREFIX . $separator . $option, $default );
		if( $allow_empty == false && $output != 0 && ( $output == false || $output == '' ) )
			$output = $default;
	}
	return $output;

}

function woo_ce_update_option( $option = null, $value = null ) {

	$output = false;
	if( $option !== null && $value !== null ) {
		$separator = '_';
		$output = update_option( WOO_CD_PREFIX . $separator . $option, $value );
	}
	return $output;

}
?>