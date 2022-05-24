<?php
/**
 * Product Price by Quantity for WooCommerce - Reports Section Settings
 *
 * @version 2.6.4
 * @since   2.6.3
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing_Settings_Reports' ) ) :

class Alg_WC_Wholesale_Pricing_Settings_Reports extends Alg_WC_Wholesale_Pricing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.6.3
	 * @since   2.6.3
	 */
	function __construct() {
		$this->id   = 'reports';
		$this->desc = __( 'Reports', 'wholesale-pricing-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_report.
	 *
	 * @version 2.6.4
	 * @since   2.6.3
	 *
	 * @see     https://developer.wordpress.org/reference/functions/get_terms/
	 * @see     https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
	 *
	 * @todo    [next] (dev) `'orderby' => 'name'`: there is something wrong with `product` ordering then: variations and non-published ordering is wrong for some reason?
	 * @todo    [later] (feature) editor
	 * @todo    [later] (feature) export (and import?), print
	 * @todo    [maybe] (dev) `$header`: better solution?
	 * @todo    [maybe] (dev) remove `#` column?
	 * @todo    [maybe] (dev) `product`: remove `$item->get_status()`?
	 */
	function get_report( $type ) {
		$items = array();
		// Get items
		switch ( $type ) {
			case 'product':
				$types           = array_merge( array_keys( wc_get_product_types() ), array( 'variation' ) );
				$items           = wc_get_products( array( 'limit' => -1, 'type' => $types, 'orderby' => 'ID', 'order' => 'ASC' ) );
				$term_or_product = 'product';
				$get_func        = 'get_post_meta';
				break;
			case 'product_cat':
			case 'product_tag':
				$items           = get_terms( array( 'taxonomy' => $type, 'hide_empty' => false, 'orderby' => 'term_id', 'order' => 'ASC' ) );
				$term_or_product = 'term';
				$get_func        = 'get_term_meta';
				break;
		}
		if ( is_wp_error( $items ) ) {
			return '<p>' . __( 'Something went wrong...', 'wholesale-pricing-woocommerce' ) . '</p>';
		} elseif ( empty( $items ) ) {
			return '<p>' . __( 'No items found.', 'wholesale-pricing-woocommerce' ) . '</p>';
		} else {
			// Items loop
			require_once( 'class-alg-wc-wholesale-pricing-settings-per-item.php' );
			$columns = array( '___#', '__item_id', '__item_title' );
			$data    = array();
			$i       = 1;
			foreach ( $items as $item ) {
				$row = array();
				// Prepare data
				if ( 'term' === $term_or_product ) {
					$item_id   = $item->term_id;
					$title     = $item->name;
					$view_url  = get_term_link( $item_id );
					$edit_url  = admin_url( 'term.php?taxonomy=' . $type . '&tag_ID=' . $item_id . '&post_type=product' );
					$_item_id  = '<a target="_blank" href="' . $edit_url . '">' . $item_id . '</a>';
					$_title    = '<a target="_blank" href="' . $view_url . '">' . $title . '</a>';
				} else {
					$parent_id = $item->get_parent_id();
					$item_id   = $item->get_id();
					$title     = $item->get_name();
					$view_url  = get_permalink( $item_id );
					$edit_url  = admin_url( 'post.php?post=' . ( 0 != $parent_id ? $parent_id : $item_id ) . '&action=edit' );
					$_item_id  = '<a target="_blank" href="' . $edit_url . '">' . $item_id . '</a>';
					$_title    = ( 0 != $parent_id ? ' > ' : '' ) . '<a target="_blank" href="' . $view_url . '">' . $title . '</a>' .
						( 'publish' != ( $status = $item->get_status() ) ? " ({$status})" : '' );
				}
				// Item nr, ID & title
				$row['___#']         = $i;
				$row['__item_id']    = $_item_id;
				$row['__item_title'] = $_title;
				// Item settings loop
				$settings = new Alg_WC_Wholesale_Pricing_Settings_Per_Item();
				foreach ( $settings->get_options( $term_or_product, $item_id ) as $option ) {
					if ( isset( $option['meta_name'] ) ) {
						$key = $option['meta_name'];
						$row[ $key ] = $get_func( $item_id, $key, true );
						// Save column
						if ( ! in_array( $key, $columns ) ) {
							$columns[] = $key;
						}
					}
				}
				$data[] = $row;
				$i++;
			}
			asort( $columns );
			// Table rows
			$table = array();
			foreach ( $data as $row ) {
				$table_row = array();
				foreach ( $columns as $column ) {
					$table_row[] = ( isset( $row[ $column ] ) ? $row[ $column ] : '-' );
				}
				$table[] = '<tr><td>' . implode( '</td><td>', $table_row ) . '</td></tr>';
			}
			// Table header
			$header = '<tr><th>' .
					implode( '</th><th>', array_map( 'ucfirst', str_replace( array( '_alg_wc_wholesale_pricing_', '__', '_' ), array( '', '', ' ' ), $columns ) ) ) .
				'</th></tr>';
			// Final table
			return '<table class="widefat striped">' .
					'<thead>' . $header . '</thead>' .
					'<tbody>' . implode( $table ) . '</tbody>' .
				'</table>';
		}
	}

	/**
	 * add_style.
	 *
	 * @version 2.6.3
	 * @since   2.6.3
	 *
	 * @todo    [maybe] (dev) better styling?
	 */
	function add_style() {
		echo '<style>.widefat td, .widefat th { font-size: 12px; } </style>';
	}

	/**
	 * get_settings.
	 *
	 * @version 2.6.3
	 * @since   2.6.3
	 *
	 * @todo    [maybe] (dev) nonce, `manage_woocommerce`, etc.?
	 */
	function get_settings() {
		add_action( 'admin_footer', array( $this, 'add_style' ) );
		$GLOBALS['hide_save_button'] = true;
		$url  = 'admin.php?page=wc-settings&tab=alg_wc_wholesale_pricing&section=reports&report=';
		$menu = '<p>' . implode( ' | ', array(
				'<a href="' . admin_url( $url . 'product' )     . '">' . __( 'Products', 'wholesale-pricing-woocommerce' )           . '</a>',
				'<a href="' . admin_url( $url . 'product_cat' ) . '">' . __( 'Product categories', 'wholesale-pricing-woocommerce' ) . '</a>',
				'<a href="' . admin_url( $url . 'product_tag' ) . '">' . __( 'Product tags', 'wholesale-pricing-woocommerce' )       . '</a>',
			) ) . '</p>';
		if ( isset( $_REQUEST['report'] ) ) {
			$report = wc_clean( $_REQUEST['report'] );
			$report = $this->get_report( $report );
		} else {
			$report = '';
		}
		return array(
			array(
				'title'    => __( 'Item Settings Reports', 'wholesale-pricing-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_wholesale_pricing_reports_options',
				'desc'     => $menu . $report,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_wholesale_pricing_reports_options',
			),
		);
	}

}

endif;

return new Alg_WC_Wholesale_Pricing_Settings_Reports();
