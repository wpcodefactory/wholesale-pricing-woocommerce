<?php
/**
 * Product Price by Quantity for WooCommerce - Tools Section Settings
 *
 * @version 3.0.0
 * @since   2.6.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Tools' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Tools extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function __construct() {
		$this->id   = 'tools';
		$this->desc = __( 'Tools', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.0.0
	 * @since   2.6.0
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Tools', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Check the box and save changes to run the tool.', 'wholesale-pricing-woocommerce' ) . ' ' .
					'<strong>' . __( 'Please note that there is no undo for this action.', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_tools_options',
			),
			array(
				'title'    => __( 'Delete per product settings', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Delete', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf( __( 'Will delete all %s product price by quantity settings.', 'wholesale-pricing-woocommerce' ),
					'<strong>' . __( 'per product', 'wholesale-pricing-woocommerce' ) . '</strong>' ),
				'id'       => 'alg_wc_wholesale_pricing_tool_delete_product_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Delete per term settings', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Delete', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf( __( 'Will delete all %s product price by quantity settings.', 'wholesale-pricing-woocommerce' ),
					sprintf( __( '%s and %s', 'wholesale-pricing-woocommerce' ),
						'<strong>' . __( 'per product category', 'wholesale-pricing-woocommerce' ) . '</strong>',
						'<strong>' . __( 'per product tag', 'wholesale-pricing-woocommerce' )      . '</strong>' ) ),
				'id'       => 'alg_wc_wholesale_pricing_tool_delete_term_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_tools_options',
			),
		);
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Tools();
