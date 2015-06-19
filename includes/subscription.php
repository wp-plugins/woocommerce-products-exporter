<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Filter Subscriptions by Subscription Status widget on Store Exporter screen
	function woo_ce_subscriptions_filter_by_subscription_status() {

		$subscription_statuses = woo_ce_get_subscription_statuses();

		ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-status" /> <?php _e( 'Filter Subscriptions by Subscription Status', 'woo_ce' ); ?></label></p>
<div id="export-subscriptions-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $subscription_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Subscription Status...', 'woo_ce' ); ?>" name="subscription_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $subscription_statuses as $key => $subscription_status ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $subscription_status; ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Subscription Status\'s have been found.', 'woo_ce' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Status options you want to filter exported Subscriptions by. Default is to include all Subscription Status options.', 'woo_ce' ); ?></p>
</div>
<!-- #export-subscriptions-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Subscriptions by Subscription Product widget on Store Exporter screen
	function woo_ce_subscriptions_filter_by_subscription_product() {

		$products = woo_ce_get_subscription_products();

		ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-product" /> <?php _e( 'Filter Subscriptions by Subscription Product', 'woo_ce' ); ?></label></p>
<div id="export-subscriptions-filters-product" class="separator">
	<ul>
		<li>
<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Subscription Product...', 'woo_ce' ); ?>" name="subscription_filter_product[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>"><?php echo get_the_title( $product ); ?> (<?php printf( __( 'SKU: %s', 'woo_ce' ), get_post_meta( $product, '_sku', true ) ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Subscription Products were found.', 'woo_ce' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Product you want to filter exported Subscriptions by. Default is to include all Subscription Products.', 'woo_ce' ); ?></p>
</div>
<!-- #export-subscriptions-filters-status -->
<?php
		ob_end_flush();

	}


	/* End of: WordPress Administration */

}

