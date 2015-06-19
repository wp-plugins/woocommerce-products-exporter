<?php

// Add Doo Product Exporter to WordPress Administration menu
add_action('admin_menu', 'export_plugin_setup_menu');
function export_plugin_setup_menu(){
        add_menu_page( __( 'Doo Product Export', 'woo_pd' ), __( 'Doo Product Export', 'woo_pd' ), 'view_woocommerce_reports', 'woo_ce', 'woo_cd_html_page' );
}


// HTML template header on Store Exporter screen
function doo_exporter_( $title = '', $icon = 'woocommerce' ) {

	if( $title )
		$output = $title;
	else
		$output = __( 'Doo Products Exporter', 'woo_ce' ); ?>
<div id="woo-ce" class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-woocommerce-importer"><br /></div>
	<h2>
		<?php echo $output; ?>
		<!-- <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'export', 'empty' => null ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'woo_ce' ); ?></a> -->
	</h2>
<?php

}

// HTML template footer on Store Exporter screen
function woo_cd_template_footer() { ?>
</div>
<!-- .wrap -->
<?php
}

function doo_exporter_title() {
	return __( 'Doo Products Exporter', 'woo_ce' );
}
add_filter( 'woo_ce_template_header', 'doo_exporter_title' );

function woo_ce_export_options_export_format() {
	$export_format = woo_ce_get_option( 'export_format', 'csv' );
	ob_start(); ?>
<tr>
	<th>
		<label><?php _e( 'Export format', 'woo_ce' ); ?></label>
	</th>
	<td>
		<label><input type="radio" name="export_format" value="csv"<?php checked( $export_format, 'csv' ); ?> /> <?php _e( 'CSV', 'woo_ce' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woo_ce' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xls"<?php checked( $export_format, 'xls' ); ?> /> <?php _e( 'Excel (XLS)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woo_ce' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xlsx"<?php checked( $export_format, 'xlsx' ); ?> /> <?php _e( 'Excel (XLSX)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woo_ce' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xml"<?php checked( $export_format, 'xml' ); ?> /> <?php _e( 'XML', 'woo_ce' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woo_ce' ); ?></span></label><br />
		<div class="export-options product-options">
			<label><input type="radio" name="export_format" value="rss"<?php checked( $export_format, 'rss' ); ?> /> <?php _e( 'RSS 2.0 for Google Merchants', 'woo_ce' ); ?> <span class="description"><?php printf( __( '(<attr title="%s">XML</attr> Product feed in RSS 2.0 format)', 'woo_ce' ), __( 'EXtensible Markup Language', 'woo_ce' ) ); ?></span></label>
		</div>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}


function doo_exporter_admin_order_column_headers( $columns ) {
	// Check if another Plugin has registered this column
	if( !isset( $columns['woo_ce_export_status'] ) ) {
		$pos = array_search( 'order_title', array_keys( $columns ) );
		$columns = array_merge(
			array_slice( $columns, 0, $pos ),
			array( 'woo_ce_export_status' => __( 'Export Status', 'woo_ce' ) ),
			array_slice( $columns, $pos )
		);
	}
	return $columns;

}

function doo_export_admin_order_column_content( $column ) {
	global $post;
	if( $column == 'woo_ce_export_status' ) {
		if( $is_exported = ( get_post_meta( $post->ID, '_woo_cd_exported', true ) ? true : false ) ) {
			printf( '<mark title="%s" class="%s">%s</mark>', __( 'This Order has been exported and will not be included in future exports filtered by \'Since last export\'.', 'woo_ce' ), 'csv_exported', __( 'Exported', 'woo_ce' ) );
		} else {
			printf( '<mark title="%s" class="%s">%s</mark>', __( 'This Order has not yet been exported.', 'woo_ce' ), 'csv_not_exported', __( 'Not Exported', 'woo_ce' ) );
		}

		// Allow Plugin/Theme authors to add their own content within this column
		do_action( 'doo_export_admin_order_column_content', $post->ID );

	}

}

// Display the bulk actions for Orders on the Orders screen
function woo_ce_admin_order_bulk_actions() {

	global $post_type;

	// Check if this is the Orders screen
	if( $post_type != 'shop_order' )
		return;

	ob_start(); ?>
<script type="text/javascript">
jQuery(function() {
	jQuery('<option>').val('download_csv').text('<?php _e( 'Download as CSV', 'woo_ce' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_csv').text('<?php _e( 'Download as CSV', 'woo_ce' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_xml').text('<?php _e( 'Download as XML', 'woo_ce' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_xml').text('<?php _e( 'Download as XML', 'woo_ce' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_xls').text('<?php _e( 'Download as XLS', 'woo_ce' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_xls').text('<?php _e( 'Download as XLS', 'woo_ce' )?>').appendTo("select[name='action2']");

	jQuery('<option>').val('download_xlsx').text('<?php _e( 'Download as XLSX', 'woo_ce' )?>').appendTo("select[name='action']");
	jQuery('<option>').val('download_xlsx').text('<?php _e( 'Download as XLSX', 'woo_ce' )?>').appendTo("select[name='action2']");
});
</script>
<?php
	ob_end_flush();

}

// Process the bulk action for Orders on the Orders screen
function woo_ce_admin_order_process_bulk_action() {

	$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
	$action = $wp_list_table->current_action();
	$export_format = false;
	switch( $action ) {

		case 'download_csv':
			$export_format = 'csv';
			break;

		case 'download_xml':
			$export_format = 'xml';
			break;

		case 'download_xls':
			$export_format = 'xls';
			break;

		case 'download_xlsx':
			$export_format = 'xlsx';
			break;

		default:
			return;
			break;

	}
	if( !empty( $export_format ) ) {
		if( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'absint', (array)$_REQUEST['post'] );
			set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
			set_transient( WOO_CD_PREFIX . '_single_export_order_ids', implode( ',', $post_ids ), MINUTE_IN_SECONDS );
			unset( $post_ids );
			$gui = 'download';
			$export_type = 'order';
			woo_ce_cron_export( $gui, $export_type );
			delete_transient( WOO_CD_PREFIX . '_single_export_format' );
			delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
			unset( $gui, $export_type );
			exit();
		} else {
			error_log( '[doo-product-exporter] $_REQUEST[\'post\'] was empty so we could not run woo_ce_admin_order_process_bulk_action()' );
			return;
		}
	}

}

