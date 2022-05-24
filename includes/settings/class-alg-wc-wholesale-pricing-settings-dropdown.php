<?php
/**
 * Product Price by Quantity for WooCommerce - Dropdown Section Settings
 *
 * @version 3.0.0
 * @since   2.4.2
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Dropdown' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Dropdown extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.6.3
	 * @since   2.4.2
	 */
	function __construct() {
		$this->id   = 'dropdown';
		$this->desc = __( 'Qty Dropdown', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.0.0
	 * @since   2.4.2
	 *
	 * @todo    [next] [!] (dev) `alg_wc_wholesale_pricing_dropdown_filter_values`: default to `yes`?
	 * @todo    [maybe] (dev) `alg_wc_wholesale_pricing_dropdown_label_template`: add "pcs." to the default value?
	 * @todo    [maybe] (desc) Before/After: better title/desc?
	 * @todo    [maybe] (desc) better section desc?
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Quantity Dropdown', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'This section allows you to replace standard quantity input with a dropdown.', 'wholesale-pricing-woocommerce' ) . ' ' .
					__( 'Dropdown values will be equal to the min quantities of the product price by quantity levels.', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_dropdown_options',
			),
			array(
				'title'    => __( 'Dropdown', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this section.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Dropdown label template', 'wholesale-pricing-woocommerce' ),
				'desc'     => $this->get_placeholders_desc( array(
					'%qty%',
					'%qty_total%',
					'%old_price_single%',
					'%old_price_total%',
					'%new_price_single%',
					'%new_price_total%',
					'%discount_value%',
					'%discount_percent%',
					'%discount_single%',
					'%discount_total%',
				) ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_label_template',
				'default'  => '%qty%',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Dropdown class', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( 'HTML class for the %s element.', 'wholesale-pricing-woocommerce' ), '`select`' ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_class',
				'default'  => 'qty',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Dropdown style', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( 'HTML style for the %s element.', 'wholesale-pricing-woocommerce' ), '`select`' ),
				'desc'     => sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ), '<code>min-width:100px;</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_style',
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'HTML before', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( 'Optional HTML outputted before the %s element.', 'wholesale-pricing-woocommerce' ), '`select`' ),
				'desc'     => sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ), '<code>' . esc_html( '<table><tbody><tr><th>Quantity</th><td>' ) . '</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_before',
				'default'  => '',
				'type'     => 'textarea',
			),
			array(
				'title'    => __( 'HTML after', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( 'Optional HTML outputted after the %s element.', 'wholesale-pricing-woocommerce' ), '`select`' ),
				'desc'     => sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ), '<code>' . esc_html( '</td></tr></tbody></table>' ) . '</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_after',
				'default'  => '',
				'type'     => 'textarea',
			),
			array(
				'title'    => __( 'Filter values', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Filter dropdown values using product\'s step, min and max values.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_filter_values',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_dropdown_options',
			),
		);
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Dropdown();
