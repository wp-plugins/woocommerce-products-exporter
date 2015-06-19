<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Filter Commissions by Commission Date widget on Store Exporter screen
	function woo_ce_commissions_filter_by_date() {

		$today = date( 'l' );
		$yesterday = date( 'l', strtotime( '-1 days' ) );
		$current_month = date( 'F' );
		$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
		$commission_dates_variable = '';
		$commission_dates_variable_length = '';
		$commission_dates_from = woo_ce_get_commission_first_date();
		$commission_dates_to = date( 'd/m/Y' );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-date" /> <?php _e( 'Filter Commissions by Commission Date', 'woo_ce' ); ?></label></p>
<div id="export-commissions-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="today" /> <?php _e( 'Today', 'woo_ce' ); ?> (<?php echo $today; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="yesterday" /> <?php _e( 'Yesterday', 'woo_ce' ); ?> (<?php echo $yesterday; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="current_week" /> <?php _e( 'Current week', 'woo_ce' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_week" /> <?php _e( 'Last week', 'woo_ce' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="current_month" /> <?php _e( 'Current month', 'woo_ce' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_month" /> <?php _e( 'Last month', 'woo_ce' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
<!--
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_quarter" /> <?php _e( 'Last quarter', 'woo_ce' ); ?> (Nov. - Jan.)</label>
		</li>
-->
		<li>
			<label><input type="radio" name="commission_dates_filter" value="variable" /> <?php _e( 'Variable date', 'woo_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<?php _e( 'Last', 'woo_ce' ); ?>
				<input type="text" name="commission_dates_filter_variable" class="text code" size="4" maxlength="4" value="<?php echo $commission_dates_variable; ?>" />
				<select name="commission_dates_filter_variable_length" style="vertical-align:top;">
					<option value=""<?php selected( $commission_dates_variable_length, '' ); ?>>&nbsp;</option>
					<option value="second"<?php selected( $commission_dates_variable_length, 'second' ); ?>><?php _e( 'second(s)', 'woo_ce' ); ?></option>
					<option value="minute"<?php selected( $commission_dates_variable_length, 'minute' ); ?>><?php _e( 'minute(s)', 'woo_ce' ); ?></option>
					<option value="hour"<?php selected( $commission_dates_variable_length, 'hour' ); ?>><?php _e( 'hour(s)', 'woo_ce' ); ?></option>
					<option value="day"<?php selected( $commission_dates_variable_length, 'day' ); ?>><?php _e( 'day(s)', 'woo_ce' ); ?></option>
					<option value="week"<?php selected( $commission_dates_variable_length, 'week' ); ?>><?php _e( 'week(s)', 'woo_ce' ); ?></option>
					<option value="month"<?php selected( $commission_dates_variable_length, 'month' ); ?>><?php _e( 'month(s)', 'woo_ce' ); ?></option>
					<option value="year"<?php selected( $commission_dates_variable_length, 'year' ); ?>><?php _e( 'year(s)', 'woo_ce' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="manual" /> <?php _e( 'Fixed date', 'woo_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="commission_dates_from" name="commission_dates_from" value="<?php echo esc_attr( $commission_dates_from ); ?>" class="text code datepicker" /> to <input type="text" size="10" maxlength="10" id="commission_dates_to" name="commission_dates_to" value="<?php echo esc_attr( $commission_dates_to ); ?>" class="text code datepicker" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first Commission to today.', 'woo_ce' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-commissions-filters-date -->
<?php
		ob_end_flush();

	}

	// Returns date of first Commission received, any status
	function woo_ce_get_commission_first_date() {

		$output = date( 'd/m/Y', mktime( 0, 0, 0, date( 'n' ), 1 ) );
		$post_type = 'shop_commission';
		$args = array(
			'post_type' => $post_type,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'numberposts' => 1
		);
		$commissions = get_posts( $args );
		if( $commissions ) {
			$commission = strtotime( $commissions[0]->post_date );
			$output = date( 'd/m/Y', $commission );
			unset( $commissions, $commission );
		}
		return $output;

	}

	// HTML template for Commission Sorting widget on Store Exporter screen
	function woo_ce_commission_sorting() {

		$orderby = woo_ce_get_option( 'commission_orderby', 'ID' );
		$order = woo_ce_get_option( 'commission_order', 'ASC' );

		ob_start(); ?>
<p><label><?php _e( 'Commission Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="commission_orderby">
		<option value="ID"<?php selected( 'ID', $orderby ); ?>><?php _e( 'Commission ID', 'woo_ce' ); ?></option>
		<option value="title"<?php selected( 'title', $orderby ); ?>><?php _e( 'Commission Title', 'woo_ce' ); ?></option>
		<option value="date"<?php selected( 'date', $orderby ); ?>><?php _e( 'Date Created', 'woo_ce' ); ?></option>
		<option value="modified"<?php selected( 'modified', $orderby ); ?>><?php _e( 'Date Modified', 'woo_ce' ); ?></option>
		<option value="rand"<?php selected( 'rand', $orderby ); ?>><?php _e( 'Random', 'woo_ce' ); ?></option>
	</select>
	<select name="commission_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Commissions within the exported file. By default this is set to export Commissions by Commission ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for Filter Commissions by Product Vendor widget on Store Exporter screen
	function woo_ce_commissions_filter_by_product_vendor() {

		$product_vendors = woo_ce_get_product_vendors( array(), 'full' );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-product_vendor" /> <?php _e( 'Filter Commissions by Product Vendors', 'woo_ce' ); ?></label></p>
<div id="export-commissions-filters-product_vendor" class="separator">
<?php if( $product_vendors ) { ?>
	<ul>
	<?php foreach( $product_vendors as $product_vendor ) { ?>
		<li>
			<label><input type="checkbox" name="commission_filter_product_vendor[<?php echo $product_vendor->term_id; ?>]" value="<?php echo $product_vendor->term_id; ?>" title="<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_vendor->term_id ); ?>"<?php disabled( $product_vendor->count, 0 ); ?> /> <?php echo $product_vendor->name; ?></label>
			<span class="description">(<?php echo $product_vendor->count; ?>)</span>
		</li>
	<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Product Vendors you want to filter exported Commissions by. Default is to include all Product Vendors.', 'woo_ce' ); ?></p>
<?php } else { ?>
	<p><?php _e( 'No Product Vendors were found.', 'woo_ce' ); ?></p>
<?php } ?>
</div>
<!-- #export-commissions-filters-product_vendor -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Commissions by Commission Status widget on Store Exporter screen
	function woo_ce_commissions_filter_by_commission_status() {

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-commission_status" /> <?php _e( 'Filter Commissions by Commission Status', 'woo_ce' ); ?></label></p>
<div id="export-commissions-filters-commission_status" class="separator">
	<ul>
		<li>
			<label><input type="checkbox" name="commission_filter_commission_status[]" value="unpaid"<?php disabled( woo_ce_commissions_stock_status_count( 'unpaid' ), 0 ); ?> /> <?php _e( 'Unpaid', 'woo_ce' ); ?></label>
			<span class="description">(<?php echo woo_ce_commissions_stock_status_count( 'unpaid' ); ?>)</span>
		</li>
		<li>
			<label><input type="checkbox" name="commission_filter_commission_status[]" value="paid"<?php disabled( woo_ce_commissions_stock_status_count( 'paid' ), 0 ); ?> /> <?php _e( 'Paid', 'woo_ce' ); ?></label>
			<span class="description">(<?php echo woo_ce_commissions_stock_status_count( 'paid' ); ?>)</span>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Commission Status you want to filter exported Commissions by. Default is to include all Commission Statuses.', 'woo_ce' ); ?></p>
</div>
<!-- #export-commissions-filters-commission_status -->
<?php
		ob_end_flush();

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_commissions_stock_status_count( $type = '' ) {

		$output = 0;
		$post_type = 'shop_commission';
		$meta_key = '_paid_status';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'fields' => 'ids'
		);
		if( $type )
			$args['meta_value'] = $type;
		$commission_ids = new WP_Query( $args );
		if( !empty( $commission_ids->posts ) )
			$output = count( $commission_ids->posts );
		return $output;

	}

	/* End of: WordPress Administration */

}

function woo_ce_get_commission_fields( $format = 'full' ) {

	$export_type = 'commission';

	$fields = array();
	$fields[] = array(
		'name' => 'ID',
		'label' => __( 'Commission ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Commission Date', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'title',
		'label' => __( 'Commission Title', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_name',
		'label' => __( 'Product Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_sku',
		'label' => __( 'Product SKU', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_vendor_id',
		'label' => __( 'Product Vendor ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_vendor_name',
		'label' => __( 'Product Vendor Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'commission_amount',
		'label' => __( 'Commission Amount', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'paid_status',
		'label' => __( 'Commission Status', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'post_status',
		'label' => __( 'Post Status', 'woo_ce' )
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

function woo_ce_override_commission_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'commission_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_commission_fields', 'woo_ce_override_commission_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_commission_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_commission_fields();
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

// Returns a list of Commission Post IDs
function woo_ce_get_commissions( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;

	if( $args ) {
		$product_vendors = ( isset( $args['commission_product_vendors'] ) ? $args['commission_product_vendors'] : false );
		$status = ( isset( $args['commission_status'] ) ? $args['commission_status'] : false );
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : false );
		$orderby = ( isset( $args['commission_orderby'] ) ? $args['commission_orderby'] : 'ID' );
		$order = ( isset( $args['commission_order'] ) ? $args['commission_order'] : 'ASC' );
		$commission_dates_filter = ( isset( $args['commission_dates_filter'] ) ? $args['commission_dates_filter'] : false );
		switch( $commission_dates_filter ) {

			case 'today':
				$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );
				$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );
				break;

			case 'yesterday':
				$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '-2 days' ) ), date( 'd', strtotime( '-2 days' ) ) ) );
				$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '-1 days' ) ), date( 'd', strtotime( '-1 days' ) ) ) );
				break;

			case 'current_week':
				$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'this Monday' ) ), date( 'd', strtotime( 'this Monday' ) ) ) );
				$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'next Sunday' ) ), date( 'd', strtotime( 'next Sunday' ) ) ) );
				break;

			case 'last_week':
				$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'last Monday' ) ), date( 'd', strtotime( 'last Monday' ) ) ) );
				$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( 'last Sunday' ) ), date( 'd', strtotime( 'last Sunday' ) ) ) );
				break;

			case 'current_month':
				$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), 1 ) );
				$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '+1 month' ) ), 0 ) );
				break;

			case 'last_month':
				$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( '-1 month' ) ), 1 ) );
				$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), 0 ) );
				break;

			case 'last_quarter':
				break;

			case 'manual':
				$commission_dates_from = woo_ce_format_order_date( $args['commission_dates_from'] );
				$commission_dates_to = woo_ce_format_order_date( $args['commission_dates_to'] );
				break;

			case 'variable':
				$commission_filter_date_variable = $args['commission_dates_filter_variable'];
				$commission_filter_date_variable_length = $args['commission_dates_filter_variable_length'];
				if( $commission_filter_date_variable !== false && $commission_filter_date_variable_length !== false ) {
					$commission_filter_date_strtotime = sprintf( '-%d %s', $commission_filter_date_variable, $commission_filter_date_variable_length );
					$commission_dates_from = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n', strtotime( $commission_filter_date_strtotime ) ), date( 'd', strtotime( $commission_filter_date_strtotime ) ) ) );
					$commission_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );
					unset( $commission_filter_date_variable, $commission_filter_date_variable_length, $commission_filter_date_strtotime );
				}
				break;

			default:
				$commission_dates_from = false;
				$commission_dates_to = false;
				break;

		}
		if( $commission_dates_from && $commission_dates_to ) {
			$commission_dates_from = strtotime( $commission_dates_from );
			$commission_dates_to = explode( '-', $commission_dates_to );
			// Check that a valid date was provided
			if( isset( $commission_dates_to[0] ) && isset( $commission_dates_to[1] ) && isset( $commission_dates_to[2] ) )
				$commission_dates_to = strtotime( date( 'd-m-Y', mktime( 0, 0, 0, $commission_dates_to[1], $commission_dates_to[0]+1, $commission_dates_to[2] ) ) );
			else	
				$commission_dates_to = false;
		}
	}
	$post_type = 'shop_commission';
	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'post_status' => woo_ce_post_statuses(),
		'fields' => 'ids',
		'suppress_filters' => false
	);
	if( !empty( $product_vendors ) ) {
		$args['meta_query'][] = array(
			'key' => '_commission_vendor',
			'value' => $product_vendors,
			'compare' => 'IN'
		);
	}
	if( !empty( $status ) ) {
		$args['meta_query'][] = array(
			'key' => '_paid_status',
			'value' => $status,
			'compare' => 'IN'
		);
	}
	$commissions = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_commissions_args', $args );

	$commission_ids = new WP_Query( $args );
	if( $commission_ids->posts ) {
		foreach( $commission_ids->posts as $commission_id ) {

			// Get Commission details
			$commission = get_post( $commission_id );

			// Filter Commission dates by dropping those outside the date range
			if( $commission_dates_from && $commission_dates_to ) {
				if( ( strtotime( $commission->post_date ) > $commission_dates_from ) && ( strtotime( $commission->post_date ) < $commission_dates_to ) ) {
					// Do nothing
				} else {
					unset( $commission );
					continue;
				}
			}

			$commissions[] = $commission_id;
		}
		unset( $commission_ids, $commission_id );
	}
	return $commissions;

}

