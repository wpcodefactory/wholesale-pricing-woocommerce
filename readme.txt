=== Wholesale Pricing for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, wholesale pricing, wholesale, buy more pay less, buy more pay more, woo commerce
Requires at least: 4.4
Tested up to: 5.9
Stable tag: 2.8.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set WooCommerce wholesale pricing depending on product quantity in cart.

== Description ==

**Wholesale Pricing for WooCommerce** plugin lets you set WooCommerce product pricing rules, when product price depends on product **quantity in cart**.

### &#9989; Main Features ###

* You can implement popular **"buy more pay less"** pricing, as well as less common **"buy more pay more"** pricing.
* Pricing rules can be set **for all products**, or on **per product basis**.
* You can use **total cart quantity** or **product quantity**.
* Optionally you can set to apply wholesale discount only **if no other cart discounts were applied**.
* Discounts can be set as **percent** from the original price, **fixed** discount, or set **price directly** on per product basis.
* Additionally you can set different wholesale pricing options for different **user roles**.
* If you want to display prices table on frontend, use `[alg_wc_wholesale_pricing_table]` and/or `[alg_wc_product_wholesale_pricing_table]` **shortcodes**.
* The plugin also has option to **display price by quantity in real time**, i.e. when customer changes product quantity on single product page.
* You can also optionally use **formulas** and **custom product fields** in level "Min quantity" and "Discount" options.
* And more...

### &#127942; Premium Version ###

With [Wholesale Pricing for WooCommerce Pro](https://wpfactory.com/item/wholesale-pricing-woocommerce/) you can:

* Set pricing rules per **product category**, per **product tag** or per **product variation**.
* Automatically display **discount pricing table on single product pages**.
* Replace standard quantity input on frontend with a **dropdown**.
* And more...

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/wholesale-pricing-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Wholesale Pricing".

== Changelog ==

= 2.8.2 - 21/04/2022 =
* Dev - Info - "Cart & Checkout Totals" options added.
* Dev - Info - Admin settings descriptions updated.
* Dev - Info - Code refactoring.
* Dev - Developers - `alg_wc_product_wholesale_pricing` filter added.

= 2.8.1 - 19/04/2022 =
* Fix - Info - Cart Page - Item subtotal - "Undefined property: Alg_WC_Wholesale_Pricing_Pro_Frontend::$core" notice fixed.
* Fix - Shortcodes - `[alg_wc_wholesale_pricing_products_list]` - "Undefined property: Alg_WC_Wholesale_Pricing_Pro_Shortcodes::$core" notice fixed.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_table]` - attributes added:
    * `hide_table_if_guest`,
    * `hide_table_for_user_roles`,
    * `show_table_for_user_roles`,
    * `hide_table_if_out_of_stock`,
    * `hide_if_insufficient_quantity`.
* Dev - Code refactoring.

= 2.8.0 - 18/04/2022 =
* Fix - Per product - Per variation - Now ignoring if wholesale pricing is enabled for the main variable product.
* Fix - Shortcodes - `[alg_wc_product_wholesale_pricing_table]` - `add_discount_row` fixed.
* Dev - Info - Discount Pricing Table - Single product page - "Position" (and "Priority") options added.
* Dev - Info - Discount Pricing Table - "Shop page" options added.
* Dev - All Products - Advanced - "Required/Excluded products" options now use AJAX. Separate settings section removed.
* Dev - General - "All products > Enable" option is now duplicated in "General > Settings".
* Dev - General - "Discount type" option moved to the "All Products" section.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_table]`
    * `add_discount_row` - Showing value for non-fixed discount types now.
    * `add_percent_row` - Showing value for non-percent discount types now.
    * `add_percent_row_rounded` attribute added (defaults to `no`).
    * `heading_format` - `%level_discount%`, `%level_discount_amount%`, `%level_discount_percent%`, `%level_discount_percent_rounded%` placeholders added.
    * `table_style` attribute added.
    * Developers - `alg_wc_product_wholesale_pricing_table_heading_format` filter added.
* Dev - Code refactoring.
* WC tested up to: 6.4.
* Tested up to: 5.9.

