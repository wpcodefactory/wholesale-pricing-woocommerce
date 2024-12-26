<?php
/**
 * Product Price by Quantity for WooCommerce - Dropdown Section Settings
 *
 * @version 4.0.0
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
	 * @version 4.0.0
	 * @since   2.4.2
	 *
	 * @todo    (dev) `alg_wc_wholesale_pricing_dropdown_filter_values`: default to `yes`?
	 * @todo    (dev) `alg_wc_wholesale_pricing_dropdown_label_template`: add "pcs." to the default value?
	 * @todo    (desc) Before/After: better title/desc?
	 * @todo    (desc) better section desc?
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
					'You will need <a target="_blank" href="%s">Price by Quantity & Bulk Quantity Discounts for WooCommerce Pro</a> plugin to enable this section.',
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
					'%old_price_single_incl_tax%',
					'%old_price_single_excl_tax%',
					'%old_price_total_incl_tax%',
					'%old_price_total_excl_tax%',
					'%new_price_single_incl_tax%',
					'%new_price_single_excl_tax%',
					'%new_price_total_incl_tax%',
					'%new_price_total_excl_tax%',
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
				'title'    => __( 'Custom dropdown values', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'By default, the plugin will automatically add product\'s level min quantities to the dropdown, however, you can override it with your own values here.', 'wholesale-pricing-woocommerce' ),
				'desc'     => sprintf( __( 'Comma-separated list of quantities, e.g.: %s', 'wholesale-pricing-woocommerce' ), '<code>1,2,3,5,10,15,20</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_dropdown_custom_values',
				'default'  => '',
				'type'     => 'text',
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
