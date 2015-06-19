<?php if( $enable_auto == 1 ) { ?>
<p style="font-size:0.8em;"><div class="dashicons dashicons-yes"></div>&nbsp;<strong><?php _e( 'Scheduled Exports is enabled', 'woo_ce' ); ?></strong></p>
<p><?php printf( __( 'Next scheduled export will run in %s', 'woo_ce' ), $next_export ); ?></p>
<?php } ?>
