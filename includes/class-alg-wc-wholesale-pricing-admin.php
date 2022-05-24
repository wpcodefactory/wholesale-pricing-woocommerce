<?php
/**
 * Product Price by Quantity for WooCommerce - Admin Class
 *
 * @version 3.0.0
 * @since   2.6.2
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Admin' ) ) :

class Alg_WC_Wholesale_Pricing_Admin {

	/**
	 * Constructor.
	 *
	 * @version 2.6.2
	 * @since   2.6.2
	 *
	 * @todo    [next] (dev) move more admin stuff to this class
	 */
	function __construct() {
		// Order "Recalculate" meta box
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_admin_recalculate_order', 'yes' ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add_order_meta_box' ), 10, 2 );
			add_action( 'admin_init',     array( $this, 'recalculate_order_action' ) );
			add_action( 'admin_notices',  array( $this, 'order_recalculated_notice' ) );
		}
	}

	/*
	 * recalculate_order_action.
	 *
	 * @version 2.6.2
	 * @since   2.6.2
	 *
	 * @todo    [next] (dev) better notices (i.e. errors)
	 */
	function recalculate_order_action() {
		if ( ! empty( $_GET['alg_wc_wholesale_pricing_recalculate_order_id'] ) ) {
			if ( ! isset( $_REQUEST['_wpnonce_alg_wc_wholesale_pricing'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_alg_wc_wholesale_pricing'], 'recalculate' ) ) {
				wp_die( __( 'Nonce verification failed. Please try again.', 'wholesale-pricing-woocommerce' ) );
			}
			if ( current_user_can( 'manage_woocommerce' ) && ( $order_id = intval( $_GET['alg_wc_wholesale_pricing_recalculate_order_id'] ) ) && ( $order = wc_get_order( $order_id ) ) ) {
				$this->recalculate_order( $order );
			}
			wp_safe_redirect( remove_query_arg( array( 'alg_wc_wholesale_pricing_recalculate_order_id', '_wpnonce_alg_wc_wholesale_pricing' ), add_query_arg( 'alg_wc_wholesale_pricing_order_recalculated', true ) ) );
			exit;
		}
	}

	/*
	 * order_recalculated_notice.
	 *
	 * @version 2.6.2
	 * @since   2.6.2
	 */
	function order_recalculated_notice() {
		if ( isset( $_REQUEST['alg_wc_wholesale_pricing_order_recalculated'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Order recalculated.', 'wholesale-pricing-woocommerce' ) . '</p></div>';
		}
	}

	/*
	 * add_order_meta_box.
	 *
	 * @version 3.0.0
	 * @since   2.6.2
	 */
	function add_order_meta_box( $post_type, $post ) {
		add_meta_box(
			'alg-wc-wholesale_pricing-meta-box',
			__( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
			array( $this, 'create_order_meta_box' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/*
	 * create_order_meta_box.
	 *
	 * @version 2.6.2
	 * @since   2.6.2
	 *
	 * @todo    [maybe] (desc) better desc?
	 */
	function create_order_meta_box( $post ) {
		$do_confirmation = ( 'yes' === get_option( 'alg_wc_wholesale_pricing_admin_recalculate_order_confirm', 'yes' ) );
		$html  = '';
		$html .= '<p>' .
				'<a' .
					' href="' . add_query_arg( array( 'alg_wc_wholesale_pricing_recalculate_order_id' => $post->ID, '_wpnonce_alg_wc_wholesale_pricing' => wp_create_nonce( 'recalculate' ) ) ) . '"' .
					' class="button"' .
					( $do_confirmation ? ' onclick="return confirm(\'' . __( 'There is no undo for this action. Are you sure?', 'wholesale-pricing-woocommerce' ) . '\');"' : '' ) .
				'>' . __( 'Recalculate', 'wholesale-pricing-woocommerce' ) . '</a>' .
			'</p>';
		echo $html;
	}

	/**
	 * recalculate_order.
	 *
	 * @version 3.0.0
	 * @since   2.6.2
	 *
	 * @todo    [next] (feature) calculate the wholesale price on item insert at once (i.e. instead of going through all items with a button)
	 * @todo    [later] (dev) better order note?
	 * @todo    [maybe] (feature) bulk recalculate?
	 */
	function recalculate_order( $order ) {
		// Switch user role
		if ( isset( alg_wc_wholesale_pricing()->core->current_user_role ) ) {
			$_current_user_role = alg_wc_wholesale_pricing()->core->current_user_role;
		}
		unset( alg_wc_wholesale_pricing()->core->current_user_role );
		$user = new WP_User( $order->get_user_id() );
		alg_wc_wholesale_pricing()->core->get_current_user_role( $user );
		// Recalculate items
		foreach ( $order->get_items() as $item ) {
			$product_id = ( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );
			if ( ( $product = wc_get_product( $product_id ) ) ) {
				$is_changed   = false;
				$single_price = alg_wc_wholesale_pricing()->core->get_wholesale_price( $product->get_price(), $item['quantity'], $product_id );
				$total_price  = $single_price * $item['quantity'];
				if ( is_callable( array( $item, 'set_subtotal' ) ) ) {
					$item->set_subtotal( $total_price );
					$is_changed = true;
				}
				if ( is_callable( array( $item, 'set_total' ) ) ) {
					$item->set_total( $total_price );
					$is_changed = true;
				}
				if ( $is_changed ) {
					$item->calculate_taxes();
					$item->save();
				}
			}
		}
		// Switch back user role
		unset( alg_wc_wholesale_pricing()->core->current_user_role );
		if ( isset( $_current_user_role ) ) {
			alg_wc_wholesale_pricing()->core->current_user_role = $_current_user_role;
		}
		// Calculate totals and save order
		$order->calculate_totals();
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_admin_recalculate_order_note', 'yes' ) ) {
			$order->add_order_note( __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ) . ': ' .
				__( 'Order recalculated.', 'wholesale-pricing-woocommerce' ) );
		}
		$order->save();
		// Return recalculated order
		return $order;
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Admin();