// Add Download as... buttons to Actions column on Orders screen
function woo_ce_admin_order_actions( $actions = array(), $order = false ) {

	if( woo_ce_get_option( 'order_actions_csv', 1 ) ) {
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => 'csv', 'order_ids' => $order->id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as CSV', 'woo_ce' ),
			'action' => 'download_csv'
		);
	}
	if( woo_ce_get_option( 'order_actions_xml', 0 ) ) {
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => 'xml', 'order_ids' => $order->id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as XML', 'woo_ce' ),
			'action' => 'download_xml'
		);
	}
	if( woo_ce_get_option( 'order_actions_xls', 1 ) ) {
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => 'xls', 'order_ids' => $order->id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as XLS', 'woo_ce' ),
			'action' => 'download_xls'
		);
	}
	if( woo_ce_get_option( 'order_actions_xlsx', 1 ) ) {
		$actions[] = array(
			'url' => wp_nonce_url( admin_url( add_query_arg( array( 'action' => 'woo_ce_export_order', 'format' => 'xlsx', 'order_ids' => $order->id ), 'admin-ajax.php' ) ), 'woo_ce_export_order' ),
			'name' => __( 'Download as XLSX', 'woo_ce' ),
			'action' => 'download_xlsx'
		);
	}
	return $actions;

}

// Generate exports for Download as... button clicks
function woo_ce_ajax_export_order() {

	if( check_admin_referer( 'woo_ce_export_order' ) ) {
		$gui = 'download';
		$export_type = 'order';
		$order_ids = ( isset( $_GET['order_ids'] ) ? sanitize_text_field( $_GET['order_ids'] ) : false );
		if( $order_ids ) {
			woo_ce_cron_export( $gui, $export_type );
			exit();
		}
	}

}

function woo_ce_admin_order_single_export_csv( $order = false ) {

	if( $order !== false ) {
		// Set the export format type
		$export_format = 'csv';

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();
	}

}

function woo_ce_admin_order_single_export_xml( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_format = 'xml';

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_format, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();
	}

}

function woo_ce_admin_order_single_export_xls( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_type = 'xls';

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_type, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_export_xlsx( $order = false ) {

	if( $order !== false ) {

		// Set the export format type
		$export_type = 'xlsx';

		// Set up our export
		set_transient( WOO_CD_PREFIX . '_single_export_format', $export_type, MINUTE_IN_SECONDS );
		set_transient( WOO_CD_PREFIX . '_single_export_order_ids', $order->id, MINUTE_IN_SECONDS );

		// Run the export
		$gui = 'download';
		$export_type = 'order';
		woo_ce_cron_export( $gui, $export_type );

		// Clean up
		delete_transient( WOO_CD_PREFIX . '_single_export_format' );
		delete_transient( WOO_CD_PREFIX . '_single_export_order_ids' );
		exit();

	}

}

function woo_ce_admin_order_single_actions( $actions ) {

	$actions['woo_ce_export_order_csv'] = __( 'Download as CSV', 'woo_ce' );
	$actions['woo_ce_export_order_xml'] = __( 'Download as XML', 'woo_ce' );
	$actions['woo_ce_export_order_xls'] = __( 'Download as XLS', 'woo_ce' );
	$actions['woo_ce_export_order_xlsx'] = __( 'Download as XLSX', 'woo_ce' );
	return $actions;

}

// Add Store Export page to WooCommerce screen IDs
function woo_ce_wc_screen_ids( $screen_ids = array() ) {

	$screen_ids[] = 'woocommerce_page_woo_ce';
	return $screen_ids;

}
add_filter( 'woocommerce_screen_ids', 'woo_ce_wc_screen_ids', 10, 1 );

// Add Store Export to WordPress Administration menu
function woo_ce_admin_menu() {

	$hook = add_submenu_page( 'woocommerce', __( 'Doo Product Exporter', 'woo_ce' ), __( 'Store Export', 'woo_ce' ), 'view_woocommerce_reports', 'woo_ce', 'woo_cd_html_page' );
	add_action( 'admin_print_styles-' . $hook, 'woo_ce_enqueue_scripts' );
	add_action( 'load-' . $hook, 'doo_export_archives_add_options' );
	add_action( 'current_screen', 'woo_ce_add_help_tab' );

}
add_action( 'admin_menu', 'woo_ce_admin_menu', 11 );

function doo_export_archives_add_options() {

	global $archives_table;

	$args = array(
		'label' => __( 'Archives per page', 'woo_ce' ),
		'default' => 10,
		'option' => 'archive_per_page'
	);
	add_screen_option( 'per_page', $args );

	$archives_table = new WOO_CD_Archives_List_Table();

}

if( !class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WOO_CD_Archives_List_Table extends WP_List_Table {

	function __construct(){

		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'archive',     //singular name of the listed records
			'plural' => 'archives',    //plural name of the listed records
			'ajax' => false        //does this table support ajax?
		) );

	}

	function get_columns(){

		$columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'filename' => __( 'Filename', 'woo_ce' ),
			'type' => __( 'Type', 'woo_ce' ),
			'format' => __( 'Format', 'woo_ce' ),
			'filesize' => __( 'Filesize', 'woo_ce' ),
			'rows' => __( 'Rows', 'woo_ce' ),
			'author' => __( 'Author', 'woo_ce' ),
			'date' => __( 'Date', 'woo_ce' )
		);
		return $columns;

	}

	function column_cb( $item ){

		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item->ID                //The value of the checkbox should be the record's id
		);

	}

	function column_filename( $item ){

		//Build row actions
		$actions = array(
			'download' => sprintf( '<a href="%s">Download</a>', wp_get_attachment_url( $item->ID ) ),
			'edit' => sprintf( '<a href="%s">Edit</a>', get_edit_post_link( $item->ID ) ),
			'delete' => sprintf( '<a href="%s">Delete</a>', get_delete_post_link( $item->ID ) ),
		);

		//Return the title contents
		return sprintf( '%s%s',
			'<a href="' . $item->guid . '"><strong>' . $item->post_title . '</strong></a>',
			$this->row_actions( $actions )
		);

	}

	function column_type( $item ) {

		$export_type = get_post_meta( $item->ID, '_woo_export_type', true );
		$export_type_label = woo_ce_export_type_label( $export_type );
		if( empty( $export_type ) )
			$export_type = __( 'Unassigned', 'woo_ce' );

		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter', $export_type ) ), $export_type_label );

	}

	function column_format( $item ) {

		$export_format = woo_ce_return_mime_type_extension( $item->post_mime_type, 'mime_type' );
		return $export_format;

	}

	function column_filesize( $item ) {

		$output = '-';
		$filepath = get_attached_file( $item->ID );
		if( !empty( $filepath ) ) {
			$filesize = @filesize( $filepath );
			if( $filesize != 0 )
				$output = size_format( $filesize );
		}
		return $output;

	}

	function column_rows( $item ) {

		$output = '-';
		$rows = get_post_meta( $item->ID, '_woo_rows', true );
		if( !empty( $rows ) )
			$output = $rows;
		return $output;

	}

	function column_author( $item ) {

		// post_author is empty if the export is generated by CRON or scheduled export
		$author_name = __( 'WP-CRON', 'woo_ce' );
		if( !empty( $item->post_author ) ) {
			if( $author_name = get_user_by( 'id', $item->post_author ) )
				$author_name = $author_name->display_name;
		}

		return $author_name;

	}

	function column_date( $item ) {

		return woo_ce_format_archive_date( $item->ID );

	}

	function column_default($item, $column_name){

		switch( $column_name ) {

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
				break;

		}

	}

	function get_sortable_columns() {

		$sortable_columns = array(
			'title' => array( 'title',false ),     //true means it's already sorted
			'type' => array( 'type',false ),
			'format' => array( 'format',false ),
			'author' => array( 'author', false ),
			'date' => array( 'date', false )
		);
		return $sortable_columns;

	}

	function get_bulk_actions() {

		$actions = array(
			'delete' => __( 'Delete', 'woo_ce' )
		);
		return $actions;

	}

	function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if( 'delete' === $this->current_action() ) {
			$items = ( isset( $_POST['archive'] ) ? array_map( 'absint', $_POST['archive'] ) : false );
			if( !empty( $items ) ) {
				foreach( $items as $id ) {
					wp_delete_attachment( $id, true );
				}
			}
		}

	}

	function prepare_items() {

		$per_page = get_user_meta( get_current_user_id(), 'archive_per_page', true );
		if( $per_page == '' )
			$per_page = 10;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		// $this->_column_headers = array( $columns, $hidden, $sortable );
		$this->_column_headers = $this->get_column_info();
		$this->process_bulk_action();

		$data = woo_ce_get_archive_files();
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );

	}

}