= 2.7.0 - 10/12/2021 =
* Dev - Info - Replace Price - Now outputting unchanged (original) product price, if the (shortcode) result is empty.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_table]` - `before` and `after` attributes added.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_data]` - `before` and `after` attributes added.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_data]` - `use_variation` attribute added (defaults to `no`).
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_table]` - `before` and `after` attributes added.
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_data]` - `before` and `after` attributes added.
* Dev - Developers - `alg_wc_wholesale_pricing_info_single_product_page_hook` and `alg_wc_wholesale_pricing_info_single_product_page_priority` filters added.
* WC tested up to: 5.9

= 2.6.5 - 27/10/2021 =
* Dev - Price Display by Quantity - "Apply to all products" option added (defaults to `yes`).
* WC tested up to: 5.8.

= 2.6.4 - 24/09/2021 =
* Fix - Reports - Ensuring `Alg_WC_Wholesale_Pricing_Settings_Per_Item` class is available now.

= 2.6.3 - 22/09/2021 =
* Dev - "Reports" section added.
* Dev - All admin settings input is properly sanitized now.
* Dev - Admin settings restyled; descriptions updated.
* WC tested up to: 5.7.

= 2.6.2 - 09/08/2021 =
* Dev - General - "Admin recalculate order" options added.
* Dev - Admin settings rearranged ("General > Compatibility Options" subsection added).

= 2.6.1 - 08/08/2021 =
* Dev - Allowing "Price directly" to be "0" now.
* Dev - Price Display by Qty - Better algorithm for choosing when to use "no discount" template.
* Dev - Admin settings descriptions updated ("Advanced" section).
* Dev - Code refactoring - `get_discount_by_quantity()`.

= 2.6.0 - 27/07/2021 =
* Fix - Shortcodes - `get_product_price()` - Variable - "Price directly". Affects `[alg_wc_product_wholesale_pricing_table]` and `[alg_wc_product_wholesale_pricing_data]` shortcodes.
* Dev - Tools - "Delete per product settings" and "Delete per term settings" tools added.
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_products_list]` - Shortcode rewritten. `heading_format`, `row_sku`, `row_name`, `row_category`, `row_price`, `link_rows`, `custom_columns` attributes removed. `columns` attribute added.
* Dev - Developers - `alg_wc_wholesale_pricing_get_levels_data` filter added.
* Dev - Developers - `alg_wc_wholesale_pricing_dropdown_levels_data` filter added.
* Dev - Developers - `alg_wc_wh_pr_get_discount_type` filter renamed to `alg_wc_wholesale_pricing_get_discount_type`.
* Dev - Developers - `alg_wc_wh_pr_ajax_price_display_by_qty` filter renamed to `alg_wc_wholesale_pricing_ajax_price_display_by_qty`.
* Dev - Developers - `alg_wc_wh_pr_dropdown_input_value` filter renamed to `alg_wc_wholesale_pricing_dropdown_input_value`.
* Dev - Developers - `alg_wc_wh_pr_dropdown_option_label` filter renamed to `alg_wc_wholesale_pricing_dropdown_option_label`.
* Dev - Developers - `alg_wc_wh_pr_dropdown_option_style` filter renamed to `alg_wc_wholesale_pricing_dropdown_option_style`.
* Dev - Developers - `alg_wc_wh_pr_dropdown_option_class` filter renamed to `alg_wc_wholesale_pricing_dropdown_option_class`.
* Dev - Code refactoring.
* Tested up to: 5.8.
* WC tested up to: 5.5.

