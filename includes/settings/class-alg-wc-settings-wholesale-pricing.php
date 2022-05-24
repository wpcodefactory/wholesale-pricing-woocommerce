<?php
/**
 * Product Price by Quantity for WooCommerce - Settings
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Settings_Wholesale_Pricing' ) ) :

class Alg_WC_Settings_Wholesale_Pricing extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_wholesale_pricing';
		$this->label = __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' );
		parent::__construct();
		// Sections
		require_once( 'class-alg-wc-wholesale-pricing-settings-section.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-general.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-all-products.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-user-roles.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-info.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-price-display-by-qty.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-dropdown.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-tools.php' );
		require_once( 'class-alg-wc-wholesale-pricing-settings-reports.php' );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.6.3
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), ( 'reports' === $current_section ? array() : array(
			array(
				'title'     => __( 'Reset Settings', 'wholesale-pricing-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'wholesale-pricing-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'wholesale-pricing-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'wholesale-pricing-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) ) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.1.2
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.1.2
	 * @since   1.1.2
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'wholesale-pricing-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 2.6.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		do_action( 'alg_wc_wholesale_pricing_settings_saved' );
	}

}

endif;

return new Alg_WC_Settings_Wholesale_Pricing();
