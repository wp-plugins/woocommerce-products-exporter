<?php
function woo_ce_get_product_vendor_fields( $format = 'full' ) {

	$export_type = 'product_vendor';

	$fields = array();
	$fields[] = array(
		'name' => 'ID',
		'label' => __( 'Product Vendor ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'title',
		'label' => __( 'Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'url',
		'label' => __( 'Product Vendor URL', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'commission',
		'label' => __( 'Commission', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'paypal_email',
		'label' => __( 'PayPal E-mail Address', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Vendor Username', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'Vendor User ID', 'woo_ce' )
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

function woo_ce_override_product_vendor_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'product_vendor_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_product_vendor_fields', 'woo_ce_override_product_vendor_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_product_vendor_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_product_vendor_fields();
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

// Returns a list of Product Vendor Term IDs
function woo_ce_get_product_vendors( $args = array(), $output = 'term_id' ) {

	global $export;

	$term_taxonomy = 'shop_vendor';
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_product_vendors_args', $args );

	$product_vendors = get_terms( $term_taxonomy, $args );
	if( !empty( $product_vendors ) && is_wp_error( $product_vendors ) == false ) {
		if( $output == 'term_id' ) {
			$vendor_ids = array();
			foreach( $product_vendors as $key => $product_vendor )
				$vendor_ids[] = $product_vendor->term_id;
			$export->total_rows = count( $vendor_ids );
			unset( $product_vendors, $product_vendor );
			return $vendor_ids;
		} else if( $output == 'full' ) {
			return $product_vendors;
		}
	}

}

function woo_ce_get_product_vendor_data( $vendor_id = 0, $args = array() ) {

	$defaults = array();
	$args = wp_parse_args( $args, $defaults );

	// Get Product Vendor details
	$product_vendor_data = ( function_exists( 'get_vendor' ) ? get_vendor( $vendor_id ) : array() );

	$product_vendor = new stdClass;
	if( $product_vendor_data !== false ) {
		$product_vendor = $product_vendor_data;
		$product_vendor->user_name = woo_ce_format_product_vendor_users( $product_vendor->admins, 'user_login' );
		$product_vendor->user_id = woo_ce_format_product_vendor_users( $product_vendor->admins, 'ID' );
	}
	return apply_filters( 'woo_ce_product_vendor', $product_vendor );

}

function woo_ce_get_product_assoc_product_vendors( $product_id = 0, $parent_id = 0, $return = 'name' ) {

	global $export;

	$output = '';
	$term_taxonomy = 'shop_vendor';
	// Return Product Vendors of Parent if this is a Variation
	if( $parent_id )
		$product_id = $parent_id;
	if( $product_id )
		$vendors = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $vendors ) && is_wp_error( $vendors ) == false ) {
		$size = count( $vendors );
		for( $i = 0; $i < $size; $i++ ) {
			if( $return == 'term_id' ) {
				$output .= $vendors[$i]->term_id . $export->category_separator;
			} else if( $return == 'name' ) {
				if( $vendor = get_term( $vendors[$i]->term_id, $term_taxonomy ) )
					$output .= $vendor->name . $export->category_separator;
			}
		}
		unset( $vendors, $vendor );
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

function woo_ce_get_product_assoc_product_vendor_commission( $product_id = 0, $vendor_ids = array() ) {

	global $export;

	$output = '';
	if( !empty( $vendor_ids ) ) {
		// Loop through each Vendor
		$size = count( $vendor_ids );
		if( $size == 1 )
			$vendor_ids = array( $vendor_ids );
		for( $i = 0; $i < $size; $i++ ) {
			// Use get_commission_parent() as default and use Post meta as fall-back
			$output .= ( function_exists( 'get_commission_percent' ) ? get_commission_percent( $product_id, $vendor_ids[$i] ) : get_post_meta( $product_id, '_product_vendors_commission', true ) ) . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

function woo_ce_format_product_vendor_users( $users = null, $return = 'user_login' ) {

	global $export;

	$output = '';
	if( !empty( $users ) ) {
		foreach( $users as $user ) {
			if( $return == 'ID' )
				$output .= $user->ID;
			else if( $return == 'user_login' )
				$output .= $user->user_login;
		}
	}
	return $output;

}
?>