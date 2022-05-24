<?php
/**
 * Product Price by Quantity for WooCommerce - Core Class
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Core' ) ) :

class Alg_WC_Wholesale_Pricing_Core {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] user roles: separate "Discount type", and maybe "Enable" ("all products", "per term", "per product")
	 * @todo    [next] add `[alg_wc_wh_pr_product_qty]` shortcode?
	 * @todo    [next] [maybe] decimal min quantity?
	 * @todo    [maybe] remove `$this->is_children`
	 * @todo    [maybe] remove `$this->do_process_formula`
	 * @todo    [maybe] `$product->apply_changes()`
	 * @todo    [maybe] `$this->is_children`: rename to `is_per_variation`?
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_enabled', 'yes' ) ) {

			// Options
			$this->is_children = ( 'yes' === get_option( 'alg_wc_wholesale_pricing_product_children', 'no' ) );

			// Shortcodes
			$this->shortcodes = require_once( 'class-alg-wc-wholesale-pricing-shortcodes.php' );

			// Frontend
			$this->frontend = require_once( 'class-alg-wc-wholesale-pricing-frontend.php' );

			// Formula (discount table values)
			$this->do_process_formula     = ( 'yes' === get_option( 'alg_wc_wholesale_pricing_process_formula', 'no' ) );
			$this->product_id_for_formula = false;
			if ( $this->do_process_formula ) {
				add_shortcode( 'alg_wc_wh_pr_product_meta', array( $this, 'product_meta_shortcode' ) );
				add_shortcode( 'alg_wc_ppq_product_meta',   array( $this, 'product_meta_shortcode' ) );
			}

			// Per product settings
			require_once( 'settings/class-alg-wc-wholesale-pricing-settings-per-item.php' );
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_per_product_enabled', 'yes' ) ) {
				require_once( 'settings/class-alg-wc-wholesale-pricing-settings-per-product.php' );
			}

			// Hooks
			require_once( 'class-alg-wc-wholesale-pricing-hooks.php' );

			// Compatibility
			if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_lumise_enabled', 'no' ) ) {
				add_filter( 'alg_wc_wholesale_pricing_get_item_product_id',         array( $this, 'lumise_get_item_product_id' ), 10, 2 );
				add_filter( 'alg_wc_wholesale_pricing_calculate_totals_product_id', array( $this, 'lumise_calculate_totals_product_id' ), 10, 2 );
			}

			// Admin stuff
			if ( is_admin() ) {
				require_once( 'class-alg-wc-wholesale-pricing-admin.php' );
			}

		}

		// Admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Tools
		require_once( 'class-alg-wc-wholesale-pricing-tools.php' );

		// Core loaded
		do_action( 'alg_wc_wholesale_pricing_core_loaded', $this );

	}

	/**
	 * admin_scripts.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function admin_scripts( $hook ) {
		if ( 'woocommerce_page_wc-settings' != $hook || ! isset( $_GET['tab'] ) || 'alg_wc_wholesale_pricing' != $_GET['tab'] || ! apply_filters( 'alg_wc_wholesale_pricing_settings', true ) ) {
			return;
		}
		$min_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' );
		wp_enqueue_script( 'alg-wc-wholesale-pricing-admin',
			trailingslashit( alg_wc_wholesale_pricing()->plugin_url() ) . 'includes/js/alg-wc-wholesale-pricing-admin' . $min_suffix . '.js',
			array( 'jquery' ),
			alg_wc_wholesale_pricing()->version,
			true
		);
	}

	/**
	 * lumise_get_variation_id.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function lumise_get_variation_id( $item ) {
		if ( isset( $item['lumise_data'] ) && isset( $item['lumise_data']['product_id'] ) && false !== strpos( $item['lumise_data']['product_id'], 'variable:' ) ) {
			$variation_id = explode( ':', $item['lumise_data']['product_id'] );
			if ( 2 == count( $variation_id ) ) {
				return $variation_id[1];
			}
		}
		return 0;
	}

	/**
	 * lumise_get_item_product_id.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function lumise_get_item_product_id( $product_id, $item ) {
		return ( $this->is_children && 0 != ( $variation_id = $this->lumise_get_variation_id( $item ) ) ? $variation_id : $product_id );
	}

	/**
	 * lumise_calculate_totals_product_id.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function lumise_calculate_totals_product_id( $product_id, $item ) {
		return ( 0 != ( $variation_id = $this->lumise_get_variation_id( $item ) ) ? $variation_id : $product_id );
	}

	/**
	 * product_meta_shortcode.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function product_meta_shortcode( $atts, $content = '' ) {
		return ( empty( $atts['key'] ) || empty( $this->product_id_for_formula ) ? 0 : get_post_meta( $this->product_id_for_formula, $atts['key'], true ) );
	}

	/**
	 * set_product_id_for_formula.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function set_product_id_for_formula( $product_id ) {
		$this->product_id_for_formula = $product_id;
	}

	/**
	 * maybe_process_formula.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 *
	 * @todo    [maybe] find/replace instead of `do_shortcode()`
	 * @todo    [maybe] call `require_once` only once (e.g. in `init` hook)
	 */
	function maybe_process_formula( $value ) {
		if ( $this->do_process_formula ) {
			require_once( WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php' );
			return WC_Eval_Math::evaluate( do_shortcode( $value ) );
		} else {
			return $value;
		}
	}

	/**
	 * get_total_quantity.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	function get_total_quantity( $cart, $item ) {
		$qty = ( 'yes' === get_option( 'alg_wc_wholesale_pricing_use_total_cart_quantity', 'no' ) ? $cart->cart_contents_count : $item['quantity'] );
		return apply_filters( 'alg_wc_wholesale_pricing_get_total_quantity', $qty, $cart, $item );
	}

	/**
	 * get_item_product_id.
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 */
	function get_item_product_id( $item ) {
		$product_id = ( $this->is_children && ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );
		return apply_filters( 'alg_wc_wholesale_pricing_get_item_product_id', $product_id, $item );
	}

	/**
	 * get_product_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_product_id( $product ) {
		if ( ! $product ) {
			return false;
		}
		return ( $this->is_children || 0 == $product->get_parent_id() ? $product->get_id() : $product->get_parent_id() );
	}

	/**
	 * get_wholesale_price.
	 *
	 * @version 2.6.1
	 * @since   1.0.0
	 *
	 * @todo    [next] [!] (dev) `if ( '' === $price && 'price_directly' !== $discount_type ) { return $price; }` || `if ( ! is_numeric( $price ) && 'price_directly' !== $discount_type ) { return $price; }`
	 */
	function get_wholesale_price( $price, $quantity, $product_id ) {
		if ( false !== ( $discount = $this->get_discount_by_quantity( $quantity, $product_id ) ) ) {
			$discount_type = $this->get_discount_type( $product_id, $quantity );
			switch ( $discount_type ) {
				case 'price_directly':
					return $discount;
				case 'percent':
					return $price * ( 1.0 - ( $discount / 100.0 ) );
				default: // 'fixed'
					$discounted_price = $price - $discount;
					return ( $discounted_price >= 0 ) ? $discounted_price : 0;
			}
		}
		return $price;
	}

	/**
	 * is_enabled.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] [!] (dev) in each `enabled_...()` function: check if number of levels > 0?
	 * @todo    [next] (feature) "wholesale product" product badge
	 * @todo    [maybe] cache results
	 */
	function is_enabled( $product_id ) {
		return ( $this->is_enabled_per_product( $product_id ) || $this->is_enabled_per_term( $product_id ) || $this->is_enabled_all_products( $product_id ) );
	}

	/**
	 * is_enabled_all_products.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [maybe] include/exclude: product cats/tags
	 * @todo    [maybe] include/exclude: variations
	 * @todo    [maybe] cache results
	 */
	function is_enabled_all_products( $product_id ) {
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_all_products_enabled', 'yes' ) ) {
			$products_to_include  = get_option( 'alg_wc_wholesale_pricing_products_to_include', array() );
			$products_to_exclude  = get_option( 'alg_wc_wholesale_pricing_products_to_exclude', array() );
			$product_or_parent_id = ( 0 != ( $parent_id = wp_get_post_parent_id( $product_id ) ) ? $parent_id : $product_id );
			return (
				( empty ( $products_to_include ) ||   in_array( $product_or_parent_id, $products_to_include ) ) &&
				( empty ( $products_to_exclude ) || ! in_array( $product_or_parent_id, $products_to_exclude ) ) );
		}
		return false;
	}

	/**
	 * is_enabled_per_product.
	 *
	 * @version 2.8.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) `$this->is_children`: `... && ( $children = get_children( $product_id ) ) && ! empty( $children )`
	 * @todo    [maybe] cache results
	 */
	function is_enabled_per_product( $product_id ) {
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_per_product_enabled', 'yes' ) ) {
			return ( $this->is_children && ( $product = wc_get_product( $product_id ) ) && $product->is_type( 'variable' ) ? false :
				( 'yes' === get_post_meta( $product_id, '_' . 'alg_wc_wholesale_pricing_per_product_enabled', true ) ) );
		}
		return false;
	}

	/**
	 * is_enabled_per_term.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function is_enabled_per_term( $product_id ) {
		return apply_filters( 'alg_wc_wholesale_pricing_is_enabled_per_term', false, $product_id );
	}

	/**
	 * get_discount_type.
	 *
	 * @version 2.6.0
	 * @since   1.0.0
	 *
	 * @param   int $product_id Product ID.
	 * @param   int|false $quantity Quantity. Optional. Used only for `alg_wc_wholesale_pricing_get_discount_type` filter.
	 *
	 * @todo    [maybe] `is_enabled_all_products()` (i.e. for `get_option( $key, 'percent' )`)
	 * @todo    [maybe] add `$type` as function param (i.e. check if `is_enabled...()` in advance)
	 */
	function get_discount_type( $product_id, $quantity = false ) {
		$key = 'alg_wc_wholesale_pricing_discount_type';
		if ( $this->is_enabled_per_product( $product_id ) ) {
			$discount_type = get_post_meta( $product_id, '_' . $key, true );
		} elseif ( ( $term_id = $this->is_enabled_per_term( $product_id ) ) ) {
			$discount_type = get_term_meta( $term_id, '_' . $key, true );
		} else {
			$discount_type = get_option( $key, 'percent' );
		}
		return apply_filters( 'alg_wc_wholesale_pricing_get_discount_type', $discount_type,
			array( 'product_id' => $product_id, 'quantity' => $quantity ) );
	}

	/**
	 * get_discount_by_quantity.
	 *
	 * @version 2.6.1
	 * @since   1.0.0
	 *
	 * @param   float|false discount, or boolean false when no level data was found, or discount was an empty string
	 */
	function get_discount_by_quantity( $quantity, $product_id ) {
		foreach ( $this->get_levels_data( $product_id ) as $level_data ) {
			if ( $quantity >= $level_data['quantity'] ) {
				return ( '' !== $level_data['discount'] ? $level_data['discount'] : false );
			}
		}
		return false;
	}

	/**
	 * get_levels_data.
	 *
	 * @version 2.6.0
	 * @since   1.0.0
	 *
	 * @todo    [next] recheck: `$term_or_product_id = 0;`
	 * @todo    [maybe] code refactoring ("Get type and term/product ID")
	 */
	function get_levels_data( $product_id, $type = false, $sort = 'desc' ) {
		// Get type and term/product ID
		$term_or_product_id = 0;
		if ( ! $type ) {
			if ( $this->is_enabled_per_product( $product_id ) ) {
				$type               = 'per_product';
				$term_or_product_id = $product_id;
			} elseif ( ( $term_id = $this->is_enabled_per_term( $product_id ) ) ) {
				$type               = 'per_term';
				$term_or_product_id = $term_id;
			} elseif ( $this->is_enabled_all_products( $product_id ) ) {
				$type               = 'all';
			}
		} else {
			switch ( $type ) {
				case 'per_product':
					if ( ! $this->is_enabled_per_product( $product_id ) ) {
						$type = false;
					} else {
						$term_or_product_id = $product_id;
					}
					break;
				case 'per_term':
					if ( ! ( $term_id = $this->is_enabled_per_term( $product_id ) ) ) {
						$type = false;
					} else {
						$term_or_product_id = $term_id;
					}
					break;
				case 'all':
					if ( ! $this->is_enabled_all_products( $product_id ) ) {
						$type = false;
					}
					break;
			}
		}
		// Get levels data
		return apply_filters( 'alg_wc_wholesale_pricing_get_levels_data', $this->get_levels_data_array( $product_id, $term_or_product_id, $type, $sort ),
			array( 'product_id' => $product_id, 'term_or_product_id' => $term_or_product_id, 'type' => $type, 'sort' => $sort ) );
	}

	/**
	 * get_levels_data_array.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [maybe] rename
	 */
	function get_levels_data_array( $product_id, $term_or_product_id, $type, $sort ) {
		$levels_data = array();
		if ( $type ) {
			$this->set_product_id_for_formula( $product_id );
			$role_option_name_addon = $this->get_user_role_option_name_addon();
			for ( $level = 1; $level <= $this->get_total_levels( $type, $term_or_product_id, $role_option_name_addon ); $level++ ) {
				$levels_data[] = array(
					'quantity' => floatval( $this->maybe_process_formula( $this->get_level_quantity( $type, $level, $term_or_product_id, $role_option_name_addon ) ) ),
					'discount' => floatval( $this->maybe_process_formula( $this->get_level_discount( $type, $level, $term_or_product_id, $role_option_name_addon ) ) ),
				);
			}
			usort( $levels_data, array( $this, 'sort_levels_data_by_quantity_' . $sort ) );
			$this->set_product_id_for_formula( false );
		}
		return $levels_data;
	}

	/**
	 * get_total_levels.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] `return 1;`?
	 * @todo    [maybe] merge with `get_level_discount_or_min_qty()`
	 */
	function get_total_levels( $type, $term_or_product_id = 0, $role_option_name_addon = '' ) {
		$key = "alg_wc_wholesale_pricing_levels_number{$role_option_name_addon}";
		switch ( $type ) {
			case 'all':
				return get_option( $key, 1 );
			case 'per_product':
			case 'per_term':
				$func = ( 'per_product' === $type ? 'get_post_meta' : 'get_term_meta' );
				return $func( $term_or_product_id, '_' .  $key, true );
		}
		return 1;
	}

	/**
	 * get_level_quantity.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_level_quantity( $type, $level, $term_or_product_id = 0, $role_option_name_addon = '' ) {
		return $this->get_level_discount_or_min_qty( 'min_qty', $type, $level, $term_or_product_id, $role_option_name_addon );
	}

	/**
	 * get_level_discount.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_level_discount( $type, $level, $term_or_product_id = 0, $role_option_name_addon = '' ) {
		return $this->get_level_discount_or_min_qty( 'discount', $type, $level, $term_or_product_id, $role_option_name_addon );
	}

	/**
	 * get_level_discount_or_min_qty.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_level_discount_or_min_qty( $discount_or_min_qty, $type, $level, $term_or_product_id = 0, $role_option_name_addon = '' ) {
		$key = "alg_wc_wholesale_pricing_level_{$discount_or_min_qty}{$role_option_name_addon}_{$level}";
		switch ( $type ) {
			case 'all':
				return get_option( $key, 0 );
			case 'per_product':
			case 'per_term':
				$func = ( 'per_product' === $type ? 'get_post_meta' : 'get_term_meta' );
				return $func( $term_or_product_id, '_' .  $key, true );
		}
	}

	/**
	 * get_user_roles_options.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_user_roles_options() {
		global $wp_roles;
		$all_roles = array_merge( array( 'guest' => array( 'name' => __( 'Guest', 'wholesale-pricing-woocommerce' ), 'capabilities' => array() ) ),
			apply_filters( 'editable_roles', ( isset( $wp_roles ) && is_object( $wp_roles ) ? $wp_roles->roles : array() ) ) );
		return wp_list_pluck( $all_roles, 'name' );
	}

	/**
	 * get_current_user_role.
	 *
	 * @version 2.6.2
	 * @since   2.0.0
	 *
	 * @todo    [next] (dev) multiple user roles, i.e. not only the first one
	 */
	function get_current_user_role( $user = false ) {
		if ( ! isset( $this->current_user_role ) ) {
			$current_user = ( $user ? $user : wp_get_current_user() );
			$this->current_user_role = ( isset( $current_user->roles[0] ) && '' != $current_user->roles[0] ? $current_user->roles[0] : 'guest' );
		}
		return $this->current_user_role;
	}

	/**
	 * get_user_role_option_name_addon.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_user_role_option_name_addon() {
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_by_user_role_enabled', 'yes' ) ) {
			$user_roles = get_option( 'alg_wc_wholesale_pricing_by_user_role_roles', array() );
			if ( ! empty( $user_roles ) ) {
				$current_user_role = $this->get_current_user_role();
				if ( in_array( $current_user_role, $user_roles ) ) {
					return '_' . $current_user_role;
				}
			}
		}
		return '';
	}

	/**
	 * sort_levels_data_by_quantity_asc.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function sort_levels_data_by_quantity_asc( $a, $b ) {
		return ( $a['quantity'] == $b['quantity'] ? 0 : ( $a['quantity'] < $b['quantity'] ? -1 : 1 ) );
	}

	/**
	 * sort_levels_data_by_quantity_desc.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function sort_levels_data_by_quantity_desc( $a, $b ) {
		return ( $a['quantity'] == $b['quantity'] ? 0 : ( $a['quantity'] < $b['quantity'] ? 1 : -1 ) );
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Core();
