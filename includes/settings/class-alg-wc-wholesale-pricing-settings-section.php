<?php
/**
 * Product Price by Quantity for WooCommerce - Section Settings
 *
 * @version 3.3.2
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Section' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_wholesale_pricing',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_wholesale_pricing_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_save_changes_msg.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_save_changes_msg() {
		return __( '"Save changes" after you change this option - new settings fields will be displayed.', 'wholesale-pricing-woocommerce' );
	}

	/**
	 * get_levels_num_custom_atts.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) `'max' => '100'` (same in "per-item")
	 */
	function get_levels_num_custom_atts() {
		return array( 'step' => '1', 'min' => '1', 'max' => '100' );
	}

	/**
	 * get_placeholders_desc.
	 *
	 * @version 3.3.2
	 * @since   2.0.0
	 *
	 * @todo    (dev) add default value for the `$placeholders`, i.e., `array( '%qty%', '%qty_total%', '%old_price_single%', ... )`
	 */
	function get_placeholders_desc( $placeholders ) {

		asort( $placeholders );
		$placeholders_html = '<ul><li><code>' . implode( '</code></li><li><code>', $placeholders ) . '</code></li></ul>';

		$summary_style = 'cursor: pointer; color: #2271b1;';
		$summary_text  = esc_html__( 'Available placeholders', 'wholesale-pricing-woocommerce' );

		return sprintf( '<details><summary style="%s">%s</summary>%s</details>', $summary_style, $summary_text, $placeholders_html );

	}

}

endif;
