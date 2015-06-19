<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for Filter Products by Product Category widget on Store Exporter screen
	function woo_ce_products_filter_by_product_category() {

		$args = array(
			'hide_empty' => 1
		);
		$product_categories = woo_ce_get_product_categories( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-categories" /> <?php _e( 'Filter Products by Product Category', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-categories" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_categories ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woo_ce' ); ?>" name="product_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
				<option value="<?php echo $product_category->term_id; ?>"<?php disabled( $product_category->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Categories were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Products by. Product Categories not assigned to Products are hidden from view. Default is to include all Product Categories.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-categories -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Tag widget on Store Exporter screen
	function woo_ce_products_filter_by_product_tag() {

		$args = array(
			'hide_empty' => 1
		);
		$product_tags = woo_ce_get_product_tags( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-tags" /> <?php _e( 'Filter Products by Product Tag', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-tags" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_tags ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woo_ce' ); ?>" name="product_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
				<option value="<?php echo $product_tag->term_id; ?>"<?php disabled( $product_tag->count, 0 ); ?>><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Tags were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Products by. Product Tags not assigned to Products are hidden from view. Default is to include all Product Tags.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-tags -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Brand widget on Store Exporter screen
	function woo_ce_products_filter_by_product_brand() {

		// Check if Brands is available
		if( ( class_exists( 'WC_Brands' ) || class_exists( 'woo_brands' ) || taxonomy_exists( apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' ) ) ) == false )
			return;

		$args = array(
			'hide_empty' => 1,
			'orderby' => 'term_group'
		);
		$product_brands = woo_ce_get_product_brands( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-brands" /> <?php _e( 'Filter Products by Product Brand', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-brands" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_brands ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Brand...', 'woo_ce' ); ?>" name="product_filter_brand[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_brands as $product_brand ) { ?>
				<option value="<?php echo $product_brand->term_id; ?>"<?php disabled( $product_brand->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_brand->name, $product_brand->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_brand->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Brands were found.', 'woo_ce' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Brands you want to filter exported Products by. Product Brands not assigned to Products are hidden from view. Default is to include all Product Brands.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-brands -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Vendor widget on Store Exporter screen
	function woo_ce_products_filter_by_product_vendor() {

		if( !class_exists( 'WooCommerce_Product_Vendors' ) )
			return;

		$args = array(
			'hide_empty' => 1
		);
		$product_vendors = woo_ce_get_product_vendors( $args, 'full' );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-vendors" /> <?php _e( 'Filter Products by Product Vendor', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-vendors" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_vendors ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Vendor...', 'woo_ce' ); ?>" name="product_filter_vendor[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_vendors as $product_vendor ) { ?>
				<option value="<?php echo $product_vendor->term_id; ?>"<?php disabled( $product_vendor->count, 0 ); ?>><?php echo $product_vendor->name; ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_vendor->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Vendors were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Vendors you want to filter exported Products by. Product Vendors not assigned to Products are hidden from view. Default is to include all Product Vendors.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-vendors -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Status widget on Store Exporter screen
	function woo_ce_products_filter_by_product_status() {

		$product_statuses = get_post_statuses();
		if( !isset( $product_statuses['trash'] ) )
			$product_statuses['trash'] = __( 'Trash', 'woo_ce' );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-status" /> <?php _e( 'Filter Products by Product Status', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Status...', 'woo_ce' ); ?>" name="product_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_statuses as $key => $product_status ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $product_status; ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Status were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Status options you want to filter exported Products by. Default is to include all Product Status options.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Type widget on Store Exporter screen
	function woo_ce_products_filter_by_product_type() {

		$product_types = woo_ce_get_product_types();

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-type" /> <?php _e( 'Filter Products by Product Type', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-type" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_types ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Type...', 'woo_ce' ); ?>" name="product_filter_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
				<option value="<?php echo $key; ?>"<?php disabled( $product_type['count'], 0 ); ?>><?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Types were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types except Variations.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-type -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Type widget on Store Exporter screen
	function woo_ce_products_filter_by_stock_status() {

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-stock" /> <?php _e( 'Filter Products by Stock Status', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-stock" class="separator">
	<ul>
		<li value=""><label><input type="radio" name="product_filter_stock" value="" checked="checked" /><?php _e( 'Include both', 'woo_ce' ); ?></label></li>
		<li value="instock"><label><input type="radio" name="product_filter_stock" value="instock" /><?php _e( 'In stock', 'woo_ce' ); ?></label></li>
		<li value="outofstock"><label><input type="radio" name="product_filter_stock" value="outofstock" /><?php _e( 'Out of stock', 'woo_ce' ); ?></label></li>
	</ul>
	<p class="description"><?php _e( 'Select the Stock Status\'s you want to filter exported Products by. Default is to include all Stock Status\'s.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-stock -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Language widget on Store Exporter screen
	function woo_ce_products_filter_by_language() {

		if( !woo_ce_is_wpml_activated() )
			return;

		$languages = ( function_exists( 'icl_get_languages' ) ? icl_get_languages( 'skip_missing=N' ) : array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-language" /> <?php _e( 'Filter Products by Language', 'woo_ce' ); ?></label></p>
<div id="export-products-filters-language" class="separator">
	<ul>
		<li>
<?php if( !empty( $languages ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Language...', 'woo_ce' ); ?>" name="product_filter_language[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $languages as $key => $language ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $language['native_name']; ?> (<?php echo $language['translated_name']; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Languages were found.', 'woo_ce' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Language\'s you want to filter exported Products by. Default is to include all Language\'s.', 'woo_ce' ); ?></p>
</div>
<!-- #export-products-filters-language -->

<?php
		ob_end_flush();

	}

	// HTML template for Product Sorting widget on Store Exporter screen
	function woo_ce_product_sorting() {

		$product_orderby = woo_ce_get_option( 'product_orderby', 'ID' );
		$product_order = woo_ce_get_option( 'product_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Product Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="product_orderby">
		<option value="ID"<?php selected( 'ID', $product_orderby ); ?>><?php _e( 'Product ID', 'woo_ce' ); ?></option>
		<option value="title"<?php selected( 'title', $product_orderby ); ?>><?php _e( 'Product Name', 'woo_ce' ); ?></option>
		<option value="date"<?php selected( 'date', $product_orderby ); ?>><?php _e( 'Date Created', 'woo_ce' ); ?></option>
		<option value="modified"<?php selected( 'modified', $product_orderby ); ?>><?php _e( 'Date Modified', 'woo_ce' ); ?></option>
		<option value="rand"<?php selected( 'rand', $product_orderby ); ?>><?php _e( 'Random', 'woo_ce' ); ?></option>
		<option value="menu_order"<?php selected( 'menu_order', $product_orderby ); ?>><?php _e( 'Sort Order', 'woo_ce' ); ?></option>
	</select>
	<select name="product_order">
		<option value="ASC"<?php selected( 'ASC', $product_order ); ?>><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $product_order ); ?>><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Products within the exported file. By default this is set to export Products by Product ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for Up-sells formatting on Store Exporter screen
	function woo_ce_products_upsells_formatting() {

		$upsell_formatting = woo_ce_get_option( 'upsell_formatting', 1 );

		ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Up-sells formatting', 'woo_ce' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_upsell_formatting" value="0"<?php checked( $upsell_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Up-Sells as Product ID', 'woo_ce' ); ?></label><br />
		<label><input type="radio" name="product_upsell_formatting" value="1"<?php checked( $upsell_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Up-Sells as Product SKU', 'woo_ce' ); ?></label>
		<p class="description"><?php _e( 'Choose the up-sell formatting that is accepted by your WooCommerce import Plugin (e.g. Doo Product Exporter, Product Import Suite, etc.).', 'woo_ce' ); ?></p>
	</td>
</tr>

<?php
		ob_end_flush();

	}

	// HTML template for Cross-sells formatting on Store Exporter screen
	function woo_ce_products_crosssells_formatting() {

		$crosssell_formatting = woo_ce_get_option( 'crosssell_formatting', 1 );

		ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Cross-sells formatting', 'woo_ce' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_crosssell_formatting" value="0"<?php checked( $crosssell_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Cross-Sells as Product ID', 'woo_ce' ); ?></label><br />
		<label><input type="radio" name="product_crosssell_formatting" value="1"<?php checked( $crosssell_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Cross-Sells as Product SKU', 'woo_ce' ); ?></label>
		<p class="description"><?php _e( 'Choose the cross-sell formatting that is accepted by your WooCommerce import Plugin (e.g. Doo Product Exporter, Product Import Suite, etc.).', 'woo_ce' ); ?></p>
	</td>
</tr>

<?php
		ob_end_flush();

	}

	// HTML template for Variation formatting on Store Exporter screen
	function woo_ce_products_variation_formatting() {

		$variation_formatting = woo_ce_get_option( 'variation_formatting', 0 );

		ob_start(); ?>
					<tr class="export-options product-options">
						<th><label for=""><?php _e( 'Variation formatting', 'woo_ce' ); ?></label></th>
						<td>
							<label><input type="radio" name="variation_formatting" value="0"<?php checked( $variation_formatting, 0 ); ?> />&nbsp;<?php _e( 'Leave empty Variant details intact', 'woo_ce' ); ?></label><br />
							<label><input type="radio" name="variation_formatting" value="1"<?php checked( $variation_formatting, 1 ); ?> />&nbsp;<?php _e( 'Default Variant details to Parent Product', 'woo_ce' ); ?></label>
							<p class="description"><?php _e( 'Choose the default formatting rule that is applied to Product Variations.', 'woo_ce' ); ?></p>
						</td>
					</tr>

<?php
		ob_end_flush();

	}

	function woo_ce_products_description_excerpt_formatting() {

		$description_excerpt_formatting = woo_ce_get_option( 'description_excerpt_formatting', 0 );

		ob_start(); ?>
					<tr class="export-options product-options category-options tag-options order-options">
						<th><label for=""><?php _e( 'Description/Excerpt formatting', 'woo_ce' ); ?></label></th>
						<td>
							<label><input type="radio" name="description_excerpt_formatting" value="0"<?php checked( $description_excerpt_formatting, 0 ); ?> />&nbsp;<?php _e( 'Leave HTML tags from Description/Excerpt intact', 'woo_ce' ); ?></label><br />
							<label><input type="radio" name="description_excerpt_formatting" value="1"<?php checked( $description_excerpt_formatting, 1 ); ?> />&nbsp;<?php _e( 'Strip HTML tags from Description/Excerpt', 'woo_ce' ); ?></label>
							<p class="description"><?php _e( 'Choose the HTML tag formatting rule that is applied to the Description/Excerpt within the Product, Category, Tag, Brand and Order export.', 'woo_ce' ); ?></p>
						</td>
					</tr>
<?php
		ob_end_flush();

	}

	function woo_ce_export_options_gallery_format() {

		$gallery_formatting = woo_ce_get_option( 'gallery_formatting', 0 );
		$gallery_unique = woo_ce_get_option( 'gallery_unique', 0 );
		$max_size = woo_ce_get_option( 'max_product_gallery', 3 );

		ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Product gallery formatting', 'woo_ce' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_gallery_formatting" value="0"<?php checked( $gallery_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as Post ID', 'woo_ce' ); ?></label><br />
		<label><input type="radio" name="product_gallery_formatting" value="1"<?php checked( $gallery_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as Image URL', 'woo_ce' ); ?></label>
		<hr />
		<label><input type="radio" name="product_gallery_unique" value="0"<?php checked( $gallery_unique, 0 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as a single combined image cell', 'woo_ce' ); ?></label><br />
		<label><input type="radio" name="product_gallery_unique" value="1"<?php checked( $gallery_unique, 1 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as individual image cells', 'woo_ce' ); ?></label>
		<p class="description"><?php _e( 'Choose the product gallery formatting that is accepted by your WooCommerce import Plugin (e.g. Doo Product Exporter, Product Import Suite, etc.).', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Max unique Product Gallery images', 'woo_ce' ); ?></label></th>
	<td>
		<input type="text" id="max_product_gallery" name="max_product_gallery" size="3" class="text" value="<?php echo esc_attr( $max_size ); ?>" />
		<p class="description"><?php _e( 'Manage the number of Product Gallery colums displayed when the \'Export Product Gallery as individual image cells\' Product gallery formatting option is selected.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of WooCommerce Product IDs to export process
function woo_ce_get_products( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$product_categories = false;
	$product_tags = false;
	$product_brands = false;
	$product_vendors = false;
	$product_status = false;
	$product_type = false;
	$product_stock = false;
	$product_status = false;
	$product_language = false;
	$orderby = 'ID';
	$order = 'ASC';
	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : false );
		if( !empty( $args['product_categories'] ) )
			$product_categories = $args['product_categories'];
		if( !empty( $args['product_tags'] ) )
			$product_tags = $args['product_tags'];
		if( !empty( $args['product_brands'] ) )
			$product_brands = $args['product_brands'];
		if( !empty( $args['product_vendors'] ) )
			$product_vendors = $args['product_vendors'];
		if( !empty( $args['product_status'] ) )
			$product_status = $args['product_status'];
		if( !empty( $args['product_type'] ) )
			$product_type = $args['product_type'];
		if( !empty( $args['product_stock'] ) )
			$product_stock = $args['product_stock'];
		if( !empty( $args['product_language'] ) )
			$product_language = $args['product_language'];
		if( isset( $args['product_orderby'] ) )
			$orderby = $args['product_orderby'];
		if( isset( $args['product_order'] ) )
			$order = $args['product_order'];
	}
	$post_type = apply_filters( 'woo_ce_get_products_post_type', array( 'product' ) );
	$post_status = apply_filters( 'woo_ce_get_products_status', array( 'publish', 'pending', 'draft', 'future', 'private' ) );

	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'post_status' => woo_ce_post_statuses( $post_status, true ),
		'fields' => 'ids',
		'suppress_filters' => false
	);
	// Filter Products by Product Category
	if( $product_categories ) {
		$term_taxonomy = 'product_cat';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_categories
			)
		);
	}
	// Filter Products by Product Tag
	if( $product_tags ) {
		$term_taxonomy = 'product_tag';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_tags
			)
		);
	}
	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	if( $product_brands ) {
		$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_brands
			)
		);
	}
	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( $product_vendors ) {
		$term_taxonomy = 'shop_vendor';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_vendors
			)
		);
	}
	// Filter Products by Language
	if( $product_language ) {

		global $sitepress;

		// See if our WPML integration magic sticks
		remove_filter( 'posts_where' , array( $sitepress, 'posts_where_filter' ), 10 );
		add_filter( 'posts_where' , 'woo_ce_wp_query_product_where_override_language' );

	}
	// Filter Products by Post Status
	if( $product_status )
		$args['post_status'] = woo_ce_post_statuses( $product_status, true );
	// Filter Products by Product Type
	if( $product_type ) {
		// Check if we are just exporting variations
		if( in_array( 'variation', $product_type ) && count( $product_type ) == 1 )
			$args['post_type'] = array( 'product_variation' );
		$args['meta_query'] = array(
			'relation' => 'OR'
		);
		if( in_array( 'downloadable', $product_type ) ) {
			$args['meta_query'][] = array(
				'key' => '_downloadable',
				'value' => 'yes',
				'compare' => 'EXISTS'
			);
		}
		if( in_array( 'virtual', $product_type ) ) {
			$args['meta_query'][] = array(
				'key' => '_virtual',
				'value' => 'yes'
			);
		}
		// Remove non-Term based Product Types before we tack on our tax_query
		$term_product_type = $product_type;
		foreach( $term_product_type as $key => $type ) {
			if( in_array( $type, array( 'downloadable', 'virtual', 'variation' ) ) )
				unset( $term_product_type[$key] );
		}
		if( !empty( $term_product_type ) ) {
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => $term_product_type
				)
			);
		} else {
			unset( $args['meta_query'] );
		}
		unset( $term_product_type );
	}
	$products = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_products_args', $args );

	$product_ids = new WP_Query( $args );
	if( $product_ids->posts ) {
		foreach( $product_ids->posts as $product_id ) {
			$product = get_post( $product_id );
			// Filter out variations that don't have a Parent Product that exists
			if( isset( $product->post_type ) && $product->post_type == 'product_variation' ) {
				// Check if Parent exists
				if( $product->post_parent ) {
					if( !get_post( $product->post_parent ) ) {
						unset( $product_id, $product );
						continue;
					}
				}
			}
			// Filter out Products based on the Stock Status and Quantity
			if( $product_stock ) {
				$manage_stock = get_post_meta( $product_id, '_manage_stock', true );
				$stock_status = get_post_meta( $product_id, '_stock_status', true );
				$quantity = get_post_meta( $product_id, '_stock', true );
				$quantity = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $quantity ) : $quantity );
				switch( $product_stock ) {

					case 'outofstock':
						if( ( $manage_stock == 'yes' && $quantity > 0 ) || $stock_status <> 'outofstock' ) {
							unset( $product_id, $product );
							continue;
						}
						break;

					case 'instock':
						if( ( $manage_stock == 'yes' && $quantity == 0 ) || $stock_status <> 'instock' ) {
							unset( $product_id, $product );
							continue;
						}
						break;

				}
				unset( $stock_status );
			}
			if( isset( $product_id ) )
				$products[] = $product_id;
			// Include Variables in a new WP_Query if a tax_query filter is used or WPML exists
			if( isset( $args['tax_query'] ) || woo_ce_is_wpml_activated() ) {
				$term_taxonomy = 'product_type';
				if( has_term( 'variable', $term_taxonomy, $product_id ) && ( $product_type !== false && in_array( 'variation', $product_type ) ) ) {
					$variable_args = array(
						'post_type' => 'product_variation',
						'orderby' => $orderby,
						'order' => $order,
						'post_parent' => $product_id,
						'post_status' => array( 'publish' ),
						'fields' => 'ids'
					);
					$variables = array();
					$variable_ids = new WP_Query( $variable_args );
					if( $variable_ids->posts ) {
						foreach( $variable_ids->posts as $variable_id ) {
							// Check we're not including a duplicate Product ID
							if( !in_array( $variable_id, $product_ids->posts ) )
								$products[] = $variable_id;
						}
					}
					unset( $variables, $variable_ids, $variable_args, $variable_id );
				}
			}
		}
		// Check if the global $export has been created
		if( isset( $export ) )
			$export->total_rows = count( $products );
		unset( $product_ids, $product_id );
	}
	// Filter Products by Language
	if( $product_language ) {

		global $sitepress;

		add_filter( 'posts_where' , array( $sitepress, 'posts_where_filter' ), 10, 2 );
		remove_filter( 'posts_where' , 'woo_ce_wp_query_product_where_override_language' );
	}
	return $products;

}

function woo_ce_wp_query_product_where_override_language( $where ) {

	global $export;

	$condition = '';
	if( !empty( $export->args ) ) {
		$languages = $export->args['product_language'];
		if( !empty( $languages ) ) {
			$where = " AND t.language_code IN ('" . implode( "', '", array_values( $languages ) ) . "')";
		}
	}
	return $where . $condition;

}

function woo_ce_get_product_data( $product_id = 0, $args = array(), $fields = array() ) {

	// Get Product defaults
	$weight_unit = get_option( 'woocommerce_weight_unit' );
	$dimension_unit = get_option( 'woocommerce_dimension_unit' );
	$height_unit = $dimension_unit;
	$width_unit = $dimension_unit;
	$length_unit = $dimension_unit;

	$product = get_post( $product_id );
	$_product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false );

	$product->parent_id = '';
	$product->parent_sku = '';
	if( $product->post_type == 'product_variation' ) {
		// Assign Parent ID for Variants then check if Parent exists
		if( $product->parent_id = $product->post_parent )
			$product->parent_sku = get_post_meta( $product->post_parent, '_sku', true );
		else
			$product->parent_id = '';
	}
	$product->product_id = $product_id;
	$product->sku = get_post_meta( $product_id, '_sku', true );
	$product->name = get_the_title( $product_id );
	if( $product->post_type <> 'product_variation' )
		$product->permalink = get_permalink( $product_id );
	$product->product_url = ( method_exists( $_product, 'get_permalink' ) ? $_product->get_permalink() : get_permalink( $product_id ) );
	$product->slug = $product->post_name;
	$product->description = woo_ce_format_description_excerpt( $product->post_content );
	$product->excerpt = woo_ce_format_description_excerpt( $product->post_excerpt );
	$product->price = get_post_meta( $product_id, '_regular_price', true );
	$product->sale_price = get_post_meta( $product_id, '_sale_price', true );

	// Check if we're dealing with a Variable Product Type
	$term_taxonomy = 'product_type';
	if( has_term( 'variable', $term_taxonomy, $product_id ) )
		$product->price = get_post_meta( $product_id, '_price', true );

	if( isset( $product->price ) && $product->price != '' )
		$product->price = woo_ce_format_price( $product->price );
	if( isset( $product->sale_price ) && $product->sale_price != '' )
		$product->sale_price = woo_ce_format_price( $product->sale_price );
	$product->sale_price_dates_from = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_sale_price_dates_from', true ) );
	$product->sale_price_dates_to = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_sale_price_dates_to', true ) );
	$product->post_date = woo_ce_format_date( $product->post_date );
	$product->post_modified = woo_ce_format_date( $product->post_modified );
	$product->type = woo_ce_get_product_assoc_type( $product_id );
	if( $product->post_type == 'product_variation' ) {
		// Override the Product Type for Variations
		$product->type = __( 'Variation', 'woo_ce' );
		// Override the Description and Excerpt if Variation Formatting is enabled
		if( woo_ce_get_option( 'variation_formatting', 0 ) ) {
			$parent = get_post( $product->parent_id );
			$product->description = $parent->post_content;
			$product->excerpt = $parent->post_excerpt;
			unset( $parent );
		}
	}
	$product->visibility = woo_ce_format_product_visibility( get_post_meta( $product_id, '_visibility', true ) );
	$product->featured = woo_ce_format_switch( get_post_meta( $product_id, '_featured', true ) );
	$product->virtual = woo_ce_format_switch( get_post_meta( $product_id, '_virtual', true ) );
	$product->downloadable = woo_ce_format_switch( get_post_meta( $product_id, '_downloadable', true ) );
	$product->weight = get_post_meta( $product_id, '_weight', true );
	$product->weight_unit = ( $product->weight != '' ? $weight_unit : '' );
	$product->height = get_post_meta( $product_id, '_height', true );
	$product->height_unit = ( $product->height != '' ? $height_unit : '' );
	$product->width = get_post_meta( $product_id, '_width', true );
	$product->width_unit = ( $product->width != '' ? $width_unit : '' );
	$product->length = get_post_meta( $product_id, '_length', true );
	$product->length_unit = ( $product->length != '' ? $length_unit : '' );
	$product->category = woo_ce_get_product_assoc_categories( $product_id, $product->parent_id );
	$product->tag = woo_ce_get_product_assoc_tags( $product_id );
	$product->manage_stock = woo_ce_format_switch( get_post_meta( $product_id, '_manage_stock', true ) );
	$product->allow_backorders = woo_ce_format_switch( get_post_meta( $product_id, '_backorders', true ) );
	$product->sold_individually = woo_ce_format_switch( get_post_meta( $product_id, '_sold_individually', true ) );
	$product->total_sales = get_post_meta( $product_id, 'total_sales', true );
	$product->upsell_ids = woo_ce_get_product_assoc_upsell_ids( $product_id );
	$product->crosssell_ids = woo_ce_get_product_assoc_crosssell_ids( $product_id );
	$product->quantity = get_post_meta( $product_id, '_stock', true );
	$product->quantity = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $product->quantity ) : $product->quantity );
	$product->stock_status = woo_ce_format_product_stock_status( get_post_meta( $product_id, '_stock_status', true ), $product->quantity );
	$product->image = woo_ce_get_product_assoc_featured_image( $product_id, $product->parent_id );
	$product->image_thumbnail = woo_ce_get_product_assoc_featured_image( $product_id, $product->parent_id, 'thumbnail' );
	$product->product_gallery = woo_ce_get_product_assoc_product_gallery( $product_id );
	$product->product_gallery_thumbnail = woo_ce_get_product_assoc_product_gallery( $product_id, 'thumbnail' );
	$product->tax_status = woo_ce_format_product_tax_status( get_post_meta( $product_id, '_tax_status', true ) );
	$product->tax_class = woo_ce_format_product_tax_class( get_post_meta( $product_id, '_tax_class', true ) );
	$product->shipping_class = woo_ce_get_product_assoc_shipping_class( $product_id );
	$product->external_url = get_post_meta( $product_id, '_product_url', true );
	$product->button_text = get_post_meta( $product_id, '_button_text', true );
	$product->download_file_path = woo_ce_get_product_assoc_download_files( $product_id, 'url' );
	$product->download_file_name = woo_ce_get_product_assoc_download_files( $product_id, 'name' );
	$product->download_limit = get_post_meta( $product_id, '_download_limit', true );
	$product->download_expiry = get_post_meta( $product_id, '_download_expiry', true );
	$product->download_type = woo_ce_format_product_download_type( get_post_meta( $product_id, '_download_type', true ) );
	$product->purchase_note = get_post_meta( $product_id, '_purchase_note', true );
	$product->product_status = woo_ce_format_post_status( $product->post_status );
	$product->enable_reviews = woo_ce_format_comment_status( $product->comment_status );
	$product->menu_order = $product->menu_order;

	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		// Scan for global Attributes first
		if( $product->post_type == 'product_variation' ) {
			// We're dealing with a single Variation, strap yourself in.
			foreach( $attributes as $attribute ) {
				$attribute_value = get_post_meta( $product_id, sprintf( 'attribute_pa_%s', $attribute->attribute_name ), true );
				if( !empty( $attribute_value ) ) {
					$term_id = term_exists( $attribute_value, sprintf( 'pa_%s', $attribute->attribute_name ) );
					if( $term_id !== 0 && $term_id !== null && !is_wp_error( $term_id ) ) {
						$term = get_term( $term_id['term_id'], sprintf( 'pa_%s', $attribute->attribute_name ) );
						$attribute_value = $term->name;
						unset( $term );
					}
					unset( $term_id );
				}
				$product->{'attribute_' . $attribute->attribute_name} = $attribute_value;
				unset( $attribute_value );
			}
		} else {
			// Either the Variation Parent or a Simple Product, scan for global and custom Attributes
			$product->attributes = maybe_unserialize( get_post_meta( $product_id, '_product_attributes', true ) );
			if( !empty( $product->attributes ) ) {
				$default_attributes = maybe_unserialize( get_post_meta( $product_id, '_default_attributes', true ) );
				$product->default_attributes = '';
				// Check for taxonomy-based attributes
				foreach( $attributes as $attribute ) {
					if( !empty( $default_attributes ) && is_array( $default_attributes ) ) {
						if( array_key_exists( 'pa_' . $attribute->attribute_name, $default_attributes ) )
							$product->default_attributes .= $attribute->attribute_label . ': ' . $default_attributes['pa_' . $attribute->attribute_name] . "|";
					}
					if( isset( $product->attributes['pa_' . $attribute->attribute_name] ) )
						$product->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product_id, $product->attributes['pa_' . $attribute->attribute_name], 'product' );
					else
						$product->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product_id, $attribute, 'global' );
				}
				// Check for per-Product attributes (custom)
				foreach( $product->attributes as $key => $attribute ) {
					if( $attribute['is_taxonomy'] == 0 ) {
						if( !isset( $product->{'attribute_' . $key} ) )
							$product->{'attribute_' . $key} = $attribute['value'];
					}
				}
				if( !empty( $product->default_attributes ) )
					$product->default_attributes = substr( $product->default_attributes, 0, -1 );
			}
		}
	}

	// Allow Plugin/Theme authors to add support for additional Product columns
	$product = apply_filters( 'woo_ce_product_item', $product, $product_id );

	// Trim back the Product just to requested export fields
	if( !empty( $fields ) ) {
		$fields = array_merge( $fields, array( 'id', 'ID', 'post_parent', 'filter' ) );
		if( !empty( $product ) ) {
			foreach( $product as $key => $data ) {
				if( !in_array( $key, $fields ) )
					unset( $product->$key );
			}
		}
	}

	return $product;

}

function woo_ce_get_product_title( $title = '', $post_id = '' ) {

	if( !empty( $post_id ) ) {

		$product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $post_id ) : false );
		if( !empty( $product ) ) {
			// Check if we're dealing with a Variation
			$title = $product->get_title();
			if ( $product->is_type( 'variation' ) ) {
				$list_attributes = array();
				$attributes = $product->get_variation_attributes();
				if( !empty( $attributes ) ) {
					foreach ( $attributes as $name => $attribute ) {
						$list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': ' . $attribute;
					}
					$title .= ' - ' . implode( ', ', $list_attributes );
				}
				unset( $attributes );
			}
			$sku = $product->get_sku();
			if( !empty( $sku ) )
				$title .= ' (' . sprintf( __( 'SKU: %s', 'woo_ce' ), $sku ) . ')';
			unset( $sku );
		}

	}
	return $title;

}

// Returns Product Categories associated to a specific Product
function woo_ce_get_product_assoc_categories( $product_id = 0, $parent_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_cat';
	// Return Product Categories of Parent if this is a Variation
	$categories = array();
	if( !empty( $parent_id ) )
		$product_id = $parent_id;
	if( !empty( $product_id ) )
		$categories = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $categories ) && !is_wp_error( $categories ) ) {
		$size = apply_filters( 'woo_ce_get_product_assoc_categories_size', count( $categories ) );
		for( $i = 0; $i < $size; $i++ ) {
			if( $categories[$i]->parent == '0' ) {
				$output .= $categories[$i]->name . $export->category_separator;
			} else {
				// Check if Parent -> Child
				$parent_category = get_term( $categories[$i]->parent, $term_taxonomy );
				// Check if Parent -> Child -> Subchild
				if( $parent_category->parent == '0' ) {
					$output .= $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					$output = str_replace( $parent_category->name . $export->category_separator, '', $output );
				} else {
					$root_category = get_term( $parent_category->parent, $term_taxonomy );
					$output .= $root_category->name . '>' . $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					$output = str_replace( array(
						$root_category->name . '>' . $parent_category->name . $export->category_separator,
						$parent_category->name . $export->category_separator
					), '', $output );
				}
				unset( $root_category, $parent_category );
			}
		}
		$output = substr( $output, 0, -1 );
	} else {
		$output .= __( 'Uncategorized', 'woo_ce' );
	}
	return $output;

}

