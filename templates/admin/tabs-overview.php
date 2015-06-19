<div class="overview-left">

	<h3><div class="dashicons dashicons-migrate"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>"><?php _e( 'Export', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Export store details out of WooCommerce into common export files (e.g. CSV, XLSX, XML, etc.).', 'woo_ce' ); ?></p>
	<ul class="ul-disc">
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-product"><?php _e( 'Export Products', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-category"><?php _e( 'Export Categories', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-tag"><?php _e( 'Export Tags', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-brand"><?php _e( 'Export Brands', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-order"><?php _e( 'Export Orders', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-customer"><?php _e( 'Export Customers', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-user"><?php _e( 'Export Users', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-coupon"><?php _e( 'Export Coupons', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-subscription"><?php _e( 'Export Subscriptions', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-product_vendor"><?php _e( 'Export Product Vendors', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-commission"><?php _e( 'Export Commissions', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-shipping_class"><?php _e( 'Export Shipping Classes', 'woo_ce' ); ?></a>
		</li>
<!--
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-attribute"><?php _e( 'Export Attributes', 'woo_ce' ); ?></a>
		</li>
-->
	</ul>

	<h3><div class="dashicons dashicons-list-view"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'archive' ) ); ?>"><?php _e( 'Archives', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Download copies of prior store exports.', 'woo_ce' ); ?></p>

	<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>"><?php _e( 'Settings', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Manage export options from a single detailed screen.', 'woo_ce' ); ?></p>
	<ul class="ul-disc">
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#general-settings"><?php _e( 'General Settings', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#csv-settings"><?php _e( 'CSV Settings', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#xml-settings"><?php _e( 'XML Settings', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#scheduled-exports"><?php _e( 'Scheduled Exports', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#cron-exports"><?php _e( 'CRON Exports', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#orders-screen"><?php _e( 'Orders Screen', 'woo_ce' ); ?></a>
		</li>
	</ul>

	<h3><div class="dashicons dashicons-hammer"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Tools', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Export tools for WooCommerce.', 'woo_ce' ); ?></p>

	<hr />
	<form id="skip_overview_form" method="post">
		<label><input type="checkbox" id="skip_overview" name="skip_overview"<?php checked( $skip_overview ); ?> /> <?php _e( 'Jump to Export screen in the future', 'woo_ce' ); ?></label>
		<input type="hidden" name="action" value="skip_overview" />
	</form>

</div>
<!-- .overview-left -->