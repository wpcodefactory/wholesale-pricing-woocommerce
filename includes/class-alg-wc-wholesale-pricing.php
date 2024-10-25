<?php
/**
 * Product Price by Quantity for WooCommerce - Main Class
 *
 * @version 3.9.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Wholesale_Pricing' ) ) :

final class Alg_WC_Wholesale_Pricing {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_WHOLESALE_PRICING_VERSION;

	/**
	 * core.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	public $core;

	/**
	 * pro.
	 *
	 * @version 3.6.0
	 * @since   3.6.0
	 */
	public $pro;

	/**
	 * @var   Alg_WC_Wholesale_Pricing The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Wholesale_Pricing Instance
	 *
	 * Ensures only one instance of Alg_WC_Wholesale_Pricing is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_Wholesale_Pricing - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Wholesale_Pricing Constructor.
	 *
	 * @version 3.9.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Load libs
		if ( is_admin() ) {
			require_once plugin_dir_path( ALG_WC_WHOLESALE_PRICING_FILE ) . 'vendor/autoload.php';
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'wholesale-pricing-woocommerce-pro.php' === basename( ALG_WC_WHOLESALE_PRICING_FILE ) ) {
			$this->pro = require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-wholesale-pricing-pro.php';
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * localize.
	 *
	 * @version 2.5.0
	 * @since   2.2.4
	 */
	function localize() {
		load_plugin_textdomain(
			'wholesale-pricing-woocommerce',
			false,
			dirname( plugin_basename( ALG_WC_WHOLESALE_PRICING_FILE ) ) . '/langs/'
		);
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 3.5.5
	 * @since   3.5.2
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$files = ( defined( 'ALG_WC_WHOLESALE_PRICING_FILE_FREE' ) ?
				array( ALG_WC_WHOLESALE_PRICING_FILE, ALG_WC_WHOLESALE_PRICING_FILE_FREE ) :
				array( ALG_WC_WHOLESALE_PRICING_FILE )
			);
			foreach ( $files as $file ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $file, true );
			}
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 3.9.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-wholesale-pricing-core.php';
	}

	/**
	 * admin.
	 *
	 * @version 3.9.0
	 * @since   1.1.0
	 */
	function admin() {

		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_WHOLESALE_PRICING_FILE ), array( $this, 'action_links' ) );

		// "Recommendations" page
		$this->add_cross_selling_library();

		// WC Settings tab as WPFactory submenu item
		$this->move_wc_settings_tab_to_wpfactory_menu();

		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

		// Version update
		if ( get_option( 'alg_wc_wholesale_pricing_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}

	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 3.9.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_wholesale_pricing' ) . '">' .
			__( 'Settings', 'wholesale-pricing-woocommerce' ) .
		'</a>';
		if ( 'wholesale-pricing-woocommerce.php' === basename( ALG_WC_WHOLESALE_PRICING_FILE ) ) {
			$custom_links[] = '<a style="color: green; font-weight: bold;" href="https://wpfactory.com/item/wholesale-pricing-woocommerce/">' .
				__( 'Go Pro', 'wholesale-pricing-woocommerce' ) .
			'</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 3.9.0
	 * @since   3.9.0
	 */
	function add_cross_selling_library() {

		if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
			return;
		}

		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_WHOLESALE_PRICING_FILE ) );
		$cross_selling->init();

	}

	/**
	 * move_wc_settings_tab_to_wpfactory_menu.
	 *
	 * @version 3.9.0
	 * @since   3.9.0
	 */
	function move_wc_settings_tab_to_wpfactory_menu() {

		if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
			return;
		}

		$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

		if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
			return;
		}

		$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu( array(
			'wc_settings_tab_id' => 'alg_wc_wholesale_pricing',
			'menu_title'         => __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
			'page_title'         => __( 'Product Price by Quantity', 'wholesale-pricing-woocommerce' ),
		) );

	}

	/**
	 * Add "Product Price by Quantity" settings tab to WooCommerce settings.
	 *
	 * @version 3.9.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-settings-wholesale-pricing.php';
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_updated() {
		update_option( 'alg_wc_wholesale_pricing_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_WHOLESALE_PRICING_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_WHOLESALE_PRICING_FILE ) );
	}

}

endif;
