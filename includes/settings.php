<?php
function woo_ce_export_settings_quicklinks() {

	ob_start(); ?>
<li>| <a href="#xml-settings"><?php _e( 'XML Settings', 'woo_ce' ); ?></a> |</li>
<li><a href="#rss-settings"><?php _e( 'RSS Settings', 'woo_ce' ); ?></a> |</li>
<li><a href="#scheduled-exports"><?php _e( 'Scheduled Exports', 'woo_ce' ); ?></a> |</li>
<li><a href="#cron-exports"><?php _e( 'CRON Exports', 'woo_ce' ); ?></a> |</li>
<li><a href="#orders-screen"><?php _e( 'Orders Screen', 'woo_ce' ); ?></a> |</li>
<li><a href="#export-triggers"><?php _e( 'Export Triggers', 'woo_ce' ); ?></a></li>
<?php
	ob_end_flush();

}

function woo_ce_export_settings_csv() {

	$header_formatting = woo_ce_get_option( 'header_formatting', 1 );

	ob_start(); ?>
<tr>
	<th>
		<label for="header_formatting"><?php _e( 'Header formatting', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" name="header_formatting" value="1"<?php checked( $header_formatting, '1' ); ?> />&nbsp;<?php _e( 'Include export field column headers', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="header_formatting" value="0"<?php checked( $header_formatting, '0' ); ?> />&nbsp;<?php _e( 'Do not include export field column headers', 'woo_ce' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Choose the header format that suits your spreadsheet software (e.g. Excel, OpenOffice, etc.). This rule applies to CSV, XLS and XLSX export types.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();
	
}

// Returns the HTML template for the CRON, scheduled exports, Secret Export Key and Export Trigger options for the Settings screen
function woo_ce_export_settings_extend() {

	// XML settings
	$xml_attribute_url = woo_ce_get_option( 'xml_attribute_url', 1 );
	$xml_attribute_title = woo_ce_get_option( 'xml_attribute_title', 1 );
	$xml_attribute_date = woo_ce_get_option( 'xml_attribute_date', 1 );
	$xml_attribute_time = woo_ce_get_option( 'xml_attribute_time', 0 );
	$xml_attribute_export = woo_ce_get_option( 'xml_attribute_export', 1 );
	$xml_attribute_orderby = woo_ce_get_option( 'xml_attribute_orderby', 0 );
	$xml_attribute_order = woo_ce_get_option( 'xml_attribute_order', 0 );
	$xml_attribute_limit = woo_ce_get_option( 'xml_attribute_limit', 0 );
	$xml_attribute_offset = woo_ce_get_option( 'xml_attribute_offset', 0 );

	// RSS settings
	$rss_title = woo_ce_get_option( 'rss_title', '' );
	$rss_link = woo_ce_get_option( 'rss_link', '' );
	$rss_description = woo_ce_get_option( 'rss_description', '' );

	// Scheduled exports
	$enable_auto = woo_ce_get_option( 'enable_auto', 0 );
	$auto_schedule = woo_ce_get_option( 'auto_schedule', 'custom' );
	$auto_interval = woo_ce_get_option( 'auto_interval', 1440 );
	$auto_commence = woo_ce_get_option( 'auto_commence', 'now' );
	$auto_commence_date = woo_ce_get_option( 'auto_commence_date', date( 'd/m/Y H:i', current_time( 'timestamp', 1 ) ) );
	// Legacy update of Comment Date to support time
	if( strlen( $auto_commence_date ) <= 10 ) {
		$auto_commence_date .= sprintf( ' %s', date( 'H:i', current_time( 'timestamp', 1 ) ) );
	}
	if( $enable_auto == 1 ) {
		if( ( $next_export = woo_ce_next_scheduled_export() ) == false )
			$next_export = __( 'a little while... just waiting on WP-CRON to refresh its task list', 'woo_ce' );
		if( $auto_schedule <> 'custom' )
			$next_export = sprintf( __( '%s (in %s)', 'woo_ce' ), $auto_schedule, $next_export );
	}
	$auto_type = woo_ce_get_option( 'auto_type', 'product' );
	$types = woo_ce_return_export_types();
	$order_statuses = woo_ce_get_order_statuses();
	$product_types = woo_ce_get_product_types();
	$args = array(
		'hide_empty' => 1
	);
	$product_categories = woo_ce_get_product_categories( $args );
	$product_tags = woo_ce_get_product_tags( $args );
	$product_statuses = get_post_statuses();
	if( !isset( $product_statuses['trash'] ) )
		$product_statuses['trash'] = __( 'Trash', 'woo_ce' );

	$product_filter_type = woo_ce_get_option( 'auto_product_type', array() );
	$product_filter_status = woo_ce_get_option( 'auto_product_status', array() );
	$product_filter_stock = woo_ce_get_option( 'auto_product_stock', false );
	$product_filter_category = woo_ce_get_option( 'auto_product_category', array() );
	$product_filter_tag = woo_ce_get_option( 'auto_product_tag', array() );

	$order_filter_status = woo_ce_get_option( 'auto_order_status', array() );
	if( empty( $order_filter_status ) )
		$order_filter_status = array();
	$order_filter_product = woo_ce_get_option( 'auto_order_product', array() );
	if( empty( $order_filter_product ) )
		$order_filter_product = array();
	$order_filter_date = woo_ce_get_option( 'auto_order_date', false );
	$order_filter_dates_from = woo_ce_get_option( 'auto_order_dates_from', '' );
	$order_filter_dates_to = woo_ce_get_option( 'auto_order_dates_to', '' );
	$order_filter_date_variable = woo_ce_get_option( 'auto_order_date_variable', '' );
	$order_filter_date_variable_length = woo_ce_get_option( 'auto_order_date_variable_length', '' );
	$countries = woo_ce_allowed_countries();
	$order_filter_billing_country = woo_ce_get_option( 'auto_order_billing_country', array() );
	$order_filter_shipping_country = woo_ce_get_option( 'auto_order_shipping_country', array() );
	$payment_gateways = woo_ce_get_order_payment_gateways();
	$order_filter_payment = woo_ce_get_option( 'auto_order_payment', array() );
	$shipping_methods = woo_ce_get_order_shipping_methods();
	$order_filter_shipping = woo_ce_get_option( 'auto_order_shipping', array() );

	$auto_format = woo_ce_get_option( 'auto_format', 'csv' );
	$auto_method = woo_ce_get_option( 'auto_method', 'archive' );

	// Send to e-mail
	$email_to = woo_ce_get_option( 'email_to', get_option( 'admin_email', '' ) );
	$email_subject = woo_ce_get_option( 'email_subject', '' );
	// Default subject
	if( empty( $email_subject ) )
		$email_subject = __( '[%store_name%] Export: %export_type% (%export_filename%)', 'woo_ce' );

	// Post to remote URL
	$post_to = woo_ce_get_option( 'post_to', '' );

	// Export to FTP
	$ftp_method_host = woo_ce_get_option( 'auto_ftp_method_host', '' );
	$ftp_method_port = woo_ce_get_option( 'auto_ftp_method_port', '' );
	$ftp_method_protocol = woo_ce_get_option( 'auto_ftp_method_protocol', 'ftp' );
	$ftp_method_user = woo_ce_get_option( 'auto_ftp_method_user', '' );
	$ftp_method_pass = woo_ce_get_option( 'auto_ftp_method_pass', '' );
	$ftp_method_path = woo_ce_get_option( 'auto_ftp_method_path', '' );
	$ftp_method_filename = woo_ce_get_option( 'auto_ftp_method_filename', '' );
	$ftp_method_passive = woo_ce_get_option( 'auto_ftp_method_passive', '' );
	$ftp_method_timeout = woo_ce_get_option( 'auto_ftp_method_timeout', '' );

	$scheduled_fields = woo_ce_get_option( 'scheduled_fields', 'all' );

	// CRON exports
	$enable_cron = woo_ce_get_option( 'enable_cron', 0 );
	$secret_key = woo_ce_get_option( 'secret_key', '' );

	$cron_fields = woo_ce_get_option( 'cron_fields', 'all' );

	// Orders Screen
	$order_actions_csv = woo_ce_get_option( 'order_actions_csv', 1 );
	$order_actions_xml = woo_ce_get_option( 'order_actions_xml', 0 );
	$order_actions_xls = woo_ce_get_option( 'order_actions_xls', 1 );
	$order_actions_xlsx = woo_ce_get_option( 'order_actions_xlsx', 1 );

	// Export Triggers
	$enable_trigger_new_order = woo_ce_get_option( 'enable_trigger_new_order', 0 );
	$trigger_new_order_format = woo_ce_get_option( 'trigger_new_order_format', 'csv' );
	$trigger_new_order_method = woo_ce_get_option( 'trigger_new_order_format', 'archive' );
	$trigger_new_order_fields = woo_ce_get_option( 'trigger_new_order_fields', 'all' );

	$troubleshooting_url = '';

	ob_start(); ?>
<tr id="xml-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'XML Settings', 'woo_ce' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Attribute display', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="xml_attribute_url" value="1"<?php checked( $xml_attribute_url ); ?> /> <?php _e( 'Site Address', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_title" value="1"<?php checked( $xml_attribute_title ); ?> /> <?php _e( 'Site Title', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_date" value="1"<?php checked( $xml_attribute_date ); ?> /> <?php _e( 'Export Date', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_time" value="1"<?php checked( $xml_attribute_time ); ?> /> <?php _e( 'Export Time', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_export" value="1"<?php checked( $xml_attribute_export ); ?> /> <?php _e( 'Export Type', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_orderby" value="1"<?php checked( $xml_attribute_orderby ); ?> /> <?php _e( 'Export Order By', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_order" value="1"<?php checked( $xml_attribute_order ); ?> /> <?php _e( 'Export Order', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_limit" value="1"<?php checked( $xml_attribute_limit ); ?> /> <?php _e( 'Limit Volume', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="xml_attribute_offset" value="1"<?php checked( $xml_attribute_offset ); ?> /> <?php _e( 'Volume Offset', 'woo_ce' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different attributes in the XML export.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="rss-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'RSS Settings', 'woo_ce' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_title"><?php _e( 'Title element', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="rss_title" type="text" id="rss_title" value="<?php echo esc_attr( $rss_title ); ?>" class="large-text" />
		<p class="description"><?php _e( 'Defines the title of the data feed (e.g. Product export for WordPress Shop).', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_link"><?php _e( 'Link element', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="rss_link" type="text" id="rss_link" value="<?php echo esc_attr( $rss_link ); ?>" class="large-text" />
		<p class="description"><?php _e( 'A link to your website, this doesn\'t have to be the location of the RSS feed.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_description"><?php _e( 'Description element', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="rss_description" type="text" id="rss_description" value="<?php echo esc_attr( $rss_description ); ?>" class="large-text" />
		<p class="description"><?php _e( 'A description of your data feed.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="scheduled-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3>
			<div class="dashicons dashicons-calendar"></div>&nbsp;<?php _e( 'Scheduled Exports', 'woo_ce' ); ?>
			<!-- <a href="/woocommerce/wp-admin/admin.php?page=woo_ce&amp;tab=export" class="add-new-h2">Add New</a> -->
		</h3>
<?php if( $enable_auto == 1 ) { ?>
		<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php printf( __( 'Scheduled Exports is enabled, next scheduled export will run %s.', 'woo_ce' ), $next_export ); ?></strong></p>
<?php } ?>
		<p class="description"><?php _e( 'Configure Doo Product Exporter to automatically generate exports, apply filters to export just what you need.<br />Adjusting options within the Scheduling sub-section will after clicking Save Changes refresh the scheduled export engine, editing filters, formats, methods, etc. will not affect the scheduling of the current scheduled export.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_auto"><?php _e( 'Enable scheduled exports', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="enable_auto" name="enable_auto">
			<option value="1"<?php selected( $enable_auto, 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
			<option value="0"<?php selected( $enable_auto, 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Enabling Scheduled Exports will trigger automated exports at the interval specified under Scheduling.', 'woo_ce' ); ?></p>
	</td>
</tr>
<!--
<tr>
	<th>&nbsp;</th>
	<td style="padding:1em 0 1em 0.8em;">

		<table class="widefat page fixed scheduled-exports">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'woo_ce' ); ?></label><input id="cb-select-all-1" type="checkbox" />
					</th>
					<th class="manage-column"><?php _e( 'Name', 'woo_ce' ); ?></th>
					<th class="manage-column"><?php _e( 'Type', 'woo_ce' ); ?></th>
					<th class="manage-column"><?php _e( 'Format', 'woo_ce' ); ?></th>
					<th class="manage-column"><?php _e( 'Status', 'woo_ce' ); ?></th>
					<th class="manage-column"><?php _e( 'Date', 'woo_ce' ); ?></th>
					<th class="manage-column separator">&nbsp;</th>
				</tr>
			</thead>
			<tbody>

<?php if( !empty( $posts ) ) { ?>
				<tr>
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-18619"><?php printf( __( 'Select %s', 'woo_ce' ), get_the_title( $post ) ); ?></label>
						<input id="cb-select-<?php echo $post; ?>" type="checkbox" name="post[]" value="<?php echo $post; ?>">
					</th>
					<td>My Daily Product Export</td>
					<td>Product</td>
					<td>CSV</td>
					<td>Enabled</td>
					<td>30 days ago</td>
					<td class="edit"><a href="#">Edit</a></td>
				</tr>
<?php } else { ?>
				<tr>
						<td class="colspanchange" colspan="6"><?php _e( 'No scheduled exports found.', 'woo_ce' ); ?></td>
				</tr>
<?php } ?>

			</tbody>
		</table>

	</td>
</tr>
-->
<tr>
	<th>
		<label for="auto_type"><?php _e( 'Export type', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="auto_type" name="auto_type">
<?php if( !empty( $types ) ) { ?>
	<?php foreach( $types as $key => $type ) { ?>
			<option value="<?php echo $key; ?>"<?php selected( $auto_type, $key ); ?>><?php echo $type; ?></option>
	<?php } ?>
<?php } else { ?>
			<option value=""><?php _e( 'No export types were found.', 'woo_ce' ); ?></option>
<?php } ?>
		</select>
		<p class="description"><?php _e( 'Select the data type you want to export.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr class="auto_type_options">
	<th>
		<label><?php _e( 'Export filters', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product category', 'woo_ce' ); ?></p>
<?php if( !empty( $product_categories ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woo_ce' ); ?>" name="product_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
					<option value="<?php echo $product_category->term_id; ?>"<?php checked( in_array( $product_category->term_id, $product_filter_category ), true ); ?><?php disabled( $product_category->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Categories were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Category\'s you want to filter exported Products by. Default is to include all Product Categories.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product tag', 'woo_ce' ); ?></p>
<?php if( !empty( $product_tags ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woo_ce' ); ?>" name="product_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
					<option value="<?php echo $product_tag->term_id; ?>"<?php selected( in_array( $product_tag->term_id, $product_filter_tag ), true ); ?><?php disabled( $product_category->count, 0 ); ?>><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Tags were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Tag\'s you want to filter exported Products by. Default is to include all Product Tags.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product status', 'woo_ce' ); ?></p>
<?php if( !empty( $product_statuses ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Status...', 'woo_ce' ); ?>" name="product_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_statuses as $key => $product_status ) { ?>
					<option value="<?php echo $key; ?>"<?php selected( in_array( $key, $product_filter_status ), true ); ?>><?php echo $product_status; ?></option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Status were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Status\'s you want to filter exported Products by. Default is to include all Product Status\'s.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product type', 'woo_ce' ); ?></p>
<?php if( !empty( $product_types ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Type...', 'woo_ce' ); ?>" name="product_filter_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
					<option value="<?php echo $key; ?>"<?php selected( in_array( $key, $product_filter_type ), true ); ?>><?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Types were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types except Variations.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Stock status', 'woo_ce' ); ?></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" name="product_filter_stock" value=""<?php checked( $product_filter_stock, false ); ?> /> <?php _e( 'Include both', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="product_filter_stock" value="instock"<?php checked( $product_filter_stock, 'instock' ); ?> /> <?php _e( 'In stock', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="product_filter_stock" value="outofstock"<?php checked( $product_filter_stock, 'outofstock' ); ?> /> <?php _e( 'Out of stock', 'woo_ce' ); ?></label></li>
				</ul>
				<p class="description"><?php _e( 'Select the Stock Status\'s you want to filter exported Products by. Default is to include all Stock Status\'s.', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Order date', 'woo_ce' ); ?></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" name="order_dates_filter" value=""<?php checked( $order_filter_date, false ); ?> /><?php _e( 'All', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="order_dates_filter" value="today"<?php checked( $order_filter_date, 'today' ); ?> /><?php _e( 'Today', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="order_dates_filter" value="yesterday"<?php checked( $order_filter_date, 'yesterday' ); ?> /><?php _e( 'Yesterday', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="order_dates_filter" value="current_week"<?php checked( $order_filter_date, 'current_week' ); ?> /><?php _e( 'Current week', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="order_dates_filter" value="last_week"<?php checked( $order_filter_date, 'last_week' ); ?> /><?php _e( 'Last week', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="order_dates_filter" value="current_month"<?php checked( $order_filter_date, 'current_month' ); ?> /><?php _e( 'Current month', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="order_dates_filter" value="last_month"<?php checked( $order_filter_date, 'last_month' ); ?> /><?php _e( 'Last month', 'woo_ce' ); ?></label></li>
					<li>
						<label><input type="radio" name="order_dates_filter" value="variable"<?php checked( $order_filter_date, 'variable' ); ?> /><?php _e( 'Variable date', 'woo_ce' ); ?></label>
						<div style="margin-top:0.2em;">
							<?php _e( 'Last', 'woo_ce' ); ?>
							<input type="text" name="order_dates_filter_variable" class="text" size="4" value="<?php echo $order_filter_date_variable; ?>" />
							<select name="order_dates_filter_variable_length">
								<option value=""<?php selected( $order_filter_date_variable_length, '' ); ?>>&nbsp;</option>
								<option value="second"<?php selected( $order_filter_date_variable_length, 'second' ); ?>><?php _e( 'second(s)', 'woo_ce' ); ?></option>
								<option value="minute"<?php selected( $order_filter_date_variable_length, 'minute' ); ?>><?php _e( 'minute(s)', 'woo_ce' ); ?></option>
								<option value="hour"<?php selected( $order_filter_date_variable_length, 'hour' ); ?>><?php _e( 'hour(s)', 'woo_ce' ); ?></option>
								<option value="day"<?php selected( $order_filter_date_variable_length, 'day' ); ?>><?php _e( 'day(s)', 'woo_ce' ); ?></option>
								<option value="week"<?php selected( $order_filter_date_variable_length, 'week' ); ?>><?php _e( 'week(s)', 'woo_ce' ); ?></option>
								<option value="month"<?php selected( $order_filter_date_variable_length, 'month' ); ?>><?php _e( 'month(s)', 'woo_ce' ); ?></option>
								<option value="year"<?php selected( $order_filter_date_variable_length, 'year' ); ?>><?php _e( 'year(s)', 'woo_ce' ); ?></option>
							</select>
						</div>
					</li>
					<li>
						<label><input type="radio" name="order_dates_filter" value="manual"<?php checked( $order_filter_date, 'manual' ); ?> /><?php _e( 'Fixed date', 'woo_ce' ); ?></label>
						<div style="margin-top:0.2em;">
							<input type="text" name="order_dates_from" value="<?php echo $order_filter_dates_from; ?>" size="10" maxlength="10" class="text datepicker" /> to <input type="text" name="order_dates_to" value="<?php echo $order_filter_dates_to; ?>" size="10" maxlength="10" class="text datepicker" />
						</div>
					</li>
					<li>
						<label><input type="radio" name="order_dates_filter" value="last_export"<?php checked( $order_filter_date, 'last_export' ); ?> /> <?php _e( 'Since last export', 'woo_ce' ); ?></label>
						<p class="description"><?php _e( 'Export Orders which have not previously been included in an export. Decided by whether the <code>_woo_cd_exported</code> custom Post meta key has not been assigned to an Order.', 'woo_ce' ); ?></p>
					</li>
				</ul>
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. If manually selecting dates ensure the Fixed date radio field is checked, likewise for variable dates. Default is to include all Orders made in the date format <code>DD/MM/YYYY</code>.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Order status', 'woo_ce' ); ?></p>
<?php if( !empty( $order_statuses ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Order Status...', 'woo_ce' ); ?>" name="order_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $order_statuses as $order_status ) { ?>
					<option value="<?php echo $order_status->slug; ?>"<?php selected( in_array( $order_status->slug, $order_filter_status ), true ); ?>><?php echo ucfirst( $order_status->name ); ?> (<?php echo $order_status->count; ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Order Status were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Product', 'woo_ce' ); ?></p>
<?php if( wp_script_is( 'wc-enhanced-select', 'enqueued' ) ) { ?>
			<p><input type="hidden" id="order_filter_product" name="order_filter_product[]" class="multiselect wc-product-search" data-multiple="true" style="width:95;" data-placeholder="<?php _e( 'Search for a Product&hellip;', 'woo_ce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
<?php
	$json_ids = array();
?>
<?php if( !empty( $order_filter_product ) ) { ?>
<?php
	foreach( $order_filter_product as $product_id ) {
		$product = wc_get_product( $product_id );
		if( is_object( $product ) ) {
			$json_ids[$product_id] = wp_kses_post( $product->get_formatted_name() );
		}
	}
	echo esc_attr( json_encode( $json_ids ) ); ?>
<?php } ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /></p>
<?php } else { ?>
<?php
	$products = woo_ce_get_products();
	add_filter( 'the_title', 'woo_ce_get_product_title', 10, 2 );
?>
	<?php if( !empty( $products ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product...', 'woo_ce' ); ?>" name="order_filter_product[]" multiple class="chzn-select" style="width:95%;">
		<?php foreach( $products as $product ) { ?>
					<option value="<?php echo $product; ?>"<?php selected( in_array( $product, $order_filter_product ), true ); ?>><?php echo get_the_title( $product ); ?></option>
		<?php } ?>
				</select>
	<?php } else { ?>
				<?php _e( 'No Products were found.', 'woo_ce' ); ?>
	<?php } ?>
<?php
	remove_filter( 'the_title', 'woo_ce_get_product_title' );
?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Products you want to filter exported Orders by. Default is to include all Products.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Billing country', 'woo_ce' ); ?></p>
<?php if( !empty( $countries ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Billing Country...', 'woo_ce' ); ?>" name="order_filter_billing_country[]" multiple class="chzn-select" style="width:95%;">
					<option value=""><?php _e( 'Show all Countries', 'woo_ce' ); ?></option>
	<?php foreach( $countries as $country_prefix => $country ) { ?>
					<option value="<?php echo $country_prefix; ?>"<?php selected( in_array( $country_prefix, $order_filter_billing_country ), true ); ?>><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
	<?php } ?>
				</select>
<?php } else { ?>
				<p><?php _e( 'No Countries were found.', 'woo_ce' ); ?></p>
<?php } ?>
				<p class="description"><?php _e( 'Filter Orders by Billing Country to be included in the export. Default is to include all Countries.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Shipping country', 'woo_ce' ); ?></p>
<?php if( !empty( $countries ) ) { ?>
				<select id="order_filter_shipping_country" data-placeholder="<?php _e( 'Choose a Shipping Country...', 'woo_ce' ); ?>" name="order_filter_shipping_country[]" multiple class="chzn-select" style="width:95%;">
					<option value=""><?php _e( 'Show all Countries', 'woo_ce' ); ?></option>
	<?php foreach( $countries as $country_prefix => $country ) { ?>
					<option value="<?php echo $country_prefix; ?>"<?php selected( in_array( $country_prefix, $order_filter_shipping_country ), true ); ?>><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
	<?php } ?>
				</select>
<?php } else { ?>
				<p><?php _e( 'No Countries were found.', 'woo_ce' ); ?></p>
<?php } ?>
				<p class="description"><?php _e( 'Filter Orders by Shipping Country to be included in the export. Default is to include all Countries.', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Payment gateway', 'woo_ce' ); ?></p>
<?php if( !empty( $payment_gateways ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Payment Gateway...', 'woo_ce' ); ?>" name="order_filter_payment[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $payment_gateways as $payment_gateway ) { ?>
					<option value="<?php echo $payment_gateway->id; ?>"<?php selected( in_array( $payment_gateway->id, $order_filter_payment ), true ); ?>><?php echo ucfirst( woo_ce_format_order_payment_gateway( $payment_gateway->id ) ); ?></option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Payment Gateways were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Payment Gateways you want to filter exported Orders by. Default is to include all Payment Gateways.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Shipping method', 'woo_ce' ); ?></p>
<?php if( !empty( $shipping_methods ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Shipping Method...', 'woo_ce' ); ?>" name="order_filter_shipping[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $shipping_methods as $shipping_method ) { ?>
					<option value="<?php echo $shipping_method->id; ?>"<?php selected( in_array( $shipping_method->id, $order_filter_shipping ), true ); ?>><?php echo ucfirst( woo_ce_format_order_shipping_method( $shipping_method->id ) ); ?></option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Shipping Methods were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Shipping Methods you want to filter exported Orders by. Default is to include all Shipping Methods.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options category-options tag-options brand-options customer-options user-options coupon-options subscription-options product_vendor-options commission-options shipping_class-options">
				<p><?php _e( 'No export filter options are available for this export type.', 'woo_ce' ); ?></p>
			</li>

		</ul>
	</td>
</tr>

<tr>
	<th>
		<label><?php _e( 'Scheduling', 'woo_ce' ); ?></label>
	</th>
	<td>
		<p><?php _e( 'How often do you want the export to run?', 'woo_ce' ); ?></p>
		<ul>
			<li>
				<label><input type="radio" name="auto_schedule" value="custom"<?php checked( $auto_schedule, 'custom' ); ?> /> <?php _e( 'Once every ', 'woo_ce' ); ?></label>
				<input name="auto_interval" type="text" id="auto_interval" value="<?php echo esc_attr( $auto_interval ); ?>" size="6" maxlength="6" class="text" />
				<?php _e( 'minutes', 'woo_ce' ); ?>
			</li>
			<li><label><input type="radio" name="auto_schedule" value="daily"<?php checked( $auto_schedule, 'daily' ); ?> /> <?php _e( 'Daily', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="auto_schedule" value="weekly"<?php checked( $auto_schedule, 'weekly' ); ?> /> <?php _e( 'Weekly', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="auto_schedule" value="monthly"<?php checked( $auto_schedule, 'monthly' ); ?> /> <?php _e( 'Monthly', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="auto_schedule" value="one-time" /> <?php _e( 'One time', 'woo_ce' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Choose how often Doo Product Exporter generates new exports. Default is every 1440 minutes (once every 24 hours).', 'woo_ce' ); ?></p>
		<hr />
		<p><?php _e( 'When do you want scheduled exports to start?', 'woo_ce' ); ?></p>
		<ul>
			<li><label><input type="radio" name="auto_commence" value="now"<?php checked( $auto_commence, 'now' ); ?> /><?php _e( 'From now', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="auto_commence" value="future"<?php checked( $auto_commence, 'future' ); ?> /><?php _e( 'From the following', 'woo_ce' ); ?></label>: <input type="text" name="auto_commence_date" size="20" maxlength="20" class="text datetimepicker" value="<?php echo $auto_commence_date; ?>" /><!--, <?php _e( 'at this time', 'woo_ce' ); ?>: <input type="text" name="auto_interval_time" size="10" maxlength="10" class="text timepicker" />--></li>
		</ul>
	</td>
</tr>

<tr>
	<th>
		<label><?php _e( 'Export format', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" name="auto_format" value="csv"<?php checked( $auto_format, 'csv' ); ?> /> <?php _e( 'CSV', 'woo_ce' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woo_ce' ); ?></span></label></li>
			<li><label><input type="radio" name="auto_format" value="xml"<?php checked( $auto_format, 'xml' ); ?> /> <?php _e( 'XML', 'woo_ce' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woo_ce' ); ?></span></label></li>
			<li><label><input type="radio" name="auto_format" value="xls"<?php checked( $auto_format, 'xls' ); ?> /> <?php _e( 'Excel (XLS)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woo_ce' ); ?></span></label></li>
			<li><label><input type="radio" name="auto_format" value="xlsx"<?php checked( $auto_format, 'xlsx' ); ?> /> <?php _e( 'Excel (XLSX)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woo_ce' ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats. Default is CSV.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr>
	<th>
		<label for="auto_method"><?php _e( 'Export method', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="auto_method" name="auto_method">
			<option value="archive"<?php selected( $auto_method, 'archive' ); ?>><?php _e( 'Archive to WordPress Media', 'woo_ce' ); ?></option>
			<option value="email"<?php selected( $auto_method, 'email' ); ?>><?php _e( 'Send as e-mail', 'woo_ce' ); ?></option>
			<option value="post"<?php selected( $auto_method, 'post' ); ?>><?php _e( 'POST to remote URL', 'woo_ce' ); ?></option>
			<option value="ftp"<?php selected( $auto_method, 'ftp' ); ?>><?php _e( 'Upload to remote FTP/SFTP', 'woo_ce' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Choose what Doo Product Exporter does with the generated export. Default is to archive the export to the WordPress Media for archival purposes.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr class="auto_method_options">
	<th>
		<label><?php _e( 'Export method options', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>

			<li class="export-options email-options">
				<p>
					<label for="email_to"><?php _e( 'Default e-mail recipient', 'woo_ce' ); ?></label><br />
					<input name="email_to" type="text" id="email_to" value="<?php echo esc_attr( $email_to ); ?>" class="large-text code" />
				</p>
				<p class="description"><?php _e( 'Set the default recipient of scheduled export e-mails, multiple recipients can be added using the <code><attr title="comma">,</attr></code> separator. This option can be overriden via CRON using the <code>to</code> argument.<br />Default is the Blog Administrator e-mail address set on the WordPress &raquo; Settings screen.', 'woo_ce' ); ?></p>

				<p>
					<label for="email_subject"><?php _e( 'Default e-mail subject', 'woo_ce' ); ?></label><br />
					<input name="email_subject" type="text" id="email_subject" value="<?php echo esc_attr( $email_subject ); ?>" class="large-text code" />
				</p>
				<p class="description"><?php _e( 'Set the default subject of scheduled export e-mails, can be overriden via CRON using the <code>subject</code> argument. Tags can be used: <code>%store_name%</code>, <code>%export_type%</code>, <code>%export_filename%</code>.', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options post-options">
				<p>
					<label for="post_to"><?php _e( 'Default remote POST URL', 'woo_ce' ); ?></label><br />
					<input name="post_to" type="text" id="post_to" value="<?php echo esc_url( $post_to ); ?>" class="large-text code" />
				</p>
				<p class="description"><?php printf( __( 'Set the default remote POST address for scheduled exports, can be overriden via CRON using the <code>to</code> argument. Default is empty. See our <a href="%s" target="_blank">Usage</a> document for more information on Default remote POST URL.', 'woo_ce' ), $troubleshooting_url ); ?></p>
			</li>

			<li class="export-options ftp-options">
				<label for="ftp_method_host"><?php _e( 'Host', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_host" name="ftp_method_host" size="15" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_host ); ?>" />&nbsp;
				<label for="ftp_method_port" style="width:auto;"><?php _e( 'Port', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_port" name="ftp_method_port" size="5" class="short-text code" value="<?php echo sanitize_text_field( $ftp_method_port ); ?>" maxlength="5" /><br />
				<label for="ftp_method_protocol"><?php _e( 'Protocol', 'woo_ce' ); ?>:</label>
				<select name="ftp_method_protocol">
					<option value="ftp"<?php selected( $ftp_method_protocol, 'ftp' ); ?>><?php _e( 'FTP - File Transfer Protocol', 'woo_ce' ); ?></option>
					<option value="sftp"<?php selected( $ftp_method_protocol, 'sftp' ); ?><?php disabled( ( !function_exists( 'ssh2_connect' ) ? true : false ), true ); ?>><?php _e( 'SFTP - SSH File Transfer Protocol', 'woo_ce' ); ?></option>
				</select><br />
				<label for="ftp_method_user"><?php _e( 'Username', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_user" name="ftp_method_user" size="15" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_user ); ?>" /><br />
				<label for="ftp_method_pass"><?php _e( 'Password', 'woo_ce' ); ?>:</label> <input type="password" id="ftp_method_pass" name="ftp_method_pass" size="15" class="regular-text code" value="" /><?php if( !empty( $ftp_method_pass ) ) { echo ' ' . __( '(password is saved, fill this field to change it)', 'woo_ce' ); } ?><br />
				<label for="ftp_method_file_path"><?php _e( 'File path', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_file_path" name="ftp_method_path" size="25" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_path ); ?>" /><br />
				<label for="ftp_method_filename"><?php _e( 'Fixed filename', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_filename" name="ftp_method_filename" size="25" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_filename ); ?>" /><br />
				<label for="ftp_method_passive"><?php _e( 'Transfer mode', 'woo_ce' ); ?>:</label> 
				<select id="ftp_method_passive" name="ftp_method_passive">
					<option value="auto"<?php selected( $ftp_method_passive, '' ); ?>><?php _e( 'Auto', 'woo_ce' ); ?></option>
					<option value="active"<?php selected( $ftp_method_passive, 'active' ); ?>><?php _e( 'Active', 'woo_ce' ); ?></option>
					<option value="passive"<?php selected( $ftp_method_passive, 'passive' ); ?>><?php _e( 'Passive', 'woo_ce' ); ?></option>
				</select><br />
				<label for="ftp_method_timeout"><?php _e( 'Timeout', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_timeout" name="ftp_method_timeout" size="5" class="short-text code" value="<?php echo sanitize_text_field( $ftp_method_timeout ); ?>" /><br />
				<p class="description">
					<?php _e( 'Enter the FTP host (minus <code>ftp://</code>), login details and path of where to save the export file, do not provide the filename within File path. For file path example: <code>wp-content/uploads/exports/</code><br />The export filename can be set within the Fixed filename field otherwise it defaults to the Export filename provided within General Settings above. Tags can be used: ', 'woo_ce' ); ?> <code>%dataset%</code>, <code>%date%</code>, <code>%time%</code>, <code>%store_name%</code>.
<?php if( !function_exists( 'ssh2_connect' ) ) { ?>
					<br /><?php _e( 'The SFTP - SSH File Transfer Protocol option is not available as the required function ssh2_connect() is disabled within your WordPress site.', 'woo_ce' ); ?></p>
<?php } ?>
				</p>
			</li>

			<li class="export-options archive-options">
				<p><?php _e( 'No export method options are available for this export method.', 'woo_ce' ); ?></p>
			</li>

		</ul>
	</td>
</tr>
<tr>
	<th>
		<label for="scheduled_fields"><?php _e( 'Export fields', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="scheduled_fields" name="scheduled_fields" value="all"<?php checked( $scheduled_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="scheduled_fields" value="saved"<?php checked( $scheduled_fields, 'saved' ); ?> /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woo_ce' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="cron-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-clock"></div>&nbsp;<?php _e( 'CRON Exports', 'woo_ce' ); ?></h3>
<?php if( $enable_cron == 1 ) { ?>
		<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php _e( 'CRON Exports is enabled', 'woo_ce' ); ?></strong></p>
<?php } ?>
		<p class="description"><?php printf( __( 'Doo Product Exporter supports exporting via a command line request, to do this you need to prepare a specific URL and pass it the following required inline parameters. For sample CRON requests and supported arguments consult our <a href="%s" target="_blank">online documentation</a>.', 'woo_ce' ), $troubleshooting_url ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_cron"><?php _e( 'Enable CRON', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="enable_cron" name="enable_cron">
			<option value="1"<?php selected( $enable_cron, 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
			<option value="0"<?php selected( $enable_cron, 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Enabling CRON allows developers to schedule automated exports and connect with Doo Product Exporter remotely.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="secret_key"><?php _e( 'Export secret key', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="secret_key" type="text" id="secret_key" value="<?php echo esc_attr( $secret_key ); ?>" class="large-text code" />
		<p class="description"><?php _e( 'This secret key (can be left empty to allow unrestricted access) limits access to authorised developers who provide a matching key when working with Doo Product Exporter.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="cron_fields"><?php _e( 'Export fields', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="cron_fields" name="cron_fields" value="all"<?php checked( $cron_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="cron_fields" value="saved"<?php checked( $cron_fields, 'saved' ); ?> /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woo_ce' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="orders-screen">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Orders Screen', 'woo_ce' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Actions display', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="order_actions_csv" value="1"<?php checked( $order_actions_csv ); ?> /> <?php _e( 'Export to CSV', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_xml" value="1"<?php checked( $order_actions_xml ); ?> /> <?php _e( 'Export to XML', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_xls" value="1"<?php checked( $order_actions_xls ); ?> /> <?php _e( 'Export to XLS', 'woo_ce' ); ?></label></li>
			<li><label><input type="checkbox" name="order_actions_xlsx" value="1"<?php checked( $order_actions_xlsx ); ?> /> <?php _e( 'Export to XLSX', 'woo_ce' ); ?></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different Order actions on the WooCommerce &raquo; Orders screen.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="export-triggers">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Export Triggers', 'woo_ce' ); ?></h3>
		<p class="description"><?php _e( 'Configure Doo Product Exporter to run exports on specific triggers within your WooCommerce store.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr>
	<th>
		<label><?php _e( 'New Order', 'woo_ce' ); ?></label>
	</th>
	<td>
<?php if( $enable_trigger_new_order == 1 ) { ?>
		<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php _e( 'Export on New Order is enabled, this will run for each new Order received.', 'woo_ce' ); ?></strong></p>
<?php } ?>
		<p class="description"><?php _e( 'Trigger an export of each new Order that is generated after successful Checkout.', 'woo_ce' ); ?></p>
		<ul>

			<li>
				<p>
					<label for="enable_trigger_new_order"><?php _e( 'Enable trigger', 'woo_ce' ); ?></label><br />
					<select id="enable_trigger_new_order" name="enable_trigger_new_order">
						<option value="1"<?php selected( $enable_trigger_new_order, 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
						<option value="0"<?php selected( $enable_trigger_new_order, 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
					</select>
				</p>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export format', 'woo_ce' ); ?></label></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" name="trigger_new_order_format" value="csv"<?php checked( $trigger_new_order_format, 'csv' ); ?> /> <?php _e( 'CSV', 'woo_ce' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woo_ce' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="xml"<?php checked( $trigger_new_order_format, 'xml' ); ?> /> <?php _e( 'XML', 'woo_ce' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woo_ce' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="xls"<?php checked( $trigger_new_order_format, 'xls' ); ?> /> <?php _e( 'Excel (XLS)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woo_ce' ); ?></span></label></li>
					<li><label><input type="radio" name="trigger_new_order_format" value="xlsx"<?php checked( $trigger_new_order_format, 'xlsx' ); ?> /> <?php _e( 'Excel (XLSX)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woo_ce' ); ?></span></label></li>
				</ul>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export method', 'woo_ce' ); ?></label></p>
				<select id="trigger_new_order_method" name="trigger_new_order_method">
					<option value="archive"<?php selected( $trigger_new_order_method, 'archive' ); ?>><?php _e( 'Archive to WordPress Media', 'woo_ce' ); ?></option>
<!--
					<option value="email"<?php selected( $trigger_new_order_method, 'email' ); ?>><?php _e( 'Send as e-mail', 'woo_ce' ); ?></option>
					<option value="post"<?php selected( $trigger_new_order_method, 'post' ); ?>><?php _e( 'POST to remote URL', 'woo_ce' ); ?></option>
					<option value="ftp"<?php selected( $trigger_new_order_method, 'ftp' ); ?>><?php _e( 'Upload to remote FTP/SFTP', 'woo_ce' ); ?></option>
-->
				</select>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export method options', 'woo_ce' ); ?></label></p>
				<p><?php _e( 'No export method options are available for this export method.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li>
				<p><label><?php _e( 'Export fields', 'woo_ce' ); ?></label></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" id="trigger_new_order_fields" name="trigger_new_order_fields" value="all"<?php checked( $trigger_new_order_fields, 'all' ); ?> /> <?php _e( 'Include all Order Fields', 'woo_ce' ); ?></label></li>
					<li><label><input type="radio" name="trigger_new_order_fields" value="saved"<?php checked( $trigger_new_order_fields, 'saved' ); ?> /> <?php _e( 'Use the saved Export Fields preference for Orders set on the Export screen', 'woo_ce' ); ?></label></li>
				</ul>
				<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for Orders. Default is to include all export fields.', 'woo_ce' ); ?></p>
			</li>

		</ul>
	</td>
</tr>

<?php
	ob_end_flush();

}

function woo_ce_admin_save_settings() {

	// Strip file extension from export filename
	$export_filename = strip_tags( $_POST['export_filename'] );
	if( ( strpos( $export_filename, '.csv' ) !== false ) || ( strpos( $export_filename, '.xml' ) !== false ) || ( strpos( $export_filename, '.xls' ) !== false ) )
		$export_filename = str_replace( array( '.csv', '.xml', '.xls' ), '', $export_filename );
	woo_ce_update_option( 'export_filename', $export_filename );
	woo_ce_update_option( 'delete_file', absint( $_POST['delete_file'] ) );
	woo_ce_update_option( 'encoding', sanitize_text_field( $_POST['encoding'] ) );
	woo_ce_update_option( 'delimiter', sanitize_text_field( $_POST['delimiter'] ) );
	woo_ce_update_option( 'category_separator', sanitize_text_field( $_POST['category_separator'] ) );
	woo_ce_update_option( 'bom', absint( $_POST['bom'] ) );
	woo_ce_update_option( 'escape_formatting', sanitize_text_field( $_POST['escape_formatting'] ) );
	woo_ce_update_option( 'header_formatting', absint( $_POST['header_formatting'] ) );
	if( $_POST['date_format'] == 'custom' && !empty( $_POST['date_format_custom'] ) )
		woo_ce_update_option( 'date_format', sanitize_text_field( $_POST['date_format_custom'] ) );
	else
		woo_ce_update_option( 'date_format', sanitize_text_field( $_POST['date_format'] ) );
	woo_ce_update_option( 'email_to', sanitize_text_field( $_POST['email_to'] ) );
	woo_ce_update_option( 'email_subject', sanitize_text_field( $_POST['email_subject'] ) );
	woo_ce_update_option( 'post_to', sanitize_text_field( $_POST['post_to'] ) );

	// XML settings
	woo_ce_update_option( 'xml_attribute_url', ( isset( $_POST['xml_attribute_url'] ) ? absint( $_POST['xml_attribute_url'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_title', ( isset( $_POST['xml_attribute_title'] ) ? absint( $_POST['xml_attribute_title'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_date', ( isset( $_POST['xml_attribute_date'] ) ? absint( $_POST['xml_attribute_date'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_time', ( isset( $_POST['xml_attribute_time'] ) ? absint( $_POST['xml_attribute_time'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_export', ( isset( $_POST['xml_attribute_export'] ) ? absint( $_POST['xml_attribute_export'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_orderby', ( isset( $_POST['xml_attribute_orderby'] ) ? absint( $_POST['xml_attribute_orderby'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_order', ( isset( $_POST['xml_attribute_order'] ) ? absint( $_POST['xml_attribute_order'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_limit', ( isset( $_POST['xml_attribute_limit'] ) ? absint( $_POST['xml_attribute_limit'] ) : 0 ) );
	woo_ce_update_option( 'xml_attribute_offset', ( isset( $_POST['xml_attribute_offset'] ) ? absint( $_POST['xml_attribute_offset'] ) : 0 ) );

	// RSS settings
	woo_ce_update_option( 'rss_title', ( isset( $_POST['rss_title'] ) ? sanitize_text_field( $_POST['rss_title'] ) : '' ) );
	woo_ce_update_option( 'rss_link', ( isset( $_POST['rss_link'] ) ? esc_url_raw( $_POST['rss_link'] ) : '' ) );
	woo_ce_update_option( 'rss_description', ( isset( $_POST['rss_description'] ) ? sanitize_text_field( $_POST['rss_description'] ) : '' ) );

	// Scheduled export settings
	$enable_auto = absint( $_POST['enable_auto'] );
	$auto_schedule = sanitize_text_field( $_POST['auto_schedule'] );
	$auto_interval = absint( $_POST['auto_interval'] );
	$auto_commence = sanitize_text_field( $_POST['auto_commence'] );
	$auto_commence_date = sanitize_text_field( $_POST['auto_commence_date'] );
	$site_hash = md5( get_option( 'siteurl' ) );

	// Display additional notice if Enabled Scheduled Exports is enabled/disabled or scheduling options are modified
	if( 
		woo_ce_get_option( 'enable_auto', 0 ) <> $enable_auto || 
		woo_ce_get_option( 'auto_schedule', 'custom' ) <> $auto_schedule || 
		woo_ce_get_option( 'auto_interval', 1440 ) <> $auto_interval || 
		woo_ce_get_option( 'auto_commence', 'now' ) <> $auto_commence || 
		woo_ce_get_option( 'auto_commence_date', date( 'd/m/Y H:i' ) ) <> $auto_commence_date
	) {

		// Save these fields before we re-load the WP-CRON schedule
		woo_ce_update_option( 'enable_auto', $enable_auto );

		// Remove from WP-CRON schedule if disabled
		if( $enable_auto == 0 ) {
			woo_ce_cron_activation();
		// Re-load the WP-CRON schedule with our new scheduling options
		} else if( 
			woo_ce_get_option( 'auto_schedule', 'custom' ) <> $auto_schedule || 
			woo_ce_get_option( 'auto_interval', 1440 ) <> $auto_interval || 
			woo_ce_get_option( 'auto_commence', 'now' ) <> $auto_commence || 
			woo_ce_get_option( 'auto_commence_date', date( 'd/m/Y H:i' ) ) <> $auto_commence_date || 
			woo_ce_get_option( 'site_hash', '' ) <> $site_hash
		) {
			// Save these fields before we re-load the WP-CRON schedule
			woo_ce_update_option( 'auto_commence', $auto_commence );
			woo_ce_update_option( 'auto_commence_date', $auto_commence_date );
			woo_ce_update_option( 'auto_schedule', $auto_schedule );
			woo_ce_update_option( 'auto_interval', $auto_interval );

			// Update the Site URL hash we use for live vs. staging checks
			woo_ce_update_option( 'site_hash', $site_hash );

			woo_ce_cron_activation( true );
		}

		switch( $auto_schedule ) {

			case 'daily':
			case 'weekly':
			case 'monthly':
				$interval = $auto_schedule;
				break;

			case 'custom':
				$interval = sprintf( __( 'in %d minute(s)', 'woo_ce' ), $auto_interval );
				break;

		}
		switch( $auto_commence ) {

			case 'now':
				$commence = __( ' from now' );
				break;

			case 'future':
				$commence = sprintf( __( ' starting %s' ), $auto_commence_date );
				break;

		}
		$message = sprintf( __( 'Scheduled exports has been %s.', 'woo_ce' ), ( ( $enable_auto == 1 ) ? sprintf( __( 'activated, next scheduled export will run %s%s', 'woo_ce' ), $interval, $commence ) : __( 'de-activated, no further automated exports will occur', 'woo_ce' ) ) );
		woo_cd_admin_notice( $message );

	}
	woo_ce_update_option( 'auto_type', sanitize_text_field( $_POST['auto_type'] ) );
	woo_ce_update_option( 'auto_product_type', ( isset( $_POST['product_filter_type'] ) ? array_map( 'sanitize_text_field', $_POST['product_filter_type'] ) : array() ) );
	woo_ce_update_option( 'auto_product_status', ( isset( $_POST['product_filter_status'] ) ? array_map( 'sanitize_text_field', $_POST['product_filter_status'] ) : array() ) );
	woo_ce_update_option( 'auto_product_stock', sanitize_text_field( $_POST['product_filter_stock'] ) );
	woo_ce_update_option( 'auto_product_category', ( isset( $_POST['product_filter_category'] ) ? array_map( 'absint', $_POST['product_filter_category'] ) : array() ) );
	woo_ce_update_option( 'auto_product_tag', ( isset( $_POST['product_filter_tag'] ) ? array_map( 'absint', $_POST['product_filter_tag'] ) : array() ) );
	woo_ce_update_option( 'auto_order_date', sanitize_text_field( $_POST['order_dates_filter'] ) );
	woo_ce_update_option( 'auto_order_dates_from', sanitize_text_field( $_POST['order_dates_from'] ) );
	woo_ce_update_option( 'auto_order_dates_to', sanitize_text_field( $_POST['order_dates_to'] ) );
	woo_ce_update_option( 'auto_order_date_variable', sanitize_text_field( $_POST['order_dates_filter_variable'] ) );
	woo_ce_update_option( 'auto_order_date_variable_length', sanitize_text_field( $_POST['order_dates_filter_variable_length'] ) );
	woo_ce_update_option( 'auto_order_status', ( isset( $_POST['order_filter_status'] ) ? woo_ce_format_product_filters( array_map( 'sanitize_text_field', $_POST['order_filter_status'] ) ) : array() ) );
	// Check if we are dealing with a string or array
	$auto_order_product = ( isset( $_POST['order_filter_product'] ) ? $_POST['order_filter_product'] : false );
	// Select2 passes us a string whereas Chosen gives us an array
	if( is_array( $auto_order_product ) && count( $auto_order_product ) == 1 )
		$auto_order_product = explode( ',', $auto_order_product[0] );
	woo_ce_update_option( 'auto_order_product', ( !empty( $auto_order_product ) ? woo_ce_format_product_filters( array_map( 'absint', $auto_order_product ) ) : array() ) );
	unset( $auto_order_product );
	woo_ce_update_option( 'auto_order_billing_country', ( isset( $_POST['order_filter_billing_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_billing_country'] ) : array() ) );
	woo_ce_update_option( 'auto_order_shipping_country', ( isset( $_POST['order_filter_shipping_country'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping_country'] ) : array() ) );
	woo_ce_update_option( 'auto_order_payment', ( isset( $_POST['order_filter_payment'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_payment'] ) : array() ) );
	woo_ce_update_option( 'auto_order_shipping', ( isset( $_POST['order_filter_shipping'] ) ? array_map( 'sanitize_text_field', $_POST['order_filter_shipping'] ) : array() ) );
	woo_ce_update_option( 'auto_format', sanitize_text_field( $_POST['auto_format'] ) );
	woo_ce_update_option( 'auto_method', sanitize_text_field( $_POST['auto_method'] ) );
	woo_ce_update_option( 'auto_ftp_method_host', ( isset( $_POST['ftp_method_host'] ) ? woo_ce_format_ftp_host( sanitize_text_field( $_POST['ftp_method_host'] ) ) : '' ) );
	woo_ce_update_option( 'auto_ftp_method_user', sanitize_text_field( $_POST['ftp_method_user'] ) );
	// Update FTP password only if it is filled in
	if( !empty( $_POST['ftp_method_pass'] ) )
		woo_ce_update_option( 'auto_ftp_method_pass', sanitize_text_field( $_POST['ftp_method_pass'] ) );
	woo_ce_update_option( 'auto_ftp_method_port', sanitize_text_field( $_POST['ftp_method_port'] ) );
	woo_ce_update_option( 'auto_ftp_method_protocol', sanitize_text_field( $_POST['ftp_method_protocol'] ) );
	woo_ce_update_option( 'auto_ftp_method_path', sanitize_text_field( $_POST['ftp_method_path'] ) );
	// Strip file extension from export filename
	$ftp_filename = strip_tags( $_POST['ftp_method_filename'] );
	if( ( strpos( $ftp_filename, '.csv' ) !== false ) || ( strpos( $ftp_filename, '.xml' ) !== false ) || ( strpos( $ftp_filename, '.xls' ) !== false ) || ( strpos( $ftp_filename, '.xlsx' ) !== false ) )
		$ftp_filename = str_replace( array( '.csv', '.xml', '.xls', '.xlsx' ), '', $ftp_filename );
	woo_ce_update_option( 'auto_ftp_method_filename', $ftp_filename );
	unset( $ftp_filename );
	woo_ce_update_option( 'auto_ftp_method_passive', sanitize_text_field( $_POST['ftp_method_passive'] ) );
	woo_ce_update_option( 'auto_ftp_method_timeout', sanitize_text_field( $_POST['ftp_method_timeout'] ) );
	woo_ce_update_option( 'scheduled_fields', sanitize_text_field( $_POST['scheduled_fields'] ) );

	// CRON settings
	$enable_cron = absint( $_POST['enable_cron'] );
	// Display additional notice if Enabled CRON is enabled/disabled
	if( woo_ce_get_option( 'enable_cron', 0 ) <> $enable_cron ) {
		$message = sprintf( __( 'CRON support has been %s.', 'woo_ce' ), ( ( $enable_cron == 1 ) ? __( 'enabled', 'woo_ce' ) : __( 'disabled', 'woo_ce' ) ) );
		woo_cd_admin_notice( $message );
	}
	woo_ce_update_option( 'enable_cron', $enable_cron );
	woo_ce_update_option( 'secret_key', sanitize_text_field( $_POST['secret_key'] ) );
	woo_ce_update_option( 'cron_fields', sanitize_text_field( $_POST['cron_fields'] ) );

	// Orders Screen
	woo_ce_update_option( 'order_actions_csv', ( isset( $_POST['order_actions_csv'] ) ? absint( $_POST['order_actions_csv'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_xml', ( isset( $_POST['order_actions_xml'] ) ? absint( $_POST['order_actions_xml'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_xls', ( isset( $_POST['order_actions_xls'] ) ? absint( $_POST['order_actions_xls'] ) : 0 ) );
	woo_ce_update_option( 'order_actions_xlsx', ( isset( $_POST['order_actions_xlsx'] ) ? absint( $_POST['order_actions_xlsx'] ) : 0 ) );

	// Export Triggers
	woo_ce_update_option( 'enable_trigger_new_order', ( isset( $_POST['enable_trigger_new_order'] ) ? absint( $_POST['enable_trigger_new_order'] ) : 0 ) );
	woo_ce_update_option( 'trigger_new_order_format', sanitize_text_field( $_POST['trigger_new_order_format'] ) );
	woo_ce_update_option( 'trigger_new_order_method', sanitize_text_field( $_POST['trigger_new_order_method'] ) );
	woo_ce_update_option( 'trigger_new_order_fields', sanitize_text_field( $_POST['trigger_new_order_fields'] ) );

	$message = __( 'Changes have been saved.', 'woo_ce' );
	woo_cd_admin_notice( $message );

}
?>