// Load CSS and jQuery scripts for Doo Product Exporter screen
function woo_ce_enqueue_scripts() {

	// Simple check that WooCommerce is activated
	if( class_exists( 'WooCommerce' ) ) {

		global $woocommerce;

		// Load WooCommerce default Admin styling
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );

	}

	// Date Picker
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/templates/admin/jquery-ui-datepicker.css', WOO_CD_RELPATH ) );

	// Time Picker, Date Picker Addon
	wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( '/js/jquery.timepicker.js', WOO_CD_RELPATH ), array( 'jquery', 'jquery-ui-datepicker' ) );
	wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/templates/admin/jquery-ui-timepicker.css', WOO_CD_RELPATH ) );

	// Chosen
	wp_enqueue_style( 'jquery-chosen', plugins_url( '/templates/admin/chosen.css', WOO_CD_RELPATH ) );
	wp_enqueue_script( 'jquery-chosen', plugins_url( '/js/jquery.chosen.js', WOO_CD_RELPATH ), array( 'jquery' ) );
	wp_enqueue_script( 'ajax-chosen', plugins_url( '/js/ajax-chosen.js', WOO_CD_RELPATH ), array( 'jquery', 'jquery-chosen' ) );


	// Common
	wp_enqueue_style( 'woo_ce_styles', plugins_url( '/templates/admin/export.css', WOO_CD_RELPATH ) );
	wp_enqueue_script( 'woo_ce_scripts', plugins_url( '/templates/admin/export.js', WOO_CD_RELPATH ), array( 'jquery', 'jquery-ui-sortable' ) );
	wp_enqueue_style( 'dashicons' );

	if( WOO_CD_DEBUG ) {
		wp_enqueue_style( 'jquery-csvToTable', plugins_url( '/templates/admin/jquery-csvtable.css', WOO_CD_RELPATH ) );
		wp_enqueue_script( 'jquery-csvToTable', plugins_url( '/js/jquery.csvToTable.js', WOO_CD_RELPATH ), array( 'jquery' ) );
	}
	wp_enqueue_style( 'woo_vm_styles', plugins_url( '/templates/admin/woocommerce-admin_dashboard_vm-plugins.css', WOO_CD_RELPATH ) );

}

function my_action_javascript() {

?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var data = {
			'action': 'my_action',
			'whatever': 1234
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			alert('Got this from the server: ' + response);
		});
	});
	</script>
<?php

}
// add_action( 'admin_footer', 'my_action_javascript' ); // Write our JS below here

function my_action_callback() {

	global $wpdb; // this is how you get access to the database

	$whatever = intval( $_POST['whatever'] );
	$whatever += 10;
	echo $whatever;
	wp_die(); // this is required to terminate immediately and return a proper response

}
// add_action( 'wp_ajax_my_action', 'my_action_callback' );



function woo_ce_set_archives_screen_option( $status, $option, $value ) {

	if( 'archive_per_page' == $option )
		return $value;

}
add_filter( 'set-screen-option', 'woo_ce_set_archives_screen_option', 10, 3 );


// HTML active class for the currently selected tab on the Store Exporter screen
function woo_cd_admin_active_tab( $tab_name = null, $tab = null ) {

	if( isset( $_GET['tab'] ) && !$tab )
		$tab = $_GET['tab'];
	else if( !isset( $_GET['tab'] ) && woo_ce_get_option( 'skip_overview', false ) )
		$tab = 'export';
	else
		$tab = 'overview';

	$output = '';
	if( isset( $tab_name ) && $tab_name ) {
		if( $tab_name == $tab )
			$output = ' nav-tab-active';
	}
	echo $output;

}

