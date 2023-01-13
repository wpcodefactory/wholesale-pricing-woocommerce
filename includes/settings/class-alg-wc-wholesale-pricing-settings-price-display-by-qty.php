<?php
/**
 * Product Price by Quantity for WooCommerce - Price Display by Qty Section Settings
 *
 * @version 3.2.0
 * @since   1.3.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Price_Display_By_Qty' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Price_Display_By_Qty extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function __construct() {
		$this->id   = 'price_display_by_qty';
		$this->desc = __( 'Price Display by Qty', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.2.0
	 * @since   1.3.0
	 *
	 * @todo    [now] (dev) Price identifier: store in array?
	 * @todo    [now] (desc) Price identifier: add desc
	 * @todo    [maybe] (desc) `alg_wc_wholesale_pricing_price_by_qty_standard_qty_input`: better desc?
	 * @todo    [maybe] (desc) output `get_placeholders_desc()` in *section* desc instead
	 */
	function get_settings() {

		$price_by_qty_display_settings = array(
			array(
				'title'    => __( 'Price Display by Quantity', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'This section allows you to display product price by quantity in real time, i.e. when customer changes product quantity on single product page.', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_options',
			),
			array(
				'title'    => __( 'Price display by quantity', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Position', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Price display by quantity position on the frontend.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_position',
				'default'  => 'instead',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'instead' => __( 'Instead of the price', 'wholesale-pricing-woocommerce' ),
					'before'  => __( 'Before the price', 'wholesale-pricing-woocommerce' ),
					'after'   => __( 'After the price', 'wholesale-pricing-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Template', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Price display by quantity template for quantities with discount.', 'wholesale-pricing-woocommerce' ),
				'desc'     => $this->get_placeholders_desc( array(
						'%qty%',
						'%old_price_single%',
						'%old_price_total%',
						'%new_price_single%',
						'%new_price_total%',
						'%discount_value%',
						'%discount_percent%',
						'%discount_single%',
						'%discount_total%',
					) ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_template',
				'default'  => sprintf( __( '%s for %s pcs.', 'wholesale-pricing-woocommerce' ), '<del>%old_price_total%</del> %new_price_total%', '%qty%' ) . ' ' .
					sprintf( __( 'You save: %s', 'wholesale-pricing-woocommerce' ), '<span style="color:red">%discount_percent%%</span>' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Template (no discount)', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Price display by quantity template for quantities with no discount.', 'wholesale-pricing-woocommerce' ),
				'desc'     => $this->get_placeholders_desc( array(
						'%qty%',
						'%old_price_single%',
						'%old_price_total%',
					) ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_template_zero',
				'default'  => sprintf( __( '%s for %s pcs.', 'wholesale-pricing-woocommerce' ), '%old_price_total%', '%qty%' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_options',
			),
			array(
				'title'    => __( 'Advanced Options', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_advanced_options',
			),
			array(
				'title'    => __( 'Apply to all products', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Apply to all products vs. only to products with the enabled product price by quantity.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_all_products',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Variable products', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Display in variation price', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Will display price by quantity in variation price section.', 'wholesale-pricing-woocommerce' ) . ' ' .
					__( 'Ignored if it\'s not a variable product, or if variation prices are the same.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_in_variation',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force standard quantity input', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Enable this if you are using non-standard quantity input (e.g. dropdown), and having issues with the price display by quantity.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_standard_qty_input',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Refresh interval', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'milliseconds', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_interval_ms',
				'default'  => 500,
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Price identifier', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_id',
				'default'  => 'p.price',
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Variations', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_id_variation',
				'default'  => 'div.woocommerce-variation-price span.price',
				'type'     => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_display_advanced_options',
			),
			array(
				'title'    => __( 'Compatibility Options', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_compatibility_options',
			),
			array(
				'title'    => __( 'Sticky Add To Cart Bar For WooCommerce', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( '%s plugin compatibility.', 'wholesale-pricing-woocommerce' ),
					'<a href="https://wordpress.org/plugins/sticky-add-to-cart-bar-for-wc/" target="_blank">' .
						__( 'Sticky Add To Cart Bar For WooCommerce', 'wholesale-pricing-woocommerce' ) .
					'</a>'
				),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_price_by_qty_sitcky_add_to_cart',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_compatibility_options',
			),
		);

		return $price_by_qty_display_settings;
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Price_Display_By_Qty();