function woo_ce_get_commission_data( $commission_id = 0, $args = array() ) {

	global $export;

	$commission = get_post( $commission_id );

	$commission->title = $commission->post_title;
	$commission->product_id = get_post_meta( $commission->ID, '_commission_product', true );
	$commission->product_name = get_the_title( $commission->product_id );
	$commission->product_sku = get_post_meta( $commission->product_id, '_sku', true );
	$commission->product_vendor_id = get_post_meta( $commission->ID, '_commission_vendor', true );
	$product_vendor = woo_ce_get_product_vendor_data( $commission->product_vendor_id );
	$commission->product_vendor_name = ( isset( $product_vendor->title ) ? $product_vendor->title : '' );
	unset( $product_vendor );

	$commission->commission_amount = get_post_meta( $commission->ID, '_commission_amount', true );
	// Check that a valid price has been provided
	if( isset( $commission->commission_amount ) && $commission->commission_amount != '' && function_exists( 'wc_format_localized_price' ) )
		$commission->commission_amount = woo_ce_format_price( $commission->commission_amount );
	$commission->paid_status = woo_ce_format_commission_paid_status( get_post_meta( $commission->ID, '_paid_status', true ) );
	$commission->post_date = woo_ce_format_date( $commission->post_date );
	$commission->post_status = woo_ce_format_post_status ( $commission->post_status );

	return $commission;

}

function woo_ce_format_commission_paid_status( $paid_status = '' ) {

	$output = $paid_status;
	switch( $output ) {

		case 'paid':
			$output = __( 'Paid', 'woo_ce' );
			break;

		default:
		case 'unpaid':
			$output = __( 'Unpaid', 'woo_ce' );
			break;

	}
	return $output;

}
?>