<?php
/**
 * Product Price by Quantity for WooCommerce - Admin Class
 *
 * @version 4.0.3
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
	 * @todo    (dev) move more admin stuff to this class
	 */
	function __construct() {
		// Order "Recalculate" meta box
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_admin_recalculate_order', 'yes' ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add_order_meta_box' ), 10, 2 );
			add_action( 'admin_init', array( $this, 'recalculate_order_action' ) );
			add_action( 'admin_notices', array( $this, 'order_recalculated_notice' ) );
		}
	}

	/**
	 * recalculate_order_action.
	 *
	 * @version 4.0.0
	 * @since   2.6.2
	 *
	 * @todo    (dev) better notices (i.e., errors)
	 */
	function recalculate_order_action() {
		if ( ! empty( $_GET['alg_wc_wholesale_pricing_recalculate_order_id'] ) ) {
			if (
				! isset( $_REQUEST['_wpnonce_alg_wc_wholesale_pricing'] ) ||
				! wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce_alg_wc_wholesale_pricing'] ) ),
					'recalculate'
				)
			) {
				wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'wholesale-pricing-woocommerce' ) );
			}
			if (
				current_user_can( 'manage_woocommerce' ) &&
				( $order_id = intval( $_GET['alg_wc_wholesale_pricing_recalculate_order_id'] ) ) &&
				( $order = wc_get_order( $order_id ) )
			) {
				$this->recalculate_order( $order );
			}
			wp_safe_redirect( remove_query_arg(
				array( 'alg_wc_wholesale_pricing_recalculate_order_id', '_wpnonce_alg_wc_wholesale_pricing' ),
				add_query_arg( 'alg_wc_wholesale_pricing_order_recalculated', true )
			) );
			exit;
		}
	}

	/**
	 * order_recalculated_notice.
	 *
	 * @version 4.0.0
	 * @since   2.6.2
	 */
	function order_recalculated_notice() {
		if ( isset( $_REQUEST['alg_wc_wholesale_pricing_order_recalculated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice notice-success is-dismissible"><p>' .
				esc_html__( 'Order recalculated.', 'wholesale-pricing-woocommerce' ) .
			'</p></div>';
		}
	}

	/**
	 * add_order_meta_box.
	 *
	 * @version 4.0.3
	 * @since   2.6.2
	 */
	function add_order_meta_box( $post_type, $post_or_order ) {
		add_meta_box(
			'alg-wc-wholesale_pricing-meta-box',
			__( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
			array( $this, 'create_order_meta_box' ),
			wc_get_page_screen_id( 'shop-order' ),
			'side',
			'default'
		);
	}

	/**
	 * create_order_meta_box.
	 *
	 * @version 4.0.3
	 * @since   2.6.2
	 *
	 * @todo    (desc) better desc?
	 */
	function create_order_meta_box( $post_or_order ) {

		if ( ! ( $order = wc_get_order( $post_or_order ) ) ) {
			return;
		}

		$button_text = esc_html__( 'Recalculate', 'wholesale-pricing-woocommerce' );

		if (
			function_exists( 'get_current_screen' ) &&
			( $current_screen = get_current_screen() ) &&
			! empty( $current_screen->id ) &&
			(
				'shop_order' === $current_screen->id &&
				! empty( $current_screen->action ) &&
				'add' === $current_screen->action
			) ||
			(
				'woocommerce_page_wc-orders' === $current_screen->id &&
				! empty( $_GET['action'] ) &&
				'new' === sanitize_text_field( wp_unslash( $_GET['action'] ) )
			)
		) {

			// New order (button disabled)
			$notice = esc_attr__( 'Create the order first.', 'wholesale-pricing-woocommerce' );
			$button = (
				'<button' .
					' type="button"' .
					' class="button"' .
					' title="' . $notice . '"' .
					' disabled' .
				'>' .
					$button_text .
				'</button>'
			);

		} else {

			// Edit order
			$confirmation = (
				'yes' === get_option( 'alg_wc_wholesale_pricing_admin_recalculate_order_confirm', 'yes' ) ?
				' onclick="return confirm(\'' .
					__( 'There is no undo for this action. Are you sure?', 'wholesale-pricing-woocommerce' ) .
				'\');"' :
				''
			);
			$url = add_query_arg( array(
				'alg_wc_wholesale_pricing_recalculate_order_id' => $order->get_id(),
				'_wpnonce_alg_wc_wholesale_pricing'             => wp_create_nonce( 'recalculate' ),
			) );
			$button = (
				'<a' .
					' href="' . esc_url( $url ) . '"' .
					' class="button"' .
					$confirmation .
				'>' .
					$button_text .
				'</a>'
			);

		}

		// Output
		echo "<p>{$button}</p>";

	}

	/**
	 * recalculate_order.
	 *
	 * @version 3.7.1
	 * @since   2.6.2
	 *
	 * @todo    (feature) calculate the wholesale price on item insert at once (i.e., instead of going through all items with a button)
	 * @todo    (dev) better order note?
	 * @todo    (feature) bulk recalculate?
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
			$product_id = (
				! empty( $item['variation_id'] ) ?
				$item['variation_id'] :
				$item['product_id']
			);
			if ( ( $product = wc_get_product( $product_id ) ) ) {

				$is_changed   = false;
				$single_price = alg_wc_wholesale_pricing()->core->get_wholesale_price(
					$product->get_price(),
					$item['quantity'],
					$product_id
				);
				$total_price  = wc_get_price_excluding_tax(
					$product,
					array(
						'price' => $single_price,
						'qty'   => $item['quantity'],
					)
				);

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
			$order->add_order_note(
				__( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ) . ': ' .
				__( 'Order recalculated.', 'wholesale-pricing-woocommerce' )
			);
		}
		$order->save();

		// Return recalculated order
		return $order;

	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Admin();
