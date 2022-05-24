<?php
/**
 * Product Price by Quantity for WooCommerce - All Products Section Settings
 *
 * @version 3.0.0
 * @since   1.2.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_All_Products' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_All_Products extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'all_products';
		$this->desc = __( 'All Products', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.0.0
	 * @since   1.2.0
	 *
	 * @todo    [next] save settings (levels data?) in array, i.e. `alg_wc_wholesale_pricing_level_min_qty[$i]` etc. (and same for "user roles" options, and "per product" and "per term" meta?)
	 * @todo    [next] [maybe] add link to "User Roles" section
	 * @todo    [next] [maybe] `alg_wc_wholesale_pricing_all_products_enabled`: better description
	 * @todo    [next] [maybe] "price directly"
	 */
	function get_settings() {

		// Levels
		$do_process_formula = ( 'yes' === get_option( 'alg_wc_wholesale_pricing_process_formula', 'no' ) );
		$option_type        = ( ! $do_process_formula ? 'number' : 'text' );
		$levels_settings = array(
			array(
				'title'    => __( 'All Products', 'wholesale-pricing-woocommerce' ),
				'desc'     => sprintf( __( 'You can exclude selected products in "%s" section below.', 'wholesale-pricing-woocommerce' ),
					__( 'Advanced Options', 'wholesale-pricing-woocommerce' ) ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_level_options',
			),
			array(
				'title'    => __( 'All products', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_wholesale_pricing_all_products_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Discount type', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_discount_type',
				'default'  => 'percent',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'percent' => __( 'Percent', 'wholesale-pricing-woocommerce' ),
					'fixed'   => __( 'Fixed', 'wholesale-pricing-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Number of levels', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => $this->get_save_changes_msg(),
				'id'       => 'alg_wc_wholesale_pricing_levels_number',
				'default'  => 1,
				'type'     => 'number',
				'custom_attributes' => $this->get_levels_num_custom_atts(),
			),
		);
		for ( $i = 1; $i <= alg_wc_wholesale_pricing()->core->get_total_levels( 'all' ); $i++ ) {
			$levels_settings = array_merge( $levels_settings, array(
				array(
					'title'    => __( 'Level', 'wholesale-pricing-woocommerce' ) . ' #' . $i,
					'desc_tip' => __( 'Minimum quantity to apply discount.', 'wholesale-pricing-woocommerce' ),
					'desc'     => __( 'Min quantity', 'wholesale-pricing-woocommerce' ) .
						( ! $do_process_formula ? '' : '<br>' . sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ),
							'<code>10+[alg_wc_ppq_product_meta key="your_custom_qty_field_key"]</code>' ) ),
					'id'       => 'alg_wc_wholesale_pricing_level_min_qty_' . $i,
					'default'  => 0,
					'type'     => $option_type,
					'css'      => ( ! $do_process_formula ? '' : 'width:100%;' ),
					'custom_attributes' => ( ! $do_process_formula ? array( 'step' => '1', 'min' => '0' ) : '' ),
				),
				array(
					'desc_tip' => __( 'To set fee instead of discount - enter negative number.', 'wholesale-pricing-woocommerce' ),
					'desc'     => __( 'Discount', 'wholesale-pricing-woocommerce' ) .
						( ! $do_process_formula ? '' : '<br>' . sprintf( __( 'E.g.: %s', 'wholesale-pricing-woocommerce' ),
							'<code>[alg_wc_ppq_product_meta key="_price"]+[alg_wc_ppq_product_meta key="your_custom_discount_field_key"]</code>' ) ),
					'id'       => 'alg_wc_wholesale_pricing_level_discount_' . $i,
					'default'  => 0,
					'type'     => $option_type,
					'css'      => ( ! $do_process_formula ? '' : 'width:100%;' ),
					'custom_attributes' => ( ! $do_process_formula ? array( 'step' => '0.0001' ) : '' ),
				),
			) );
		}
		$levels_settings = array_merge( $levels_settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_level_options',
			),
		) );

		// Advanced options
		$products = array();
		foreach ( array( 'include', 'exclude' ) as $include_or_exclude ) {
			$current_products = get_option( 'alg_wc_wholesale_pricing_products_to_' . $include_or_exclude, array() );
			$products[ $include_or_exclude ] = array();
			foreach ( $current_products as $product_id ) {
				$product = wc_get_product( $product_id );
				$products[ $include_or_exclude ][ esc_attr( $product_id ) ] = ( is_object( $product ) ?
					esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) : sprintf( esc_html__( 'Product #%d', 'wholesale-pricing-woocommerce' ), $product_id ) );
			}
		}
		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced Options', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_advanced_options',
			),
			array(
				'title'    => __( 'Required products', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => __( 'Ignored if empty.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_products_to_include',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'wc-product-search',
				'options'  => $products['include'],
				'custom_attributes' => array(
					'data-placeholder' => esc_attr__( 'Search for a product&hellip;', 'woocommerce' ),
					'data-action'      => 'woocommerce_json_search_products_and_variations',
				),
			),
			array(
				'title'    => __( 'Excluded products', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_products_to_exclude',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'wc-product-search',
				'options'  => $products['exclude'],
				'custom_attributes' => array(
					'data-placeholder' => esc_attr__( 'Search for a product&hellip;', 'woocommerce' ),
					'data-action'      => 'woocommerce_json_search_products_and_variations',
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_advanced_options',
			),
		);

		return array_merge( $levels_settings, $advanced_settings );
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_All_Products();