// Returns Product Tags associated to a specific Product
function woo_ce_get_product_assoc_tags( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_tag';
	$tags = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $tags ) && is_wp_error( $tags ) == false ) {
		$size = count( $tags );
		for( $i = 0; $i < $size; $i++ ) {
			if( $tag = get_term( $tags[$i]->term_id, $term_taxonomy ) )
				$output .= $tag->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Featured Image associated to a specific Product
function woo_ce_get_product_assoc_featured_image( $product_id = 0, $parent_id = 0, $size = 'full' ) {

	$output = '';
	if( !empty( $product_id ) ) {
		$thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
		if( !empty( $thumbnail_id ) ) {
			if( $size == 'full' )
				$output = wp_get_attachment_url( $thumbnail_id );
			else if( $size == 'thumbnail' )
				$output = wp_get_attachment_thumb_url( $thumbnail_id );
		} else if( !empty( $parent_id ) && woo_ce_get_option( 'variation_formatting', 0 ) ) {
			// Return Feature Image of Parent if this is a Variation
			$thumbnail_id = get_post_meta( $parent_id, '_thumbnail_id', true );
			if( !empty( $thumbnail_id ) ) {
				if( $size == 'full' )
					$output = wp_get_attachment_url( $thumbnail_id );
				else if( $size == 'thumbnail' )
					$output = wp_get_attachment_thumb_url( $thumbnail_id );
			}
		}
	}
	return $output;

}

// Returns the Product Galleries associated to a specific Product
function woo_ce_get_product_assoc_product_gallery( $product_id = 0, $image_format = 'full' ) {

	global $export;

	if( !empty( $product_id ) ) {
		$images = get_post_meta( $product_id, '_product_image_gallery', true );
		if( !empty( $images ) ) {
			// Check if we're returning ID's or URL's
			if( $export->gallery_formatting == '0' ) {
				$images = explode( ',', $images );
				$output = implode( $export->category_separator, $images );
			} else if( $export->gallery_formatting == '1' ) {
				$images = explode( ',', $images );
				$size = count( $images );
				for( $i = 0; $i < $size; $i++ ) {
					// Media URL
					if( $image_format == 'full' )
						$images[$i] = wp_get_attachment_url( $images[$i] );
					else if( $image_format == 'thumbnail' )
						$images[$i] = wp_get_attachment_thumb_url( $images[$i] );
				}
				$output = implode( $export->category_separator, $images );
			}
			return $output;
		}
	}

}

// Returns the Product Type of a specific Product
function woo_ce_get_product_assoc_type( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_type';
	$types = wp_get_object_terms( $product_id, $term_taxonomy );
	if( empty( $types ) )
		$types = array( get_term_by( 'name', 'simple', $term_taxonomy ) );
	if( $types ) {
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$type = get_term( $types[$i]->term_id, $term_taxonomy );
			$output .= woo_ce_format_product_type( $type->name ) . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Shipping Class of a specific Product
function woo_ce_get_product_assoc_shipping_class( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_shipping_class';
	$types = wp_get_object_terms( $product_id, $term_taxonomy );
	if( empty( $types ) )
		$types = get_term_by( 'name', 'simple', $term_taxonomy );
	if( !empty( $types ) ) {
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$type = get_term( $types[$i]->term_id, $term_taxonomy );
			if( is_wp_error( $type ) !== true )
				$output .= $type->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Up-Sell associated to a specific Product
function woo_ce_get_product_assoc_upsell_ids( $product_id = 0 ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$upsell_ids = get_post_meta( $product_id, '_upsell_ids', true );
		// Convert Product ID to Product SKU as per Up-Sells Formatting
		if( $export->upsell_formatting == 1 && !empty( $upsell_ids ) ) {
			$size = count( $upsell_ids );
			for( $i = 0; $i < $size; $i++ ) {
				$upsell_ids[$i] = get_post_meta( $upsell_ids[$i], '_sku', true );
				if( empty( $upsell_ids[$i] ) )
					unset( $upsell_ids[$i] );
			}
			// 'reindex' array
			$upsell_ids = array_values( $upsell_ids );
		}
		$output = woo_ce_convert_product_ids( $upsell_ids );
	}
	return $output;

}

// Returns the Cross-Sell associated to a specific Product
function woo_ce_get_product_assoc_crosssell_ids( $product_id = 0 ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$crosssell_ids = get_post_meta( $product_id, '_crosssell_ids', true );
		// Convert Product ID to Product SKU as per Cross-Sells Formatting
		if( $export->crosssell_formatting == 1 && !empty( $crosssell_ids ) ) {
			$size = count( $crosssell_ids );
			for( $i = 0; $i < $size; $i++ ) {
				$crosssell_ids[$i] = get_post_meta( $crosssell_ids[$i], '_sku', true );
				// Remove Cross-Sell if SKU is empty
				if( empty( $crosssell_ids[$i] ) )
					unset( $crosssell_ids[$i] );
			}
			// 'reindex' array
			$crosssell_ids = array_values( $crosssell_ids );
		}
		$output = woo_ce_convert_product_ids( $crosssell_ids );
	}
	return $output;
	
}

// Returns Product Attributes associated to a specific Product
function woo_ce_get_product_assoc_attributes( $product_id = 0, $attribute = array(), $type = 'product' ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$terms = array();
		if( $type == 'product' ) {
			if( $attribute['is_taxonomy'] == 1 )
				$term_taxonomy = $attribute['name'];
		} else if( $type == 'global' ) {
			$term_taxonomy = 'pa_' . $attribute->attribute_name;
		}
		$terms = wp_get_object_terms( $product_id, $term_taxonomy );
		if( !empty( $terms ) && is_wp_error( $terms ) == false ) {
			$size = count( $terms );
			for( $i = 0; $i < $size; $i++ )
				$output .= $terms[$i]->name . $export->category_separator;
			unset( $terms );
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns File Downloads associated to a specific Product
function woo_ce_get_product_assoc_download_files( $product_id = 0, $type = 'url' ) {

	global $export;

	$output = '';
	if( $product_id ) {
		if( version_compare( WOOCOMMERCE_VERSION, '2.0', '>=' ) ) {
			// If WooCommerce 2.0+ is installed then use new _downloadable_files Post meta key
			if( $file_downloads = maybe_unserialize( get_post_meta( $product_id, '_downloadable_files', true ) ) ) {
				foreach( $file_downloads as $file_download ) {
					if( $type == 'url' )
						$output .= $file_download['file'] . $export->category_separator;
					else if( $type == 'name' )
						$output .= $file_download['name'] . $export->category_separator;
				}
				unset( $file_download, $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		} else {
			// If WooCommerce -2.0 is installed then use legacy _file_paths Post meta key
			if( $file_downloads = maybe_unserialize( get_post_meta( $product_id, '_file_paths', true ) ) ) {
				foreach( $file_downloads as $file_download ) {
					if( $type == 'url' )
						$output .= $file_download . $export->category_separator;
				}
				unset( $file_download, $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		}
	}
	return $output;

}

// Returns list of Product Add-on columns
function woo_ce_get_product_addons() {

	// Product Add-ons - http://www.woothemes.com/
	if( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) {
		$post_type = 'global_product_addon';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1
		);
		$output = array();

		// First grab the Global Product Add-ons
		if( $product_addons = get_posts( $args ) ) {
			foreach( $product_addons as $product_addon ) {
				if( $meta = maybe_unserialize( get_post_meta( $product_addon->ID, '_product_addons', true ) ) ) {
					$size = count( $meta );
					for( $i = 0; $i < $size; $i++ ) {
						$output[] = (object)array(
							'post_name' => $meta[$i]['name'],
							'post_title' => $meta[$i]['name'],
							'form_title' => sprintf( __( 'Global Product Add-on: %s', 'woo_ce' ), $product_addon->post_title )
						);
					}
					unset( $size );
				}
				unset( $meta );
			}
		}

		// Custom Product Add-ons
		$custom_product_addons = woo_ce_get_option( 'custom_product_addons', '' );
		if( !empty( $custom_product_addons ) ) {
			foreach( $custom_product_addons as $custom_product_addon ) {
				if( !empty( $custom_product_addon ) ) {
					$output[] = (object)array(
						'post_name' => $custom_product_addon,
						'post_title' => woo_ce_clean_export_label( $custom_product_addon ),
						'form_title' => sprintf( __( 'Custom Product Add-on: %s', 'woo_ce' ), $custom_product_addon )
					);
				}
			}
		}
		unset( $custom_product_addons, $custom_product_addon );

		if( !empty( $output ) )
			return $output;
	}

}

function woo_ce_format_product_visibility( $visibility = '' ) {

	$output = '';
	if( !empty( $visibility ) ) {
		switch( $visibility ) {

			case 'visible':
				$output = __( 'Catalog & Search', 'woo_ce' );
				break;

			case 'catalog':
				$output = __( 'Catalog', 'woo_ce' );
				break;

			case 'search':
				$output = __( 'Search', 'woo_ce' );
				break;

			case 'hidden':
				$output = __( 'Hidden', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_download_type( $download_type = '' ) {

	$output = __( 'Standard', 'woo_ce' );
	if( !empty( $download_type ) ) {
		switch( $download_type ) {

			case 'application':
				$output = __( 'Application', 'woo_ce' );
				break;

			case 'music':
				$output = __( 'Music', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_gpf_availability( $availability = null ) {

	$output = '';
	if( !empty( $availability ) ) {
		switch( $availability ) {

			case 'in stock':
				$output = __( 'In Stock', 'woo_ce' );
				break;

			case 'available for order':
				$output = __( 'Available For Order', 'woo_ce' );
				break;

			case 'preorder':
				$output = __( 'Pre-order', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_gpf_condition( $condition ) {

	$output = '';
	if( !empty( $condition ) ) {
		switch( $condition ) {

			case 'new':
				$output = __( 'New', 'woo_ce' );
				break;

			case 'refurbished':
				$output = __( 'Refurbished', 'woo_ce' );
				break;

			case 'used':
				$output = __( 'Used', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_stock_status( $stock_status = '', $stock = '' ) {

	$output = '';
	if( empty( $stock_status ) && !empty( $stock ) ) {
		if( $stock )
			$stock_status = 'instock';
		else
			$stock_status = 'outofstock';
	}
	if( $stock_status ) {
		switch( $stock_status ) {

			case 'instock':
				$output = __( 'In Stock', 'woo_ce' );
				break;

			case 'outofstock':
				$output = __( 'Out of Stock', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_tax_status( $tax_status = null ) {

	$output = '';
	if( !empty( $tax_status ) ) {
		switch( $tax_status ) {
	
			case 'taxable':
				$output = __( 'Taxable', 'woo_ce' );
				break;
	
			case 'shipping':
				$output = __( 'Shipping Only', 'woo_ce' );
				break;

			case 'none':
				$output = __( 'None', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_tax_class( $tax_class = '' ) {

	global $export;

	$output = '';
	if( $tax_class ) {
		switch( $tax_class ) {

			case '*':
				$tax_class = __( 'Standard', 'woo_ce' );
				break;

			case 'reduced-rate':
				$tax_class = __( 'Reduced Rate', 'woo_ce' );
				break;

			case 'zero-rate':
				$tax_class = __( 'Zero Rate', 'woo_ce' );
				break;

		}
		$output = $tax_class;
	}
	return $output;

}

function woo_ce_format_product_type( $type_id = '' ) {

	$output = $type_id;
	if( $output ) {
		$product_types = apply_filters( 'woo_ce_format_product_types', array(
			'simple' => __( 'Simple Product', 'woocommerce' ),
			'downloadable' => __( 'Downloadable', 'woocommerce' ),
			'grouped' => __( 'Grouped Product', 'woocommerce' ),
			'virtual' => __( 'Virtual', 'woocommerce' ),
			'variable' => __( 'Variable', 'woocommerce' ),
			'external' => __( 'External/Affiliate Product', 'woocommerce' ),
			'variation' => __( 'Variation', 'woo_ce' ),
			'subscription' => __( 'Simple Subscription', 'woo_ce' ),
			'variable-subscription' => __( 'Variable Subscription', 'woo_ce' )
		) );
		if( isset( $product_types[$type_id] ) )
			$output = $product_types[$type_id];
	}
	return $output;

}

// Returns a list of Product export columns
function woo_ce_get_product_fields( $format = 'full' ) {

	$export_type = 'product';

	$fields = array();
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'parent_sku',
		'label' => __( 'Parent SKU', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'sku',
		'label' => __( 'Product SKU', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Product Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'permalink',
		'label' => __( 'Permalink', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_url',
		'label' => __( 'Product URL', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'excerpt',
		'label' => __( 'Excerpt', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Product Published', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'post_modified',
		'label' => __( 'Product Modified', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'type',
		'label' => __( 'Type', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'visibility',
		'label' => __( 'Visibility', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'featured',
		'label' => __( 'Featured', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'virtual',
		'label' => __( 'Virtual', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'downloadable',
		'label' => __( 'Downloadable', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'price',
		'label' => __( 'Price', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'sale_price',
		'label' => __( 'Sale Price', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'sale_price_dates_from',
		'label' => __( 'Sale Price Dates From', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'sale_price_dates_to',
		'label' => __( 'Sale Price Dates To', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'weight',
		'label' => __( 'Weight', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'weight_unit',
		'label' => __( 'Weight Unit', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'height',
		'label' => __( 'Height', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'height_unit',
		'label' => __( 'Height Unit', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'width',
		'label' => __( 'Width', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'width_unit',
		'label' => __( 'Width Unit', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'length',
		'label' => __( 'Length', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'length_unit',
		'label' => __( 'Length Unit', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'category',
		'label' => __( 'Category', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'tag',
		'label' => __( 'Tag', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Featured Image', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'image_thumbnail',
		'label' => __( 'Featured Image Thumbnail', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_gallery',
		'label' => __( 'Product Gallery', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_gallery_thumbnail',
		'label' => __( 'Product Gallery Thumbnail', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'tax_status',
		'label' => __( 'Tax Status', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'tax_class',
		'label' => __( 'Tax Class', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'shipping_class',
		'label' => __( 'Shipping Class', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'download_file_name',
		'label' => __( 'Download File Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'download_file_path',
		'label' => __( 'Download File URL Path', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'download_limit',
		'label' => __( 'Download Limit', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'download_expiry',
		'label' => __( 'Download Expiry', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'download_type',
		'label' => __( 'Download Type', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'manage_stock',
		'label' => __( 'Manage Stock', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'quantity',
		'label' => __( 'Quantity', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'stock_status',
		'label' => __( 'Stock Status', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'allow_backorders',
		'label' => __( 'Allow Backorders', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'sold_individually',
		'label' => __( 'Sold Individually', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'total_sales',
		'label' => __( 'Total Sales', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'upsell_ids',
		'label' => __( 'Up-Sells', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'crosssell_ids',
		'label' => __( 'Cross-Sells', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'external_url',
		'label' => __( 'External URL', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'button_text',
		'label' => __( 'Button Text', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'purchase_note',
		'label' => __( 'Purchase Note', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_status',
		'label' => __( 'Product Status', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'enable_reviews',
		'label' => __( 'Enable Reviews', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'menu_order',
		'label' => __( 'Sort Order', 'woo_ce' )
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

function woo_ce_override_product_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'product_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_product_fields', 'woo_ce_override_product_field_labels', 11 );

function woo_ce_extend_product_fields( $fields ) {

	// Attributes
	$has_attributes = false;
	$attribute_taxonomies = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
	if( !empty( $attribute_taxonomies ) ) {
		$has_attributes = true;
		foreach( $attribute_taxonomies as $tax ) {
			$label = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
			$fields[] = array(
				'name' => sprintf( 'attribute_%s', esc_attr( $tax->attribute_name ) ),
				'label' => sprintf( __( 'Attribute: %s', 'woo_ce' ), esc_attr( $label ) )
			);
		}
		unset( $attribute_taxonomies, $tax, $label );
	}

	// Custom Attributes
	$custom_attributes = woo_ce_get_option( 'custom_attributes', '' );
	if( !empty( $custom_attributes ) ) {
		$has_attributes = true;
		foreach( $custom_attributes as $custom_attribute ) {
			if( !empty( $custom_attribute ) ) {
				$fields[] = array(
					'name' => sprintf( 'attribute_%s', $custom_attribute ),
					'label' => sprintf( __( 'Attribute: %s', 'woo_ce' ), woo_ce_clean_export_label( $custom_attribute ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_product_fields_custom_attribute_hover', '%s: %s' ), __( 'Custom Attribute', 'woo_ce' ), $custom_attribute )
				);
			}
		}
		unset( $custom_attributes, $custom_attribute );
	}

	// Show Default Attributes field
	if( $has_attributes ) {
		$fields[] = array(
			'name' => 'default_attributes',
			'label' => __( 'Default Attributes', 'woo_ce' )
		);
	}

/*
	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( empty( $attribute->attribute_label ) )
				$attribute->attribute_label = $attribute->attribute_name;
			$fields[] = array(
				'name' => sprintf( 'attribute_%s', $attribute->attribute_name ),
				'label' => sprintf( __( 'Attribute: %s', 'woo_ce' ), $attribute->attribute_label )
			);
		}
	}
*/

	// Advanced Google Product Feed - http://www.leewillis.co.uk/wordpress-plugins/
	if( function_exists( 'woocommerce_gpf_install' ) ) {
		$fields[] = array(
			'name' => 'gpf_availability',
			'label' => __( 'Advanced Google Product Feed - Availability', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_condition',
			'label' => __( 'Advanced Google Product Feed - Condition', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_brand',
			'label' => __( 'Advanced Google Product Feed - Brand', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_product_type',
			'label' => __( 'Advanced Google Product Feed - Product Type', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_google_product_category',
			'label' => __( 'Advanced Google Product Feed - Google Product Category', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_gtin',
			'label' => __( 'Advanced Google Product Feed - Global Trade Item Number (GTIN)', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_mpn',
			'label' => __( 'Advanced Google Product Feed - Manufacturer Part Number (MPN)', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_gender',
			'label' => __( 'Advanced Google Product Feed - Gender', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_agegroup',
			'label' => __( 'Advanced Google Product Feed - Age Group', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_colour',
			'label' => __( 'Advanced Google Product Feed - Colour', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'gpf_size',
			'label' => __( 'Advanced Google Product Feed - Size', 'woo_ce' )
		);
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( function_exists( 'aioseop_activate' ) ) {
		$fields[] = array(
			'name' => 'aioseop_keywords',
			'label' => __( 'All in One SEO - Keywords', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_description',
			'label' => __( 'All in One SEO - Description', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_title',
			'label' => __( 'All in One SEO - Title', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_title_attributes',
			'label' => __( 'All in One SEO - Title Attributes', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'aioseop_menu_label',
			'label' => __( 'All in One SEO - Menu Label', 'woo_ce' )
		);
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( function_exists( 'wpseo_admin_init' ) ) {
		$fields[] = array(
			'name' => 'wpseo_focuskw',
			'label' => __( 'WordPress SEO - Focus Keyword', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'wpseo_metadesc',
			'label' => __( 'WordPress SEO - Meta Description', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'wpseo_title',
			'label' => __( 'WordPress SEO - SEO Title', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'wpseo_googleplus_description',
			'label' => __( 'WordPress SEO - Google+ Description', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_description',
			'label' => __( 'WordPress SEO - Facebook Description', 'woo_ce' )
		);
	}

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( function_exists( 'su_wp_incompat_notice' ) ) {
		$fields[] = array(
			'name' => 'useo_meta_title',
			'label' => __( 'Ultimate SEO - Title Tag', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'useo_meta_description',
			'label' => __( 'Ultimate SEO - Meta Description', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'useo_meta_keywords',
			'label' => __( 'Ultimate SEO - Meta Keywords', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'useo_social_title',
			'label' => __( 'Ultimate SEO - Social Title', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'useo_social_description',
			'label' => __( 'Ultimate SEO - Social Description', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noindex',
			'label' => __( 'Ultimate SEO - NoIndex', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noautolinks',
			'label' => __( 'Ultimate SEO - Disable Autolinks', 'woo_ce' )
		);
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$fields[] = array(
			'name' => 'msrp',
			'label' => __( 'MSRP', 'woo_ce' ),
			'hover' => __( 'Manufacturer Suggested Retail Price (MSRP)', 'woo_ce' )
		);
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( class_exists( 'WC_Brands' ) || class_exists( 'woo_brands' ) || taxonomy_exists( apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' ) ) ) {
		$fields[] = array(
			'name' => 'brands',
			'label' => __( 'Brands', 'woo_ce' )
		);
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$fields[] = array(
			'name' => 'cost_of_goods',
			'label' => __( 'Cost of Goods', 'woo_ce' )
		);
	}

	// Per-Product Shipping - http://www.woothemes.com/products/per-product-shipping/
	if( function_exists( 'woocommerce_per_product_shipping_init' ) ) {
		$fields[] = array(
			'name' => 'per_product_shipping',
			'label' => __( 'Per-Product Shipping', 'woo_ce' )
		);
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		$fields[] = array(
			'name' => 'vendors',
			'label' => __( 'Product Vendors', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'vendor_ids',
			'label' => __( 'Product Vendor ID\'s', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'vendor_commission',
			'label' => __( 'Vendor Commission', 'woo_ce' )
		);
	}

	// WooCommerce Wholesale Pricing - http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/
	if( class_exists( 'woocommerce_wholesale_pricing' ) ) {
		$fields[] = array(
			'name' => 'wholesale_price',
			'label' => __( 'Wholesale Price', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'wholesale_price_text',
			'label' => __( 'Wholesale Text', 'woo_ce' )
		);
	}

	// Advanced Custom Fields - http://www.advancedcustomfields.com
	if( class_exists( 'acf' ) ) {
		if( $custom_fields = woo_ce_get_acf_product_fields() ) {
			foreach( $custom_fields as $custom_field ) {
				$fields[] = array(
					'name' => $custom_field['name'],
					'label' => $custom_field['label']
				);
			}
			unset( $custom_fields, $custom_field );
		}
	}

	// WooCommerce Subscriptions -
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		$fields[] = array(
			'name' => 'subscription_price',
			'label' => __( 'Subscription Price', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_period_interval',
			'label' => __( 'Subscription Period Interval', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_period',
			'label' => __( 'Subscription Period', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_length',
			'label' => __( 'Subscription Length', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_sign_up_fee',
			'label' => __( 'Subscription Sign-up Fee', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_trial_length',
			'label' => __( 'Subscription Trial Length', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_trial_period',
			'label' => __( 'Subscription Trial Period', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'subscription_limit',
			'label' => __( 'Limit Subscription', 'woo_ce' )
		);
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$fields[] = array(
			'name' => 'booking_has_persons',
			'label' => __( 'Booking Has Persons', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'booking_has_resources',
			'label' => __( 'Booking Has Resources', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'booking_base_cost',
			'label' => __( 'Booking Base Cost', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'booking_block_cost',
			'label' => __( 'Booking Block Cost', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'booking_display_cost',
			'label' => __( 'Booking Display Cost', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'booking_requires_confirmation',
			'label' => __( 'Booking Requires Confirmation', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'booking_user_can_cancel',
			'label' => __( 'Booking Can Be Cancelled', 'woo_ce' )
		);
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		$fields[] = array(
			'name' => 'barcode_type',
			'label' => __( 'Barcode Type', 'woo_ce' )
		);
		$fields[] = array(
			'name' => 'barcode',
			'label' => __( 'Barcode', 'woo_ce' )
		);
	}

	// Custom Product meta
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$fields[] = array(
					'name' => $custom_product,
					'label' => woo_ce_clean_export_label( $custom_product ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_product_fields_custom_product_hover', '%s: %s' ), __( 'Custom Product', 'woo_ce' ), $custom_product )
				);
			}
		}
	}
	unset( $custom_products, $custom_product );

	return $fields;

}
add_filter( 'woo_ce_product_fields', 'woo_ce_extend_product_fields' );

// Returns the export column header label based on an export column slug
function woo_ce_get_product_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_product_fields();
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

function woo_ce_product_fields_gm_rss( $fields = array() ) {

	foreach( $fields as $key => $field ) {
		switch( $field['name'] ) {

			case 'product_id':
				$fields[$key]['label'] = 'g:id';
				break;

			case 'name':
				$fields[$key]['label'] = 'g:title';
				break;

			case 'description':
				$fields[$key]['label'] = 'g:description';
				break;

			case 'product_url':
				$fields[$key]['label'] = 'g:link';
				break;

			case 'image':
				$fields[$key]['label'] = 'g:image_link';
				break;

/*
			case 'condition':
				break;
*/

			case 'stock_status':
				$fields[$key]['label'] = 'g:availability';
				break;

			case 'price':
				$fields[$key]['label'] = 'g:price';
				break;

		}
	}
	return $fields;

}

// Returns a list of WooCommerce Product Types to export process
function woo_ce_get_product_types() {

	$term_taxonomy = 'product_type';
	$args = array(
		'hide_empty' => 0
	);
	$types = get_terms( $term_taxonomy, $args );
	if( !empty( $types ) && is_wp_error( $types ) == false ) {
		$output = array();
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$output[$types[$i]->slug] = array(
				'name' => ucfirst( $types[$i]->name ),
				'count' => $types[$i]->count
			);
			// Override the Product Type count for Downloadable and Virtual
			if( in_array( $types[$i]->slug, array( 'downloadable', 'virtual' ) ) ) {
				if( $types[$i]->slug == 'downloadable' ) {
					$args = array(
						'meta_key' => '_downloadable',
						'meta_value' => 'yes'
					);
				} else if( $types[$i]->slug == 'virtual' ) {
					$args = array(
						'meta_key' => '_virtual',
						'meta_value' => 'yes'
					);
				}
				$output[$types[$i]->slug]['count'] = woo_ce_get_product_type_count( 'product', $args );
			}
		}
		$output['variation'] = array(
			'name' => __( 'variation', 'woo_ce' ),
			'count' => woo_ce_get_product_type_count( 'product_variation' )
		);
		asort( $output );
		return $output;
	}

}

function woo_ce_get_product_type_count( $post_type = 'product', $args = array() ) {

	$defaults = array(
		'post_type' => $post_type,
		'posts_per_page' => 1,
		'fields' => 'ids'
	);
	$args = wp_parse_args( $args, $defaults );
	$product_ids = new WP_Query( $args );
	$size = $product_ids->found_posts;
	return $size;

}

// Returns a list of WooCommerce Product Attributes to export process
function woo_ce_get_product_attributes() {

	global $wpdb;

	$output = array();
	$attributes_sql = "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies";
	$attributes = $wpdb->get_results( $attributes_sql );
	$wpdb->flush();
	if( !empty( $attributes ) ) {
		$output = $attributes;
		unset( $attributes );
	} else {
		$output = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
	}
	return $output;

}

function woo_ce_get_acf_product_fields() {

	global $wpdb;

	$post_type = 'acf';
	$args = array(
		'post_type' => $post_type,
		'numberposts' => -1
	);
	if( $field_groups = get_posts( $args ) ) {
		$fields = array();
		$post_types = array( 'product', 'product_variation' );
		foreach( $field_groups as $field_group ) {
			$has_fields = false;
			if( $rules = get_post_meta( $field_group->ID, 'rule' ) ) {
				$size = count( $rules );
				for( $i = 0; $i < $size; $i++ ) {
					if( ( $rules[$i]['param'] == 'post_type' ) && ( $rules[$i]['operator'] == '==' ) && ( in_array( $rules[$i]['value'], $post_types ) ) ) {
						$has_fields = true;
						$i = $size;
					}
				}
			}
			unset( $rules );
			if( $has_fields ) {
				$custom_fields_sql = "SELECT `meta_value` FROM `" . $wpdb->postmeta . "` WHERE `post_id` = " . absint( $field_group->ID ) . " AND `meta_key` LIKE 'field_%'";
				if( $custom_fields = $wpdb->get_col( $custom_fields_sql ) ) {
					foreach( $custom_fields as $custom_field ) {
						$custom_field = maybe_unserialize( $custom_field );
						$fields[] = array(
							'name' => $custom_field['name'],
							'label' => $custom_field['label']
						);
					}
				}
				unset( $custom_fields, $custom_field );
			}
		}
		return $fields;
	}

}

function woo_ce_get_product_assoc_brands( $product_id = 0, $parent_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
	// Return Product Brands of Parent if this is a Variation
	if( $parent_id )
		$product_id = $parent_id;
	if( $product_id )
		$brands = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $brands ) && is_wp_error( $brands ) == false ) {
		$size = count( $brands );
		for( $i = 0; $i < $size; $i++ ) {
			if( $brands[$i]->parent == '0' ) {
				$output .= $brands[$i]->name . $export->category_separator;
			} else {
				// Check if Parent -> Child
				$parent_brand = get_term( $brands[$i]->parent, $term_taxonomy );
				// Check if Parent -> Child -> Subchild
				if( $parent_brand->parent == '0' ) {
					$output .= $parent_brand->name . '>' . $brands[$i]->name . $export->category_separator;
					$output = str_replace( $parent_brand->name . $export->category_separator, '', $output );
				} else {
					$root_brand = get_term( $parent_brand->parent, $term_taxonomy );
					$output .= $root_brand->name . '>' . $parent_brand->name . '>' . $brands[$i]->name . $export->category_separator;
					$output = str_replace( array(
						$root_brand->name . '>' . $parent_brand->name . $export->category_separator,
						$parent_brand->name . $export->category_separator
					), '', $output );
				}
				unset( $root_brand, $parent_brand );
			}
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

function woo_ce_extend_product_item( $product, $product_id ) {

	global $export;

	// Advanced Google Product Feed - http://plugins.leewillis.co.uk/downloads/wp-e-commerce-product-feeds/
	if( function_exists( 'woocommerce_gpf_install' ) ) {
		$product->gpf_data = get_post_meta( $product_id, '_woocommerce_gpf_data', true );
		$product->gpf_availability = ( isset( $product->gpf_data['availability'] ) ? woo_ce_format_gpf_availability( $product->gpf_data['availability'] ) : '' );
		$product->gpf_condition = ( isset( $product->gpf_data['condition'] ) ? woo_ce_format_gpf_condition( $product->gpf_data['condition'] ) : '' );
		$product->gpf_brand = ( isset( $product->gpf_data['brand'] ) ? $product->gpf_data['brand'] : '' );
		$product->gpf_product_type = ( isset( $product->gpf_data['product_type'] ) ? $product->gpf_data['product_type'] : '' );
		$product->gpf_google_product_category = ( isset( $product->gpf_data['google_product_category'] ) ? $product->gpf_data['google_product_category'] : '' );
		$product->gpf_gtin = ( isset( $product->gpf_data['gtin'] ) ? $product->gpf_data['gtin'] : '' );
		$product->gpf_mpn = ( isset( $product->gpf_data['mpn'] ) ? $product->gpf_data['mpn'] : '' );
		$product->gpf_gender = ( isset( $product->gpf_data['gender'] ) ? $product->gpf_data['gender'] : '' );
		$product->gpf_age_group = ( isset( $product->gpf_data['age_group'] ) ? $product->gpf_data['age_group'] : '' );
		$product->gpf_color = ( isset( $product->gpf_data['color'] ) ? $product->gpf_data['color'] : '' );
		$product->gpf_size = ( isset( $product->gpf_data['size'] ) ? $product->gpf_data['size'] : '' );
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( function_exists( 'aioseop_activate' ) ) {
		$product->aioseop_keywords = get_post_meta( $product_id, '_aioseop_keywords', true );
		$product->aioseop_description = get_post_meta( $product_id, '_aioseop_description', true );
		$product->aioseop_title = get_post_meta( $product_id, '_aioseop_title', true );
		$product->aioseop_title_attributes = get_post_meta( $product_id, '_aioseop_titleatr', true );
		$product->aioseop_menu_label = get_post_meta( $product_id, '_aioseop_menulabel', true );
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( function_exists( 'wpseo_admin_init' ) ) {
		$product->wpseo_focuskw = get_post_meta( $product_id, '_yoast_wpseo_focuskw', true );
		$product->wpseo_metadesc = get_post_meta( $product_id, '_yoast_wpseo_metadesc', true );
		$product->wpseo_title = get_post_meta( $product_id, '_yoast_wpseo_title', true );
		$product->wpseo_googleplus_description = get_post_meta( $product_id, '_yoast_wpseo_google-plus-description', true );
		$product->wpseo_opengraph_description = get_post_meta( $product_id, '_yoast_wpseo_opengraph-description', true );
	}

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( function_exists( 'su_wp_incompat_notice' ) ) {
		$product->useo_meta_title = get_post_meta( $product_id, '_su_title', true );
		$product->useo_meta_description = get_post_meta( $product_id, '_su_description', true );
		$product->useo_meta_keywords = get_post_meta( $product_id, '_su_keywords', true );
		$product->useo_social_title = get_post_meta( $product_id, '_su_og_title', true );
		$product->useo_social_description = get_post_meta( $product_id, '_su_og_description', true );
		$product->useo_meta_noindex = get_post_meta( $product_id, '_su_meta_robots_noindex', true );
		$product->useo_meta_noautolinks = get_post_meta( $product_id, '_su_disable_autolinks', true );
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$product->msrp = get_post_meta( $product_id, '_msrp_price', true );
		if( $product->msrp == false && $product->post_type == 'product_variation' )
			$product->msrp = get_post_meta( $product_id, '_msrp', true );
			// Check that a valid price has been provided
			if( isset( $product->msrp ) && $product->msrp != '' )
				$product->msrp = woo_ce_format_price( $product->msrp );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( class_exists( 'WC_Brands' ) || class_exists( 'woo_brands' ) || taxonomy_exists( apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' ) ) )
		$product->brands = woo_ce_get_product_assoc_brands( $product_id, $product->parent_id );

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) )
		$product->cost_of_goods = get_post_meta( $product_id, '_wc_cog_cost', true );

	// Per-Product Shipping - http://www.woothemes.com/products/per-product-shipping/
	if( function_exists( 'woocommerce_per_product_shipping_init' ) )
		$product->per_product_shipping = get_post_meta( $product_id, '_per_product_shipping', true );

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		$product->vendors = woo_ce_get_product_assoc_product_vendors( $product_id, $product->parent_id );
		$product->vendor_ids = woo_ce_get_product_assoc_product_vendors( $product_id, $product->parent_id, 'term_id' );
		$product->vendor_commission = woo_ce_get_product_assoc_product_vendor_commission( $product_id, $product->vendor_ids );
	}

	// WooCommerce Wholesale Pricing - http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/
	if( class_exists( 'woocommerce_wholesale_pricing' ) ) {
		$product->wholesale_price = woo_ce_format_price( get_post_meta( $product_id, 'wholesale_price', true ) );
		$product->wholesale_price_text = get_post_meta( $product_id, 'wholesale_price_text', true );
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		$product->subscription_price = get_post_meta( $product_id, '_subscription_price', true );
		$product->subscription_period_interval = woo_ce_format_product_subscription_period_interval( get_post_meta( $product_id, '_subscription_period_interval', true ) );
		$product->subscription_period = get_post_meta( $product_id, '_subscription_period', true );
		$product->subscription_length = woo_ce_format_product_subscripion_length( get_post_meta( $product_id, '_subscription_length', true ), $product->subscription_period );
		$product->subscription_sign_up_fee = get_post_meta( $product_id, '_subscription_sign_up_fee', true );
		$product->subscription_trial_length = get_post_meta( $product_id, '_subscription_trial_length', true );
		$product->subscription_trial_period = get_post_meta( $product_id, '_subscription_trial_period', true );
		$product->subscription_limit = woo_ce_format_product_subscription_limit( get_post_meta( $product_id, '_subscription_limit', true ) );
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$product->booking_has_persons = get_post_meta( $product_id, '_wc_booking_has_persons', true );
		$product->booking_has_resources = get_post_meta( $product_id, '_wc_booking_has_resources', true );
		$product->booking_base_cost = get_post_meta( $product_id, '_wc_booking_cost', true );
		$product->booking_block_cost = get_post_meta( $product_id, '_wc_booking_base_cost', true );
		$product->booking_display_cost = get_post_meta( $product_id, '_wc_display_cost', true );
		$product->booking_requires_confirmation = get_post_meta( $product_id, '_wc_booking_requires_confirmation', true );
		$product->booking_user_can_cancel = get_post_meta( $product_id, '_wc_booking_user_can_cancel', true );
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		// Cannot clean up the barcode type as the developer has not exposed any functions or methods
		$product->barcode_type = get_post_meta( $product_id, '_barcode_type', true );
		$product->barcode = get_post_meta( $product_id, '_barcode', true );
	}

	// Custom Product meta
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$product->{$custom_product} = woo_ce_format_custom_meta( get_post_meta( $product_id, $custom_product, true ) );
			}
		}
	}

	if( $export->gallery_unique ) {
		$max_size = woo_ce_get_option( 'max_product_gallery', 3 );
		if( !empty( $product->product_gallery ) ) {
			// Tack on a extra digit to max_size so we get the correct number of columns
			$max_size++;
			$product_gallery = explode( $export->category_separator, $product->product_gallery );
			$size = count( $product_gallery );
			for( $i = 1; $i < $size; $i++ ) {
				if( $i == $max_size )
					break;
				$product->{'product_gallery_' . $i} = $product_gallery[$i];
			}
			$product->product_gallery = $product_gallery[0];
			unset( $product_gallery );
		}
	}

	return $product;

}
add_filter( 'woo_ce_product_item', 'woo_ce_extend_product_item', 10, 2 );

function woo_ce_format_product_sale_price_dates( $sale_date = '' ) {

	$output = $sale_date;
	if( $sale_date )
		$output = woo_ce_format_date( date( 'Y-m-d H:i:s', $sale_date ) );
	return $output;

}

// Returns a list of Attribute export columns
function woo_ce_get_attribute_fields( $format = 'full' ) {

	$export_type = 'attribute';

	$fields = array();
	$fields[] = array(
		'name' => 'attribute',
		'label' => __( 'Attribute', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'term_name',
		'label' => __( 'Term Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'term_slug',
		'label' => __( 'Term Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'term_parent',
		'label' => __( 'Term Parent', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'term_description',
		'label' => __( 'Term Description', 'woo_ce' )
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
			usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_unique_product_gallery_fields( $fields = array() ) {

	$max_size = woo_ce_get_option( 'max_product_gallery', 3 );
	if( !empty( $fields ) ) {
		// Tack on a extra digit to max_size so we get the correct number of columns
		$max_size++;
		for( $i = 1; $i < $max_size; $i++ ) {
			if( isset( $fields['product_gallery'] ) )
				$fields[sprintf( 'product_gallery_%d', $i )] = 'on';
		}
	}
	return $fields;

}

function woo_ce_unique_product_gallery_columns( $columns = array(), $fields = array() ) {

	$max_size = woo_ce_get_option( 'max_product_gallery', 3 );
	if( !empty( $columns ) ) {
		// Tack on a extra digit to max_size so we get the correct number of columns
		$max_size++;
		for( $i = 1; $i < $max_size; $i++ ) {
			if( isset( $fields[sprintf( 'product_gallery_%d', $i )] ) )
				$columns[] = sprintf( apply_filters( 'woo_ce_unique_product_gallery_column', __( '%s #%d', 'woo_ce' ) ), woo_ce_get_product_field( 'product_gallery' ), $i );
		}
	}
	return $columns;

}

// Returns the export column header label based on an export column slug
function woo_ce_get_attribute_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_attribute_fields();
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

/*
function woo_ce_get_attributes( $args = array() ) {

}
*/
?>