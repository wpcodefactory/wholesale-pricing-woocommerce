/**
 * alg-wc-wholesale-pricing-price-by-qty-display.js
 *
 * @version 2.4.2
 * @since   1.3.0
 *
 * @author  Algoritmika Ltd.
 */

jQuery( document ).ready( function() {

	/**
	 * vars.
	 *
	 * @version 2.4.2
	 * @since   1.3.0
	 */
	var alg_wc_wh_pr_input_timer;
	var alg_wc_wh_pr_input_timer_interval_ms = alg_wc_wh_pr_pbqd_obj.interval_ms;
	var alg_wc_wh_pr_price_identifier = ( 'variable' === alg_wc_wh_pr_pbqd_obj.product_type && alg_wc_wh_pr_pbqd_obj.is_variable_different_prices ?
		'div.woocommerce-variation-price span.price' : 'p.price' );
	var alg_wc_wh_pr_price_display_by_qty_element_id = 'p.alg-wc-wholesale-pricing-price-display-by-qty';
	var alg_wc_wh_pr_price_display_by_qty_element    = '<p class="alg-wc-wholesale-pricing-price-display-by-qty"></p>';

	/**
	 * alg_wc_wh_pr_insert_element.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function alg_wc_wh_pr_insert_element() {
		switch ( alg_wc_wh_pr_pbqd_obj.position ) {
			case 'before':
				jQuery( alg_wc_wh_pr_price_identifier ).before( alg_wc_wh_pr_price_display_by_qty_element );
				break;
			case 'after':
				jQuery( alg_wc_wh_pr_price_identifier ).after( alg_wc_wh_pr_price_display_by_qty_element );
				break;
		}
	}

	/**
	 * alg_wc_wh_pr_price_by_qty_display.
	 *
	 * @version 2.4.1
	 * @since   1.3.0
	 *
	 * @todo    [next] [!] (dev) `jQuery( '[name="' + element_name + '"]' ).val()` instead of passing `qty` param?
	 */
	function alg_wc_wh_pr_price_by_qty_display( qty ) {
		if ( 'undefined' === typeof qty || null === qty ) {
			return;
		}
		var data = {
			'action'                      : 'alg_wc_wholesale_pricing_price_by_qty_display',
			'alg_wc_wholesale_pricing_qty': qty,
			'alg_wc_wholesale_pricing_id' : ( 'variable' === alg_wc_wh_pr_pbqd_obj.product_type ?
				jQuery( 'input[name="variation_id"]' ).val() : alg_wc_wh_pr_pbqd_obj.product_id ),
		};
		jQuery.ajax( {
			type   : 'POST',
			url    : alg_wc_wh_pr_pbqd_obj.ajax_url,
			data   : data,
			success: function( response ) {
				if ( '' != response ) {
					if ( 'instead' == alg_wc_wh_pr_pbqd_obj.position ) {
						jQuery( alg_wc_wh_pr_price_identifier ).html( response );
					} else {
						if ( ! jQuery( alg_wc_wh_pr_price_display_by_qty_element_id ).length ) {
							alg_wc_wh_pr_insert_element();
						}
						jQuery( alg_wc_wh_pr_price_display_by_qty_element_id ).html( response );
					}
				}
			},
		} );
	}

	/**
	 * alg_wc_wh_pr_price_by_qty_display_run.
	 *
	 * @version 2.4.2
	 * @since   1.3.0
	 *
	 * @todo    [next] [!] (dev) Variation hide: before/after: hide instead of setting it to empty string?
	 * @todo    [next] [!] (dev) use `jQuery( this )` (instead of `jQuery( '[name="' + element_name + '"]' )`) where possible?
	 * @todo    [next] find better solution for `do_force_standard_qty_input`
	 * @todo    [maybe] customizable quantity events: `cut copy paste keyup keydown`
	 * @todo    [maybe] customizable elements (e.g. `quantity_pq_dropdown`)
	 * @todo    [maybe] `setInterval( alg_wc_wh_pr_price_by_qty_display_all, 1000 );`
	 */
	function alg_wc_wh_pr_price_by_qty_display_run() {

		if ( 'instead' != alg_wc_wh_pr_pbqd_obj.position ) {
			alg_wc_wh_pr_insert_element();
		}

		var element_name = ( ! alg_wc_wh_pr_pbqd_obj.do_force_standard_qty_input && jQuery( '[name="quantity_pq_dropdown"]' ).length ? 'quantity_pq_dropdown' : 'quantity' );
		var do_timer     = ( 'quantity' === element_name );

		// Update on init
		alg_wc_wh_pr_price_by_qty_display( jQuery( '[name="' + element_name + '"]' ).val() );

		// Update on change
		if ( do_timer ) {
			// E.g. standard qty input
			jQuery( '[name="' + element_name + '"]' ).on( 'input change', function() {
				clearTimeout( alg_wc_wh_pr_input_timer );
				alg_wc_wh_pr_input_timer = setTimeout( function() {
					alg_wc_wh_pr_price_by_qty_display( jQuery( '[name="' + element_name + '"]' ).val() );
				}, alg_wc_wh_pr_input_timer_interval_ms );
			} );
		} else {
			// E.g. qty dropdown from "Product Quantity for WooCommerce" plugin
			jQuery( '[name="' + element_name + '"]' ).on( 'change', function() {
				alg_wc_wh_pr_price_by_qty_display( jQuery( '[name="' + element_name + '"]' ).val() );
			} );
		}

		// Variation show
		jQuery( document.body ).on( 'show_variation', function() {
			alg_wc_wh_pr_price_by_qty_display( jQuery( '[name="' + element_name + '"]' ).val() );
		} );

		// Variation hide
		jQuery( document.body ).on( 'hide_variation', function() {
			if ( 'instead' == alg_wc_wh_pr_pbqd_obj.position ) {
				jQuery( alg_wc_wh_pr_price_identifier ).html( alg_wc_wh_pr_pbqd_obj.product_price_default );
			} else {
				jQuery( alg_wc_wh_pr_price_display_by_qty_element_id ).html( '' );
			}
		} );

	}

	alg_wc_wh_pr_price_by_qty_display_run();

} );
