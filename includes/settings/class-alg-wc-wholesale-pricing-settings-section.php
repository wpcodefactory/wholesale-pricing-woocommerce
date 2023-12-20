<?php
/**
 * Product Price by Quantity for WooCommerce - Section Settings
 *
 * @version 3.6.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Section' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * id.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	public $id;

	/**
	 * desc.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	public $desc;

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
	 * @version 3.4.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) `'max' => '100'` (same in "per-item")
	 */
	function get_levels_num_custom_atts() {
		return array( 'step' => '1', 'min' => '0', 'max' => '100' );
	}

	/**
	 * get_placeholders_desc.
	 *
	 * @version 3.6.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) add default value for the `$placeholders`, i.e., `array( '%qty%', '%qty_total%', '%old_price_single%', ... )`
	 */
	function get_placeholders_desc( $placeholders ) {
		asort( $placeholders );
		return $this->get_details_summary(
			esc_html( _n( 'Available placeholder', 'Available placeholders', count( $placeholders ), 'wholesale-pricing-woocommerce' ) ),
			'<ul><li><code>' . implode( '</code></li><li><code>', $placeholders ) . '</code></li></ul>'
		);
	}

	/**
	 * get_examples_desc.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	function get_examples_desc( $examples ) {
		return $this->get_details_summary(
			esc_html( _n( 'Example', 'Examples', count( $examples ), 'wholesale-pricing-woocommerce' ) ),
			'<ul><li><code>' . implode( '</code></li><li><code>', $examples ) . '</code></li></ul>'
		);
	}

	/**
	 * get_details_summary.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	function get_details_summary( $title, $text ) {
		return sprintf( '<details><summary style="%s">%s</summary>%s</details>', 'cursor: pointer; color: #2271b1;', $title, $text );
	}

}

endif;
