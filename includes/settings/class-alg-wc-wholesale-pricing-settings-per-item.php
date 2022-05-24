<?php
/**
 * Product Price by Quantity for WooCommerce - Per Item Settings
 *
 * @version 3.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Per_Item' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Per_Item {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_options.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] [maybe] use `get_total_levels()` etc. instead of `get_post_meta()` / `get_term_meta()`
	 */
	function get_options( $term_or_product, $term_or_product_id ) {
		$discount_type_options = array(
			'percent'        => __( 'Percent', 'wholesale-pricing-woocommerce' ),
			'fixed'          => __( 'Fixed', 'wholesale-pricing-woocommerce' ),
			'price_directly' => __( 'Price directly', 'wholesale-pricing-woocommerce' ),
		);
		$levels_number_tooltip = ( 'product' === $term_or_product ?
			__( 'UPDATE product after you change this option - new settings fields will be displayed.', 'wholesale-pricing-woocommerce' ) :
			'<input type="submit" class="button button-primary" value="' . __( 'Update', 'wholesale-pricing-woocommerce' ) . '">' );
		$options = array();
		$options = array_merge( $options, array(
			array(
				'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_per_' . $term_or_product . '_enabled' . '_' . $term_or_product_id : '' ),
				'meta_name'         => '_' . 'alg_wc_wholesale_pricing_per_' . $term_or_product . '_enabled',
				'default'           => 'no',
				'type'              => 'select',
				'options'           => array(
					'yes' => __( 'Yes', 'wholesale-pricing-woocommerce' ),
					'no'  => __( 'No', 'wholesale-pricing-woocommerce' ),
				),
				'title'             => __( 'Enable', 'wholesale-pricing-woocommerce' ),
				'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
			),
			array(
				'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_discount_type' . '_' . $term_or_product_id : '' ),
				'meta_name'         => '_' . 'alg_wc_wholesale_pricing_discount_type',
				'default'           => 'percent',
				'type'              => 'select',
				'options'           => $discount_type_options,
				'title'             => __( 'Discount type', 'wholesale-pricing-woocommerce' ),
				'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
			),
			array(
				'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_levels_number' . '_' . $term_or_product_id : '' ),
				'meta_name'         => '_' . 'alg_wc_wholesale_pricing_levels_number',
				'default'           => 0,
				'type'              => 'number',
				'title'             => __( 'Number of levels', 'wholesale-pricing-woocommerce' ),
				'tooltip'           => $levels_number_tooltip,
				'custom_attributes' => 'min="0" max="100"',
				'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
			),
		) );
		// Levels
		$discount_type          = ( 'product' === $term_or_product ?
			get_post_meta( $term_or_product_id, '_' . 'alg_wc_wholesale_pricing_discount_type', true ) :
			get_term_meta( $term_or_product_id, '_' . 'alg_wc_wholesale_pricing_discount_type', true ) );
		$level_title_desc       = ( 'price_directly' === $discount_type ? __( 'Price', 'wholesale-pricing-woocommerce' ) : __( 'Discount', 'wholesale-pricing-woocommerce' ) );
		$level_min_qty_tooltip  = ( 'product' === $term_or_product ?
			__( 'Minimum quantity to apply discount.', 'wholesale-pricing-woocommerce' ) : '' );
		$level_discount_tooltip = ( 'product' === $term_or_product ?
			( 'price_directly' === $discount_type ? '' : __( 'To set fee instead of discount - enter negative number.', 'wholesale-pricing-woocommerce' ) ) : '' );
		$levels_num = ( 'product' === $term_or_product ?
			get_post_meta( $term_or_product_id, '_' . 'alg_wc_wholesale_pricing_levels_number', true ) :
			get_term_meta( $term_or_product_id, '_' . 'alg_wc_wholesale_pricing_levels_number', true ) );
		for ( $i = 1; $i <= $levels_num; $i++ ) {
			if ( 'product' === $term_or_product ) {
				$options = array_merge( $options, array(
					array(
						'type'              => 'title',
						'title'             => __( 'Level', 'wholesale-pricing-woocommerce' ) . ' #' . $i,
					),
				) );
			}
			$options = array_merge( $options, array(
				array(
					'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_level_min_qty_' . $i . '_' . $term_or_product_id : '' ),
					'meta_name'         => '_' . 'alg_wc_wholesale_pricing_level_min_qty_' . $i,
					'default'           => 0,
					'type'              => 'number',
					'custom_attributes' => 'min="0"',
					'title'             => ( 'product' === $term_or_product ?
						__( 'Min quantity', 'wholesale-pricing-woocommerce' ) :
						__( 'Level', 'wholesale-pricing-woocommerce' ) . ' #' . $i ),
					'tooltip'           => ( 'product' === $term_or_product ? $level_min_qty_tooltip : __( 'Min quantity', 'wholesale-pricing-woocommerce' ) ),
					'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
				),
				array(
					'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_level_discount_' . $i . '_' . $term_or_product_id : '' ),
					'meta_name'         => '_' . 'alg_wc_wholesale_pricing_level_discount_' . $i,
					'default'           => 0,
					'type'              => 'price',
					'title'             => ( 'product' === $term_or_product ? $level_title_desc : '' ),
					'tooltip'           => ( 'product' === $term_or_product ? $level_discount_tooltip : $level_title_desc ),
					'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
				),
			) );
		}
		// User roles
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_by_user_role_enabled', 'yes' ) ) {
			$user_roles = get_option( 'alg_wc_wholesale_pricing_by_user_role_roles', array() );
			if ( ! empty( $user_roles ) ) {
				$all_user_roles = alg_wc_wholesale_pricing()->core->get_user_roles_options();
				foreach ( $user_roles as $user_role_key ) {
					$user_role_title = ( isset( $all_user_roles[ $user_role_key ] ) ? $all_user_roles[ $user_role_key ] : $user_role_key );
					$options = array_merge( $options, array(
						array(
							'type'              => 'title',
							'background-color'  => '#dfdfdf',
							'title'             => ( 'product' === $term_or_product ?
								$user_role_title : __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ) . ': ' . $user_role_title ),
						),
					) );
					$options = array_merge( $options, array(
						array(
							'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_levels_number_' . $user_role_key . '_' . $term_or_product_id : '' ),
							'meta_name'         => '_' . 'alg_wc_wholesale_pricing_levels_number_' . $user_role_key,
							'default'           => 0,
							'type'              => 'number',
							'title'             => __( 'Number of levels', 'wholesale-pricing-woocommerce' ),
							'tooltip'           => $levels_number_tooltip,
							'custom_attributes' => 'min="0" max="100"',
							'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
						),
					) );
					$levels_num_user_role = ( 'product' === $term_or_product ?
						get_post_meta( $term_or_product_id, '_' . 'alg_wc_wholesale_pricing_levels_number_' . $user_role_key, true ) :
						get_term_meta( $term_or_product_id, '_' . 'alg_wc_wholesale_pricing_levels_number_' . $user_role_key, true ) );
					for ( $i = 1; $i <= $levels_num_user_role; $i++ ) {
						if ( 'product' === $term_or_product ) {
							$options = array_merge( $options, array(
								array(
									'type'              => 'title',
									'title'             => __( 'Level', 'wholesale-pricing-woocommerce' ) . ' #' . $i,
								),
							) );
						}
						$options = array_merge( $options, array(
							array(
								'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_level_min_qty_' . $user_role_key . '_' . $i . '_' . $term_or_product_id : '' ),
								'meta_name'         => '_' . 'alg_wc_wholesale_pricing_level_min_qty_' . $user_role_key . '_' . $i,
								'default'           => 0,
								'type'              => 'number',
								'title'             => ( 'product' === $term_or_product ?
									__( 'Min quantity', 'wholesale-pricing-woocommerce' ) :
									$user_role_title . ': ' . __( 'Level', 'wholesale-pricing-woocommerce' ) . ' #' . $i ),
								'tooltip'           => ( 'product' === $term_or_product ? $level_min_qty_tooltip : __( 'Min quantity', 'wholesale-pricing-woocommerce' ) ),
								'custom_attributes' => 'min="0"',
								'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
							),
							array(
								'name'              => ( 'product' === $term_or_product ? 'alg_wc_wholesale_pricing_level_discount_' . $user_role_key . '_' . $i . '_' . $term_or_product_id : '' ),
								'meta_name'         => '_' . 'alg_wc_wholesale_pricing_level_discount_' . $user_role_key . '_' . $i,
								'default'           => 0,
								'type'              => 'price',
								'title'             => ( 'product' === $term_or_product ? $level_title_desc : '' ),
								'tooltip'           => ( 'product' === $term_or_product ? $level_discount_tooltip : $level_title_desc ),
								'product_id'        => ( 'product' === $term_or_product ? $term_or_product_id : '' ),
							),
						) );
					}
				}
			}
		}
		return $options;
	}

}

endif;
