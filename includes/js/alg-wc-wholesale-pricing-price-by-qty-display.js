/**
 * alg-wc-wholesale-pricing-price-by-qty-display.js
 *
 * @version 3.2.1
 * @since   1.3.0
 *
 * @author  Algoritmika Ltd.
 */

jQuery( document ).ready( function () {

	/**
	 * vars.
	 *
	 * @version 3.2.0
	 * @since   1.3.0
	 */

	var options = alg_wc_wh_pr_pbqd_obj;

	var input_timer;
	var input_timer_interval_ms = options.interval_ms;

	var is_variation     = ( 'variable' === options.product_type && options.is_variable_different_prices );
	var price_identifier = ( is_variation ? options.price_identifier_variation : options.price_identifier );

	var ppq_element_id   = 'p.alg-wc-wholesale-pricing-price-display-by-qty';
	var ppq_element_html = '<p class="alg-wc-wholesale-pricing-price-display-by-qty"></p>';

	/**
	 * insert_element.
	 *
	 * @version 3.2.0
	 * @since   1.3.0
	 */
	function insert_element() {
		switch ( options.position ) {
			case 'before':
				jQuery( price_identifier ).before( ppq_element_html );
				break;
			case 'after':
				jQuery( price_identifier ).after( ppq_element_html );
				break;
		}
	}

	/**
	 * display.
	 *
	 * @version 3.2.0
	 * @since   1.3.0
	 */
	function display( qty ) {

		if ( 'undefined' === typeof qty || null === qty ) {
			return;
		}

		var product_id = ( 'variable' === options.product_type ? jQuery( 'input[name="variation_id"]' ).val() : options.product_id );
		var data = {
			'action'                      : 'alg_wc_wholesale_pricing_price_by_qty_display',
			'alg_wc_wholesale_pricing_qty': qty,
			'alg_wc_wholesale_pricing_id' : product_id,
		};

		jQuery.ajax( {
			type   : 'POST',
			url    : options.ajax_url,
			data   : data,
			success: function ( response ) {
				if ( '' != response ) {
					if ( 'instead' == options.position ) {
						jQuery( price_identifier ).html( response );
					} else {
						if ( ! jQuery( ppq_element_id ).length ) {
							insert_element();
						}
						jQuery( ppq_element_id ).html( response );
					}
				}
			},
		} );

	}

	/**
	 * run.
	 *
	 * @version 3.2.1
	 * @since   1.3.0
	 *
	 * @todo    [next] [!] (dev) Variation hide: before/after: hide instead of setting it to empty string?
	 * @todo    [next] [!] (dev) use `jQuery( this )` (instead of `jQuery( '[name="' + element_name + '"]' )`) where possible?
	 * @todo    [next] find better solution for `do_force_standard_qty_input`
	 * @todo    [maybe] customizable quantity events: `cut copy paste keyup keydown`
	 * @todo    [maybe] customizable elements (e.g., `quantity_pq_dropdown`)
	 * @todo    [maybe] `setInterval( display_all, 1000 );`
	 * @todo    [maybe] (dev) Update on init: `jQuery( selector ).each( function () { display( jQuery( this ).val() ); } );`
	 */
	function run() {

		// Insert element
		if ( 'instead' != options.position ) {
			insert_element();
		}

		// Vars
		var is_pq_dropdown = ( ! options.do_force_standard_qty_input && jQuery( '[name="quantity_pq_dropdown"]' ).length );
		var do_timer       = ( ! is_pq_dropdown && ! options.is_sticky_add_to_cart );
		var selector       = ( is_pq_dropdown ? '[name="quantity_pq_dropdown"]' : '.qty[name="quantity"]' );

		// Update on init
		display( jQuery( selector ).val() );

		// Update on change
		if ( do_timer ) {

			// E.g., standard qty input
			jQuery( selector ).on( 'input change', function () {
				clearTimeout( input_timer );
				input_timer = setTimeout( function () {
					display( jQuery( selector ).val() );
				}, input_timer_interval_ms );
			} );

		} else {

			// E.g., qty dropdown from "Product Quantity for WooCommerce" plugin
			jQuery( selector ).on( 'input change', function () {
				display( jQuery( this ).val() );
			} );

			// "Sticky Add To Cart Bar For WooCommerce" plugin
			if ( options.is_sticky_add_to_cart ) {
				jQuery( '.wsc-input-group' ).on( 'click', '.wsc-button-plus', function () {
					display( jQuery( this ).closest( '.wsc-input-group' ).find( '.wsc-quantity-field' ).val() );
				} );
				jQuery( '.wsc-input-group' ).on( 'click', '.wsc-button-minus', function () {
					display( jQuery( this ).closest( '.wsc-input-group' ).find( '.wsc-quantity-field' ).val() );
				} );
			}

		}

		// Variation show
		jQuery( document.body ).on( 'show_variation', function () {
			display( jQuery( selector ).val() );
		} );

		// Variation hide
		jQuery( document.body ).on( 'hide_variation', function () {
			if ( 'instead' == options.position ) {
				jQuery( price_identifier ).html( options.product_price_default );
			} else {
				jQuery( ppq_element_id ).html( '' );
			}
		} );

	}

	run();

} );
