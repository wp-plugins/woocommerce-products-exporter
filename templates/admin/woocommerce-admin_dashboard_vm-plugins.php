<?php if( !empty( $vl_plugins ) ) { ?>
<div class="table table_content">

	<table class="woo_vm_version_table">
		<thead>
			<tr>
				<th class="align-left" style="text-align:left;"><?php _e( 'Plugin', 'woo_ce' ); ?></th>
				<th class="align-left" style="text-align:left;"><?php _e( 'Version', 'woo_ce' ); ?></th>
				<th class="align-left" style="text-align:left;"><?php _e( 'Status', 'woo_ce' ); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php foreach( $vl_plugins as $plugin ) { ?>
		<?php if( $plugin['version'] ) { ?>
			<tr>
				<td><a href="<?php echo $plugin['url']; ?>#toc-news" target="_blank"><?php echo $plugin['name']; ?></a></td>
			<?php if( $plugin['version_existing'] ) { ?>
				<td class="version"><?php printf( __( '%s to <span>%s</span>', 'woo_ce' ), $plugin['version_existing'], $plugin['version'] ); ?></td>
				<?php if( $plugin['url'] && current_user_can( 'update_plugins' ) ) { ?>
				<td class="status"><a href="<?php echo admin_url( 'update-core.php' ); ?>"><span class="red" title="<?php printf( __( 'Plugin update available for %s', 'woo_ce' ), $plugin['name'] ); ?>"><?php _e( 'Update', 'woo_ce' ); ?></span></a></td>
				<?php } else { ?>
				<td class="status"><span class="red" title="<?php printf( __( 'Plugin update available for %s', 'woo_ce' ), $plugin['name'] ); ?>"><?php _e( 'Update', 'woo_ce' ); ?></span></td>
				<?php } ?>
			<?php } elseif( $plugin['version_beta'] ) { ?>
				<td class="version"><?php echo $plugin['version_beta']; ?></td>
				<td class="status"><span class="yellow" title="<?php printf( __( '%s is from the future.', 'woo_ce' ), $plugin['name'] ); ?>"><?php _e( 'Beta', 'woo_ce' ); ?></span></td>
			<?php } else { ?>
				<td class="version"><?php echo $plugin['version']; ?></td>
				<td class="status"><span class="green" title="<?php printf( __( '%s is up to date.', 'woo_ce' ), $plugin['name'] ); ?>"><?php _e( 'OK', 'woo_ce' ); ?></span></td>
			<?php } ?>
			</tr>
		<?php } ?>
	<?php } ?>
		</tbody>
	</table>
	<!-- .woo_vm_version_table -->
	<p class="link"><a href="http://www.visser.com.au/woocommerce/" target="_blank"><?php _e( 'Looking for more WooCommerce Plugins?', 'woo_ce' ); ?></a></p>
</div>
<!-- .table -->
<?php } else { ?>
<p><?php _e( 'Connection failed. Please check your network settings.', 'woo_ce' ); ?></p>
<?php } ?>