function woo_ce_get_subscription_fields( $format = 'full' ) {

	$export_type = 'subscription';

	$fields = array();
	$fields[] = array(
		'name' => 'key',
		'label' => __( 'Subscription Key', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'status',
		'label' => __( 'Subscription Status', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Subscription Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user',
		'label' => __( 'User', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'email',
		'label' => __( 'E-mail Address', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'order_id',
		'label' => __( 'Order ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'order_status',
		'label' => __( 'Order Status', 'woo_ce' )
	);
	// Check if this is a pre-WooCommerce 2.2 instance
	$woocommerce_version = woo_get_woo_version();
	if( version_compare( $woocommerce_version, '2.2', '<' ) ) {
		$fields[] = array(
			'name' => 'post_status',
			'label' => __( 'Post Status', 'woo_ce' )
		);
	}
	$fields[] = array(
		'name' => 'start_date',
		'label' => __( 'Start Date', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'expiration',
		'label' => __( 'Expiration', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'end_date',
		'label' => __( 'End Date', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'trial_end_date',
		'label' => __( 'Trial End Date', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'last_payment',
		'label' => __( 'Last Payment', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'next_payment',
		'label' => __( 'Next Payment', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'renewals',
		'label' => __( 'Renewals', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_sku',
		'label' => __( 'Product SKU', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'variation_id',
		'label' => __( 'Variation ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'coupon',
		'label' => __( 'Coupon Code', 'woo_ce' )
	);
/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woo_ce' )
	);
*/

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( 'woo_ce_' . $export_type . '_fields', $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	if( $remember = woo_ce_get_option( $export_type . '_fields', array() ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['default'] = 1;
			if( !array_key_exists( $fields[$i]['name'], $remember ) )
				$fields[$i]['default'] = 0;
		}
	}

	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) )
					$output[$fields[$i]['name']] = 'on';
			}
			return $output;
			break;

		case 'full':
		default:
			$sorting = woo_ce_get_option( $export_type . '_sorting', array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				$fields[$i]['reset'] = $i;
				$fields[$i]['order'] = ( isset( $sorting[$fields[$i]['name']] ) ? $sorting[$fields[$i]['name']] : $i );
			}
			// Check if we are using PHP 5.3 and above
			if( version_compare( phpversion(), '5.3' ) >= 0 )
				usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_override_subscription_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'subscription_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_subscription_fields', 'woo_ce_override_subscription_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_subscription_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_subscription_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						break;

					case 'full':
						$output = $fields[$i];
						break;

				}
				$i = $size;
			}
		}
	}
	return $output;

}

// Adds custom Subscription columns to the Subscription fields list
function woo_ce_extend_subscription_fields( $fields = array() ) {

	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( method_exists( 'WC_Admin_Profile', 'get_customer_meta_fields' ) ) {
			$show_fields = $admin_profile->get_customer_meta_fields();
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field ) {
					$fields[] = array(
						'name' => $key,
						'label' => sprintf( apply_filters( 'woo_ce_extend_subscription_fields_wc', '%s: %s' ), $fieldset['title'], esc_html( $field['label'] ) )
					);
				}
			}
			unset( $show_fields, $fieldset, $field );
		}
	}

	// Custom Order fields
	$custom_orders = woo_ce_get_option( 'custom_orders', '' );
	if( !empty( $custom_orders ) ) {
		foreach( $custom_orders as $custom_order ) {
			if( !empty( $custom_order ) ) {
				$fields[] = array(
					'name' => $custom_order,
					'label' => woo_ce_clean_export_label( $custom_order )
				);
			}
		}
		unset( $custom_orders, $custom_order );
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => woo_ce_clean_export_label( $custom_user ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_subscription_fields_custom_user_hover', '%s: %s' ), __( 'Custom User', 'woo_ce' ), $custom_user )
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $fields;

}
add_filter( 'woo_ce_subscription_fields', 'woo_ce_extend_subscription_fields' );

// Returns a list of Subscription IDs
function woo_ce_get_subscriptions( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;

	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : -1 );
		$offset = $args['offset'];
		$subscription_status = ( isset( $args['subscription_status'] ) ? $args['subscription_status'] : array() );
		$subscription_product = ( isset( $args['subscription_product'] ) ? $args['subscription_product'] : array() );
	}

	$troubleshooting_url = '';

	// @mod - Will migrate to a WP_Query solution once feature is stable
/*
	$limit_volume = -1;
	$offset = 0;

	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = $args['offset'];
		$orderby = ( isset( $args['subscription_orderby'] ) ? $args['subscription_orderby'] : 'ID' );
		$order = ( isset( $args['subscription_order'] ) ? $args['subscription_order'] : 'ASC' );
	}
	$post_type = 'shop_order';
	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'post_status' => apply_filters( 'woo_ce_subscription_post_status', 'publish' ),
		'fields' => 'ids'
	);
	$subscription_ids = new WP_Query( $args );
	if( $subscription_ids->posts ) {
		foreach( $subscription_ids->posts as $subscription_id )
			$subscriptions[] = $subscription_id;
		$export->total_rows = count( $subscriptions );
	}
*/
	$output = array();

	// Check that WooCommerce Subscriptions exists
	if( !class_exists( 'WC_Subscriptions' ) || !class_exists( 'WC_Subscriptions_Manager' ) ) {
		$message = __( 'The WooCommerce Subscriptions class <code>WC_Subscriptions</code> or <code>WC_Subscriptions_Manager</code> could not be found, this is required to export Subscriptions.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
		woo_cd_admin_notice( $message, 'error' );
		return;
	} else {
		// Check that the get_all_users_subscriptions() function exists
		if( !method_exists( 'WC_Subscriptions_Manager', 'get_all_users_subscriptions' ) ) {
			$message = __( 'The WooCommerce Subscriptions method <code>WC_Subscriptions_Manager->get_all_users_subscriptions()</code> could not be found, this is required to export Subscriptions.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			woo_cd_admin_notice( $message, 'error' );
			return;
		}
	}

	if( class_exists( 'WC_Subscriptions' ) ) {
		if( method_exists( 'WC_Subscriptions', 'get_subscriptions' ) ) {

			$args = array(
				'subscriptions_per_page' => $limit_volume,
				'offset' => $offset
			);

			// Allow other developers to bake in their own filters
			$args = apply_filters( 'woo_ce_get_subscriptions_args', $args );

			if( $subscriptions = WC_Subscriptions::get_subscriptions( $args ) ) {
				$subscription_statuses = woo_ce_get_subscription_statuses();
				foreach( $subscriptions as $subscription ) {
					$subscription = woo_ce_get_subscription_data( $subscription );
					$key = $subscription['key'];
					$output[$key] = apply_filters( 'woo_ce_subscription', (object)$subscription );
					// Filter by Subscription Status
					if( !empty( $subscription_status ) ) {
						if( !in_array( strtolower( $subscription['status'] ), $subscription_status ) ) {
							unset( $output[$key] );
							continue;
						}
					}
					// Filter by Subscription Product
					if( !empty( $subscription_product ) ) {
						if( !in_array( $subscription['product_id'], $subscription_product ) ) {
							unset( $output[$key] );
							continue;
						}
					}
				}
			}

		}
	}
	return $output;

}

function woo_ce_get_subscription_data( $subscription ) {

	$order = woo_ce_get_order_wc_data( $subscription['order_id'] );
	$order_item = woo_ce_get_subscription_order_item( $subscription['order_id'], $subscription['product_id'] );
	$product = woo_ce_get_subscription_product( $order, $order_item );
	$subscription['key'] = woo_ce_get_subscription_key( $subscription['order_id'], $subscription['product_id'] );
	$subscription['name'] = $order_item['name'];
	if( isset( $product->variation_data ) )
		$subscription['name'] = ( function_exists( 'woocommerce_get_formatted_variation' ) ? woocommerce_get_formatted_variation( $product->variation_data, true ) : $subscription['name'] );
	else
		$subscription['variation_id'] = '';
	$subscription['order_status'] = woo_ce_format_order_status( $order->status );
	$subscription['post_status'] = ucwords( $order->post_status );
	$subscription['user_id'] = get_post_meta( $subscription['order_id'], '_customer_user', true );
	$subscription['user'] = woo_ce_get_username( $subscription['user_id'] );
	$user = woo_ce_get_user_data( $subscription['user_id'] );
	$subscription['email'] = ( isset( $user->email ) ? $user->email : '' );
	unset( $user );
	$subscription['status'] = ( isset( $subscription_statuses[$subscription['status']] ) ? $subscription_statuses[$subscription['status']] : $subscription['status'] );
	$subscription['start_date'] = date_i18n( woocommerce_date_format(), strtotime( $order_item['subscription_start_date'] ) );
	$subscription['expiration'] = ( !empty( $subscription['expiry_date'] ) ? woo_ce_format_subscription_date( $subscription['expiry_date'] ) : __( 'Never', 'woocommerce-subscriptions' ) );
	$subscription['end_date'] = ( !empty( $order_item['subscription_expiry_date'] ) ? date_i18n( woocommerce_date_format(), strtotime( $order_item['subscription_expiry_date'] ) ) : __( 'Not yet ended', 'woocommerce-subscriptions' ) );
	$subscription['trial_end_date'] = ( !empty( $order_item['subscription_trial_expiry_date'] ) ? date_i18n( woocommerce_date_format(), strtotime( $order_item['subscription_trial_expiry_date'] ) ) : '-' );
	$subscription['last_payment'] = ( !empty( $subscription['last_payment_date'] ) ? woo_ce_format_subscription_date( $subscription['last_payment_date'] ) : '-' );
	$subscription['next_payment'] = woo_ce_get_subscription_next_payment( $subscription['key'], $subscription['user_id'] );
	$subscription['renewals'] = woo_ce_get_subscription_renewals( $subscription['order_id'] );
	if( method_exists( $product, 'get_sku' ) )
		$subscription['product_sku'] = $product->get_sku();
	$subscription['coupon'] = woo_ce_get_order_assoc_coupon( $subscription['order_id'] );
	return $subscription;

}

// Populate Subscription details for export of 3rd party Plugins
function woo_ce_subscription_extend( $subscription ) {

	// WooCommerce User Profile fields
	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( $show_fields = $admin_profile->get_customer_meta_fields() ) {
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field )
					$subscription->{$key} = esc_attr( get_user_meta( $subscription->user_id, $key, true ) );
			}
		}
		unset( $show_fields, $fieldset, $field );
	}

	// Custom Order fields
	$custom_orders = woo_ce_get_option( 'custom_orders', '' );
	if( !empty( $custom_orders ) ) {
		foreach( $custom_orders as $custom_order ) {
			if( !empty( $custom_order ) && !isset( $subscription->{$custom_order} ) )
				$subscription->{$custom_order} = esc_attr( get_post_meta( $subscription->order_id, $custom_order, true ) );
		}
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) && !isset( $subscription->{$custom_user} ) ) {
				$subscription->{$custom_user} = woo_ce_format_custom_meta( get_user_meta( $subscription->user_id, $custom_user, true ) );
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $subscription;

}
add_filter( 'woo_ce_subscription', 'woo_ce_subscription_extend' );

function woo_ce_get_subscription_statuses() {

	$subscription_statuses = array(
		'active'    => __( 'Active', 'woocommerce-subscriptions' ),
		'cancelled' => __( 'Cancelled', 'woocommerce-subscriptions' ),
		'expired'   => __( 'Expired', 'woocommerce-subscriptions' ),
		'pending'   => __( 'Pending', 'woocommerce-subscriptions' ),
		'failed'   => __( 'Failed', 'woocommerce-subscriptions' ),
		'on-hold'   => __( 'On-hold', 'woocommerce-subscriptions' ),
		'trash'     => __( 'Deleted', 'woo_ce' ),
	);
	return apply_filters( 'woo_ce_subscription_statuses', $subscription_statuses );

}

function woo_ce_get_subscription_order_item( $order_id = 0, $product_id = 0 ) {

	if( method_exists( 'WC_Subscriptions_Order', 'get_item_by_product_id' ) )
		$order_item = WC_Subscriptions_Order::get_item_by_product_id( $order_id, $product_id );
	return $order_item;

}

function woo_ce_get_subscription_product( $order = false, $order_item = false ) {

	// Check that get_product_from_item() exists within the WC_Order class
	if( method_exists( 'WC_Order', 'get_product_from_item' ) ) {
		// Check that $order and $order_item aren't empty
		if( !empty( $order ) && !empty( $order_item ) )
			$product = $order->get_product_from_item( $order_item );
	}
	return $product;

}

function woo_ce_get_subscription_key( $order_id = 0, $product_id = 0 ) {

	if( method_exists( 'WC_Subscriptions_Manager', 'get_subscription_key' ) ) {
		$key = WC_Subscriptions_Manager::get_subscription_key( $order_id, $product_id );
		return $key;
	}

}

function woo_ce_get_subscription_next_payment( $subscription_key = '', $user_id = 0 ) {

	$next_payment = '-';
	// Check that get_next_payment_date() exists within the WC_Subscriptions_Manager class
	if( method_exists( 'WC_Subscriptions_Manager', 'get_next_payment_date' ) ) {
		// Check that $subscription_key and $user_id aren't empty
		if( $subscription_key && !empty( $user_id ) ) {
			if( $next_payment_timestamp = WC_Subscriptions_Manager::get_next_payment_date( $subscription_key, $user_id, 'timestamp' ) ) {
				// Date formatting for Next Payment is provided by WooCommerce Subscriptions
				$time_diff = $next_payment_timestamp - gmdate( 'U' );
				if( $time_diff > 0 && $time_diff < 7 * 24 * 60 * 60 )
					$next_payment = sprintf( __( 'In %s', 'woocommerce-subscriptions' ), human_time_diff( $next_payment_timestamp ) );
				else
					$next_payment = date_i18n( woocommerce_date_format(), $next_payment_timestamp );
			}
		}
	}
	return $next_payment;

}

function woo_ce_format_subscription_date( $end_date = '' ) {

	// Date formatting is provided by WooCommerce Subscriptions
	$current_gmt_time = gmdate( 'U' );
	$end_date_timestamp = strtotime( $end_date );
	$time_diff = $current_gmt_time - $end_date_timestamp;
	if ( $time_diff > 0 && $time_diff < 7 * 24 * 60 * 60 )
		$end_date = sprintf( __( '%s ago', 'woocommerce-subscriptions' ), human_time_diff( $end_date_timestamp, $current_gmt_time ) );
	else
		$end_date = date_i18n( woocommerce_date_format(), $end_date_timestamp + get_option( 'gmt_offset' ) * 3600 );
	return $end_date;

}

function woo_ce_get_subscription_renewals( $order_id = 0 ) {

	$renewals = 0;
	// Check that get_renewal_order_count() exists within the WC_Subscriptions_Renewal_Order class
	if( method_exists( 'WC_Subscriptions_Renewal_Order', 'get_renewal_order_count' ) )
		$renewals = WC_Subscriptions_Renewal_Order::get_renewal_order_count( $order_id );
	return $renewals;

}

function woo_ce_get_subscription_products() {

	$term_taxonomy = 'product_type';
	$args = array(
		'post_type' => array( 'product', 'product_variation' ),
		'posts_per_page' => -1,
		'fields' => 'ids',
		'suppress_filters' => false,
		'tax_query' => array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'slug',
				'terms' => array( 'subscription', 'variable-subscription' )
			)
		)
	);
	$products = array();
	$product_ids = new WP_Query( $args );
	if( $product_ids->posts ) {
		foreach( $product_ids->posts as $product_id )
			$products[] = $product_id;
	}
	return $products;

}

function woo_ce_format_product_subscription_period_interval( $interval ) {

	$output = $interval;
	if( !empty( $interval ) ) {
		switch( $interval ) {

			case '1':
				$output = __( 'per', 'woo_ce' );
				break;

			case '2':
				$output = __( 'every 2nd', 'woo_ce' );
				break;

			case '3':
				$output = __( 'every 3rd', 'woo_ce' );
				break;

			case '4':
				$output = __( 'every 4th', 'woo_ce' );
				break;

			case '5':
				$output = __( 'every 5th', 'woo_ce' );
				break;

			case '6':
				$output = __( 'every 6th', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_subscripion_length( $length, $period = '' ) {

	$output = $length;
	if( $length == '0' ) {
		$output = __( 'all time', 'woo_ce' );
	}
	return $output;

}

function woo_ce_format_product_subscription_limit( $limit ) {

	$output = $limit;
	if( !empty( $limit ) ) {
		$limit = strtolower( $limit );
		switch( $limit ) {

			case 'active':
				$output = __( 'Active Subscription', 'woo_ce' );
				break;

			case 'any':
				$output = __( 'Any Subscription', 'woo_ce' );
				break;

			case 'no':
				$output = __( 'Do not limit', 'woo_ce' );
				break;

		}
	}
	return $output;

}
?>