= 2.5.0 - 30/06/2021 =
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_products_list]` - `custom_columns` attribute added.
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_products_list]` - `link_rows` attribute added (defaults to `price,levels`).
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_products_list]` - `use_transients` attribute added (defaults to `no`).
* Dev - Shortcodes - `get_product_price()` - Variable - Safe-checks added.
* Dev - Developers - `alg_wc_wh_pr_get_discount_type` filter added.
* Dev - Developers - `alg_wc_wh_pr_dropdown_input_value` filter added.
* Dev - Developers - `alg_wc_wh_pr_dropdown_option_label` filter added.
* Dev - Developers - `alg_wc_wh_pr_dropdown_option_style` filter added.
* Dev - Developers - `alg_wc_wh_pr_dropdown_option_class` filter added.
* Dev - Developers - `alg_wc_wh_pr_ajax_price_display_by_qty` filter added.
* Dev - Plugin is initialized on the `plugins_loaded` action now.
* Dev - Code refactoring.
* WC tested up to: 5.4.

= 2.4.2 - 14/05/2021 =
* Dev - Price Display by Qty - Variation hide event handler added.
* Dev - Price Display by Qty - Variation change (`table.variations select` on `change`) event handler removed.
* Dev - Dropdown - "Filter values" option added.
* Dev - Dropdown - "HTML before" and "HTML after" options added.
* Dev - Dropdown - Wrapping select element in `div` now.
* Dev - Settings - Dropdown options moved to a separate settings section. Section descriptions updated.
* WC tested up to: 5.3.

= 2.4.1 - 06/05/2021 =
* Fix - Dropdown - Prices in cart page dropdown fixed.
* Fix - Dropdown - Unnecessary `1` quantity value removed from dropdown.
* Dev - Dropdown - Variable products support added.
* Dev - Price Display by Qty - JS code refactoring.
* Dev - Price Display by Qty - `show_variation` JS event added.

= 2.4.0 - 30/04/2021 =
* Dev - General - "Dropdown" options added.

= 2.3.0 - 13/04/2021 =
* Dev - General - Quantity calculation - "Group by product parent (e.g. for variations)" option added.
* Dev - General - Quantity calculation - "Group by product category" option added.
* Dev - General - Quantity calculation - "Group by product tag" option added.
* Dev - General - "Total cart quantity" option redone as select (was checkbox); renamed to "Quantity calculation".
* Dev - Info - Cart Page - `%qty_total%` placeholder added.
* Dev - Code refactoring.
* WC tested up to: 5.2.

= 2.2.5 - 23/03/2021 =
* Dev - "WCFM plugin compatibility" options added.
* Dev - Code refactoring.
* Tested up to: 5.7.
* WC tested up to: 5.1.

= 2.2.4 - 02/03/2021 =
* Dev - Info - Discount Pricing Table - "Hide main variable table on visible variation" option added.
* Dev - Info - Discount Pricing Table - "Template for variable products" option added.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_data]` - Min level quantity is now passed to the `price` function, so now `%old_price_total%` and `%new_price_total%` placeholders are correctly processed in the `price_format` attribute.
* Dev - Localization - `load_plugin_textdomain()` function moved to the `init` action.
* Dev - Settings - Restyled and descriptions updated, e.g. "Info > Single Product Page" renamed to "Info > Discount Pricing Table", etc.
* WC tested up to: 5.0.

= 2.2.3 - 29/12/2020 =
* Dev - Price Display by Quantity - "Force standard quantity input" option added.

= 2.2.2 - 22/12/2020 =
* Fix - Settings - All Products - "Undefined property: Alg_WC_Wholesale_Pricing_Core::$do_process_formula..." notice fixed.
* Dev - Info - "Replace Price" options section added.
* WC tested up to: 4.8.
* Tested up to: 5.6.

