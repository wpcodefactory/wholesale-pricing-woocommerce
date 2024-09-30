<?php
/*
Plugin Name: Quantity Dynamic Pricing & Bulk Discounts for WooCommerce
Plugin URI: https://wpfactory.com/item/wholesale-pricing-woocommerce/
Description: Set WooCommerce product prices depending on quantity in cart.
Version: 3.8.0
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: wholesale-pricing-woocommerce
Domain Path: /langs
WC tested up to: 9.3
Requires Plugins: woocommerce
*/

defined( 'ABSPATH' ) || exit;

if ( 'wholesale-pricing-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 3.5.5
	 * @since   2.5.0
	 */
	$plugin = 'wholesale-pricing-woocommerce-pro/wholesale-pricing-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		defined( 'ALG_WC_WHOLESALE_PRICING_FILE_FREE' ) || define( 'ALG_WC_WHOLESALE_PRICING_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_WHOLESALE_PRICING_VERSION' ) || define( 'ALG_WC_WHOLESALE_PRICING_VERSION', '3.8.0' );

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
