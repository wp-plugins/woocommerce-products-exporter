<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Shipping Class Sorting widget on Store Exporter screen
	function woo_ce_shipping_class_sorting() {

		$shipping_class_orderby = woo_ce_get_option( 'shipping_class_orderby', 'ID' );
		$shipping_class_order = woo_ce_get_option( 'shipping_class_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Shipping Class Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="shipping_class_orderby">
		<option value="id"<?php selected( 'id', $shipping_class_orderby ); ?>><?php _e( 'Term ID', 'woo_ce' ); ?></option>
		<option value="name"<?php selected( 'name', $shipping_class_orderby ); ?>><?php _e( 'Shipping Class Name', 'woo_ce' ); ?></option>
	</select>
	<select name="shipping_class_order">
		<option value="ASC"<?php selected( 'ASC', $shipping_class_order ); ?>><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $shipping_class_order ); ?>><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Shipping Classes within the exported file. By default this is set to export Shipping Classes by Term ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of WooCommerce Shipping Classes to export process
function woo_ce_get_shipping_classes( $args = array() ) {

	$term_taxonomy = 'product_shipping_class';
	$defaults = array(
		'orderby' => 'ID',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_shipping_clases_args', $args );

	$shipping_classes = get_terms( $term_taxonomy, $args );
	if( !empty( $shipping_classes ) && is_wp_error( $shipping_classes ) == false ) {
		$size = count( $shipping_classes );
		for( $i = 0; $i < $size; $i++ ) {
			$shipping_classes[$i]->disabled = 0;
			if( $shipping_classes[$i]->count == 0 )
				$shipping_classes[$i]->disabled = 1;
		}
		return $shipping_classes;
	}

}

// Returns a list of Shipping Classes export columns
function woo_ce_get_shipping_class_fields( $format = 'full' ) {

	$export_type = 'shipping_class';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Shipping Class Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Shipping Class Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Shipping Class Description', 'woo_ce' )
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

function woo_ce_override_shipping_class_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'shipping_class_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_shipping_class_fields', 'woo_ce_override_shipping_class_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_shipping_class_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_shipping_class_fields();
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
?>