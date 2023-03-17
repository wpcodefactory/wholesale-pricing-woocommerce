<?php
/**
 * Product Price by Quantity for WooCommerce - Frontend Class
 *
 * @version 3.3.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Frontend' ) ) :

class Alg_WC_Wholesale_Pricing_Frontend {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {

		// Cart items: Item price
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_show_info_on_cart', 'no' ) ) {
			add_filter( 'woocommerce_cart_item_price', array( $this, 'add_discount_info_to_cart_page_item_price' ), PHP_INT_MAX, 3 );
		}

		// Price display by quantity
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_enabled', 'no' ) ) {
			// AJAX
			add_action( 'wp_ajax_'        . 'alg_wc_wholesale_pricing_price_by_qty_display', array( $this, 'ajax_price_display_by_qty' ) );
			add_action( 'wp_ajax_nopriv_' . 'alg_wc_wholesale_pricing_price_by_qty_display', array( $this, 'ajax_price_display_by_qty' ) );
			// Scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_price_display_by_qty' ) );
		}

	}

	/**
	 * get_core.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_core() {
		if ( ! isset( $this->core ) ) {
			$this->core = alg_wc_wholesale_pricing()->core;
		}
		return $this->core;
	}

	/**
	 * ajax_price_display_by_qty.
	 *
	 * @version 3.3.0
	 * @since   1.3.0
	 *
	 * @todo    [next] grouped products
	 * @todo    [next] variable products (range)
	 * @todo    [next] [maybe] do not display for qty=1
	 * @todo    [next] [maybe] add option to disable "price by qty" on initial screen (i.e. before qty input was changed)
	 * @todo    [maybe] other pages (e.g. cart)
	 */
	function ajax_price_display_by_qty() {
		if ( isset( $_POST['alg_wc_wholesale_pricing_qty'] ) && '' !== $_POST['alg_wc_wholesale_pricing_qty'] && ! empty( $_POST['alg_wc_wholesale_pricing_id'] ) ) {
			$quantity    = floatval( $_POST['alg_wc_wholesale_pricing_qty'] );
			$_product_id = intval( $_POST['alg_wc_wholesale_pricing_id'] );
			$product     = wc_get_product( $_product_id );
			$product_id  = $this->get_core()->get_product_id( $product );
			if ( $product_id ) {
				// Get placeholders
				$old_price_single = wc_get_price_to_display( $product );
				$discount         = ( $this->get_core()->is_enabled( $product_id ) ? $this->get_core()->get_discount_by_quantity( $quantity, $product_id ) : 0 );
				$discount_type    = $this->get_core()->get_discount_type( $product_id, $quantity );
				if ( false !== $discount ) {
					switch ( $discount_type ) {
						case 'price_directly':
							$new_price_single = wc_get_price_to_display( $product, array( 'price' => $discount ) );
							break;
						case 'percent':
							$new_price_single = wc_get_price_to_display( $product ) * ( 1 - $discount / 100 );
							break;
						default: // 'fixed'
							$new_price_single = wc_get_price_to_display( $product, array( 'price' => ( $product->get_price() - $discount ) ) );
							break;
					}
				} else {
					$new_price_single = $old_price_single;
				}
				$placeholders = $this->get_placeholders(
					$old_price_single,
					$new_price_single,
					$discount,
					$discount_type,
					$quantity
				);
				// Handle deprecated placeholders
				$placeholders['%price_single%'] = $placeholders['%old_price_single%'];
				$placeholders['%price%']        = $placeholders['%old_price_total%'];
				$placeholders['%new_price%']    = $placeholders['%new_price_total%'];
				// Final message
				$template = ( $old_price_single != $new_price_single ?
					get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_template',
						sprintf( __( '%s for %s pcs.', 'wholesale-pricing-woocommerce' ), '<del>%old_price_total%</del> %new_price_total%', '%qty%' ) . ' ' .
							sprintf( __( 'You save: %s', 'wholesale-pricing-woocommerce' ), '<span style="color:red">%discount_percent%%</span>' ) ) :
					get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_template_zero',
						sprintf( __( '%s for %s pcs.', 'wholesale-pricing-woocommerce' ), '%old_price_total%', '%qty%' ) ) );
				$template = do_shortcode( $template );
				echo apply_filters( 'alg_wc_wholesale_pricing_ajax_price_display_by_qty', str_replace( array_keys( $placeholders ), $placeholders, $template ),
					array( 'placeholders' => $placeholders, 'template' => $template, 'product_id' => $product_id, 'quantity' => $quantity, 'discount' => $discount ) );
			}
		}
		die();
	}

	/**
	 * enqueue_scripts_price_display_by_qty.
	 *
	 * @version 3.3.0
	 * @since   1.3.0
	 */
	function enqueue_scripts_price_display_by_qty() {
		if (
			is_product() && ( $product_id = get_the_ID() ) && ( $product = wc_get_product( $product_id ) ) &&
			( 'yes' === get_option( 'alg_wc_wholesale_pricing_price_by_qty_all_products', 'yes' ) || $this->get_core()->is_enabled( $product_id ) )
		) {
			$min_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' );
			wp_enqueue_script(  'alg-wc-wholesale-pricing-price-by-qty-display',
				trailingslashit( alg_wc_wholesale_pricing()->plugin_url() ) . 'includes/js/alg-wc-wholesale-pricing-price-by-qty-display' . $min_suffix . '.js',
				array( 'jquery' ),
				alg_wc_wholesale_pricing()->version,
				true
			);
			wp_localize_script( 'alg-wc-wholesale-pricing-price-by-qty-display',
				'alg_wc_wh_pr_pbqd_obj', apply_filters( 'alg_wc_ppq_price_display_by_qty_localize_script_args', array(
					'ajax_url'                     => admin_url( 'admin-ajax.php' ),
					'product_id'                   => $product_id,
					'product_type'                 => $product->get_type(),
					'is_variable_different_prices' => ( ( 'yes' === get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_in_variation', 'yes' ) ) &&
						$product->is_type( 'variable' ) && $product->get_variation_price( 'min' ) != $product->get_variation_price( 'max' ) ),
					'product_price_default'        => $product->get_price_html(),
					'position'                     => get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_position', 'instead' ),
					'interval_ms'                  => get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_interval_ms', 500 ),
					'do_force_standard_qty_input'  => ( 'yes' === get_option( 'alg_wc_wholesale_pricing_price_by_qty_standard_qty_input', 'no' ) ),
					'price_identifier'             => get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_id', 'p.price' ),
					'price_identifier_variation'   => get_option( 'alg_wc_wholesale_pricing_price_by_qty_display_id_variation', 'div.woocommerce-variation-price span.price' ),
					'is_sticky_add_to_cart'        => ( 'yes' === get_option( 'alg_wc_wholesale_pricing_price_by_qty_sitcky_add_to_cart', 'no' ) ),
				), $product )
			);
		}
	}

	/**
	 * get_placeholders.
	 *
	 * @version 2.6.1
	 * @since   2.0.0
	 *
	 * @todo    [next] (dev) params as array
	 * @todo    [next] [maybe] handle deprecated placeholders here
	 * @todo    [maybe] list `%quantity%` and `%quantity_total%` in settings?
	 */
	function get_placeholders( $old_price_single, $new_price_single, $discount, $discount_type, $quantity, $total_quantity = false ) {
		$discount_single  = ( $old_price_single - $new_price_single );
		$discount_percent = ( 0 != $old_price_single ? round( ( $discount_single / $old_price_single * 100 ), 2 ) : 0 );
		$total_quantity   = ( $total_quantity ? $total_quantity : $quantity );
		return array(
			'%old_price_single%'    => wc_price( $old_price_single ),
			'%old_price_total%'     => wc_price( $old_price_single * $quantity ),
			'%new_price_single%'    => wc_price( $new_price_single ),
			'%new_price_total%'     => wc_price( $new_price_single * $quantity ),
			'%discount_percent%'    => $discount_percent,
			'%discount_single%'     => wc_price( $discount_single ),
			'%discount_total%'      => wc_price( $discount_single * $quantity ),
			'%qty%'                 => $quantity,
			'%quantity%'            => $quantity,
			'%qty_total%'           => $total_quantity,
			'%quantity_total%'      => $total_quantity,
			'%discount_value%'      => $this->get_discount_value_placeholder( $old_price_single, ( false !== $discount ? $discount : 0 ), $discount_type ),
		);
	}

	/**
	 * get_discount_value_placeholder.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [maybe] (dev) deprecate?
	 */
	function get_discount_value_placeholder( $old_price_single, $discount, $discount_type ) {
		switch ( $discount_type ) {
			case 'price_directly':
				return wc_price( $old_price_single - $discount );
			case 'percent':
				return $discount . '%';
			default: // 'fixed'
				return wc_price( $discount );
		}
	}

	/**
	 * add_discount_info_to_cart_page_item_price.
	 *
	 * @version 3.3.0
	 * @since   2.0.0
	 */
	function add_discount_info_to_cart_page_item_price( $price_html, $cart_item, $cart_item_key ) {
		$template = get_option( 'alg_wc_wholesale_pricing_show_info_on_cart_format', '<del>%old_price_single%</del> %new_price_single%<br>' .
			sprintf( __( 'You save: %s', 'wholesale-pricing-woocommerce' ), '<span style="color:red">%discount_percent%%</span>' ) );
		$template = do_shortcode( $template );
		return $this->add_discount_info_to_cart_page( $price_html, $cart_item, $cart_item_key, $template );
	}

	/**
	 * add_discount_info_to_cart_page.
	 *
	 * @version 2.6.1
	 * @since   1.0.0
	 */
	function add_discount_info_to_cart_page( $price_html, $cart_item, $cart_item_key, $template ) {
		if ( isset( $cart_item['alg_wc_wholesale_pricing'] ) ) {
			if ( ( $product_id = $this->get_core()->get_item_product_id( $cart_item ) ) ) {
				$quantity = $this->get_core()->get_total_quantity( WC()->cart, $cart_item );
				if (
					false !== ( $discount = $this->get_core()->get_discount_by_quantity( $quantity, $product_id ) ) &&
					isset( $cart_item['alg_wc_wholesale_pricing_old'], $cart_item['alg_wc_wholesale_pricing'] ) &&
					$cart_item['alg_wc_wholesale_pricing_old'] !== $cart_item['alg_wc_wholesale_pricing']
				) {
					// Get placeholders
					$placeholders = $this->get_placeholders(
						$cart_item['alg_wc_wholesale_pricing_old'],
						$cart_item['alg_wc_wholesale_pricing'],
						$discount,
						$this->get_core()->get_discount_type( $product_id, $cart_item['quantity'] ),
						$cart_item['quantity'],
						$quantity
					);
					// Handle deprecated placeholders
					$placeholders['%old_price%'] = $placeholders['%old_price_single%'];
					$placeholders['%price%']     = $placeholders['%new_price_single%'];
					// Final message
					return str_replace( array_keys( $placeholders ), $placeholders, $template );
				}
			}
		}
		return $price_html;
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Frontend();