= 2.2.1 - 25/10/2020 =
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_table]` - Initializing all variables now.

= 2.2.0 - 25/10/2020 =
* Dev - Price Display by Quantity - `quantity_pq_dropdown` - Timer removed (also for `table.variations select`); `input` event removed; getting value from the element itself now (i.e. instead of from the original `quantity` field); standard qty input event handler removed.
* Dev - Shortcodes - `[alg_wc_product_wholesale_pricing_table]` - New attributes added: `add_total_min_qty_price_row` and `total_min_qty_price_row_format`; `extra_row_before` and `extra_row_after`; `table_heading_type`; `columns_styles`; `table_class`.
* Dev - Info - Single product pages - Template for non-variable products - Admin settings description updated.
* WC tested up to: 4.6.

= 2.1.0 - 09/10/2020 =
* Dev - Info - Single Product Page - "Template" options added.
* Dev - "Lumise - Product Designer Tool" plugin compatibility added.
* Dev - `alg_wc_wholesale_pricing_get_item_product_id` filter added.
* Dev - `alg_wc_wholesale_pricing_calculate_totals_product_id` filter added.

= 2.0.0 - 15/09/2020 =
* Dev - Free version max levels num limitation removed.
* Dev - General - "Rounding" option added (defaults to `yes`).
* Dev - General - Settings - "Enable per product category" and "Enable per product tag" options added.
* Dev - General - Settings - "Per variation" option added.
* Dev - General - Settings - Per product - "Price directly" option available in variable products as well now.
* Dev - General - Settings - Per product - "Reset all", "Copy variation" and "Reset variation" actions added to product settings.
* Dev - Info - "Single Product Page" subsection added.
* Dev - Info - Cart Page - Default message value added to translations.
* Dev - Info - Cart Page - Message placeholders renamed; default message value updated.
* Dev - Info - Cart Page - `%qty%`, `%old_price_total%`, `%new_price_total%`, `%discount_percent%`, `%discount_single%`, `%discount_total%` placeholders added.
* Dev - Info - Cart Page - "Item subtotal" options added.
* Dev - All Products - "Enable" option added.
* Dev - User Roles - "Enable" option added.
* Dev - Price Display by Quantity - Select event added (for the variable products).
* Dev - Price Display by Quantity - `change` event added for the quantity input.
* Dev - Price Display by Quantity - `quantity_pq_dropdown` events handler added.
* Dev - Price Display by Quantity - Message placeholders renamed; default message values updated.
* Dev - Price Display by Quantity - `%discount_percent%`, `%discount_single%` and `%discount_total%` placeholders added.
* Dev - Price Display by Quantity - "Variable products > Display in variation price" option added (defaults to `yes`).
* Dev - Shortcodes - `[alg_wc_wholesale_pricing_products_list]` shortcode added.
* Dev - Shortcodes - Placeholders renamed.
* Dev - Shortcodes - Handling products with empty price properly now.
* Dev - All input is sanitized now.
* Dev - JS files minified.
* Dev - Admin settings restyled; descriptions updated; "Info" section added; "Discount Table" section renamed to "All Products".
* Dev - Code refactoring.
* Tested up to: 5.5.
* WC tested up to: 4.5.

= 1.4.1 - 19/06/2020 =
* Fix - Advanced - Products to exclude - Bug fixed.
* Dev - Price Display by Quantity - Checking if wholesale pricing is enabled for the product now (e.g. "Products to include/exclude" option).
* Dev - Advanced - Products to include/exclude - Product ID info added to the dropdown.
* Dev - Reset Settings - Description updated.
* Tested up to: 5.4.
* WC tested up to: 4.2.

= 1.4.0 - 19/03/2020 =
* Dev - General - "Process formula and shortcodes" option added.
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.

= 1.3.0 - 27/12/2019 =
* Dev - "Price Display by Quantity" options added.
* Dev - Admin settings restyled; "Advanced" section added.
* Dev - Code refactoring.
* Tested up to: 5.3.

= 1.2.0 - 06/11/2019 =
* Dev - Additional safeguards added when getting product ID.
* Dev - Admin settings split into sections.
* Dev - Code refactoring.
* WC tested up to: 3.8.

= 1.1.2 - 23/09/2019 =
* Dev - `[alg_wc_product_wholesale_pricing_data]` shortcode added.
* Dev - `[alg_wc_wholesale_pricing_data]` shortcode added.
* Dev - Code refactoring.
* Dev - Admin Settings - "Your settings have been reset" notice added.
* WC tested up to: 3.7.

= 1.1.1 - 07/06/2019 =
* Tested up to: 5.2.
* WC tested up to: 3.6.

= 1.1.0 - 31/10/2018 =
* Dev - "Raw" value is now allowed in "Discount info on cart page format" option.
* Dev - Admin settings restyled.
* Dev - Code refactoring.
* Dev - Plugin URI updated.

= 1.0.0 - 13/08/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.