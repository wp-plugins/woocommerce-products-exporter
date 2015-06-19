<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Filter Customers by Order Status widget on Store Exporter screen
	function woo_ce_customers_filter_by_status() {

		$order_statuses = woo_ce_get_order_statuses();

		ob_start(); ?>
<p><label><input type="checkbox" id="customers-filters-status" /> <?php _e( 'Filter Customers by Order Status', 'woo_ce' ); ?></label></p>
<div id="export-customers-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $order_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Order Status...', 'woo_ce' ); ?>" name="customer_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $order_statuses as $order_status ) { ?>
				<option value="<?php echo $order_status->name; ?>"><?php echo ucfirst( $order_status->name ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Order Status\'s were found.', 'woo_ce' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Order Status you want to filter exported Customers by. Default is to include all Order Status options.', 'woo_ce' ); ?></p>
</div>
<!-- #export-customers-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Customers by User Role widget on Store Exporter screen
	function woo_ce_customers_filter_by_user_role() {

		$user_roles = woo_ce_get_user_roles();

		ob_start(); ?>
<p><label><input type="checkbox" id="customers-filters-user_role" /> <?php _e( 'Filter Customers by User Role', 'woo_ce' ); ?></label></p>
<div id="export-customers-filters-user_role" class="separator">
	<ul>
		<li>
<?php if( !empty( $user_roles ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a User Role...', 'woo_ce' ); ?>" name="customer_filter_user_role[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $user_roles as $key => $user_role ) { ?>
				<option value="<?php echo $key; ?>"><?php echo ucfirst( $user_role['name'] ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No User Roles were found.', 'woo_ce' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the User Roles you want to filter exported Customers by. Default is to include all User Role options.', 'woo_ce' ); ?></p>
</div>
<!-- #export-customers-filters-user_role -->
<?php
		ob_end_flush();

	}

	// HTML template for Custom Customers widget on Store Exporter screen
	function woo_ce_customers_custom_fields() {

		if( $custom_customers = woo_ce_get_option( 'custom_customers', '' ) )
			$custom_customers = implode( "\n", $custom_customers );

		$troubleshooting_url = '';

		ob_start(); ?>
<form method="post" id="export-customers-custom-fields" class="export-options customer-options">
	<div id="poststuff">

		<div class="postbox" id="export-options customer-options">
			<h3 class="hndle"><?php _e( 'Custom Customer Fields', 'woo_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom Customer meta in the Export Customers table above fill the Customers text box then click Save Custom Fields.', 'woo_ce' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'Customer meta', 'woo_ce' ); ?></label>
						</th>
						<td>
							<textarea name="custom_customers" rows="5" cols="70"><?php echo esc_textarea( $custom_customers ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Customer meta in your export file by adding each custom Customer meta name to a new line above.<br />For example: <code>Customer UA</code> (new line) <code>Customer IP Address</code>', 'woo_ce' ); ?></p>
						</td>
					</tr>

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Custom Fields', 'woo_ce' ); ?>" class="button-primary" />
				</p>
				<p class="description"><?php printf( __( 'For more information on custom Customer meta consult our <a href="%s" target="_blank">online documentation</a>.', 'woo_ce' ), $troubleshooting_url ); ?></p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-customers-custom-fields -->
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of Customer export columns
function woo_ce_get_customer_fields( $format = 'full' ) {

	$export_type = 'customer';

	$fields = array();
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Username', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_role',
		'label' => __( 'User Role', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_full_name',
		'label' => __( 'Billing: Full Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_first_name',
		'label' => __( 'Billing: First Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_last_name',
		'label' => __( 'Billing: Last Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_company',
		'label' => __( 'Billing: Company', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_address',
		'label' => __( 'Billing: Street Address', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_city',
		'label' => __( 'Billing: City', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_postcode',
		'label' => __( 'Billing: ZIP Code', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_state',
		'label' => __( 'Billing: State (prefix)', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_state_full',
		'label' => __( 'Billing: State', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_country',
		'label' => __( 'Billing: Country', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_phone',
		'label' => __( 'Billing: Phone Number', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'billing_email',
		'label' => __( 'Billing: E-mail Address', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_full_name',
		'label' => __( 'Shipping: Full Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_first_name',
		'label' => __( 'Shipping: First Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_last_name',
		'label' => __( 'Shipping: Last Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_company',
		'label' => __( 'Shipping: Company', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_address',
		'label' => __( 'Shipping: Street Address', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_city',
		'label' => __( 'Shipping: City', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_postcode',
		'label' => __( 'Shipping: ZIP Code', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_state',
		'label' => __( 'Shipping: State (prefix)', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_state_full',
		'label' => __( 'Shipping: State', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_country',
		'label' => __( 'Shipping: Country (prefix)', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_country_full',
		'label' => __( 'Shipping: Country', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'total_spent',
		'label' => __( 'Total Spent', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'completed_orders',
		'label' => __( 'Completed Orders', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'total_orders',
		'label' => __( 'Total Orders', 'woo_ce' )
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

function woo_ce_override_customer_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'customer_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_customer_fields', 'woo_ce_override_customer_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_customer_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_customer_fields();
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

// Adds custom Customer columns to the Customer fields list
function woo_ce_extend_customer_fields( $fields = array() ) {

	// WooCommerce Follow-Up Emails
	if( class_exists( 'FollowUpEmails' ) ) {
		$fields[] = array(
			'name' => 'followup_optout',
			'label' => __( 'Follow-Up Emails: Opted Out', 'woo_ce' )
		);
	}

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( class_exists( 'WooCommerce_HearAboutUs' ) ) {
		$fields[] = array(
			'name' => 'hear_about_us',
			'label' => __( 'Source', 'woo_ce' ),
			'hover' => __( 'WooCommerce Hear About Us', 'woo_ce' )
		);
	}

	// Custom Customer fields
	$custom_customers = woo_ce_get_option( 'custom_customers', '' );
	if( !empty( $custom_customers ) ) {
		foreach( $custom_customers as $custom_customer ) {
			if( !empty( $custom_customer ) ) {
				$fields[] = array(
					'name' => $custom_customer,
					'label' => woo_ce_clean_export_label( $custom_customer ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_customer_fields_custom_customer_hover', '%s: %s' ), __( 'Custom Customer', 'woo_ce' ), $custom_customer )
				);
			}
		}
		unset( $custom_customers, $custom_customer );
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => woo_ce_clean_export_label( $custom_user ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_customer_fields_custom_user_hover', '%s: %s' ), __( 'Custom User', 'woo_ce' ), $custom_user )
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $fields;

}
add_filter( 'woo_ce_customer_fields', 'woo_ce_extend_customer_fields' );

function woo_ce_get_customers_list() {

	$args = array(
		'fields' => array( 'ID', 'user_email', 'display_name' ),
		// Disabled Customer filters, will re-enable with UI options in future Plugin update
/*
		'orderby' => 'display_name',
		'meta_key' => 'billing_email',
		'meta_value' => null,
		'search_columns'	=> array( 'ID', 'user_login', 'user_email', 'user_nicename' )
*/
	);
	$customers = get_users( $args );
	return $customers;

}

function woo_ce_is_duplicate_customer( $customers = array(), $order = array() ) {

	foreach( $customers as $key => $customer ) {
		if( $customer->billing_email == $order->billing_email ) {
			return $key;
			break;
		}
	}
	return 0;

}
?>