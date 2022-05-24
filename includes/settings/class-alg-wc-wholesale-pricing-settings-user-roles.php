<?php
/**
 * Product Price by Quantity for WooCommerce - User Roles Section Settings
 *
 * @version 2.2.4
 * @since   1.2.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_User_Roles' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_User_Roles extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'user_roles';
		$this->desc = __( 'User Roles', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.0.0
	 * @since   1.2.0
	 *
	 * @todo    [maybe] `alg_wc_wholesale_pricing_by_user_role_roles`: better description
	 */
	function get_settings() {
		$all_user_roles = alg_wc_wholesale_pricing()->core->get_user_roles_options();
		$user_roles_settings = array(
			array(
				'title'    => __( 'Additional User Roles', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'If you want to set different product price by quantity options for different user roles, fill in this section.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_by_user_role_options',
			),
			array(
				'title'    => __( 'User roles', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_wholesale_pricing_by_user_role_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'User roles settings', 'wholesale-pricing-woocommerce' ),
				'desc_tip' => $this->get_save_changes_msg() . ' ' . __( 'Ignored if empty.', 'wholesale-pricing-woocommerce' ),
				'type'     => 'multiselect',
				'id'       => 'alg_wc_wholesale_pricing_by_user_role_roles',
				'default'  => array(),
				'class'    => 'chosen_select',
				'options'  => $all_user_roles,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_by_user_role_options',
			),
		);
		$user_roles = get_option( 'alg_wc_wholesale_pricing_by_user_role_roles', array() );
		if ( ! empty( $user_roles ) ) {
			foreach ( $user_roles as $user_role_key ) {
				$user_roles_settings = array_merge( $user_roles_settings, array(
					array(
						'title'    => ( isset( $all_user_roles[ $user_role_key ] ) ? $all_user_roles[ $user_role_key ] : $user_role_key ),
						'type'     => 'title',
						'id'       => 'alg_wc_wholesale_pricing_by_user_role_' . $user_role_key,
					),
					array(
						'title'    => __( 'Number of levels', 'wholesale-pricing-woocommerce' ),
						'desc_tip' => $this->get_save_changes_msg(),
						'id'       => 'alg_wc_wholesale_pricing_levels_number_' . $user_role_key,
						'default'  => 1,
						'type'     => 'number',
						'custom_attributes' => $this->get_levels_num_custom_atts(),
					),
				) );
				for ( $i = 1; $i <= alg_wc_wholesale_pricing()->core->get_total_levels( 'all', 0, '_' . $user_role_key ); $i++ ) {
					$user_roles_settings = array_merge( $user_roles_settings, array(
						array(
							'title'    => __( 'Level', 'wholesale-pricing-woocommerce' ) . ' #' . $i,
							'desc'     => __( 'Min quantity', 'wholesale-pricing-woocommerce' ),
							'desc_tip' => __( 'Minimum quantity to apply discount.', 'wholesale-pricing-woocommerce' ),
							'id'       => 'alg_wc_wholesale_pricing_level_min_qty_' . $user_role_key . '_' . $i,
							'default'  => 0,
							'type'     => 'number',
							'custom_attributes' => array('step' => '1', 'min' => '0', ),
						),
						array(
							'desc'     => __( 'Discount', 'wholesale-pricing-woocommerce' ),
							'desc_tip' => __( 'To set fee instead of discount - enter negative number.', 'wholesale-pricing-woocommerce' ),
							'id'       => 'alg_wc_wholesale_pricing_level_discount_' . $user_role_key . '_' . $i,
							'default'  => 0,
							'type'     => 'number',
							'custom_attributes' => array('step' => '0.0001' ),
						),
					) );
				}
				$user_roles_settings = array_merge( $user_roles_settings, array(
					array(
						'type'     => 'sectionend',
						'id'       => 'alg_wc_wholesale_pricing_by_user_role_' . $user_role_key,
					),
				) );
			}
		}
		return $user_roles_settings;
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_User_Roles();