// HTML template for each tab on the Store Exporter screen
function woo_cd_tab_template( $tab = '' ) {

	
		$tab = 'export';

	$troubleshooting_url = '';

	switch( $tab ) {

		case 'overview':
			$skip_overview = woo_ce_get_option( 'skip_overview', false );
			break;

		case 'export':
			$export_type = sanitize_text_field( ( isset( $_POST['dataset'] ) ? $_POST['dataset'] : woo_ce_get_option( 'last_export', 'product' ) ) );
			$types = array_keys( woo_ce_return_export_types() );
			// Check if the default export type exists
			if( !in_array( $export_type, $types ) )
				$export_type = 'product';

			$products = woo_ce_return_count( 'product' );
			$categories = woo_ce_return_count( 'category' );
			$tags = woo_ce_return_count( 'tag' );
			$brands = woo_ce_return_count( 'brand' );
			$orders = woo_ce_return_count( 'order' );
			$customers = woo_ce_return_count( 'customer' );
			$users = woo_ce_return_count( 'user' );
			$coupons = woo_ce_return_count( 'coupon' );
			$attributes = woo_ce_return_count( 'attribute' );
			$subscriptions = woo_ce_return_count( 'subscription' );
			$product_vendors = woo_ce_return_count( 'product_vendor' );
			$commissions = woo_ce_return_count( 'commission' );
			$shipping_classes = woo_ce_return_count( 'shipping_class' );

			add_action( 'woo_ce_export_options', 'woo_ce_export_options_export_format' );
			if( $product_fields = woo_ce_get_product_fields() ) {
				foreach( $product_fields as $key => $product_field )
					$product_fields[$key]['disabled'] = ( isset( $product_field['disabled'] ) ? $product_field['disabled'] : 0 );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_category' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_tag' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_brand' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_vendor' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_status' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_type' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_stock_status' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_language' );
				add_action( 'woo_ce_export_product_options_after_table', 'woo_ce_product_sorting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_upsells_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_crosssells_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_variation_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_description_excerpt_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_export_options_gallery_format' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_products_custom_fields' );
			}
			if( $category_fields = woo_ce_get_category_fields() ) {
				foreach( $category_fields as $key => $category_field )
					$category_fields[$key]['disabled'] = ( isset( $category_field['disabled'] ) ? $category_field['disabled'] : 0 );
				add_action( 'woo_ce_export_category_options_before_table', 'woo_ce_categories_filter_by_language' );
				add_action( 'woo_ce_export_category_options_after_table', 'woo_ce_category_sorting' );
			}
			if( $tag_fields = woo_ce_get_tag_fields() ) {
				foreach( $tag_fields as $key => $tag_field )
					$tag_fields[$key]['disabled'] = ( isset( $tag_field['disabled'] ) ? $tag_field['disabled'] : 0 );
				add_action( 'woo_ce_export_tag_options_before_table', 'woo_ce_tags_filter_by_language' );
				add_action( 'woo_ce_export_tag_options_after_table', 'woo_ce_tag_sorting' );
			}
			if( $brand_fields = woo_ce_get_brand_fields() ) {
				foreach( $brand_fields as $key => $brand_field )
					$brand_fields[$key]['disabled'] = ( isset( $brand_field['disabled'] ) ? $brand_field['disabled'] : 0 );
				add_action( 'woo_ce_export_brand_options_before_table', 'woo_ce_brand_sorting' );
			}
			if( $order_fields = woo_ce_get_order_fields() ) {
				foreach( $order_fields as $key => $order_field ) {
					$order_fields[$key]['disabled'] = ( isset( $order_field['disabled'] ) ? $order_field['disabled'] : 0 );
					if( $order_field['hidden'] )
						unset( $order_fields[$key] );
				}
				add_action( 'woo_ce_export_quicklinks', 'woo_ce_quicklink_custom_fields' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_date' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_status' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_customer' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_billing_country' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_shipping_country' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_user_role' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_coupon' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_category' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_tag' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_brand' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_order_id' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_payment_gateway' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_shipping_method' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_custom_fields_link' );
				add_action( 'woo_ce_export_order_options_after_table', 'woo_ce_order_sorting' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_items_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_max_order_items' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_items_types' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_orders_custom_fields' );
			}
			if( $customer_fields = woo_ce_get_customer_fields() ) {
				foreach( $customer_fields as $key => $customer_field )
					$customer_fields[$key]['disabled'] = ( isset( $customer_field['disabled'] ) ? $customer_field['disabled'] : 0 );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_filter_by_status' );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_filter_by_user_role' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_customers_custom_fields' );
			}
			if( $user_fields = woo_ce_get_user_fields() ) {
				foreach( $user_fields as $key => $user_field )
					$user_fields[$key]['disabled'] = ( isset( $user_field['disabled'] ) ? $user_field['disabled'] : 0 );
				add_action( 'woo_ce_export_user_options_after_table', 'woo_ce_user_sorting' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_users_custom_fields' );
			}
			if( $coupon_fields = woo_ce_get_coupon_fields() ) {
				foreach( $coupon_fields as $key => $coupon_field )
					$coupon_fields[$key]['disabled'] = ( isset( $coupon_field['disabled'] ) ? $coupon_field['disabled'] : 0 );
				add_action( 'woo_ce_export_coupon_options_before_table', 'woo_ce_coupons_filter_by_discount_type' );
				add_action( 'woo_ce_export_coupon_options_before_table', 'woo_ce_coupon_sorting' );
			}
			if( $subscription_fields = woo_ce_get_subscription_fields() ) {
				foreach( $subscription_fields as $key => $subscription_field )
					$subscription_fields[$key]['disabled'] = ( isset( $subscription_field['disabled'] ) ? $subscription_field['disabled'] : 0 );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_subscription_status' );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_subscription_product' );
			}
			if( $product_vendor_fields = woo_ce_get_product_vendor_fields() ) {
				foreach( $product_vendor_fields as $key => $product_vendor_field )
					$product_vendor_fields[$key]['disabled'] = ( isset( $product_vendor_field['disabled'] ) ? $product_vendor_field['disabled'] : 0 );
			}
			if( $commission_fields = woo_ce_get_commission_fields() ) {
				foreach( $commission_fields as $key => $commission_field )
					$commission_fields[$key]['disabled'] = ( isset( $commission_field['disabled'] ) ? $commission_field['disabled'] : 0 );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_date' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_product_vendor' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_commission_status' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commission_sorting' );
			}
			if( $shipping_class_fields = woo_ce_get_shipping_class_fields() ) {
				foreach( $shipping_class_fields as $key => $shipping_class_field )
					$shipping_class_fields[$key]['disabled'] = ( isset( $shipping_class_field['disabled'] ) ? $shipping_class_field['disabled'] : 0 );
				add_action( 'woo_ce_export_shipping_class_options_after_table', 'woo_ce_shipping_class_sorting' );
			}
			// $attribute_fields = woo_ce_get_attribute_fields();
			if( $attribute_fields = false ) {
				foreach( $attribute_fields as $key => $attribute_field )
					$attribute_fields[$key]['disabled'] = ( isset( $attribute_field['disabled'] ) ? $attribute_field['disabled'] : 0 );
			}

			// Export modules
			$modules = woo_ce_modules_list();

			// Export options
			$limit_volume = woo_ce_get_option( 'limit_volume' );
			$offset = woo_ce_get_option( 'offset' );
			break;

		case 'fields':
			$export_type = ( isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '' );
			$types = array_keys( woo_ce_return_export_types() );
			$fields = array();
			if( in_array( $export_type, $types ) ) {
				if( has_filter( 'woo_ce_' . $export_type . '_fields', 'woo_ce_override_' . $export_type . '_field_labels' ) )
					remove_filter( 'woo_ce_' . $export_type . '_fields', 'woo_ce_override_' . $export_type . '_field_labels', 11 );
				if( function_exists( sprintf( 'woo_ce_get_%s_fields', $export_type ) ) )
					$fields = call_user_func( 'woo_ce_get_' . $export_type . '_fields' );
				$labels = woo_ce_get_option( $export_type . '_labels', array() );
			}
			break;

		case 'archive':
			if( isset( $_POST['archive'] ) || isset( $_GET['trashed'] ) ) {
				if( isset( $_POST['archive'] ) ) {
					$post_id = count( $_POST['archive'] );
				} else if( isset( $_GET['trashed'] ) ) {
					$post_id = count( $_GET['ids'] );
				}
				$message = _n( 'Archived export has been deleted.', 'Archived exports has been deleted.', $post_id, 'woo_ce' );
				woo_cd_admin_notice_html( $message );
			}

			global $archives_table;

			$archives_table->prepare_items();

			break;

		case 'settings':
			$export_filename = woo_ce_get_option( 'export_filename', '' );
			// Strip file extension from export filename
			if( ( strpos( $export_filename, '.csv' ) !== false ) || ( strpos( $export_filename, '.xml' ) !== false ) || ( strpos( $export_filename, '.xls' ) !== false ) )
				$export_filename = str_replace( array( '.csv', '.xml', '.xls' ), '', $export_filename );
			// Default export filename
			if( $export_filename == false )
				$export_filename = '%store_name%-export_%dataset%-%date%-%time%';
			$delete_file = woo_ce_get_option( 'delete_file', 1 );
			$timeout = woo_ce_get_option( 'timeout', 0 );
			$encoding = woo_ce_get_option( 'encoding', 'UTF-8' );
			$bom = woo_ce_get_option( 'bom', 1 );
			$delimiter = woo_ce_get_option( 'delimiter', ',' );
			$category_separator = woo_ce_get_option( 'category_separator', '|' );
			$line_ending_formatting = woo_ce_get_option( 'line_ending_formatting', 'windows' );
			$escape_formatting = woo_ce_get_option( 'escape_formatting', 'all' );
			$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
			if( $date_format == 1 || $date_format == '' )
				$date_format = 'd/m/Y';
			$file_encodings = ( function_exists( 'mb_list_encodings' ) ? mb_list_encodings() : false );
			add_action( 'woo_ce_export_settings_top', 'woo_ce_export_settings_quicklinks' );
			add_action( 'woo_ce_export_settings_after', 'woo_ce_export_settings_csv' );
			add_action( 'woo_ce_export_settings_after', 'woo_ce_export_settings_extend' );
			break;

		case 'tools':
			$woo_pd_url = '';
			$woo_pd_target = ' target="_blank"';
			if( function_exists( 'woo_pd_init' ) ) {
				$woo_pd_url = esc_url( add_query_arg( array( 'page' => 'woo_pd', 'tab' => null ) ) );
				$woo_pd_target = false;
			}

			// Store Toolkit
			$woo_st_url = '';
			$woo_st_target = ' target="_blank"';
			if( function_exists( 'woo_st_admin_init' ) ) {
				$woo_st_url = esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => null ) ) );
				$woo_st_target = false;
			}
			break;

	}
	if( $tab ) {
		if( file_exists( WOO_CD_PATH . 'templates/admin/tabs-' . $tab . '.php' ) ) {
			include_once( WOO_CD_PATH . 'templates/admin/tabs-' . $tab . '.php' );
		} else {
			$message = sprintf( __( 'We couldn\'t load the export template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woo_ce' ), 'tabs-' . $tab . '.php', WOO_CD_PATH . 'templates/admin/...' );
			woo_cd_admin_notice_html( $message, 'error' );
			ob_start(); ?>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woo_ce' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woo_ce' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woo_ce' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woo_ce' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woo_ce' ); ?></p>
<?php
			ob_end_flush();
		}
	}

}

