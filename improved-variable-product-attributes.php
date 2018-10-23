<?php
/*
Plugin Name: Improved Variable Product Attributes for WooCommerce
Plugin URI: https://www.mihajlovicnenad.com/improved-variable-product-attributes
Description: Improved Variable Product Attributes for WooCommerce! - mihajlovicnenad.com
Author: پرشین اسکریت
Version: 4.4.0
Requires at least: 4.5
Tested up to: 4.9.8
WC requires at least: 3.0.0
WC tested up to: 3.4.4
Author URI: http://www.persianscript.ir
Text Domain: ivpawoo
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['svx'] = isset( $GLOBALS['svx'] ) && version_compare( $GLOBALS['svx'], '1.0.7') == 1 ? $GLOBALS['svx'] : '1.0.7';

if ( !class_exists( 'WC_Improved_Variable_Product_Attributes_Init' ) ) :

	final class WC_Improved_Variable_Product_Attributes_Init {

		public static $version = '4.4.0';

		protected static $_instance = null;

		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			do_action( 'wcmnivpa_loading' );

			$this->includes();

			$this->init_hooks();

			do_action( 'wcmnivpa_loaded' );
		}

		private function init_hooks() {
			register_activation_hook( __FILE__, array( $this, '_ivpa_flush_cache' ) );
			register_deactivation_hook( __FILE__, array( $this, '_ivpa_flush_cache' ) );
			add_action( 'plugins_loaded', array( $this, '_ivpa_flush_cache_on_update' ) );

			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'init', array( $this, 'load_svx' ), 100 );
			add_action( 'plugins_loaded', array( $this, 'fix_svx' ), 100 );
		}

		public function fix_svx() {
			include_once ( 'includes/svx-settings/svx-fixoptions.php' );
		}

		public function load_svx() {
			if ( $this->is_request( 'admin' ) ) {
				include_once ( 'includes/svx-settings/svx-settings.php' );
			}
		}


		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		public function includes() {

			if ( $this->is_request( 'admin' ) ) {

				include_once ( 'includes/ivpa-settings.php' );

			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}

		}

		public function frontend_includes() {
			include_once( 'includes/ivpa-frontend.php' );
		}

		public function init() {

			do_action( 'before_wcmnivpa_init' );

			$this->load_plugin_textdomain();

			do_action( 'after_wcmnivpa_init' );

		}

		public function load_plugin_textdomain() {

			$domain = 'ivpawoo';
			$dir = untrailingslashit( WP_LANG_DIR );
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			}
			else {
				load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
			}

		}

		public function _ivpa_flush_cache_on_update() {

			$version = self::$version;
			$transient = get_transient( '_ivpa_version' );

			if ( $transient === false ) {
				set_transient( '_ivpa_version', $version );
			}
			else if ( version_compare( $transient, $version, '<' ) ) {
				global $wpdb;
				$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta WHERE meta.meta_key LIKE '_ivpa_cached_%';" );
				set_transient( '_ivpa_version', $version );
			}

		}

		public function _ivpa_flush_cache() {

			global $wpdb;
			$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta WHERE meta.meta_key LIKE '_ivpa_cached_%';" );

		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function plugin_basename() {
			return untrailingslashit( plugin_basename( __FILE__ ) );
		}

		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		public static function version_check( $version = '3.0.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if( version_compare( $woocommerce->version, $version, ">=" ) ) {
					return true;
				}
			}
			return false;
		}

		public function version() {
			return self::$version;
		}

	}

	add_filter( 'svx_plugins', 'svx_improved_options_add_plugin', 20 );
	add_filter( 'svx_plugins_settings_short', 'svx_improved_options_add_short' );

	function svx_improved_options_add_plugin( $plugins ) {

		$plugins['improved_options'] = array(
			'slug' => 'improved_options',
			'name' => esc_html__( 'Improved Options', 'ivpawoo' )
		);

		return $plugins;

	}
	function svx_improved_options_add_short( $plugins ) {

		$plugins['improved_options'] = array(
			'slug' => 'improved_options',
			'settings' => array(

				'wc_settings_ivpa_single_enable' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_single_selectbox' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_single_addtocart' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_single_desc' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_single_image' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_single_ajax' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_single_action' => array(
					'autoload' => true,
				),
				'wc_settings_ivpa_archive_enable' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_archive_quantity' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_archive_mode' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_archive_align' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_archive_action' => array(
					'autoload' => true,
				),
				'wc_settings_ivpa_single_selector' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_archive_selector' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_addcart_selector' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_price_selector' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_outofstock_mode' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_image_attributes' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_simple_support' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_step_selection' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_disable_unclick' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_backorder_support' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_force_scripts' => array(
					'autoload' => false,
				),
				'wc_settings_ivpa_use_caching' => array(
					'autoload' => false,
				),

				'wc_ivpa_attribute_customization' => array(
					'autoload' => false,
					'translate' => true
				),

			)
		);
		return $plugins;
	}


	function Wcmnivpa() {
		return WC_Improved_Variable_Product_Attributes_Init::instance();
	}

	WC_Improved_Variable_Product_Attributes_Init::instance();

endif;

?>