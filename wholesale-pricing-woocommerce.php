<?php
/*
Plugin Name: Wholesale Pricing for WooCommerce
Plugin URI: https://wpfactory.com/item/wholesale-pricing-woocommerce/
Description: Set WooCommerce wholesale pricing depending on product quantity in cart.
Version: 2.8.2-dev
Author: Algoritmika Ltd
Author URI: https://algoritmika.com
Text Domain: wholesale-pricing-woocommerce
Domain Path: /langs
WC tested up to: 6.4
*/

defined( 'ABSPATH' ) || exit;

if ( 'wholesale-pricing-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 */
	$plugin = 'wholesale-pricing-woocommerce-pro/wholesale-pricing-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_WHOLESALE_PRICING_VERSION' ) || define( 'ALG_WC_WHOLESALE_PRICING_VERSION', '2.8.2-dev-20220520-0941' );

defined( 'ALG_WC_WHOLESALE_PRICING_FILE' ) || define( 'ALG_WC_WHOLESALE_PRICING_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-wholesale-pricing.php' );

if ( ! function_exists( 'alg_wc_wholesale_pricing' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Wholesale_Pricing to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_wholesale_pricing() {
		return Alg_WC_Wholesale_Pricing::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_wholesale_pricing' );
