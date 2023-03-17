<?php
/**
 * Product Price by Quantity for WooCommerce - Shortcodes
 *
 * @version 3.3.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Shortcodes' ) ) :

class Alg_WC_Wholesale_Pricing_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 3.3.0
	 * @since   2.0.0
	 *
	 * @todo    [next] `[alg_wc_term_wholesale_pricing_table]` and `[alg_wc_term_wholesale_pricing_data]`
	 */
	function __construct() {
		add_shortcode( 'alg_wc_wholesale_pricing_table',         array( $this, 'wholesale_pricing_table' ) );
		add_shortcode( 'alg_wc_ppq_table',                       array( $this, 'wholesale_pricing_table' ) );
		add_shortcode( 'alg_wc_product_wholesale_pricing_table', array( $this, 'product_wholesale_pricing_table' ) );
		add_shortcode( 'alg_wc_product_ppq_table',               array( $this, 'product_wholesale_pricing_table' ) );
		add_shortcode( 'alg_wc_wholesale_pricing_data',          array( $this, 'wholesale_pricing_data' ) );
		add_shortcode( 'alg_wc_ppq_data',                        array( $this, 'wholesale_pricing_data' ) );
		add_shortcode( 'alg_wc_product_wholesale_pricing_data',  array( $this, 'product_wholesale_pricing_data' ) );
		add_shortcode( 'alg_wc_product_ppq_data',                array( $this, 'product_wholesale_pricing_data' ) );
		add_shortcode( 'alg_wc_ppq_translate',                   array( $this, 'translate' ) );
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
	 * translate.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 */
	function translate( $atts, $content = '' ) {
		// E.g.: `[alg_wc_ppq_translate lang="EN,DE" lang_text="Text for EN & DE" not_lang_text="Text for other languages"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_ppq_translate lang="EN,DE"]Text for EN & DE[/alg_wc_ppq_translate][alg_wc_ppq_translate not_lang="EN,DE"]Text for other languages[/alg_wc_ppq_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * wholesale_pricing_table (global only).
	 *
	 * @version 3.3.0
	 * @since   1.0.0
	 *
	 * @todo    [next] [!!] (dev) `shortcode_atts`: `alg_wc_wholesale_pricing_table` to `alg_wc_ppq_table`?
	 * @todo    [next] [!] (dev) `alg_wc_wholesale_pricing_discount_type` option + `alg_wc_wholesale_pricing_get_discount_type` filter?
	 * @todo    [next] add all applicable atts from `product_wholesale_pricing_table()`, e.g. `table_heading_type` etc.
	 */
	function wholesale_pricing_table( $atts ) {
		// Shortcode atts
		$atts = shortcode_atts( array(
			'heading_format'        => sprintf( __( 'from %s pcs.', 'wholesale-pricing-woocommerce' ), '%level_min_qty%' ),
			'before_level_max_qty'  => '-',
			'last_level_max_qty'    => '+',
			'hide_if_zero_quantity' => 'no',
			'table_format'          => 'horizontal',
			'qty_thousand_sep'      => '',
			'before'                => '',
			'after'                 => '',
		), $atts, 'alg_wc_wholesale_pricing_table' );
		// Get levels data
		$price_levels = $this->get_core()->get_levels_data( 0, 'all', 'asc' );
		// Form table data
		$data_qty       = array();
		$data_discount  = array();
		$columns_styles = array();
		$i              = -1;
		foreach ( $price_levels as $price_level ) {
			$i++;
			if ( 0 == $price_level['quantity'] && 'yes' === $atts['hide_if_zero_quantity'] ) {
				continue;
			}
			// Quantity row
			$level_max_qty = ( isset( $price_levels[ $i + 1 ]['quantity'] ) ) ?
				$atts['before_level_max_qty'] . ( $price_levels[ $i + 1 ]['quantity'] - 1 ) : $atts['last_level_max_qty'];
			$placeholders  = array(
				'%level_min_qty%' => $this->format_qty( $price_level['quantity'], $atts ),
				'%level_max_qty%' => $this->format_qty( $level_max_qty, $atts ),
			);
			$data_qty[]    = str_replace( array_keys( $placeholders ), $placeholders, $atts['heading_format'] );
			// Discount row
			$data_discount[]  = ( 'fixed' === get_option( 'alg_wc_wholesale_pricing_discount_type', 'percent' ) )
				? '-' . wc_price( $price_level['discount'] ) : '-' . $price_level['discount'] . '%';
			// Column style
			$columns_styles[] = 'text-align: center;';
		}
		$table_rows = array( $data_qty, $data_discount );
		// Maybe switch rows and columns
		if ( 'vertical' === $atts['table_format'] ) {
			$table_rows_modified = array();
			foreach ( $table_rows as $row_number => $table_row ) {
				foreach ( $table_row as $column_number => $cell ) {
					$table_rows_modified[ $column_number ][ $row_number ] = $cell;
				}
			}
			$table_rows = $table_rows_modified;
		}
		// Get table HTML
		if ( ! empty( $table_rows ) ) {
			return $atts['before'] . $this->get_table_html( $table_rows, array(
				'table_class'        => 'alg_wc_ppq_table alg_wc_wholesale_pricing_table',
				'columns_styles'     => $columns_styles,
				'table_heading_type' => $atts['table_format'],
			) ) . $atts['after'];
		} else {
			return '';
		}
	}

	/**
	 * product_wholesale_pricing_table.
	 *
	 * @version 3.3.0
	 * @since   1.0.0
	 *
	 * @todo    [next] [!!] (dev) `shortcode_atts`: `alg_wc_product_wholesale_pricing_table` to `alg_wc_product_ppq_table`?
	 * @todo    [next] [!!] (dev) `apply_filters`: `alg_wc_product_wholesale_pricing_table_heading_format` to `alg_wc_product_ppq_table_heading_format`?
	 * @todo    [next] [!] (dev) check if price `is_numeric()`?
	 * @todo    [next] `extra_column_before`, `extra_column_after`
	 * @todo    [next] `columns_styles`: different styles for different elements, i.e. `explode( '|', $atts['columns_styles'] )`
	 * @todo    [next] "add to cart" (min qty) link/button
	 * @todo    [next] variations (when `product_id` is not set in atts) (same in `product_wholesale_pricing_data()`)
	 * @todo    [maybe] new atts: `html_before_rows` and `html_after_rows`
	 */
	function product_wholesale_pricing_table( $atts ) {

		// Shortcode atts
		$atts = shortcode_atts( array(
			'product_id'                     => 0,
			'heading_format'                 => sprintf( __( 'from %s pcs.', 'wholesale-pricing-woocommerce' ), '%level_min_qty%' ),
			'before_level_max_qty'           => '-',
			'last_level_max_qty'             => '+',
			'hide_if_zero_quantity'          => 'no',
			'hide_if_insufficient_quantity'  => 'no',
			'table_format'                   => 'horizontal',
			'qty_thousand_sep'               => '',
			'hide_currency'                  => 'no',
			'add_price_row'                  => 'yes',
			'price_row_format'               => '<del>%old_price_single%</del> %new_price_single%',
			'add_total_min_qty_price_row'    => 'no',
			'total_min_qty_price_row_format' => '<del>%old_price_total%</del> %new_price_total%',
			'add_percent_row'                => 'no',
			'add_percent_row_rounded'        => 'no',
			'add_discount_row'               => 'no',
			'table_heading_type'             => '',
			'extra_row_before'               => '',
			'extra_row_after'                => '',
			'columns_styles'                 => 'text-align: center;',
			'table_class'                    => '',
			'table_style'                    => '',
			'before'                         => '',
			'after'                          => '',
			'hide_table_if_guest'            => 'no',
			'hide_table_for_user_roles'      => '',
			'show_table_for_user_roles'      => '',
			'hide_table_if_out_of_stock'     => 'no',
		), $atts, 'alg_wc_product_wholesale_pricing_table' );

		// Hide table for guests
		if ( 'yes' === $atts['hide_table_if_guest'] && ! is_user_logged_in() ) {
			return '';
		}

		// Hide table by user roles
		if ( '' !== $atts['hide_table_for_user_roles'] || '' !== $atts['show_table_for_user_roles'] ) {
			if ( ! is_user_logged_in() ) {
				$user_roles = array( 'guest' );
			} else {
				$user       = wp_get_current_user();
				$user_roles = $user->roles;
			}
			if ( '' !== $atts['hide_table_for_user_roles'] ) {
				$intersect = array_intersect( $user_roles, array_map( 'trim', explode( ',', $atts['hide_table_for_user_roles'] ) ) );
				if ( ! empty( $intersect ) ) {
					return '';
				}
			}
			if ( '' !== $atts['show_table_for_user_roles'] ) {
				$intersect = array_intersect( $user_roles, array_map( 'trim', explode( ',', $atts['show_table_for_user_roles'] ) ) );
				if ( empty( $intersect ) ) {
					return '';
				}
			}
		}

		// General
		$product    = ( 0 != $atts['product_id'] ? wc_get_product( $atts['product_id'] ) : wc_get_product() );
		$product_id = $this->get_core()->get_product_id( $product );
		if ( ! $product_id || ! $this->get_core()->is_enabled( $product_id ) ) {
			return '';
		}

		// Hide table if is out-of-stock
		if ( 'yes' === $atts['hide_table_if_out_of_stock'] && ! $product->is_in_stock() ) {
			return '';
		}

		// Get levels data
		$price_levels = $this->get_core()->get_levels_data( $product_id, false, 'asc' );

		// Form table data
		$data_qty                      = array();
		$data_price                    = array();
		$data_total_min_qty_price      = array();
		$data_discount_percent         = array();
		$data_discount_percent_rounded = array();
		$data_discount_amount          = array();
		$columns_styles                = array();
		$i = -1;
		foreach ( $price_levels as $price_level ) {
			$i++;

			// Hide if zero quantity
			if ( 'yes' === $atts['hide_if_zero_quantity'] && 0 == $price_level['quantity'] ) {
				continue;
			}

			// Hide if insufficient quantity
			if ( 'yes' === $atts['hide_if_insufficient_quantity'] && $product->get_manage_stock() && $product->get_stock_quantity() < $price_level['quantity'] ) {
				continue;
			}

			// Discount type
			$discount_type = $this->get_core()->get_discount_type( $product_id, $price_level['quantity'] );

			// Discount amounts
			$original_price           = $product->get_price();
			$discount_amount          = ( 'fixed' === $discount_type ? $price_level['discount'] :
				( ! empty( $original_price ) ? ( $original_price - alg_wc_wholesale_pricing()->core->get_wholesale_price( $original_price, $price_level['quantity'], $product_id ) ) : 0 ) );
			$discount_percent         = ( 'percent' === $discount_type ? $price_level['discount'] :
				( ! empty( $original_price ) ? round( ( $discount_amount / $original_price * 100 ), 2 ) : 0 ) );
			$discount_percent_rounded = round( ( 'percent' === $discount_type ? $price_level['discount'] :
				( ! empty( $original_price ) ? ( $discount_amount / $original_price * 100 ) : 0 ) ) );

			// Quantity row
			$level_max_qty = ( isset( $price_levels[ $i + 1 ]['quantity'] ) ) ?
				$atts['before_level_max_qty'] . ( $price_levels[ $i + 1 ]['quantity'] - 1 ) : $atts['last_level_max_qty'];
			$placeholders  = array(
				'%level_min_qty%'                  => $this->format_qty( $price_level['quantity'], $atts ),
				'%level_max_qty%'                  => $this->format_qty( $level_max_qty, $atts ),
				'%level_discount%'                 => $price_level['discount'],
				'%level_discount_amount%'          => $discount_amount,
				'%level_discount_percent%'         => $discount_percent,
				'%level_discount_percent_rounded%' => $discount_percent_rounded,
			);
			$heading_format = apply_filters( 'alg_wc_product_wholesale_pricing_table_heading_format', $atts['heading_format'], ( $i + 1 ), $product_id );
			$data_qty[] = str_replace( array_keys( $placeholders ), $placeholders, $heading_format );

			// Price row
			if ( 'yes' === $atts['add_price_row'] ) {
				$data_price[] = $this->get_product_price( $product,
					$discount_type, $price_level['discount'], $atts['hide_currency'], $atts['price_row_format'] );
			}

			// Total min qty price row
			if ( 'yes' === $atts['add_total_min_qty_price_row'] ) {
				$data_total_min_qty_price[] = $this->get_product_price( $product,
					$discount_type, $price_level['discount'], $atts['hide_currency'], $atts['total_min_qty_price_row_format'], $price_level['quantity'] );
			}

			// Percent rows
			if ( 'yes' === $atts['add_percent_row'] ) {
				$data_discount_percent[] = '-' . $discount_percent . '%';
			}
			if ( 'yes' === $atts['add_percent_row_rounded'] ) {
				$data_discount_percent_rounded[] = '-' . $discount_percent_rounded . '%';
			}

			// Discount row
			if ( 'yes' === $atts['add_discount_row'] ) {
				$data_discount_amount[] = '-' . wc_price( $discount_amount );
			}

			// Column style
			$columns_styles[] = $atts['columns_styles'];
		}

		// Table rows
		$table_rows = array( $data_qty );
		if ( 'yes' === $atts['add_price_row'] ) {
			$table_rows[] = $data_price;
		}
		if ( 'yes' === $atts['add_total_min_qty_price_row'] ) {
			$table_rows[] = $data_total_min_qty_price;
		}
		if ( 'yes' === $atts['add_percent_row'] ) {
			$table_rows[] = $data_discount_percent;
		}
		if ( 'yes' === $atts['add_percent_row_rounded'] ) {
			$table_rows[] = $data_discount_percent_rounded;
		}
		if ( 'yes' === $atts['add_discount_row'] ) {
			$table_rows[] = $data_discount_amount;
		}

		// Maybe switch rows and columns
		if ( 'vertical' === $atts['table_format'] ) {
			$table_rows_modified = array();
			foreach ( $table_rows as $row_number => $table_row ) {
				foreach ( $table_row as $column_number => $cell ) {
					$table_rows_modified[ $column_number ][ $row_number ] = $cell;
				}
			}
			$table_rows = $table_rows_modified;
		}

		// Extra rows
		if ( '' !== $atts['extra_row_before'] ) {
			$row        = explode( '|', $atts['extra_row_before'] );
			$table_rows = array_merge( array( $row ), $table_rows );
		}
		if ( '' !== $atts['extra_row_after'] ) {
			$row        = explode( '|', $atts['extra_row_after'] );
			$table_rows = array_merge( $table_rows, array( $row ) );
		}

		// Get table HTML
		if ( ! empty( $table_rows ) ) {
			return $atts['before'] . $this->get_table_html( $table_rows, array(
				'table_class'        => implode( ' ', array(
					'alg_wc_product_ppq_table alg_wc_product_wholesale_pricing_table',
					( 0 != $atts['product_id'] ? 'alg_wc_whpr_with_product_id' : 'alg_wc_whpr_no_product_id' ),
					$atts['table_class'] ) ),
				'table_style'        => $atts['table_style'],
				'columns_styles'     => $columns_styles,
				'table_heading_type' => ( '' !== $atts['table_heading_type'] ? $atts['table_heading_type'] : $atts['table_format'] ),
			) ) . $atts['after'];
		} else {
			return '';
		}

	}

	/**
	 * wholesale_pricing_data (global only).
	 *
	 * @version 2.7.0
	 * @since   1.1.2
	 *
	 * @todo    [next] [!!] (dev) `shortcode_atts`: `alg_wc_wholesale_pricing_data` to `alg_wc_ppq_data`?
	 */
	function wholesale_pricing_data( $atts ) {
		$atts = shortcode_atts( array(
			'field'     => 'discount', // or 'quantity'
			'level_num' => 'last',     // or actual level num
			'before'    => '',
			'after'     => '',
		), $atts, 'alg_wc_wholesale_pricing_data' );
		$price_levels = $this->get_core()->get_levels_data( 0, 'all', 'asc' );
		$level_num    = ( 'last' === $atts['level_num'] ? count( $price_levels ) : $atts['level_num'] ) - 1;
		return ( isset( $price_levels[ $level_num ][ $atts['field'] ] ) ? $atts['before'] . $price_levels[ $level_num ][ $atts['field'] ] . $atts['after'] : '' );
	}

	/**
	 * product_wholesale_pricing_data.
	 *
	 * @version 3.2.0
	 * @since   1.1.2
	 *
	 * @todo    [next] [!!] (dev) `shortcode_atts`: `alg_wc_product_wholesale_pricing_data` to `alg_wc_product_ppq_data`?
	 * @todo    [maybe] `price_for_qty`? (now can be done with `price_format="<del>%old_price_total%</del> %new_price_total%"`)
	 */
	function product_wholesale_pricing_data( $atts ) {

		// Atts
		$atts = shortcode_atts( array(
			'product_id'            => 0,
			'price_format'          => '<del>%old_price_single%</del> %new_price_single%',
			'field'                 => 'price', // or 'discount' or 'quantity'
			'level_num'             => 'last',  // or actual level num
			'hide_currency'         => 'no',
			'before'                => '',
			'after'                 => '',
			'use_variation'         => 'no',
			'variation_type'        => 'first',
		), $atts, 'alg_wc_product_wholesale_pricing_data' );

		// Get product
		if (
			! ( $product    = ( 0 != $atts['product_id'] ? wc_get_product( $atts['product_id'] ) : wc_get_product() ) ) ||
			! ( $product_id = $this->get_core()->get_product_id( $product ) )
		) {
			return '';
		}

		// If not enabled, try variation
		if ( ! $this->get_core()->is_enabled( $product_id ) && 'yes' === $atts['use_variation'] && $product->is_type( 'variable' ) ) {
			$is_variation_found = false;

			if ( in_array( $atts['variation_type'], array( 'min', 'max' ) ) ) {

				// 'min', 'max'
				$prices = $product->get_variation_prices();
				while ( ! empty( $prices['price'] ) ) {
					$child_id = ( 'min' === $atts['variation_type'] ? key( $prices['price'] ) : array_key_last( $prices['price'] ) );
					if ( $this->get_core()->is_enabled( $child_id ) ) {
						$product_id         = $child_id;
						$product            = wc_get_product( $child_id );
						$is_variation_found = true;
						break;
					} else {
						unset( $prices['price'][ $child_id ] );
					}
				}

			} else {

				// 'first'
				foreach ( $product->get_children() as $child_id ) {
					if ( $this->get_core()->is_enabled( $child_id ) ) {
						$product_id         = $child_id;
						$product            = wc_get_product( $child_id );
						$is_variation_found = true;
						break;
					}
				}

			}

			if ( ! $is_variation_found ) {
				return '';
			}
		}

		// Data
		$price_levels = $this->get_core()->get_levels_data( $product_id, false, 'asc' );
		$level_num    = ( 'last' === $atts['level_num'] ? count( $price_levels ) : $atts['level_num'] ) - 1;
		if ( isset( $price_levels[ $level_num ] ) ) {
			switch ( $atts['field'] ) {

				case 'quantity':
				case 'discount':
					return ( isset( $price_levels[ $level_num ][ $atts['field'] ] ) ? $atts['before'] . $price_levels[ $level_num ][ $atts['field'] ] . $atts['after'] : '' );

				default: // 'price':
					$qty   = ( isset( $price_levels[ $level_num ]['quantity'] ) ? $price_levels[ $level_num ]['quantity'] : 1 );
					$type  = $this->get_core()->get_discount_type( $product_id, $qty );
					$price = $this->get_product_price( $product, $type, $price_levels[ $level_num ]['discount'], $atts['hide_currency'], $atts['price_format'], $qty );
					return $atts['before'] . $price . $atts['after'];

			}
		} else {
			return '';
		}

	}

	/**
	 * get_product_price.
	 *
	 * @version 3.1.0
	 * @since   1.1.2
	 *
	 * @todo    [next] [!!] (fix) Variable: per variation enabled
	 */
	function get_product_price( $product, $discount_type, $discount, $hide_currency, $price_format, $qty = 1 ) {

		if ( ! $product->get_price() ) {
			return '';
		}

		foreach ( array( '', '_incl_tax', '_excl_tax' ) as $tax_display ) {

			$price_func           = ( '' === $tax_display ? 'wc_get_price_to_display' : ( '_incl_tax' === $tax_display ? 'wc_get_price_including_tax' : 'wc_get_price_excluding_tax' ) );
			$do_hide_currency     = ( 'yes' === $hide_currency );
			$price                = '';
			$price_original       = '';
			$price_total          = '';
			$price_total_original = '';

			if ( $product->is_type( 'variable' ) ) {

				// Variable
				$prices       = $product->get_variation_prices( false );
				$min_key      = key( $prices['price'] );
				end( $prices['price'] );
				$max_key      = key( $prices['price'] );
				$min_product  = wc_get_product( $min_key );
				$max_product  = wc_get_product( $max_key );
				$min          = ( $min_product ? $price_func( $min_product ) : false );
				$max          = ( $max_product ? $price_func( $max_product ) : false );
				$min_original = $min;
				$max_original = $max;
				switch ( $discount_type ) {
					case 'price_directly':
						$min  = ( $min_product ? $price_func( $min_product, array( 'price' => $discount ) ) : false );
						$max  = ( $max_product ? $price_func( $max_product, array( 'price' => $discount ) ) : false );
						break;
					case 'fixed':
						$min  = ( $min_product ? $price_func( $min_product, array( 'price' => ( $min_product->get_price() - $discount ) ) ) : false );
						$max  = ( $max_product ? $price_func( $max_product, array( 'price' => ( $max_product->get_price() - $discount ) ) ) : false );
						break;
					default: // 'percent'
						$coef = 1.0 - ( $discount / 100.0 );
						$min  = $min * $coef;
						$max  = $max * $coef;
						break;
				}
				$price                = $this->format_price_range( $min,                 $max,                 $do_hide_currency );
				$price_original       = $this->format_price_range( $min_original,        $max_original,        $do_hide_currency );
				$price_total          = $this->format_price_range( $min          * $qty, $max          * $qty, $do_hide_currency );
				$price_total_original = $this->format_price_range( $min_original * $qty, $max_original * $qty, $do_hide_currency );

			} else {

				// Simple etc.
				$_price          = $price_func( $product );
				$_price_original = $_price;
				switch ( $discount_type ) {
					case 'price_directly':
						$_price = $price_func( $product, array( 'price' => $discount ) );
						break;
					case 'fixed':
						$_price = $price_func( $product, array( 'price' => ( $product->get_price() - $discount ) ) );
						break;
					default: // 'percent'
						$coef   = 1.0 - ( $discount / 100.0 );
						$_price = $_price * $coef;
						break;
				}
				$price                = $this->format_price( $_price,                 $do_hide_currency );
				$price_original       = $this->format_price( $_price_original,        $do_hide_currency );
				$price_total          = $this->format_price( $_price          * $qty, $do_hide_currency );
				$price_total_original = $this->format_price( $_price_original * $qty, $do_hide_currency );

			}

			// Placeholders
			$placeholders = array(
				'%old_price'        . $tax_display . '%' => $price_original,       // deprecated
				'%old_price_single' . $tax_display . '%' => $price_original,
				'%price'            . $tax_display . '%' => $price,                // deprecated
				'%new_price_single' . $tax_display . '%' => $price,
				'%old_price_total'  . $tax_display . '%' => $price_total_original,
				'%new_price_total'  . $tax_display . '%' => $price_total,
			);

			$price_format = str_replace( array_keys( $placeholders ), $placeholders, $price_format );

		}

		return $price_format;

	}

	/**
	 * format_qty.
	 *
	 * @version 3.3.1
	 * @since   3.3.0
	 */
	function format_qty( $qty, $atts ) {
		return ( '' != $atts['qty_thousand_sep'] && is_numeric( $qty ) ? number_format( floatval( $qty ), 0, '.', $atts['qty_thousand_sep'] ) : $qty );
	}

	/**
	 * format_price.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function format_price( $price, $do_hide_currency ) {
		return ( $do_hide_currency ? $price : wc_price( $price ) );
	}

	/**
	 * format_price_range.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function format_price_range( $min, $max, $do_hide_currency ) {
		return ( $min != $max ? sprintf( '%s-%s', $this->format_price( $min, $do_hide_currency ), $this->format_price( $max, $do_hide_currency ) ) :
			$this->format_price( $min, $do_hide_currency ) );
	}

	/**
	 * get_table_html.
	 *
	 * @version 2.8.1
	 * @since   1.0.0
	 */
	function get_table_html( $data, $args = array() ) {
		$args = array_merge( array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		), $args );
		$args['table_class'] = ( '' == $args['table_class'] ) ? '' : ' class="' . $args['table_class'] . '"';
		$args['table_style'] = ( '' == $args['table_style'] ) ? '' : ' style="' . $args['table_style'] . '"';
		$args['row_styles']  = ( '' == $args['row_styles'] )  ? '' : ' style="' . $args['row_styles']  . '"';
		$rows = '';
		foreach( $data as $row_number => $row ) {
			$columns = '';
			foreach( $row as $column_number => $value ) {
				$th_or_td     = ( ( 0 === $row_number && 'horizontal' === $args['table_heading_type'] ) || ( 0 === $column_number && 'vertical' === $args['table_heading_type'] ) ) ?
					'th' : 'td';
				$column_class = ( ! empty( $args['columns_classes'] ) && isset( $args['columns_classes'][ $column_number ] ) ) ?
					' class="' . $args['columns_classes'][ $column_number ] . '"' : '';
				$column_style = ( ! empty( $args['columns_styles'] )  && isset( $args['columns_styles'][ $column_number ] ) )  ?
					' style="' . $args['columns_styles'][ $column_number ]  . '"' : '';
				$columns .= '<' . $th_or_td . $column_class . $column_style . '>';
				$columns .= $value;
				$columns .= '</' . $th_or_td . '>';
			}
			if ( ! empty( $columns ) ) {
				$rows .= '<tr' . $args['row_styles'] . '>' . $columns . '</tr>';
			}
		}
		return ( ! empty( $rows ) ? '<table' . $args['table_class'] . $args['table_style'] . '>' . '<tbody>' . $rows . '</tbody>' . '</table>' : '' );
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Shortcodes();
