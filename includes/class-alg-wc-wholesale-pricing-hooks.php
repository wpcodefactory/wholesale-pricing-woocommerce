<?php
/**
 * Product Price by Quantity for WooCommerce - Hooks Class
 *
 * @version 3.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Hooks' ) ) :

class Alg_WC_Wholesale_Pricing_Hooks {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		add_action( 'woocommerce_cart_loaded_from_session',    array( $this, 'cart_loaded_from_session' ), PHP_INT_MAX, 1 );
		add_action( 'woocommerce_before_calculate_totals',     array( $this, 'calculate_totals' ),         PHP_INT_MAX, 1 );
		add_filter( 'woocommerce_product_get_price',           array( $this, 'wholesale_price' ),          PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_product_variation_get_price', array( $this, 'wholesale_price' ),          PHP_INT_MAX, 2 );
		add_action( 'woocommerce_checkout_order_processed',    array( $this, 'set_order_item_subtotals' ), PHP_INT_MAX, 3 );
	}

	/**
	 * set_order_item_subtotals.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 *
	 * @todo    [next] [!!] (dev) use `$product->get_price()` instead of `wc_get_price_excluding_tax()`?
	 * @todo    [next] [!!] (dev) make this enabled by default?
	 */
	function set_order_item_subtotals( $order_id, $posted_data, $order ) {
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_add_order_discount', 'no' ) ) {
			// Set item subtotals
			foreach ( $order->get_items() as $item ) {
				if (
					( is_callable( array( $item, 'set_subtotal' ) ) && is_callable( array( $item, 'get_total' ) ) ) &&
					( $product = wc_get_product( ( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] ) ) ) &&
					( ( $original_total = wc_get_price_excluding_tax( $product, array( 'qty' => $item['quantity'] ) ) ) != $item->get_total() )
				) {
					$item->set_subtotal( $original_total );
					$item->save();
				}
			}
			// Calculate totals and save order
			$order->calculate_totals();
			$order->save();
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
	 * cart_loaded_from_session.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function cart_loaded_from_session( $cart ) {
		foreach ( $cart->cart_contents as $item_key => $item ) {
			if ( array_key_exists( 'alg_wc_wholesale_pricing', $item ) ) {
				$cart->cart_contents[ $item_key ]['data']->alg_wc_wholesale_pricing = $item['alg_wc_wholesale_pricing'];
			}
		}
	}

	/**
	 * calculate_totals.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function calculate_totals( $cart ) {
		foreach ( $cart->cart_contents as $item_key => $item ) {

			// Maybe reset previous values
			if ( isset( $cart->cart_contents[ $item_key ]['data']->alg_wc_wholesale_pricing ) ) {
				unset( $cart->cart_contents[ $item_key ]['data']->alg_wc_wholesale_pricing );
			}
			if ( isset( $cart->cart_contents[ $item_key ]['alg_wc_wholesale_pricing'] ) ) {
				unset( $cart->cart_contents[ $item_key ]['alg_wc_wholesale_pricing'] );
			}
			if ( isset( $cart->cart_contents[ $item_key ]['alg_wc_wholesale_pricing_old'] ) ) {
				unset( $cart->cart_contents[ $item_key ]['alg_wc_wholesale_pricing_old'] );
			}

			// If other discount was applied in cart...
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_apply_only_if_no_other_discounts', 'no' ) ) {
				if ( $cart->get_total_discount() > 0 || sizeof( $cart->applied_coupons ) > 0 ) {
					break;
				}
			}

			$product_id = $this->get_core()->get_item_product_id( $item );
			if ( $product_id && $this->get_core()->is_enabled( $product_id ) ) {

				// Prices
				$_product_id = ( 0 != ( $variation_id = $cart->cart_contents[ $item_key ]['variation_id'] ) ? $variation_id : $product_id );
				$_product_id = apply_filters( 'alg_wc_wholesale_pricing_calculate_totals_product_id', $_product_id, $cart->cart_contents[ $item_key ] );
				$product     = wc_get_product( $_product_id );
				$price       = $product->get_price();
				$price_old   = wc_get_price_to_display( $product ); // used for display only

				// Maybe set wholesale price
				if ( ( $quantity = $this->get_core()->get_total_quantity( $cart, $item ) ) > 0 ) {
					$wholesale_price = $this->get_core()->get_wholesale_price( $price, $quantity, $product_id );
					if ( $wholesale_price != $price ) {

						// Setting wholesale price
						$alg_wc_wholesale_pricing = ( 'yes' === get_option( 'alg_wc_wholesale_pricing_round', 'yes' ) ?
							round( $wholesale_price, get_option( 'woocommerce_price_num_decimals', 2 ) ) : $wholesale_price );
						$alg_wc_wholesale_pricing = apply_filters( 'alg_wc_product_wholesale_pricing', $alg_wc_wholesale_pricing,
							$cart->cart_contents[ $item_key ], $price, $quantity, $product_id );
						$cart->cart_contents[ $item_key ]['data']->alg_wc_wholesale_pricing = $alg_wc_wholesale_pricing;
						$cart->cart_contents[ $item_key ]['alg_wc_wholesale_pricing']       = $alg_wc_wholesale_pricing;
						$cart->cart_contents[ $item_key ]['alg_wc_wholesale_pricing_old']   = $price_old;

					}
				}
			}
		}
	}

	/**
	 * wholesale_price.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function wholesale_price( $price, $_product ) {
		return ( ( $product_id = $this->get_core()->get_product_id( $_product ) ) && $this->get_core()->is_enabled( $product_id ) && isset( $_product->alg_wc_wholesale_pricing ) ?
			$_product->alg_wc_wholesale_pricing : $price );
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Hooks();
