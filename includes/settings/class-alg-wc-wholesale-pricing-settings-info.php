<?php
/**
 * Product Price by Quantity for WooCommerce - Info Section Settings
 *
 * @version 3.2.3
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Info' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Info extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		$this->id   = 'info';
		$this->desc = __( 'Info', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.2.3
	 * @since   2.0.0
	 *
	 * @todo    [next] (desc) `alg_wc_wholesale_pricing_info_on_single_product_hide_variable`: better desc?
	 * @todo    [next] (desc) `alg_wc_wholesale_pricing_info_on_single_product_variable`: better desc?
	 * @todo    [next] (desc) Replace Price: better desc?
	 * @todo    [next] (dev) Replace Price: better default values
	 * @todo    [next] (feature) Replace Price: separate option for variations?
	 * @todo    [next] (feature) Replace Price: separate option for variable products (i.e. price range)?
	 * @todo    [next] (feature) `alg_wc_wholesale_pricing_show_info_single_hook`: more positions?
	 * @todo    [maybe] (fix) Cart: fix cart info, when discount < 0
	 * @todo    [maybe] (desc) Discount Pricing Table: better title/desc?
	 * @todo    [maybe] (desc) Discount Pricing Table: add more (shortcodes) examples
	 * @todo    [maybe] (feature) Discount Pricing Table: customizable position(s)
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Cart Items', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_info_cart_options',
				'desc'     => __( 'Show discount pricing info in cart.', 'wholesale-pricing-woocommerce' ) . '<br>' .
					$this->get_placeholders_desc( array(
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
						)
					),
			),
			array(
				'title'    => __( 'Item price', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Show', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_on_cart',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Template', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_on_cart_format',
				'default'  => '<del>%old_price_single%</del> %new_price_single%<br>' .
					sprintf( __( 'You save: %s', 'wholesale-pricing-woocommerce' ), '<span style="color:red">%discount_percent%%</span>' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Item subtotal', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Show', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_on_cart_subtotal',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Template', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_on_cart_format_subtotal',
				'default'  => '<del>%old_price_total%</del> %new_price_total%<br>' .
					sprintf( __( 'You save: %s', 'wholesale-pricing-woocommerce' ), '<span style="color:red">%discount_total%</span>' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_info_cart_options',
			),
			array(
				'title'    => __( 'Cart & Checkout Totals', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_info_cart_totals_options',
				'desc'     => __( 'Show discount pricing info in cart totals.', 'wholesale-pricing-woocommerce' ) . '<br>' .
					$this->get_placeholders_desc( array( '%total_cart_discount%' ) ),
			),
			array(
				'title'    => __( 'Cart & checkout totals', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Show', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_info_cart_totals_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Template', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_info_cart_totals_template',
				'default'  => '<tr class="wholesale-pricing-total-discount"><th>Discount</th><td data-title="Discount">%total_cart_discount%</td></tr>',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'desc'     => __( 'Positions', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_info_cart_totals_positions',
				'default'  => array( 'woocommerce_cart_totals_before_order_total', 'woocommerce_review_order_before_order_total' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'woocommerce_cart_totals_before_order_total'  => __( 'Cart: Before order total', 'wholesale-pricing-woocommerce' ),
					'woocommerce_cart_totals_after_order_total'   => __( 'Cart: After order total', 'wholesale-pricing-woocommerce' ),
					'woocommerce_review_order_before_order_total' => __( 'Checkout: Before order total', 'wholesale-pricing-woocommerce' ),
					'woocommerce_review_order_after_order_total'  => __( 'Checkout: After order total', 'wholesale-pricing-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_info_cart_totals_options',
			),
			array(
				'title'    => __( 'Discount Pricing Table', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_info_options',
				'desc'     => __( 'Show discount pricing table on single product and shop pages.', 'wholesale-pricing-woocommerce' ) . '<br>' .
					sprintf( __( 'You can also display product price by quantity info anywhere on your site with %s <a target="_blank" href="%s">shortcodes</a>.', 'wholesale-pricing-woocommerce' ),
						'<code>' . implode( '</code>, <code>', array(
							'[alg_wc_ppq_table]',
							'[alg_wc_product_ppq_table]',
							'[alg_wc_ppq_data]',
							'[alg_wc_product_ppq_data]',
						) ) . '</code>',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/#shortcodes'
					),
			),
			array(
				'title'    => __( 'Single product page', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Show', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_on_single_product_page',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Position', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_single_hook',
				'default'  => 'woocommerce_single_product_summary',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => apply_filters( 'alg_wc_wholesale_pricing_show_info_single_hook_options', array(
					'woocommerce_before_single_product'         => __( 'Before product', 'wholesale-pricing-woocommerce' ),
					'woocommerce_before_single_product_summary' => __( 'Before product summary', 'wholesale-pricing-woocommerce' ),
					'woocommerce_single_product_summary'        => __( 'Product summary', 'wholesale-pricing-woocommerce' ),
					'woocommerce_after_single_product_summary'  => __( 'After product summary', 'wholesale-pricing-woocommerce' ),
					'woocommerce_after_single_product'          => __( 'After product', 'wholesale-pricing-woocommerce' ),
				) ),
			),
			array(
				'desc'     => __( 'Priority', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Fine-tunes the position.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_single_hook_priority',
				'default'  => 25,
				'type'     => 'number',
			),
			array(
				'desc'     => __( 'Template for <strong>non-variable products</strong>', 'wholesale-pricing-woocommerce' ) . '<br>' .
					sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ),
						'<code>' . esc_html( '[alg_wc_product_ppq_table table_format="vertical" heading_format="from %level_min_qty% pcs." price_row_format="%new_price_single%"]' ) . '</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_info_on_single_product',
				'default'  => '[alg_wc_product_ppq_table]',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'desc'     => __( 'Template for <strong>variable products</strong>', 'wholesale-pricing-woocommerce' ) . '<br>' .
					sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ),
						'<code>' . esc_html( '[alg_wc_product_ppq_table table_format="vertical" heading_format="from %level_min_qty% pcs." price_row_format="%new_price_single%"]' ) . '</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_info_on_single_product_variable',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'desc'     => __( 'Template for <strong>variations</strong>', 'wholesale-pricing-woocommerce' ) . '<br>' .
					sprintf( __( 'Available placeholders: %s.', 'wholesale-pricing-woocommerce' ), '<code>%variation_id%</code>' ) . '<br>' .
					sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ),
						'<code>' . esc_html( '[alg_wc_product_ppq_table product_id="%variation_id%" table_format="horizontal" price_row_format="<del>%old_price_single%</del> %new_price_single%"]' ) . '</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_info_on_single_product_variation',
				'default'  => '[alg_wc_product_ppq_table product_id="%variation_id%"]',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'desc'     => __( 'Hide main variable table on visible variation', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Will automatically hide main variable product pricing table, when variation table becomes visible.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_info_on_single_product_hide_variable',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Shop page', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Show', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_loop',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Position', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_loop_hook',
				'default'  => 'woocommerce_after_shop_loop_item',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'woocommerce_before_shop_loop_item'       => __( 'Before item', 'wholesale-pricing-woocommerce' ),
					'woocommerce_before_shop_loop_item_title' => __( 'Before item title', 'wholesale-pricing-woocommerce' ),
					'woocommerce_shop_loop_item_title'        => __( 'Item title', 'wholesale-pricing-woocommerce' ),
					'woocommerce_after_shop_loop_item_title'  => __( 'After item title', 'wholesale-pricing-woocommerce' ),
					'woocommerce_after_shop_loop_item'        => __( 'After item', 'wholesale-pricing-woocommerce' ),
				),
			),
			array(
				'desc'     => __( 'Priority', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Fine-tunes the position.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_show_info_loop_hook_priority',
				'default'  => 9,
				'type'     => 'number',
			),
			array(
				'desc'     => __( 'Template', 'wholesale-pricing-woocommerce' ) . '<br>' .
					sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ),
						'<code>' . esc_html( '[alg_wc_product_ppq_table table_format="vertical" heading_format="from %level_min_qty% pcs." price_row_format="%new_price_single%"]' ) . '</code>' ),
				'id'       => 'alg_wc_wholesale_pricing_info_loop',
				'default'  => '[alg_wc_product_ppq_table]',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_info_options',
			),
			array(
				'title'    => __( 'Replace Price', 'wholesale-pricing-woocommerce' ),
				'desc'     => sprintf( __( 'Replace standard product price display (for example: %s) to a discounted one (for example: %s)', 'wholesale-pricing-woocommerce' ),
					'<code>' . __( '$7,00', 'wholesale-pricing-woocommerce' ) . '</code>', '<code>' . __( 'From $5,00 for 10 pcs.', 'wholesale-pricing-woocommerce' ) . '</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_replace_price_options',
			),
			array(
				'title'    => __( 'Single product page', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Replace', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_replace_price_on_single_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc_tip' => __( 'Template for single product pages.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_replace_price_on_single_template',
				'default'  => '[alg_wc_product_ppq_data field="price" level_num="1" product_id="%product_id%"]',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Shop page', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Replace', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_replace_price_on_loop_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc_tip' => __( 'Template for shop pages.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_replace_price_on_loop_template',
				'default'  => '[alg_wc_product_ppq_data field="price" level_num="1" product_id="%product_id%"]',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_replace_price_options',
			),
		);
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Info();
