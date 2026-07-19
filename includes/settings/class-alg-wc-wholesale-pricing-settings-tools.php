<?php
/**
 * Product Price by Quantity for WooCommerce - Tools Section Settings
 *
 * @version 4.1.0
 * @since   2.6.0
 *
 * @author  WPFactory
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
	 * @version 4.1.0
	 * @since   2.6.0
	 *
	 * @todo    (dev) Delete roles: sort roles alphabetically?
	 * @todo    (dev) remove "Reset Settings"?
	 * @todo    (dev) rename button to "Run tools"?
	 * @todo    (dev) `alg_wc_wholesale_pricing_tool_add_role`: `desc_tip`: capabilities: link
	 */
	function get_settings() {

		$settings = array();

		// Settings Tools
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Settings Tools', 'wholesale-pricing-woocommerce' ),
				'desc'     => (
					sprintf(
						/* Translators: %s: Icon. */
						__( 'Check the %s box and save changes to run the tool.', 'wholesale-pricing-woocommerce' ),
						'<span class="dashicons dashicons-admin-generic"></span>'
					) . ' ' .
					'<strong>' .
						__( 'Please note that there is no undo for these tools.', 'wholesale-pricing-woocommerce' ) .
					'</strong>'
				),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_settings_tools_options',
			),
			array(
				'title'    => __( 'Delete per product settings', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span>' . ' ' .
					'<strong>' . __( 'Delete', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf(
					/* Translators: %s: Setting name list. */
					__( 'Will delete all %s product price by quantity settings.', 'wholesale-pricing-woocommerce' ),
					'<strong>' . __( 'per product', 'wholesale-pricing-woocommerce' ) . '</strong>'
				),
				'id'       => 'alg_wc_wholesale_pricing_tool_delete_product_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Delete per term settings', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span>' . ' ' .
					'<strong>' . __( 'Delete', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip' => sprintf(
					/* Translators: %s: Setting name list. */
					__( 'Will delete all %s product price by quantity settings.', 'wholesale-pricing-woocommerce' ),
					sprintf(
						/* Translators: %1$s: "per product category", %2$s: "per product tag". */
						__( '%1$s and %2$s', 'wholesale-pricing-woocommerce' ),
						'<strong>' . __( 'per product category', 'wholesale-pricing-woocommerce' ) . '</strong>',
						'<strong>' . __( 'per product tag', 'wholesale-pricing-woocommerce' )      . '</strong>'
					)
				),
				'id'       => 'alg_wc_wholesale_pricing_tool_delete_term_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_settings_tools_options',
			),
		) );

		// User Role Tools
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'User Role Tools', 'wholesale-pricing-woocommerce' ),
				'desc'     => sprintf(
					/* Translators: %s: Icon. */
					__( 'Check the %s box and save changes to run the tool.', 'wholesale-pricing-woocommerce' ),
					'<span class="dashicons dashicons-admin-generic"></span>'
				),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_role_tools_options',
			),
			array(
				'title'    => __( 'Add role', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span>' . ' ' .
					'<strong>' . __( 'Add', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Custom role\'s capabilities will be the same as WooCommerce "Customer" user role.', 'wholesale-pricing-woocommerce' ),
				'id'       => 'alg_wc_wholesale_pricing_tool_add_role',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => sprintf(
					/* Translators: %s: Example. */
					__( 'Role ID, e.g.: %s', 'wholesale-pricing-woocommerce' ),
					'<code>vip_wholesaler</code>'
				),
				'id'       => 'alg_wc_wholesale_pricing_tool_add_role_id',
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'desc'     => sprintf(
					/* Translators: %s: Example. */
					__( 'Role display name, e.g.: %s', 'wholesale-pricing-woocommerce' ),
					'<code>VIP Wholesaler</code>'
				),
				'id'       => 'alg_wc_wholesale_pricing_tool_add_role_name',
				'default'  => '',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Delete roles', 'wholesale-pricing-woocommerce' ),
				'desc'     => '<span class="dashicons dashicons-admin-generic"></span>' . ' ' .
					'<strong>' . __( 'Delete', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_wholesale_pricing_tool_remove_role',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
		) );
		$roles = get_option( 'alg_wc_wholesale_pricing_tool_roles', array() );
		global $wp_roles;
		foreach ( array_values( $roles ) as $i => $role ) {
			$settings[] = array(
				'desc'          => ( ! empty( $wp_roles->roles[ $role ]['name'] ) ? $wp_roles->roles[ $role ]['name'] : $role ),
				'id'            => "alg_wc_wholesale_pricing_tool_remove_role_id[{$role}]",
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => ( count( $roles ) > 1 ? ( 0 == $i ? 'start' : ( ( count( $roles ) - 1 ) == $i ? 'end' : '' ) ) : null ),
			);
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_role_tools_options',
			),
		) );

		return $settings;
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Tools();
