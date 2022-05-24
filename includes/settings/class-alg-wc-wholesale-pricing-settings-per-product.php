<?php
/**
 * Product Price by Quantity for WooCommerce - Per Product Settings
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Per_Product' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Per_Product extends Alg_WC_Wholesale_Pricing_Settings_Per_Item {

	/**
	 * Constructor.
	 *
	 * @version 2.2.5
	 * @since   1.0.0
	 *
	 * @todo    [next] [!] (feature) Dokan (check my EAN plugin)
	 * @todo    [next] [maybe] admin actions: copy (and maybe reset) user role (same in global "User roles" settings)
	 */
	function __construct() {
		add_action( 'add_meta_boxes',    array( $this, 'add_wholesale_pricing_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_wholesale_pricing_meta_box' ), PHP_INT_MAX, 2 );
		add_action( 'admin_init',        array( $this, 'admin_action_copy_variation' ) );
		add_action( 'admin_init',        array( $this, 'admin_action_reset' ) );
		// WCFM
		if ( 'yes' === get_option( 'alg_wc_wholesale_pricing_wcfm_enabled', 'no' ) ) {
			add_action( 'after_wcfm_products_manage_tabs_content', array( $this, 'wcfm_wholesale_product_settings' ),        PHP_INT_MAX, 4 );
			add_action( 'after_wcfm_products_manage_meta_save',    array( $this, 'wcfm_wholesale_product_settings_update' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * validate_admin_action.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function validate_admin_action( $action ) {
		global $pagenow;
		if (
			! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ||
			! current_user_can( 'manage_woocommerce' ) ||
			! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], $action ) ||
			! isset( $_GET['post'] ) || ! ( $product = wc_get_product( intval( $_GET['post'] ) ) )
		) {
			return false;
		}
		return $product;
	}

	/**
	 * admin_action_reset.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] [maybe] admin notice, e.g. "Options have been reset successfully."
	 */
	function admin_action_reset() {
		if ( ! empty( $_GET['alg_wc_wholesale_pricing_reset_variation'] ) || ! empty( $_GET['alg_wc_wholesale_pricing_reset'] ) ) {
			$action = ( ! empty( $_GET['alg_wc_wholesale_pricing_reset_variation'] ) ? 'alg_wc_wholesale_pricing_reset_variation' : 'alg_wc_wholesale_pricing_reset' );
			if ( ! ( $product = $this->validate_admin_action( $action ) ) ) {
				wp_die( __( 'Something went wrong...', 'wholesale-pricing-woocommerce' ) );
			}
			$product_id  = intval( $_GET[ $action ] );
			$product_ids = ( 'alg_wc_wholesale_pricing_reset_variation' === $action ? array( $product_id ) : array_merge( array( $product_id ), $product->get_children() ) );
			foreach ( $product_ids as $product_id ) {
				$key    = '_alg_wc_wholesale_pricing_';
				$length = strlen( $key );
				$meta   = get_post_meta( $product_id );
				foreach ( $meta as $meta_key => $meta_data ) {
					if ( $key === substr( $meta_key, 0, $length ) ) {
						delete_post_meta( $product_id, $meta_key );
					}
				}
			}
			wp_safe_redirect( remove_query_arg( array( $action, '_wpnonce' ) ) );
			exit;
		}
	}

	/**
	 * admin_action_copy_variation.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] reset -> copy: zero becomes empty
	 * @todo    [next] [maybe] admin notice, e.g. "Variation has been copied successfully."
	 */
	function admin_action_copy_variation() {
		if ( ! empty( $_GET['alg_wc_wholesale_pricing_copy_variation'] ) ) {
			$action = 'alg_wc_wholesale_pricing_copy_variation';
			if ( ! ( $product = $this->validate_admin_action( $action ) ) ) {
				wp_die( __( 'Something went wrong...', 'wholesale-pricing-woocommerce' ) );
			}
			$product_id = intval( $_GET[ $action ] );
			$options    = $this->get_options( 'product', $product_id );
			foreach ( $product->get_children() as $child_id ) {
				foreach ( $options as $option ) {
					if ( isset( $option['meta_name'] ) ) {
						$key   = $option['meta_name'];
						$value = get_post_meta( $product_id, $key, true );
						update_post_meta( $child_id, $key, $value );
					}
				}
			}
			wp_safe_redirect( remove_query_arg( array( $action, '_wpnonce' ) ) );
			exit;
		}
	}

	/**
	 * get_admin_action_link.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_admin_action_link( $action, $product_id, $link_text, $extra_confirm_text = '', $tag = 'span' ) {
		$url          = add_query_arg( array( $action => $product_id, '_wpnonce' => wp_create_nonce( $action ) ) );
		$confirm_text = __( 'Are you sure?', 'wholesale-pricing-woocommerce' );
		if ( '' !== $extra_confirm_text ) {
			$confirm_text .= ' ' . $extra_confirm_text;
		}
		return '<' . $tag. ' style="color:orange;font-size:smaller;font-weight:normal;">' .
			'[' . '<a style="color:orange;" href="' . $url . '"' . ' onclick="return confirm(\'' . $confirm_text . '\')"' . '>' . $link_text . '</a>' . ']' .
		'</' . $tag. '>';
	}

	/**
	 * add_wholesale_pricing_metabox.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function add_wholesale_pricing_metabox() {
		add_meta_box(
			'alg-wholesale-pricing',
			__( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
			array( $this, 'display_wholesale_pricing_metabox' ),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * display_wholesale_pricing_metabox.
	 *
	 * @version 2.2.5
	 * @since   1.0.0
	 */
	function display_wholesale_pricing_metabox() {
		$product_id = get_the_ID();
		$html  = '';
		$html .= $this->get_options_table( $product_id, 'widefat striped' );
		$html .= $this->get_admin_action_link( 'alg_wc_wholesale_pricing_reset', $product_id, __( 'Reset all', 'wholesale-pricing-woocommerce' ),
			__( 'This will reset all wholesale settings fields to their default values.', 'wholesale-pricing-woocommerce' ), 'p' );
		$html .= '<input type="hidden" name="alg_wc_wholesale_pricing_save_post" value="alg_wc_wholesale_pricing_save_post">';
		echo $html;
	}

	/**
	 * save_wholesale_pricing_meta_box.
	 *
	 * @version 2.2.5
	 * @since   1.0.0
	 */
	function save_wholesale_pricing_meta_box( $post_id, $post ) {
		if ( ! isset( $_POST[ 'alg_wc_wholesale_pricing_save_post' ] ) ) {
			// Check that we are saving with current metabox displayed
			return;
		}
		$this->save_options( $post_id, $_POST );
	}

	/**
	 * wcfm_wholesale_product_settings.
	 *
	 * @version 3.0.0
	 * @since   2.2.5
	 *
	 * @todo    [next] [!] (dev) customizable tab title
	 * @todo    [next] [!] (dev) better icon
	 * @todo    [next] [!] (dev) new product: better solution
	 * @todo    [next] [!] (dev) JS (then disable *all* tooltips)
	 * @todo    [next] [!] (dev) better styling
	 * @todo    [next] [!] (dev) "reset all", "reset variation", etc.
	 * @todo    [next] [!] (dev) `wcfm_is_vendor()`?
	 * @todo    [next] [!] (dev) `'wcmarketplace' === wcfm_is_marketplace()`
	 * @todo    [next] (feature) translation shortcode?
	 */
	function wcfm_wholesale_product_settings( $product_id, $product_type = '', $wcfm_is_translated_product = false, $wcfm_wpml_edit_disable_element = '' ) {
		$html  = '';
		$html .= '<div class="page_collapsible products_manage_alg_wc_wh_pr simple variable grouped external booking" id="wcfm_products_manage_form_alg_wc_wh_pr_head">' .
			'<label class="wcfmfa fa-server"></label>' . __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ) . '<span></span>' . '</div>';
		$html .= '<div class="wcfm-container simple variable external grouped booking">' . '<div id="wcfm_products_manage_form_alg_wc_wh_pr_expander" class="wcfm-content">';
		if ( $product_id ) {
			$html .= $this->get_options_table( $product_id, '', 'title_only', 'wcfm' );
		} else {
			$html .= do_shortcode( get_option( 'alg_wc_wholesale_pricing_wcfm_new_product_notification', __( 'Please save the product first.', 'wholesale-pricing-woocommerce' ) ) );
		}
		$html .= '</div>' . '</div>' . '<div class="wcfm_clearfix"></div>';
		echo $html;
	}

	/**
	 * wcfm_wholesale_product_settings_update.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 *
	 * @todo    [next] [!] (dev) `alg_wc_wholesale_pricing_save_post`
	 * @todo    [next] [!] (dev) `global $WCFM`
	 * @todo    [next] [!] (dev) `'wcmarketplace' === wcfm_is_marketplace()`
	 */
	function wcfm_wholesale_product_settings_update( $new_product_id, $wcfm_products_manage_form_data ) {
		$this->save_options( $new_product_id, $wcfm_products_manage_form_data );
	}

	/**
	 * save_options.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 */
	function save_options( $product_id, $data ) {
		foreach ( $this->get_product_options( $product_id, false ) as $option ) {
			if ( 'title' === $option['type'] ) {
				continue;
			}
			$is_enabled = ( isset( $option['enabled'] ) && 'no' === $option['enabled'] ) ? false : true;
			if ( $is_enabled ) {
				$option_value  = ( isset( $data[ $option['name'] ] ) ? sanitize_text_field( $data[ $option['name'] ] ) : $option['default'] );
				$_post_id      = ( isset( $option['product_id'] )    ? $option['product_id']                           : $product_id );
				$_meta_name    = ( isset( $option['meta_name'] )     ? $option['meta_name']                            : '_' . $option['name'] );
				update_post_meta( $_post_id, $_meta_name, $option_value );
			}
		}
	}

	/**
	 * get_field_html.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 *
	 * @todo    [next] [!] (dev) code refactoring
	 * @todo    [maybe] (dev) placeholder for textarea
	 */
	function get_field_html( $option, $product_id ) {
		$the_post_id   = ( isset( $option['product_id'] ) ) ? $option['product_id'] : $product_id;
		$the_meta_name = ( isset( $option['meta_name'] ) )  ? $option['meta_name']  : '_' . $option['name'];
		if ( get_post_meta( $the_post_id, $the_meta_name ) ) {
			$option_value = get_post_meta( $the_post_id, $the_meta_name, true );
		} else {
			$option_value = ( isset( $option['default'] ) ) ? $option['default'] : '';
		}
		$css          = ( isset( $option['css'] ) ) ? $option['css']  : '';
		$input_ending = '';
		$custom_atts  = '';
		if ( 'select' === $option['type'] ) {
			if ( isset( $option['multiple'] ) ) {
				$custom_atts = ' multiple';
				$option_name = $option['name'] . '[]';
			} else {
				$option_name = $option['name'];
			}
			if ( isset( $option['custom_attributes'] ) ) {
				$custom_atts .= ' ' . $option['custom_attributes'];
			}
			$options = '';
			foreach ( $option['options'] as $select_option_key => $select_option_value ) {
				$selected = '';
				if ( is_array( $option_value ) ) {
					foreach ( $option_value as $single_option_value ) {
						if ( '' != ( $selected = selected( $single_option_value, $select_option_key, false ) ) ) {
							break;
						}
					}
				} else {
					$selected = selected( $option_value, $select_option_key, false );
				}
				$options .= '<option value="' . $select_option_key . '" ' . $selected . '>' . $select_option_value . '</option>';
			}
		} elseif ( 'textarea' === $option['type'] ) {
			if ( '' === $css ) {
				$css = 'min-width:300px;';
			}
		} else {
			$input_ending = ' id="' . $option['name'] . '" name="' . $option['name'] . '" value="' . $option_value . '">';
			if ( isset( $option['custom_attributes'] ) ) {
				$input_ending = ' ' . $option['custom_attributes'] . $input_ending;
			}
			if ( isset( $option['placeholder'] ) ) {
				$input_ending = ' placeholder="' . $option['placeholder'] . '"' . $input_ending;
			}
		}
		switch ( $option['type'] ) {
			case 'price':
				$field_html = '<input style="' . $css . '" class="short wc_input_price" type="number" step="0.0001"' . $input_ending;
				break;
			case 'date':
				$field_html = '<input style="' . $css . '" class="input-text" display="date" type="text"' . $input_ending;
				break;
			case 'textarea':
				$field_html = '<textarea style="' . $css . '" id="' . $option['name'] . '" name="' . $option['name'] . '">' . $option_value . '</textarea>';
				break;
			case 'select':
				$field_html = '<select' . $custom_atts . ' style="' . $css . '" id="' . $option['name'] . '" name="' . $option_name . '">' . $options . '</select>';
				break;
			default:
				$field_html = '<input style="' . $css . '" class="short" type="' . $option['type'] . '"' . $input_ending;
				break;
		}
		return $field_html;
	}

	/**
	 * get_tooltip_html.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 */
	function get_tooltip_html( $content, $tooltip_type = 'wc' ) {
		switch ( $tooltip_type ) {
			case 'wcfm':
				return sprintf( '<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>', wp_kses_post ( $content ) );
			default: // 'wc'
				return wc_help_tip( $content, true );
		}
	}

	/**
	 * get_options_table.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 *
	 * @todo    [next] `description` and `desc`
	 * @todo    [next] make settings look the same everywhere
	 */
	function get_options_table( $product_id, $table_class = '', $do_add_variation_title = true, $tooltip_type = 'wc' ) {
		$html  = '';
		$html .= '<table class="' . $table_class . '">';
		foreach ( $this->get_product_options( $product_id, $do_add_variation_title ) as $option ) {
			$is_enabled = ( isset( $option['enabled'] ) && 'no' === $option['enabled'] ) ? false : true;
			if ( $is_enabled ) {
				if ( 'title' === $option['type'] ) {
					$color = ( isset( $option['background-color'] ) ? 'background-color:' . $option['background-color'] . ';' : '' );
					$html .= '<tr>';
					$html .= '<th colspan="3" style="text-align:left;font-weight:bold;' . $color . '">' . $option['title'] . '</th>';
					$html .= '</tr>';
				} else {
					$html .= '<tr>';
					$html .= '<th style="text-align:left;width:25%;">' . $option['title'] . ( ! empty( $option['tooltip'] ) ?
						$this->get_tooltip_html( $option['tooltip'], $tooltip_type ) : '' ) . '</th>';
					$html .= ( ! empty( $option['desc'] ) ? '<td style="font-style:italic;width:25%;">' . $option['desc'] . '</td>' : '' );
					$html .= '<td style="width:' . ( ! empty( $option['desc'] ) ? '50' : '75' ). '%;">' .
						$this->get_field_html( $option, $product_id ) . ( ! empty( $option['description'] ) ? '<p>' . $option['description'] . '</p>' : '' ) . '</td>';
					$html .= '</tr>';
				}
			}
		}
		$html .= '</table>';
		return $html;
	}

	/**
	 * get_product_options.
	 *
	 * @version 2.2.5
	 * @since   1.0.0
	 *
	 * @todo    [next] [!] (dev) `$do_add_variation_title`: better solution
	 * @todo    [next] (dev) per variation: add Pro desc?
	 * @todo    [maybe] (dev) restyle
	 * @todo    [maybe] (dev) Copy variation: JS
	 */
	function get_product_options( $product_id, $do_add_variation_title = true ) {
		// Get products
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return array();
		}
		$product_ids = apply_filters( 'alg_wc_wholesale_pricing_get_product_options_product_ids', array( $product_id ), $product );
		// Get options
		$options = array();
		foreach ( $product_ids as $product_id ) {
			if ( ! empty( $do_add_variation_title ) && count( $product_ids ) > 1 ) {
				// Variation title and actions
				$product = wc_get_product( $product_id );
				$title   = '<span style="color:white;">' . get_the_title( $product_id ) . ' (#' . $product_id . ')' . '</span>';
				if ( 'title_only' === $do_add_variation_title ) {
					$options = array_merge( $options, array(
						array(
							'type'              => 'title',
							'background-color'  => '#7f54b3',
							'title'             => $title,
						),
					) );
				} else {
					$copy    = $this->get_admin_action_link( 'alg_wc_wholesale_pricing_copy_variation', $product_id, __( 'Copy variation', 'wholesale-pricing-woocommerce' ),
						__( 'This will copy all wholesale settings fields from the current variation to all other variations.', 'wholesale-pricing-woocommerce' ) . ' ' .
						__( 'Please note that you need to UPDATE the product before copying the variation.', 'wholesale-pricing-woocommerce' ) );
					$reset   = $this->get_admin_action_link( 'alg_wc_wholesale_pricing_reset_variation', $product_id, __( 'Reset variation', 'wholesale-pricing-woocommerce' ),
						__( 'This will reset all wholesale settings fields for the current variation to their default values.', 'wholesale-pricing-woocommerce' ) );
					$pricing = '<span style="color:white;float:right;font-size:smaller;font-weight:normal;font-style:italic;">' .
						sprintf( __( 'Regular pricing: %s', 'wholesale-pricing-woocommerce' ), $product->get_price_html() ) . '</span>';
					$options = array_merge( $options, array(
						array(
							'type'              => 'title',
							'background-color'  => '#7f54b3',
							'title'             => $title . ' ' . $copy . ' ' . $reset . $pricing,
						),
					) );
				}
			}
			// Options
			$options = array_merge( $options, $this->get_options( 'product', $product_id ) );
		}
		return $options;
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Per_Product();
