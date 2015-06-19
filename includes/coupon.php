<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Filter Coupons by Discount Type on Store Exporter screen
	function woo_ce_coupons_filter_by_discount_type() {

		$discount_types = woo_ce_get_coupon_discount_types();

		ob_start(); ?>
<p><label><input type="checkbox" id="coupons-filters-discount_types" /> <?php _e( 'Filter Coupons by Discount Type', 'woo_ce' ); ?></label></p>
<div id="export-coupons-filters-discount_types" class="separator">
	<ul>
		<li>
<?php if( !empty( $discount_types ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Discount Type...', 'woo_ce' ); ?>" name="coupon_filter_discount_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $discount_types as $key => $discount_type ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $discount_type; ?> (<?php printf( __( 'Post meta key: %s', 'woo_ce' ), $key ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Discount Types were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Discount Types you want to filter exported Coupons by. Default is to include all Coupons.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-discount_types -->

<?php
		ob_end_flush();

	}

	// HTML template for Coupon Sorting widget on Store Exporter screen
	function woo_ce_coupon_sorting() {

		$orderby = woo_ce_get_option( 'coupon_orderby', 'ID' );
		$order = woo_ce_get_option( 'coupon_order', 'ASC' );

		ob_start(); ?>
<p><label><?php _e( 'Coupon Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="coupon_orderby">
		<option value="ID"<?php selected( 'ID', $orderby ); ?>><?php _e( 'Coupon ID', 'woo_ce' ); ?></option>
		<option value="title"<?php selected( 'title', $orderby ); ?>><?php _e( 'Coupon Code', 'woo_ce' ); ?></option>
		<option value="date"<?php selected( 'date', $orderby ); ?>><?php _e( 'Date Created', 'woo_ce' ); ?></option>
		<option value="modified"<?php selected( 'modified', $orderby ); ?>><?php _e( 'Date Modified', 'woo_ce' ); ?></option>
		<option value="rand"<?php selected( 'rand', $orderby ); ?>><?php _e( 'Random', 'woo_ce' ); ?></option>
	</select>
	<select name="coupon_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Coupons within the exported file. By default this is set to export Coupons by Coupon ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of Coupon export columns
function woo_ce_get_coupon_fields( $format = 'full' ) {

	$export_type = 'coupon';

	$fields = array();
	$fields[] = array(
		'name' => 'coupon_code',
		'label' => __( 'Coupon Code', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'coupon_description',
		'label' => __( 'Coupon Description', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'discount_type',
		'label' => __( 'Discount Type', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'coupon_amount',
		'label' => __( 'Coupon Amount', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'individual_use',
		'label' => __( 'Individual Use', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'apply_before_tax',
		'label' => __( 'Apply before tax', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'exclude_sale_items',
		'label' => __( 'Exclude sale items', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'minimum_amount',
		'label' => __( 'Minimum Amount', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_ids',
		'label' => __( 'Products', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'exclude_product_ids',
		'label' => __( 'Exclude Products', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_categories',
		'label' => __( 'Product Categories', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'exclude_product_categories',
		'label' => __( 'Exclude Product Categories', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'customer_email',
		'label' => __( 'Customer e-mails', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'usage_limit',
		'label' => __( 'Usage Limit', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'expiry_date',
		'label' => __( 'Expiry Date', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'usage_count',
		'label' => __( 'Usage Count', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'usage_cost',
		'label' => __( 'Usage Cost', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'used_by',
		'label' => __( 'Used By', 'woo_ce' )
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

function woo_ce_override_coupon_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'coupon_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_coupon_fields', 'woo_ce_override_coupon_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_coupon_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_coupon_fields();
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

// Returns a list of Coupon IDs
function woo_ce_get_coupons( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$discount_types = false;

	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : false );
		$orderby = ( isset( $args['coupon_orderby'] ) ? $args['coupon_orderby'] : 'ID' );
		$order = ( isset( $args['coupon_order'] ) ? $args['coupon_order'] : 'ASC' );
		if( !empty( $args['coupon_discount_types'] ) )
			$discount_types = $args['coupon_discount_types'];
	}

	$post_type = 'shop_coupon';
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
	if( $discount_types ) {
		$args['meta_query'] = array();
		$args['meta_query'][] = array(
			'key' => 'discount_type',
			'value' => $discount_types
		);
	}
	$coupons = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_coupons_args', $args );

	$coupon_ids = new WP_Query( $args );
	if( $coupon_ids->posts ) {
		foreach( $coupon_ids->posts as $coupon_id )
			$coupons[] = $coupon_id;
		unset( $coupon_ids, $coupon_id );
	}
	return $coupons;

}

function woo_ce_get_coupon_data( $coupon_id = 0, $args = array() ) {

	global $export;

	$coupon = get_post( $coupon_id );

	$coupon->coupon_code = $coupon->post_title;
	$coupon->discount_type = woo_ce_format_discount_type( get_post_meta( $coupon->ID, 'discount_type', true ) );
	$coupon->coupon_description = $coupon->post_excerpt;
	$coupon->coupon_amount = get_post_meta( $coupon->ID, 'coupon_amount', true );
	$coupon->individual_use = woo_ce_format_switch( get_post_meta( $coupon->ID, 'individual_use', true ) );
	$coupon->apply_before_tax = woo_ce_format_switch( get_post_meta( $coupon->ID, 'apply_before_tax', true ) );
	$coupon->exclude_sale_items = woo_ce_format_switch( get_post_meta( $coupon->ID, 'exclude_sale_items', true ) );
	$coupon->minimum_amount = get_post_meta( $coupon->ID, 'minimum_amount', true );
	$coupon->product_ids = woo_ce_convert_product_ids( get_post_meta( $coupon->ID, 'product_ids', true ) );
	$coupon->exclude_product_ids = woo_ce_convert_product_ids( get_post_meta( $coupon->ID, 'exclude_product_ids', true ) );
	$coupon->product_categories = woo_ce_convert_product_ids( get_post_meta( $coupon->ID, 'product_categories', true ) );
	$coupon->exclude_product_categories = woo_ce_convert_product_ids( get_post_meta( $coupon->ID, 'exclude_product_categories', true ) );
	$coupon->customer_email = woo_ce_convert_product_ids( get_post_meta( $coupon->ID, 'customer_email', true ) );
	$coupon->usage_limit = get_post_meta( $coupon->ID, 'usage_limit', true );
	$coupon->expiry_date = woo_ce_format_date( get_post_meta( $coupon->ID, 'expiry_date', true ) );
	$coupon->usage_count = get_post_meta( $coupon->ID, 'usage_count', true );
	$coupon->usage_cost = woo_ce_get_coupon_usage_cost( $coupon->coupon_code );
	$coupon->used_by = woo_ce_convert_product_ids( get_post_meta( $coupon->ID, '_used_by', false ) );
	return $coupon;

}

function woo_ce_get_coupon_usage_cost( $coupon_code = '' ) {

	global $wpdb;

	$count = 0;
	if( $coupon_code ) {
		$order_item_type = 'coupon';
		$meta_key = 'discount_amount';
		$count_sql = $wpdb->prepare( "SELECT SUM(order_itemmeta.meta_value) FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE order_items.order_item_id = order_itemmeta.order_item_id AND order_items.order_item_type = %s AND order_items.order_item_name = %s AND order_itemmeta.meta_key = %s LIMIT 1", $order_item_type, $coupon_code, $meta_key );
		$count = $wpdb->get_var( $count_sql );
	}
	return $count;

}

function woo_ce_get_coupon_code_usage( $coupon_code = '' ) {

	global $wpdb;

	$count = 0;
	if( $coupon_code ) {
		$order_item_type = 'coupon';
		$count_sql = $wpdb->prepare( "SELECT COUNT('order_item_id') FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_item_type` = %s AND `order_item_name` = %s", $order_item_type, $coupon_code );
		$count = $wpdb->get_var( $count_sql );
	}
	return $count;

}

function woo_ce_get_coupon_discount_types() {

	$discount_types = array(
		'fixed_cart' => __( 'Cart Discount', 'woo_ce' ),
		'percent' => __( 'Cart % Discount', 'woo_ce' ),
		'fixed_product' => __( 'Product Discount', 'woo_ce' ),
		'percent_product' => __( 'Product % Discount', 'woo_ce' )
	);
	return $discount_types;

}

// Format the discount type, defaults to Cart Discount
function woo_ce_format_discount_type( $discount_type = '' ) {

	$output = $discount_type;
	switch( $discount_type ) {

		default:
		case 'fixed_cart':
			$output = __( 'Cart Discount', 'woo_ce' );
			break;

		case 'percent':
			$output = __( 'Cart % Discount', 'woo_ce' );
			break;

		case 'fixed_product':
			$output = __( 'Product Discount', 'woo_ce' );
			break;

		case 'percent_product':
			$output = __( 'Product % Discount', 'woo_ce' );
			break;

	}
	return $output;

}
?>