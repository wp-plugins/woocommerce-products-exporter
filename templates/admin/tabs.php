<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="export" class="nav-tab<?php woo_cd_admin_active_tab( 'export' ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'export' ), 'admin.php' ) ); ?>"><?php _e( 'Export', 'woo_ce' ); ?></a>
	</h2>
	<?php woo_cd_tab_template( $tab ); ?>

</div>
<!-- #content -->