<?php if( !empty( $recent_exports ) ) { ?>
<ol>
	<?php foreach( $recent_exports as $recent_export ) { ?>
	<li>
		<p><?php echo $recent_export['name']; ?><?php if( !empty( $recent_export['post_id'] ) && get_post_status( $recent_export['post_id'] ) !== false ) { ?> <a href="<?php echo get_edit_post_link( $recent_export['post_id'] ); ?>">#</a><?php } ?></p>
		<p><?php echo woo_ce_format_archive_date( $recent_export['post_id'], $recent_export['date'] ); ?>, <?php echo ( !empty( $recent_export['error'] ) ? __( 'error', 'woo_ce' ) . ': <span class="error">' . $recent_export['error'] . '.</span>' : woo_ce_format_archive_method( $recent_export['method'] ) . '.' ); ?></p>
	</li>
	<?php } ?>
</ol>
<?php } else { ?>
<p><?php _e( 'Ready for your first scheduled export, shouldn\'t be long now.', 'woo_ce' ); ?>  <strong>:)</strong></p>
<?php } ?>
<p style="text-align:right;"><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'archive' ), 'admin.php' ) ); ?>"><?php _e( 'View all archived exports', 'woo_ce' ); ?></a></p>