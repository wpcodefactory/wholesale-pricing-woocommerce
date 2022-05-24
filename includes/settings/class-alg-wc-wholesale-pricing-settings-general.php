<?php
/**
 * Product Price by Quantity for WooCommerce - General Section Settings
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_General' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_General extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (desc) `alg_wc_wholesale_pricing_add_order_discount`: better desc
	 * @todo    [next] (dev) `alg_wc_wholesale_pricing_lumise_enabled`: remove? (or at least default to `yes`)
	 * @todo    [next] (dev) `alg_wc_wholesale_pricing_round`: default to `no`
	 * @todo    [next] (feature) `alg_wc_wholesale_pricing_round`: custom precision
	 * @todo    [next] (desc) `alg_wc_wholesale_pricing_process_formula`: better description
	 * @todo    [next] (dev) sentence capitalization for all section titles (including per term settings etc.)
	 * @todo    [maybe] (desc) Per variation: better description?
	 * @todo    [maybe] (dev) `alg_wc_wholesale_pricing_process_formula`: default to `yes`?
	 * @todo    [maybe] (desc) `alg_wc_wholesale_pricing_enable_options`: better description?
	 */
	function get_settings() {

		$main_settings = array(
			array(
				'title'    => __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_options',
			),
			array(
				'title'    => __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_wholesale_pricing_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_options',
			),
		);

		$enable_settings = array(
			array(
				'title'    => __( 'Settings', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_enable_options',
				'desc'     => __( 'Here you choose at which level you want to set wholesale discount options.', 'wholesale-pricing-woocommerce' ),
			),
			array(
				'title'    => __( 'All products', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( 'Settings are in the %s section.', 'wholesale-pricing-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_wholesale_pricing&section=all_products' ) . '">' .
						__( 'All Products', 'wholesale-pricing-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_wholesale_pricing_all_products_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Per product', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'This will add new settings meta box to each product\'s edit page.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_per_product_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Per variation', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Enable this if you want to set options for each variation of a variable product separately.', 'wholesale-pricing-woocommerce' ) .
					apply_filters( 'alg_wc_wholesale_pricing_settings', '<br>' . sprintf(
						'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
							'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_product_children',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per product category', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'This will add new settings fields to each product category edit page.', 'wholesale-pricing-woocommerce' ) .
					apply_filters( 'alg_wc_wholesale_pricing_settings', '<br>' . sprintf(
						'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
							'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_per_product_cat_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per product tag', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'This will add new settings fields to each product tag edit page.', 'wholesale-pricing-woocommerce' ) .
					apply_filters( 'alg_wc_wholesale_pricing_settings', '<br>' . sprintf(
						'You will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin to enable this option.',
							'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) ),
				'id'       => 'alg_wc_wholesale_pricing_per_product_tag_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_wholesale_pricing_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_enable_options',
			),
		);

		$general_settings = array(
			array(
				'title'    => __( 'General', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_general_options',
			),
			array(
				'title'    => __( 'Rounding', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => sprintf( __( 'Round calculated product wholesale price according to the "%s" option (i.e. %s).', 'wholesale-pricing-woocommerce' ),
					'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings' ) . '">' . __( 'Number of decimals', 'wholesale-pricing-woocommerce' ) . '</a>',
					'<code>' . get_option( 'woocommerce_price_num_decimals', 2 ) . '</code>' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_round',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Quantity calculation', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_use_total_cart_quantity', // mislabeled, should be `alg_wc_wholesale_pricing_quantity_calculation`
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'           => __( 'Single product quantity', 'wholesale-pricing-woocommerce' ),
					'yes'          => __( 'Total cart quantity', 'wholesale-pricing-woocommerce' ),
					'_parent'      => __( 'Group by product parent (e.g. for variations)', 'wholesale-pricing-woocommerce' ) . apply_filters( 'alg_wc_wholesale_pricing_settings', ' [' . __( 'Pro only', 'wholesale-pricing-woocommerce' ) . ']' ),
					'_product_cat' => __( 'Group by product category', 'wholesale-pricing-woocommerce' ) . apply_filters( 'alg_wc_wholesale_pricing_settings', ' [' . __( 'Pro only', 'wholesale-pricing-woocommerce' ) . ']' ),
					'_product_tag' => __( 'Group by product tag', 'wholesale-pricing-woocommerce' ) . apply_filters( 'alg_wc_wholesale_pricing_settings', ' [' . __( 'Pro only', 'wholesale-pricing-woocommerce' ) . ']' ),
				),
				'desc'     => apply_filters( 'alg_wc_wholesale_pricing_settings', sprintf(
					'For some options (e.g. "%s") you will need <a target="_blank" href="%s">Product Price by Quantity for WooCommerce Pro</a> plugin.',
						__( 'Group by product parent', 'wholesale-pricing-woocommerce' ),
						'https://wpfactory.com/item/wholesale-pricing-woocommerce/' ) )
			),
			array(
				'title'    => __( 'Other cart discounts', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Apply wholesale discount only if no other cart discounts were applied.', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_apply_only_if_no_other_discounts',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Formula and shortcodes', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Process formula and shortcodes in discount table values (i.e. in "Min quantity #X" and "Discount #X" fields).', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_process_formula',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Admin recalculate order', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Adds "Recalculate" button to admin order pages.', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_admin_recalculate_order',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'show_if_checked' => 'option',
				'checkboxgroup'   => 'start',
			),
			array(
				'desc'     => __( 'Require confirmation', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_admin_recalculate_order_confirm',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'show_if_checked' => 'yes',
				'checkboxgroup'   => '',
			),
			array(
				'desc'     => __( 'Add order note', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_admin_recalculate_order_note',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'show_if_checked' => 'yes',
				'checkboxgroup'   => 'end',
			),
			array(
				'title'    => __( 'Add order discount', 'wholesale-pricing-woocommerce' ),
				'desc'     => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Will replace all product price changes with an order discount.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_add_order_discount',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_general_options',
			),
			array(
				'title'    => __( 'Compatibility Options', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_compatibility_options',
			),
			array(
				'title'    => __( 'WCFM plugin compatibility', 'wholesale-pricing-woocommerce' ),
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
				'title'    => __( '"Lumise - Product Designer Tool" plugin compatibility', 'wholesale-pricing-woocommerce' ),
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

		return array_merge( $main_settings, $enable_settings, $general_settings );
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_General();
