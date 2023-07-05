<?php
/**
 * Product Price by Quantity for WooCommerce - Tools Class
 *
 * @version 3.5.0
 * @since   2.6.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Tools' ) ) :

class Alg_WC_Wholesale_Pricing_Tools {

	/**
	 * Constructor.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function __construct() {
		add_action( 'alg_wc_wholesale_pricing_settings_saved', array( $this, 'run_admin_tools' ) );
	}

	/**
	 * add_message.
	 *
	 * @version 3.5.0
	 * @since   3.5.0
	 */
	function add_message( $text ) {
		if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
			WC_Admin_Settings::add_message( $text );
		}
	}

	/**
	 * add_error.
	 *
	 * @version 3.5.0
	 * @since   3.5.0
	 */
	function add_error( $text ) {
		if ( method_exists( 'WC_Admin_Settings', 'add_error' ) ) {
			WC_Admin_Settings::add_error( $text );
		}
	}

	/**
	 * remove_roles.
	 *
	 * @version 3.5.0
	 * @since   3.5.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/remove_role/
	 *
	 * @todo    (dev) `removed_roles`: use role names (not IDs)
	 */
	function remove_roles() {
		$remove_roles  = get_option( 'alg_wc_wholesale_pricing_tool_remove_role_id', array() );
		$roles         = get_option( 'alg_wc_wholesale_pricing_tool_roles', array() );
		$removed_roles = array();
		foreach ( $remove_roles as $role => $do_remove ) {
			if ( 'yes' === $do_remove ) {
				remove_role( $role );
				if ( false !== ( $key = array_search( $role, $roles ) ) ) {
					unset( $roles[ $key ] );
				}
				$removed_roles[] = $role;
			}
		}
		update_option( 'alg_wc_wholesale_pricing_tool_remove_role_id', array() );
		update_option( 'alg_wc_wholesale_pricing_tool_roles', $roles );
		$this->add_message( sprintf( esc_html__( 'Roles removed: %s', 'wholesale-pricing-woocommerce' ), implode( ', ', $removed_roles ) ) );
	}

	/**
	 * add_role.
	 *
	 * @version 3.5.0
	 * @since   3.5.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/add_role/
	 *
	 * @todo    (dev) Role ID: check for duplicates?
	 * @todo    (dev) Role ID: allow empty (autogenerate from the "Role display name")?
	 * @todo    (dev) [!] Role ID: sanitize?
	 * @todo    (dev) Role ID and name: limitations, e.g., (leading) numbers, length, etc.?
	 */
	function add_role() {
		if (
			'' != ( $role         = get_option( 'alg_wc_wholesale_pricing_tool_add_role_id', '' ) ) &&
			'' != ( $display_name = get_option( 'alg_wc_wholesale_pricing_tool_add_role_name', '' ) )
		) {
			if ( wp_roles()->is_role( 'customer' ) ) {
				if ( add_role( $role, $display_name, get_role( 'customer' )->capabilities ) ) {

					update_option( 'alg_wc_wholesale_pricing_tool_add_role_id', '' );
					update_option( 'alg_wc_wholesale_pricing_tool_add_role_name', '' );

					$roles = get_option( 'alg_wc_wholesale_pricing_tool_roles', array() );
					$roles[] = $role;
					update_option( 'alg_wc_wholesale_pricing_tool_roles', $roles );

					$this->add_message( sprintf( esc_html__( '"%s" user role successfully added.', 'wholesale-pricing-woocommerce' ), $display_name ) );

				} else {
					$this->add_error( esc_html__( 'Error: Something went wrong. Check for duplicated role ID.', 'wholesale-pricing-woocommerce' ) );
				}
			} else {
				$this->add_error( esc_html__( 'Error: Customer user role does not exist.', 'wholesale-pricing-woocommerce' ) );
			}
		} else {
			$this->add_error( esc_html__( 'Please fill in the "Role ID" and "Role display name" options.', 'wholesale-pricing-woocommerce' ) );
		}
	}

	/**
	 * delete_meta.
	 *
	 * @version 3.0.0
	 * @since   2.6.0
	 */
	function delete_meta( $product_or_term ) {
		global $wpdb;
		$table = ( 'product' === $product_or_term ? $wpdb->postmeta : $wpdb->termmeta );
		$res   = $wpdb->query( "DELETE FROM {$table} WHERE meta_key LIKE '_alg_wc_wholesale_pricing%'" );
		if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
			$title = ( 'product' === $product_or_term ? __( 'Per product', 'wholesale-pricing-woocommerce' ) : __( 'Per product category and per product tag', 'wholesale-pricing-woocommerce' ) );
			WC_Admin_Settings::add_message( sprintf( __( '%s product price by quantity settings: %d records deleted.', 'wholesale-pricing-woocommerce' ), $title, $res ) );
		}
	}

	/**
	 * run_admin_tools.
	 *
	 * @version 3.5.0
	 * @since   2.6.0
	 */
	function run_admin_tools() {
		if ( current_user_can( 'manage_woocommerce' ) ) {

			// Product meta
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_tool_delete_product_meta', 'no' ) ) {
				update_option( 'alg_wc_wholesale_pricing_tool_delete_product_meta', 'no' );
				$this->delete_meta( 'product' );
			}

			// Term meta
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_tool_delete_term_meta', 'no' ) ) {
				update_option( 'alg_wc_wholesale_pricing_tool_delete_term_meta', 'no' );
				$this->delete_meta( 'term' );
			}

			// Add role
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_tool_add_role', 'no' ) ) {
				update_option( 'alg_wc_wholesale_pricing_tool_add_role', 'no' );
				$this->add_role();
			}

			// Remove roles
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_tool_remove_role', 'no' ) ) {
				update_option( 'alg_wc_wholesale_pricing_tool_remove_role', 'no' );
				$this->remove_roles();
			}

		}
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Tools();
