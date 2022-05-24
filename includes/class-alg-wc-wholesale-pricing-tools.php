<?php
/**
 * Product Price by Quantity for WooCommerce - Tools Class
 *
 * @version 3.0.0
 * @since   2.6.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Tools' ) ) :

class Alg_WC_Wholesale_Pricing_Tools {

	/**
	 * Constructor.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function __construct() {
		add_action( 'alg_wc_wholesale_pricing_settings_saved', array( $this, 'run_admin_tools' ) );
	}

	/**
	 * delete_meta.
	 *
	 * @version 3.0.0
	 * @since   2.6.0
	 */
	function delete_meta( $product_or_term ) {
		global $wpdb;
		$table = ( 'product' === $product_or_term ? $wpdb->postmeta : $wpdb->termmeta );
		$res   = $wpdb->query( "DELETE FROM {$table} WHERE meta_key LIKE '_alg_wc_wholesale_pricing%'" );
		if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
			$title = ( 'product' === $product_or_term ? __( 'Per product', 'wholesale-pricing-woocommerce' ) : __( 'Per product category and per product tag', 'wholesale-pricing-woocommerce' ) );
			WC_Admin_Settings::add_message( sprintf( __( '%s product price by quantity settings: %d records deleted.', 'wholesale-pricing-woocommerce' ), $title, $res ) );
		}
	}

	/**
	 * run_admin_tools.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function run_admin_tools() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			// Product meta
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_tool_delete_product_meta', 'no' ) ) {
				update_option( 'alg_wc_wholesale_pricing_tool_delete_product_meta', 'no' );
				$this->delete_meta( 'product' );
			}
			// Term meta
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_tool_delete_term_meta', 'no' ) ) {
				update_option( 'alg_wc_wholesale_pricing_tool_delete_term_meta', 'no' );
				$this->delete_meta( 'term' );
			}
		}
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Tools();
