<?php
if( !function_exists( 'woo_get_action' ) ) {
	function woo_get_action( $prefer_get = false ) {

		if ( isset( $_GET['action'] ) && $prefer_get )
			return sanitize_text_field( $_GET['action'] );

		if ( isset( $_POST['action'] ) )
			return sanitize_text_field( $_POST['action'] );

		if ( isset( $_GET['action'] ) )
			return sanitize_text_field( $_GET['action'] );

		return false;

	}
}

if( !function_exists( 'woo_is_woo_activated' ) ) {
	function woo_is_woo_activated() {

		if( class_exists( 'Woocommerce' ) )
			return true;

	}
}

if( !function_exists( 'woo_get_woo_version' ) ) {
	function woo_get_woo_version() {

		$version = false;
		if( defined( 'WC_VERSION' ) ) {
			$version = WC_VERSION;
		// Backwards compatibility
		} else if( defined( 'WOOCOMMERCE_VERSION' ) ) {
			$version = WOOCOMMERCE_VERSION;
		}
		return $version;
	
	}
}
?>