// List of WordPress Plugins that Store Exporter integrates with
function woo_ce_modules_list( $modules = array() ) {

	$modules[] = array(
		'name' => 'aioseop',
		'title' => __( 'All in One SEO Pack', 'woo_ce' ),
		'description' => __( 'Optimize your WooCommerce Products for Search Engines. Requires Store Toolkit for All in One SEO Pack integration.', 'woo_ce' ),
		'url' => 'http://wordpress.org/extend/plugins/all-in-one-seo-pack/',
		'slug' => 'all-in-one-seo-pack',
		'function' => 'aioseop_activate'
	);
	$modules[] = array(
		'name' => 'store_toolkit',
		'title' => __( 'Store Toolkit', 'woo_ce' ),
		'description' => __( 'Store Toolkit includes a growing set of commonly-used WooCommerce administration tools aimed at web developers and store maintainers.', 'woo_ce' ),
		'url' => 'http://wordpress.org/extend/plugins/woocommerce-store-toolkit/',
		'slug' => 'woocommerce-store-toolkit',
		'function' => 'woo_st_admin_init'
	);
	$modules[] = array(
		'name' => 'ultimate_seo',
		'title' => __( 'SEO Ultimate', 'woo_ce' ),
		'description' => __( 'This all-in-one SEO plugin gives you control over Product details.', 'woo_ce' ),
		'url' => 'http://wordpress.org/extend/plugins/seo-ultimate/',
		'slug' => 'seo-ultimate',
		'function' => 'su_wp_incompat_notice'
	);
	$modules[] = array(
		'name' => 'gpf',
		'title' => __( 'Advanced Google Product Feed', 'woo_ce' ),
		'description' => __( 'Easily configure data to be added to your Google Merchant Centre feed.', 'woo_ce' ),
		'url' => 'http://www.leewillis.co.uk/wordpress-plugins/',
		'function' => 'woocommerce_gpf_install'
	);
	$modules[] = array(
		'name' => 'wpseo',
		'title' => __( 'WordPress SEO by Yoast', 'woo_ce' ),
		'description' => __( 'The first true all-in-one SEO solution for WordPress.', 'woo_ce' ),
		'url' => 'http://yoast.com/wordpress/seo/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpseoplugin',
		'slug' => 'wordpress-seo',
		'function' => 'wpseo_admin_init'
	);
	$modules[] = array(
		'name' => 'msrp',
		'title' => __( 'WooCommerce MSRP Pricing', 'woo_ce' ),
		'description' => __( 'Define and display MSRP prices (Manufacturer\'s suggested retail price) to your customers.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/msrp-pricing/',
		'function' => 'woocommerce_msrp_activate'
	);
	$modules[] = array(
		'name' => 'wc_brands',
		'title' => __( 'WooCommerce Brands Addon', 'woo_ce' ),
		'description' => __( 'Create, assign and list brands for products, and allow customers to filter by brand.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/brands/',
		'class' => 'WC_Brands'
	);
	$modules[] = array(
		'name' => 'wc_cog',
		'title' => __( 'Cost of Goods', 'woo_ce' ),
		'description' => __( 'Easily track total profit and cost of goods by adding a Cost of Good field to simple and variable products.', 'woo_ce' ),
		'url' => 'http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/',
		'class' => 'WC_COG'
	);
	$modules[] = array(
		'name' => 'per_product_shipping',
		'title' => __( 'Per-Product Shipping', 'woo_ce' ),
		'description' => __( 'Define separate shipping costs per product which are combined at checkout to provide a total shipping cost.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/per-product-shipping/',
		'function' => 'woocommerce_per_product_shipping_init'
	);
	$modules[] = array(
		'name' => 'vendors',
		'title' => __( 'Product Vendors', 'woo_ce' ),
		'description' => __( 'Turn your store into a multi-vendor marketplace (such as Etsy or Creative Market).', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/product-vendors/',
		'class' => 'WooCommerce_Product_Vendors'
	);
	$modules[] = array(
		'name' => 'acf',
		'title' => __( 'Advanced Custom Fields', 'woo_ce' ),
		'description' => __( 'Powerful fields for WordPress developers.', 'woo_ce' ),
		'url' => 'http://www.advancedcustomfields.com',
		'class' => 'acf'
	);
	$modules[] = array(
		'name' => 'product_addons',
		'title' => __( 'Product Add-ons', 'woo_ce' ),
		'description' => __( 'Allow your customers to customise your products by adding input boxes, dropdowns or a field set of checkboxes.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/product-add-ons/',
		'class' => 'Product_Addon_Admin'
	);
	$modules[] = array(
		'name' => 'seq',
		'title' => __( 'WooCommerce Sequential Order Numbers', 'woo_ce' ),
		'description' => __( 'This plugin extends the WooCommerce e-commerce plugin by setting sequential order numbers for new orders.', 'woo_ce' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-sequential-order-numbers/',
		'slug' => 'woocommerce-sequential-order-numbers',
		'class' => 'WC_Seq_Order_Number'
	);
	$modules[] = array(
		'name' => 'seq_pro',
		'title' => __( 'WooCommerce Sequential Order Numbers Pro', 'woo_ce' ),
		'description' => __( 'Tame your WooCommerce Order Numbers.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/sequential-order-numbers-pro/',
		'class' => 'WC_Seq_Order_Number_Pro'
	);
	$modules[] = array(
		'name' => 'print_invoice_delivery_note',
		'title' => __( 'WooCommerce Print Invoice & Delivery Note', 'woo_ce' ),
		'description' => __( 'Print invoices and delivery notes for WooCommerce orders.', 'woo_ce' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-delivery-notes/',
		'slug' => 'woocommerce-delivery-notes',
		'class' => 'WooCommerce_Delivery_Notes'
	);
	$modules[] = array(
		'name' => 'pdf_invoices_packing_slips',
		'title' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woo_ce' ),
		'description' => __( 'Create, print & automatically email PDF invoices & packing slips for WooCommerce orders.', 'woo_ce' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/',
		'slug' => 'woocommerce-pdf-invoices-packing-slips',
		'class' => 'WooCommerce_PDF_Invoices'
	);
	$modules[] = array(
		'name' => 'checkout_manager',
		'title' => __( 'WooCommerce Checkout Manager', 'woo_ce' ),
		'description' => __( 'Manages WooCommerce Checkout.', 'woo_ce' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-checkout-manager/',
		'slug' => 'woocommerce-checkout-manager',
		'function' => 'wccs_install'
	);
	$modules[] = array(
		'name' => 'checkout_manager_pro',
		'title' => __( 'WooCommerce Checkout Manager Pro', 'woo_ce' ),
		'description' => __( 'Manages the WooCommerce Checkout page and WooCommerce Checkout processes.', 'woo_ce' ),
		'url' => 'http://www.trottyzone.com/product/woocommerce-checkout-manager-pro',
		'function' => array( 'wccs_install', 'wccs_install_pro' )
	);
	$modules[] = array(
		'name' => 'pgsk',
		'title' => __( 'Poor Guys Swiss Knife', 'woo_ce' ),
		'description' => __( 'A Swiss Knife for WooCommerce.', 'woo_ce' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-poor-guys-swiss-knife/',
		'slug' => 'woocommerce-poor-guys-swiss-knife',
		'function' => 'wcpgsk_init'
	);
	$modules[] = array(
		'name' => 'checkout_field_editor',
		'title' => __( 'Checkout Field Editor', 'woo_ce' ),
		'description' => __( 'Add, edit and remove fields shown on your WooCommerce checkout page.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-checkout-field-editor/',
		'function' => 'woocommerce_init_checkout_field_editor'
	);
	$modules[] = array(
		'name' => 'checkout_field_manager',
		'title' => __( 'Checkout Field Manager', 'woo_ce' ),
		'description' => __( 'Quickly and effortlessly add, remove and re-orders fields in the checkout process.', 'woo_ce' ),
		'url' => 'http://61extensions.com/shop/woocommerce-checkout-field-manager/',
		'function' => 'sod_woocommerce_checkout_manager_settings'
	);
	$modules[] = array(
		'name' => 'checkout_addons',
		'title' => __( 'WooCommerce Checkout Add-Ons', 'woo_ce' ),
		'description' => __( 'Add fields at checkout for add-on products and services while optionally setting a cost for each add-on.', 'woo_ce' ),
		'url' => 'http://www.skyverge.com/product/woocommerce-checkout-add-ons/',
		'function' => 'init_woocommerce_checkout_add_ons'
	);
	$modules[] = array(
		'name' => 'local_pickup_plus',
		'title' => __( 'Local Pickup Plus', 'woo_ce' ),
		'description' => __( 'Let customers pick up products from specific locations.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/local-pickup-plus/',
		'class' => 'WC_Local_Pickup_Plus'
	);
	$modules[] = array(
		'name' => 'gravity_forms',
		'title' => __( 'Gravity Forms', 'woo_ce' ),
		'description' => __( 'Gravity Forms is hands down the best contact form plugin for WordPress powered websites.', 'woo_ce' ),
		'url' => 'http://woothemes.com/woocommerce',
		'class' => 'RGForms'
	);
	$modules[] = array(
		'name' => 'currency_switcher',
		'title' => __( 'WooCommerce Currency Switcher', 'woo_ce' ),
		'description' => __( 'Currency Switcher for WooCommerce allows your shop to display prices and accept payments in multiple currencies.', 'woo_ce' ),
		'url' => 'http://aelia.co/shop/currency-switcher-woocommerce/',
		'class' => 'WC_Aelia_CurrencySwitcher'
	);
	$modules[] = array(
		'name' => 'subscriptions',
		'title' => __( 'WooCommerce Subscriptions', 'woo_ce' ),
		'description' => __( 'WC Subscriptions makes it easy to create and manage products with recurring payments.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-subscriptions/',
		'class' => 'WC_Subscriptions_Manager'
	);
	$modules[] = array(
		'name' => 'extra_product_options',
		'title' => __( 'Extra Product Options', 'woo_ce' ),
		'description' => __( 'Create extra price fields globally or per-Product', 'woo_ce' ),
		'url' => 'http://codecanyon.net/item/woocommerce-extra-product-options/7908619',
		'class' => 'TM_Extra_Product_Options'
	);
	$modules[] = array(
		'name' => 'woocommerce_jetpack',
		'title' => __( 'WooCommerce Jetpack', 'woo_ce' ),
		'description' => __( 'Supercharge your WooCommerce site with these awesome powerful features.', 'woo_ce' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-jetpack/',
		'slug' => 'woocommerce-jetpack',
		'class' => 'WC_Jetpack'
	);
	$modules[] = array(
		'name' => 'woocommerce_jetpack_plus',
		'title' => __( 'WooCommerce Jetpack Plus', 'woo_ce' ),
		'description' => __( 'Unlock all WooCommerce Jetpack features and supercharge your WordPress WooCommerce site even more.', 'woo_ce' ),
		'url' => 'http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/',
		'class' => 'WC_Jetpack_Plus'
	);
	$modules[] = array(
		'name' => 'woocommerce_brands',
		'title' => __( 'WooCommerce Brands', 'woo_ce' ),
		'description' => __( 'Woocommerce Brands Plugin. After Install and active this plugin you\'ll have some shortcode and some widget for display your brands in fornt-end website.', 'woo_ce' ),
		'url' => 'http://proword.net/Woocommerce_Brands/',
		'class' => 'woo_brands'
	);
	$modules[] = array(
		'name' => 'woocommerce_bookings',
		'title' => __( 'WooCommerce Bookings', 'woo_ce' ),
		'description' => __( 'Setup bookable products such as for reservations, services and hires.', 'woo_ce' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-bookings/',
		'class' => 'WC_Bookings'
	);
	$modules[] = array(
		'name' => 'eu_vat',
		'title' => __( 'WooCommerce EU VAT Number', 'woo_ce' ),
		'description' => __( 'The EU VAT Number extension lets you collect and validate EU VAT numbers during checkout to identify B2B transactions verses B2C.', 'woo_ce' ),
		'url' => 'http://woothemes.com/',
		'function' => '__wc_eu_vat_number_init'
	);
	$modules[] = array(
		'name' => 'hear_about_us',
		'title' => __( 'WooCommerce Hear About Us', 'woo_ce' ),
		'description' => __( 'Ask where your new customers come from at Checkout.', 'woo_ce' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-hear-about-us/',
		'slug' => 'woocommerce-hear-about-us', // Define this if the Plugin is hosted on the WordPress repo
		'class' => 'WooCommerce_HearAboutUs'
	);
	$modules[] = array(
		'name' => 'wholesale_pricing',
		'title' => __( 'WooCommerce Wholesale Pricing', 'woo_ce' ),
		'description' => __( 'Allows you to set wholesale prices for products and variations.', 'woo_ce' ),
		'url' => 'http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/',
		'class' => 'woocommerce_wholesale_pricing'
	);
	$modules[] = array(
		'name' => 'woocommerce_barcodes',
		'title' => __( 'Barcodes for WooCommerce', 'woo_ce' ),
		'description' => __( 'Allows you to add GTIN (former EAN) codes natively to your products.', 'woo_ce' ),
		'url' => 'http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/',
		'function' => 'wpps_requirements_met'
	);

/*
	$modules[] = array(
		'name' => '',
		'title' => __( '', 'woo_ce' ),
		'description' => __( '', 'woo_ce' ),
		'url' => '',
		'slug' => '', // Define this if the Plugin is hosted on the WordPress repo
		'function' => ''
	);
*/

	$modules = apply_filters( 'woo_ce_modules_addons', $modules );

	if( !empty( $modules ) ) {
		foreach( $modules as $key => $module ) {
			$modules[$key]['status'] = 'inactive';
			// Check if each module is activated
			if( isset( $module['function'] ) ) {
				if( is_array( $module['function'] ) ) {
					$size = count( $module['function'] );
					for( $i = 0; $i < $size; $i++ ) {
						if( function_exists( $module['function'][$i] ) ) {
							$modules[$key]['status'] = 'active';
							break;
						}
					}
				} else {
					if( function_exists( $module['function'] ) )
						$modules[$key]['status'] = 'active';
				}
			} else if( isset( $module['class'] ) ) {
				if( is_array( $module['class'] ) ) {
					$size = count( $module['class'] );
					for( $i = 0; $i < $size; $i++ ) {
						if( function_exists( $module['class'][$i] ) ) {
							$modules[$key]['status'] = 'active';
							break;
						}
					}
				} else {
					if( class_exists( $module['class'] ) )
					$modules[$key]['status'] = 'active';
				}
			}
			// Check if the Plugin has a slug and if current user can install Plugins
			if( current_user_can( 'install_plugins' ) && isset( $module['slug'] ) )
				$modules[$key]['url'] = admin_url( sprintf( 'plugin-install.php?tab=search&type=term&s=%s', $module['slug'] ) );
		}
	}
	return $modules;

}

function woo_ce_modules_status_class( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = 'green';
			break;

		case 'inactive':
			$output = 'yellow';
			break;

	}
	echo $output;

}

function woo_ce_modules_status_label( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = __( 'OK', 'woo_ce' );
			break;

		case 'inactive':
			$output = __( 'Install', 'woo_ce' );
			break;

	}
	echo $output;

}

function woo_ce_dashboard_setup() {

	// Check that scheduled exports is enabled and the User has permission
	if( woo_ce_get_option( 'enable_auto', 0 ) == 1 && current_user_can( 'manage_options' ) ) {
		wp_add_dashboard_widget( 'woo_ce_scheduled_export_widget', __( 'Scheduled Exports', 'woo_ce' ), 'woo_ce_scheduled_export_widget' );
		wp_add_dashboard_widget( 'woo_ce_recent_scheduled_export_widget', __( 'Recent Scheduled Exports', 'woo_ce' ), 'woo_ce_recent_scheduled_export_widget', 'woo_ce_recent_scheduled_export_widget_configure' );
	}

}

function woo_ce_scheduled_export_widget() {

	$enable_auto = woo_ce_get_option( 'enable_auto', 0 );
	if( $enable_auto == 1 ) {
		if( ( $next_export = woo_ce_next_scheduled_export() ) == false )
			$next_export = __( 'a little while... just waiting on WP-CRON to refresh its task list', 'woo_ce' );
	}

	if( file_exists( WOO_CD_PATH . 'templates/admin/dashboard_widget-scheduled_export.php' ) ) {
		include_once( WOO_CD_PATH . 'templates/admin/dashboard_widget-scheduled_export.php' );
	} else {
		$message = sprintf( __( 'We couldn\'t load the widget template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woo_ce' ), 'dashboard_widget-scheduled_export.php', WOO_CD_PATH . 'templates/admin/...' );
		ob_start(); ?>
<p><strong><?php echo $message; ?></strong></p>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woo_ce' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woo_ce' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woo_ce' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woo_ce' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woo_ce' ); ?></p>
<?php
		ob_end_flush();
	}

}

function woo_ce_recent_scheduled_export_widget() {

	$recent_exports = woo_ce_get_option( 'recent_scheduled_exports', array() );
	if( empty( $recent_exports ) )
		$recent_exports = array();
	$size = count( $recent_exports );
	$recent_exports = array_reverse( $recent_exports );

	// Get widget options
	if( !$widget_options = get_option( 'woo_ce_recent_scheduled_export_widget_options', array() ) ) {
		$widget_options = array(
			'number' => 5
		);
	}
	// Check if we need to limit the number of recent exports
	if( $size > $widget_options['number'] ) {
		$i = $size;
		// Loop through the recent exports till we get it down to our limit
		for( $i; $i >= $widget_options['number']; $i-- ) {
			unset( $recent_exports[$i] );
		}
		// Save the changes so we don't have to do this next screen refresh
		woo_ce_update_option( 'recent_scheduled_exports', $recent_exports );
	}

	if( file_exists( WOO_CD_PATH . 'templates/admin/dashboard_widget-recent_scheduled_export.php' ) ) {
		include_once( WOO_CD_PATH . 'templates/admin/dashboard_widget-recent_scheduled_export.php' );
	} else {
		$message = sprintf( __( 'We couldn\'t load the widget template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woo_ce' ), 'dashboard_widget-recent_scheduled_export.php', WOO_CD_PATH . 'templates/admin/...' );

		ob_start(); ?>
<p><strong><?php echo $message; ?></strong></p>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woo_ce' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woo_ce' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woo_ce' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woo_ce' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woo_ce' ); ?></p>
<?php
		ob_end_flush();
	}

}

function woo_ce_recent_scheduled_export_widget_configure() {

	// Get widget options
	if( !$widget_options = get_option( 'woo_ce_recent_scheduled_export_widget_options', array() ) ) {
		$widget_options = array(
			'number' => 5
		);
	}

	// Update widget options
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['woo_ce_recent_scheduled_export_widget_post'] ) ) {
		$widget_options = array_map( 'sanitize_text_field', $_POST['woo_ce_recent_scheduled_export'] );
		if( empty( $widget_options['number'] ) )
			$widget_options['number'] = 5;
		update_option( 'woo_ce_recent_scheduled_export_widget_options', $widget_options );
	} ?>
<div>
	<label for="woo_ce_recent_scheduled_export-number"><?php _e( 'Number of scheduled exports', 'woo_ce' ); ?>:</label><br />
	<input type="text" id="woo_ce_recent_scheduled_export-number" name="woo_ce_recent_scheduled_export[number]" value="<?php echo $widget_options['number']; ?>" />
	<p class="description"><?php _e( 'Control then number of recent scheduled exports that are shown.', 'woo_ce' ); ?></p>
</div>
<input name="woo_ce_recent_scheduled_export_widget_post" type="hidden" value="1" />
<?php

}

function woo_ce_register_scheduled_export_cpt() {

	register_post_type(
		'scheduled_export',
		array(
			'labels'              => array(
					'name'               => __( 'Scheduled Exports', 'woocommerce' ),
					'singular_name'      => __( 'Scheduled Export', 'woocommerce' ),
					'add_new'            => __( 'Add Scheduled Export', 'woocommerce' ),
					'add_new_item'       => __( 'Add New Scheduled Export', 'woocommerce' ),
					'edit'               => __( 'Edit', 'woocommerce' ),
					'edit_item'          => __( 'Edit Scheduled Export', 'woocommerce' ),
					'new_item'           => __( 'New Scheduled Export', 'woocommerce' ),
					'view'               => __( 'View Scheduled Export', 'woocommerce' ),
					'view_item'          => __( 'View Scheduled Export', 'woocommerce' ),
					'search_items'       => __( 'Search Scheduled Exports', 'woocommerce' ),
					'not_found'          => __( 'No scheduled exports found', 'woocommerce' ),
					'not_found_in_trash' => __( 'No scheduled exports found in trash', 'woocommerce' ),
					'parent'             => __( 'Parent Scheduled Exports', 'woocommerce' ),
					'menu_name'          => _x( 'Scheduled Exports', 'Admin menu name', 'woocommerce' )
				),
			'description'         => __( 'This is where scheduled exports for Doo Product Exporter are managed.', 'woocommerce' ),
			'public'              => false,
			'show_ui'             => false,
			'capability_type'     => 'scheduled_export',
			'map_meta_cap'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array( 'title', 'comments', 'custom-fields' ),
			'has_archive'         => false,
		)
	);

}
add_action( 'init', 'woo_ce_register_scheduled_export_cpt' );
?>