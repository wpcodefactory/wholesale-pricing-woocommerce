<?php
/**
 * Product Price by Quantity for WooCommerce - Compatibility Section Settings
 *
 * @version 3.7.0
 * @since   3.7.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Compatibility' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Compatibility extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.7.0
	 * @since   3.7.0
	 */
	function __construct() {
		$this->id   = 'compatibility';
		$this->desc = __( 'Compatibility', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.7.0
	 * @since   3.7.0
	 *
	 * @todo    (desc) `alg_wc_wholesale_pricing_wpml_wcml`: better desc, e.g., "... for the 'Fixed' and 'Price directly' discounts..."
	 * @todo    (desc) add links to all plugins, e.g., WCFM
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Compatibility', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_compatibility_options',
			),
			array(
				'title'    => __( '"WooCommerce Multilingual" (WPML) plugin', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( '%s plugin compatibility.', 'wholesale-pricing-woocommerce' ),
					'<a href="https://wordpress.org/plugins/woocommerce-multilingual/" target="_blank">' .
						__( '"WooCommerce Multilingual" (WPML)', 'wholesale-pricing-woocommerce' ) .
					'</a>' ),
				'id'       => 'alg_wc_wholesale_pricing_wpml_wcml',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'WCFM plugin', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_wcfm_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'New product notification', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_wcfm_new_product_notification',
				'default'  => __( 'Please save the product first.', 'wholesale-pricing-woocommerce' ),
				'type'     => 'textarea',
			),
			array(
				'title'    => __( '"Lumise - Product Designer Tool" plugin', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_lumise_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_compatibility_options',
			),
		);
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Compatibility();
