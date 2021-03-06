<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Coupon Sorting widget on Store Exporter screen
	function woo_ce_brand_sorting() {

		$orderby = woo_ce_get_option( 'brand_orderby', 'ID' );
		$order = woo_ce_get_option( 'brand_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Brand Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="brand_orderby">
		<option value="id"<?php selected( 'id', $orderby ); ?>><?php _e( 'Term ID', 'woo_ce' ); ?></option>
		<option value="name"<?php selected( 'name', $orderby ); ?>><?php _e( 'Brand Name', 'woo_ce' ); ?></option>
	</select>
	<select name="brand_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Brands within the exported file. By default this is set to export Product Brands by Term ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of Brand export columns
function woo_ce_get_brand_fields( $format = 'full' ) {

	$export_type = 'brand';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Brand Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Brand Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent Term ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Brand Description', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Brand Image', 'woo_ce' )
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

function woo_ce_override_brand_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'brand_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_brand_fields', 'woo_ce_override_brand_field_labels', 11 );

// Returns a list of WooCommerce Product Brands to export process
function woo_ce_get_product_brands( $args = array() ) {

	$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_product_brands_args', $args );

	$brands = get_terms( $term_taxonomy, $args );
	if( !empty( $brands ) && is_wp_error( $brands ) == false ) {
		foreach( $brands as $key => $brand ) {
			$brands[$key]->description = woo_ce_format_description_excerpt( $brand->description );
			$brands[$key]->parent_name = '';
			if( $brands[$key]->parent_id = $brand->parent ) {
				if( $parent_brand = get_term( $brands[$key]->parent_id, $term_taxonomy ) ) {
					$brands[$key]->parent_name = $parent_brand->name;
				}
				unset( $parent_brand );
			} else {
				$brands[$key]->parent_id = '';
			}
			$brands[$key]->image = ( function_exists( 'get_brand_thumbnail_url' ) ? get_brand_thumbnail_url( $brand->term_id ) : false );
		}
		return $brands;
	}

}

// Returns the export column header label based on an export column slug
function woo_ce_get_brand_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_brand_fields();
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