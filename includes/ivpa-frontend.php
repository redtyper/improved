<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	class WC_Improved_Variable_Product_Attributes {

		public static $dir;
		public static $path;
		public static $url_path;
		public static $settings;
		public static $version;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			if ( !class_exists( 'Woocommerce' ) ) {
				return;
			}

			self::$version = WC_Improved_Variable_Product_Attributes_Init::$version;

			self::$dir = Wcmnivpa()->plugin_path();
			self::$path = Wcmnivpa()->plugin_path();
			self::$url_path = Wcmnivpa()->plugin_url();

			self::$settings['single_action'] = '';
			self::$settings['archive_action'] = '';

			self::$settings['wc_settings_ivpa_single_enable'] = get_option( 'wc_settings_ivpa_single_enable', 'yes' );
			self::$settings['wc_settings_ivpa_single_selectbox'] = get_option( 'wc_settings_ivpa_single_selectbox', 'yes' );
			self::$settings['wc_settings_ivpa_archive_enable'] = get_option( 'wc_settings_ivpa_archive_enable', 'no' );
			self::$settings['wc_settings_ivpa_archive_quantity'] = get_option( 'wc_settings_ivpa_archive_quantity', 'no' );
			self::$settings['wc_settings_ivpa_archive_mode'] = get_option( 'wc_settings_ivpa_archive_mode', 'ivpa_selection' );
			self::$settings['wc_settings_ivpa_single_ajax'] = get_option( 'wc_settings_ivpa_single_ajax', 'no' );
			self::$settings['wc_settings_ivpa_archive_image_size'] = get_option( 'wc_settings_ivpa_archive_image_size', 'full' );
			self::$settings['wc_settings_ivpa_outofstock_mode'] = get_option( 'wc_settings_ivpa_outofstock_mode', 'default' );
			self::$settings['wc_settings_ivpa_force_scripts'] = get_option( 'wc_settings_ivpa_force_scripts', 'no' );
			self::$settings['wc_settings_ivpa_use_caching'] = get_option( 'wc_settings_ivpa_use_caching', 'no' );
			self::$settings['wc_settings_ivpa_single_image_size'] = get_option( 'wc_settings_ivpa_single_image_size', 'full' );
			self::$settings['wc_settings_ivpa_disable_unclick'] = get_option( 'wc_settings_ivpa_disable_unclick', 'no' );
			self::$settings['wc_settings_ivpa_single_image'] = get_option( 'wc_settings_ivpa_single_image', 'yes' );
			self::$settings['wc_settings_ivpa_image_attributes'] = get_option( 'wc_settings_ivpa_image_attributes', array() );
			self::$settings['wc_settings_ivpa_step_selection'] = get_option( 'wc_settings_ivpa_step_selection', 'no' );
			self::$settings['wc_settings_ivpa_single_addtocart'] = get_option( 'wc_settings_ivpa_single_addtocart', 'yes' );
			self::$settings['wc_settings_ivpa_single_desc'] = get_option( 'wc_settings_ivpa_single_desc', 'ivpa_afterattribute' );
			self::$settings['wc_settings_ivpa_backorder_support'] = get_option( 'wc_settings_ivpa_backorder_support', 'no' );
			self::$settings['wc_settings_ivpa_archive_align'] = get_option( 'wc_settings_ivpa_archive_align', 'ivpa_align_left' );
			self::$settings['wc_settings_ivpa_simple_support'] = get_option( 'wc_settings_ivpa_simple_support', 'none' );

			if ( isset( $_REQUEST['ivpa_page'] ) && $_REQUEST['ivpa_page'] == 'stepped' ) {
				self::$settings['wc_settings_ivpa_step_selection'] = 'yes';
			}

			self::$settings['wc_settings_ivpa_single_selector'] = get_option( 'wc_settings_ivpa_single_selector', '' );

			self::$settings['wc_settings_ivpa_archive_selector'] = get_option( 'wc_settings_ivpa_archive_selector', '' );
			if ( self::$settings['wc_settings_ivpa_archive_selector'] == '' ) {
				self::$settings['wc_settings_ivpa_archive_selector'] = '.type-product';
			}
			self::$settings['wc_settings_ivpa_addcart_selector'] = get_option( 'wc_settings_ivpa_addcart_selector', '' );
			if ( self::$settings['wc_settings_ivpa_addcart_selector'] == '' ) {
				self::$settings['wc_settings_ivpa_addcart_selector'] = '.add_to_cart_button';
			}
			self::$settings['wc_settings_ivpa_price_selector'] = get_option( 'wc_settings_ivpa_price_selector', '' );
			if ( self::$settings['wc_settings_ivpa_price_selector'] == '' ) {
				self::$settings['wc_settings_ivpa_price_selector'] = '.price';
			}

			self::$settings['wc_settings_ivpa_single_action'] = get_option( 'wc_settings_ivpa_single_action', '' );
			if ( self::$settings['wc_settings_ivpa_single_action'] == '' ) {
				self::$settings['wc_settings_ivpa_single_action'] = 'woocommerce_before_add_to_cart_button';
			}

			if ( self::$settings['wc_settings_ivpa_single_enable'] == 'yes' ) {
				if ( strpos( self::$settings['wc_settings_ivpa_single_action'], ':' ) > 0 ) {
					$explode = explode( ':', self::$settings['wc_settings_ivpa_single_action'] );
					$curr_action = array(
						'action' => $explode[0],
						'priority' => intval( $explode[1] ) > -1 ? intval( $explode[1] ) : 10
					);
				}
				else {
					$curr_action = array(
						'action' => self::$settings['wc_settings_ivpa_single_action'],
						'priority' => 10
					);
				}

				self::$settings['single_action'] = $curr_action['action'];
				add_action( $curr_action['action'], array(&$this, 'ivpa_attributes'), $curr_action['priority'] );

				add_action( 'ivpa_get_single_options', array(&$this, 'ivpa_attributes') );
			}

			self::$settings['wc_settings_ivpa_archive_action'] = get_option( 'wc_settings_ivpa_archive_action', '' );
			if ( self::$settings['wc_settings_ivpa_archive_action'] == '' ) {
				self::$settings['wc_settings_ivpa_archive_action'] = 'woocommerce_after_shop_loop_item:999';
			}

			if ( self::$settings['wc_settings_ivpa_archive_enable'] == 'yes' ) {
				if ( strpos( self::$settings['wc_settings_ivpa_archive_action'], ':' ) > 0 ) {
					$explode = explode( ':', self::$settings['wc_settings_ivpa_archive_action'] );
					$curr_action = array(
						'action' => $explode[0],
						'priority' => intval( $explode[1] ) > -1 ? intval( $explode[1] ) : 10
					);
				}
				else {
					$curr_action = array(
						'action' => self::$settings['wc_settings_ivpa_archive_action'],
						'priority' => 10
					);
				}

				self::$settings['archive_action'] = $curr_action['action'];
				add_action( $curr_action['action'], array(&$this, 'ivpa_attributes'), $curr_action['priority'] );

				add_action( 'ivpa_get_loop_options', array(&$this, 'ivpa_attributes') );
			}

			add_action( 'wp_enqueue_scripts', array(&$this, 'ivpa_scripts') );
			add_action( 'wp_footer', array( &$this, 'footer_actions' ) );

			if ( self::$settings['wc_settings_ivpa_archive_enable'] == 'yes' || self::$settings['wc_settings_ivpa_single_ajax'] == 'yes' ) {
				add_action( 'woocommerce_add_to_cart' , array(&$this, 'ivpa_repair_cart') );
				add_action( 'wp_ajax_nopriv_ivpa_add_to_cart_callback', array(&$this, 'ivpa_add_to_cart_callback') );
				add_action( 'wp_ajax_ivpa_add_to_cart_callback', array(&$this, 'ivpa_add_to_cart_callback') );
			}

			add_filter( 'woocommerce_add_cart_item_data', array( &$this, 'add_customs_item_data' ), 10, 3 );
			add_filter( 'woocommerce_add_cart_item', array( &$this, 'add_customs_item' ), 10, 3 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'add_customs_from_session' ), 10, 3 );
			add_filter( 'woocommerce_get_item_data', array( &$this, 'add_customs_get_item_data' ), 10, 2 );

			add_action( 'woocommerce_new_order_item', array( &$this, 'add_custom_meta' ), 10, 3 );

			add_filter( 'mnthemes_add_meta_information_used', array( &$this, 'ivpa_info' ) );

		}

		function ivpa_info( $val ) {
			$val = array_merge( $val, array( 'Improved Variable Product Attributes for WooCommerce' ) );
			return $val;
		}

		public static function ivpa_get_path() {
			return self::$path;
		}

		public static function get_custom() {

			if ( !isset( self::$settings['custom'] ) ) {

				$curr_language = self::ivpa_wpml_language();

				if ( $curr_language === false ) {
					$curr_customizations = get_option( 'wc_ivpa_attribute_customization', '' );
				}
				else {
					$curr_customizations = get_option( 'wc_ivpa_attribute_customization_' . $curr_language, '' );
				}

				if ( $curr_customizations == '' ) {
					$curr_customizations = array( 'ivpa_attr' => array() );
				}

				self::$settings['custom'] = $curr_customizations;

				return $curr_customizations;

			}
			else {

				return self::$settings['custom'];

			}

		}

		function add_custom_meta( $item_id, $item, $order_id ) {

			if ( is_object( $item ) ) {
				if ( !property_exists( $item, 'legacy_values' ) ) {
					return;
				}
				$values = $item->legacy_values;
			}
			else {
				$values = $item;
			}

			if ( isset( $values['ivpac'] ) && is_array( $values['ivpac'] ) ) {

				$curr_customizations = self::get_custom();

				foreach ( $values['ivpac'] as $k => $v ) {
					$v = is_array( $v ) ? implode( ', ', $v ) : $v;
					if ( isset( $curr_customizations['ivpa_attr'][$k] ) ) {
						if ( $curr_customizations['ivpa_attr'][$k] == 'ivpa_custom' ) {
							wc_add_order_item_meta( $item_id, $curr_customizations['ivpa_title'][$k], $v );
						}
						else {
							wc_add_order_item_meta( $item_id, wc_attribute_label( $curr_customizations['ivpa_attr'][$k] ), $v );
						}
					}
					else {
						wc_add_order_item_meta( $item_id, wc_attribute_label( $k ), $v );
					}
				}

			}

		}

		function add_customs_get_item_data( $item_data, $cart_item ) {

			if ( isset( $cart_item['ivpac'] ) && is_array( $cart_item['ivpac'] ) ) {

				$curr_customizations = self::get_custom();

				foreach( $cart_item['ivpac'] as $k => $v ) {
					$v = is_array( $v ) ? implode( ', ', $v ) : $v;
					if ( isset( $curr_customizations['ivpa_attr'][$k] ) ) {
						if ( $curr_customizations['ivpa_attr'][$k] == 'ivpa_custom' ) {
							$item_data[] = array( 'key' => $curr_customizations['ivpa_title'][$k], 'value' => $v );
						}
						else {
							$item_data[] = array( 'key' => wc_attribute_label( $curr_customizations['ivpa_attr'][$k] ), 'value' => $v );
						}
					}
					else {
						$item_data[] = array( 'key' => wc_attribute_label( $k ), 'value' => $v );
					}
				}
			}

			return $item_data;

		}

		function add_customs_from_session( $session_data, $values, $key ) {

			if ( isset( $values['ivpac'] ) ) {
				$session_data['ivpac'] = $values['ivpac'];
				$session_data = self::add_customs_item( $session_data, $key );
			}

			return $session_data;

		}

		function add_customs_item_data( $product_data, $product_id ) {
			if ( isset( $_REQUEST['ivpac'] ) ) {
				wp_parse_str( $_REQUEST['ivpac'], $vars );
				if ( is_array( $vars ) ) {
					foreach ( $vars as $k => $v ) {
						if ( substr( $k, 0, 6 ) == 'ivpac_' && !empty( $v ) ) {
							if ( is_array( $v ) ) {
								$v = array_filter( $v );
							}
							if ( !empty( $v ) ) {
								$product_data['ivpac'][substr( $k, 6 )] = $v;
							}
						}
					}
				}
				if ( isset( $_REQUEST['variation'] ) && is_array( $_REQUEST['variation'] ) ) {
					$_product = wc_get_product( $product_id );
					if( !$_product->is_type( 'variable' ) ) {
						foreach( $_REQUEST['variation'] as $k => $v ) {
							if ( substr( $k, 0, 10 ) == 'attribute_' && !empty( $v ) ) {
								if ( is_array( $v ) ) {
									$v = array_filter( $v );
								}
								if ( !empty( $v ) ) {
									$product_data['ivpac'][wc_attribute_label( substr( $k, 10 ) )] = $v;
								}
							}
						}
					}
				}
			}
			else {
				foreach ( $_REQUEST as $k => $v ) {
					if ( substr( $k, 0, 6 ) == 'ivpac_' && !empty( $v ) ) {
						if ( is_array( $v ) ) {
							$v = array_filter( $v );
						}
						if ( !empty( $v ) ) {
							$product_data['ivpac'][substr( $k, 6 )] = $v;
						}
					}
					else if ( substr( $k, 0, 10 ) == 'attribute_' && !empty( $v ) ) {
						if ( !isset( $_product ) ) {
							$_product = wc_get_product( $product_id );
						}
						if ( !$_product->is_type( 'variable' ) ) {
							if ( substr( $k, 0, 10 ) == 'attribute_' && !empty( $v ) ) {
								if ( is_array( $v ) ) {
									$v = array_filter( $v );
								}
								if ( !empty( $v ) ) {
									$product_data['ivpac'][wc_attribute_label( substr( $k, 10 ) )] = $v;
								}
							}
						}
					}
				}
				/*if ( isset( $_REQUEST['variation'] ) && is_array( $_REQUEST['variation'] ) ) {
					$_product = wc_get_product( $product_id );
					if( !$_product->is_type( 'variable' ) ) {
						foreach( $_REQUEST['variation'] as $k => $v ) {
							if ( substr( $k, 0, 10 ) == 'attribute_' && !empty( $v ) ) {
								if ( is_array( $v ) ) {
									$v = array_filter( $v );
								}
								if ( !empty( $v ) ) {
									$product_data['ivpac'][wc_attribute_label( substr( $k, 10 ) )] = $v;
								}
							}
						}
					}
				}*/
			}
			return $product_data;
		}

		function add_customs_item( $product_data, $cart_item_key ) {

			if ( isset( $product_data['ivpac'] ) && is_array( $product_data['ivpac'] ) ) {

				$curr_customizations = self::get_custom();

				foreach( $product_data['ivpac'] as $k => $v ) {
					$v = is_array( $v ) ? $v : array( $v => $v );
					if ( isset( $curr_customizations['ivpa_attr'][$k] ) && $curr_customizations['ivpa_attr'][$k] == 'ivpa_custom' ) {
						if ( isset( $curr_customizations['ivpa_addprice'][$k] ) && $curr_customizations['ivpa_addprice'][$k] !== '' ) {
							self::add_price( $product_data, $curr_customizations['ivpa_addprice'][$k] );
						}
						foreach( $v as $k1 ) {
							$customPrices = strpos( $k1, ', ' ) > 0 ? explode( ', ', $k1 ) : array( $k1 => $k1 );

							foreach( $customPrices as $k2 ) {
								if ( isset( $curr_customizations['ivpa_price'][$k][$k2] ) && $curr_customizations['ivpa_price'][$k][$k2] !== '' ) {
									self::add_price( $product_data, $curr_customizations['ivpa_price'][$k][$k2] );
								}
							}
						}
					}
				}

			}

			return $product_data;

		}

		public static function add_price( $product_data, $price ) {
			if ( method_exists( $product_data['data'], 'set_price' ) ) {
				$product_data['data']->set_price( $product_data['data']->get_price() + $price );
			}
			else {
				$product_data['data']->adjust_price( $price );
			}
		}

		function ivpa_scripts() {

			//wp_enqueue_style( 'ivpa-style', self::$url_path . '/assets/css/style' . ( is_rtl() ? '-rtl' : '' ) . '.css', false, self::$version );
			wp_enqueue_style( 'ivpa-style', self::$url_path . '/assets/css/style' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', false, self::$version );

			wp_register_script( 'ivpa-scripts', self::$url_path .'/assets/js/scripts.js', array( 'jquery', 'hoverIntent', 'wp-util' ), self::$version, true );
			wp_enqueue_script( 'ivpa-scripts' );

		}

		function footer_actions() {

			global $ivpa_global;

			if ( !isset( $ivpa_global['init'] ) && self::$settings['wc_settings_ivpa_force_scripts'] == 'no' ) {

				wp_dequeue_script( 'ivpa-scripts' );

			}
			else {

				global $_wp_additional_image_sizes;

				$sizes = array();

				foreach( $_wp_additional_image_sizes as $_size_key => $_size ) {

					if ( in_array( $_size_key, array( 'shop_catalog', 'shop_single', 'shop_thumbnail' ) ) && isset( $_wp_additional_image_sizes[ $_size_key ] ) ) {

						$sizes[ $_size_key ] = array(
							'getimg' => '-' . $_wp_additional_image_sizes[ $_size_key ]['width'] . 'x' . $_wp_additional_image_sizes[ $_size_key ]['height'],
							'width' => $_wp_additional_image_sizes[ $_size_key ]['width'],
							'height' => $_wp_additional_image_sizes[ $_size_key ]['height'],
							'crop' =>  $_wp_additional_image_sizes[ $_size_key ]['crop']
						);
					}

				}

				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'outofstock' => self::$settings['wc_settings_ivpa_outofstock_mode'],
					'disableunclick' => self::$settings['wc_settings_ivpa_disable_unclick'],
					'imageswitch' => self::$settings['wc_settings_ivpa_single_image'],
					'imageattributes' => self::$settings['wc_settings_ivpa_image_attributes'],
					'stepped' => self::$settings['wc_settings_ivpa_step_selection'],
					'backorders' => self::$settings['wc_settings_ivpa_backorder_support'],
					'singleajax' => self::$settings['wc_settings_ivpa_single_ajax'],
					'archiveajax' => get_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' ),
					'settings' => array(
						'single_selector' => self::$settings['wc_settings_ivpa_single_selector'],
						'archive_selector' => self::$settings['wc_settings_ivpa_archive_selector'],
						'addcart_selector' => self::$settings['wc_settings_ivpa_addcart_selector'],
						'price_selector' => self::$settings['wc_settings_ivpa_price_selector']
					),
					'localization' => array(
						'select' => esc_html__( 'Select', 'ivpawoo' ),
						'simple' => ( isset ( $ivpa_global['simple'] ) ? $ivpa_global['simple'] : esc_html__( 'Add to cart', 'ivpawoo' ) ),
						'variable' => ( isset ( $ivpa_global['variable'] ) ? $ivpa_global['variable'] : esc_html__( 'Select options', 'ivpawoo' ) )
					),
					'add_to_cart' => apply_filters( 'wc_add_to_cart_params', array(
						'ajax'                    => admin_url( 'admin-ajax.php' ),
						'ajax_url'                => WC()->ajax_url(),
						'ajax_loader_url'         => apply_filters( 'woocommerce_ajax_loader_url', '' ),
						'i18n_view_cart'          => esc_attr__( 'View Cart', 'ivpawoo' ),
						'cart_url'                => get_permalink( wc_get_page_id( 'cart' ) ),
						'is_cart'                 => is_cart(),
						'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' )
					) ),
					'imagesizes' => $sizes
				);

				wp_localize_script( 'ivpa-scripts', 'ivpa', $curr_args );
				self::ivpa_single_styles();
				self::ivpa_archive_styles();

			}

		}

		public static function ivpa_get_attributes() {
			$attributes = get_object_taxonomies( 'product' );
			$ready_attributes = array();

			if ( !empty( $attributes ) ) {

				foreach( $attributes as $k ) {

					if ( substr($k, 0, 3) == 'pa_' ) {
						$ready_attributes[] = $k;
					}

				}

			}

			return $ready_attributes;
		}

		function ivpa_single_styles() {

				$return = '';

				if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'clickable' ) {
					$return .= '.ivpa_term.ivpa_outofstock {cursor:pointer!important;}';
				}
				else if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'hidden' ) {
					$return .= '.ivpa_term.ivpa_outofstock {display:none!important;}';
				}
				if ( self::$settings['wc_settings_ivpa_single_enable'] == 'yes' && self::$settings['wc_settings_ivpa_single_selectbox'] == 'no' ) {
					$return .= '.variations_form .variations {display:block!important;}';
				}
				if ( self::$settings['wc_settings_ivpa_single_addtocart'] == 'yes' ) {
					$return .= '.woocommerce-variation-add-to-cart-disabled .quantity, .woocommerce-variation-add-to-cart-disabled .single_add_to_cart_button {display:none!important;}';
				}

				if ( !empty( $return ) ) {
					echo '<style type="text/css">' . $return . '</style>';
				}

		}

		function ivpa_archive_styles() {
			global $ivpa_global;

			if ( !isset( $ivpa_global['init'] ) ) {
				return;
			}

			$curr_language = self::ivpa_wpml_language();

			if ( $curr_language === false ) {
				$curr_customizations = get_option( 'wc_ivpa_attribute_customization', '' );
			}
			else {
				$curr_customizations = get_option( 'wc_ivpa_attribute_customization_' . $curr_language, '' );
			}

			if ( $curr_customizations == '' ) {
				$curr_customizations = array( 'ivpa_attr' => array() );
			}

			$curr_attributes = self::ivpa_get_attributes();

?>
	<style type="text/css">
<?php
			if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'clickable' ) {
?>
				.ivpa_term.ivpa_outofstock {
					cursor:pointer!important;
				}
<?php
			}
			else if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'hidden' ) {
?>
				.ivpa_term.ivpa_outofstock {
					display:none!important;
				}
<?php
			}
			$ready_customization = array();
			$keep_customization = array();

			foreach( $curr_attributes as $k ) {
				if ( isset( $curr_customizations['ivpa_attr'] ) && is_array( $curr_customizations['ivpa_attr'] ) && in_array( $k, $curr_customizations['ivpa_attr'] ) ) {
					$ready_customization[array_search($k, $curr_customizations['ivpa_attr'])] = $k;
				}
				else {
					$keep_customization[$k] = $k;
				}
			}

			ksort( $ready_customization );

			$ready_customization = $ready_customization + $keep_customization;

			$possible_customs = array();
			if ( isset( $curr_customizations['ivpa_attr'] ) ) {
				foreach ( $curr_customizations['ivpa_attr'] as $k => $possibleCustom ) {
					if ( $possibleCustom == 'ivpa_custom' ) {
						$possible_customs[$k] = $curr_customizations['ivpa_title'][$k];
					}
				}
			}
			$ready_customization = $ready_customization + $possible_customs;

			foreach ( $ready_customization as $k => $v ) {

				$v = sanitize_title( $v );

				$curr['attr'] = ( isset($curr_customizations['ivpa_attr'][$k]) ? $curr_customizations['ivpa_attr'][$k] : '' );
				$curr['style'] = ( isset($curr_customizations['ivpa_style'][$k]) ? $curr_customizations['ivpa_style'][$k] : 'ivpa_text' );
				/*$curr['title'] = ( isset($curr_customizations['ivpa_title'][$k]) ? $curr_customizations['ivpa_title'][$k] : '' );
				$curr['desc'] = ( isset($curr_customizations['ivpa_desc'][$k]) ? stripslashes( $curr_customizations['ivpa_desc'][$k] ) : '' );*/

				$curr['custom'] = ( isset($curr_customizations['ivpa_custom'][$k]) ? $curr_customizations['ivpa_custom'][$k] : array( 'style' => 'ivpa_background', 'normal' => '#bbbbbb', 'active' => '#333333', 'disabled' => '#e45050', 'outofstock' => '#e45050' ) );

				if ( $curr['style'] == 'ivpa_text' ) {
					$str = ( $curr['attr'] == 'ivpa_custom' ? 'ivpa_custom_option' : 'ivpa_attribute' );
					switch ( $curr['custom']['style'] ) {

						case 'ivpa_border' :
					?>
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active {
								border-color:<?php echo $curr['custom']['normal']; ?>;
								color:<?php echo $curr['custom']['normal']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked,
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
								border-color:<?php echo $curr['custom']['active']; ?>;
								color:<?php echo $curr['custom']['active']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_disabled {
								border-color:<?php echo $curr['custom']['disabled']; ?>;
								color:<?php echo $curr['custom']['disabled']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_outofstock {
								border-color:<?php echo $curr['custom']['outofstock']; ?>;
								color:<?php echo $curr['custom']['outofstock']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>.ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_outofstock:after {
								background-image:url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20xml%3Aspace%3D%22preserve%22%20width%3D%2232px%22%20height%3D%2232px%22%20style%3D%22shape-rendering%3AgeometricPrecision%3B%20text-rendering%3AgeometricPrecision%3B%20image-rendering%3AoptimizeQuality%3B%20fill-rule%3Aevenodd%3B%20clip-rule%3Aevenodd%22%20viewBox%3D%220%200%201181%201181%22%20preserveAspectRatio%3D%22none%22%20xmlns%3Axlink%3D%22http%3A//www.w3.org/1999/xlink%22%3E%0A%3Cdefs%3E%0A%09%3Cstyle%20type%3D%22text/css%22%3E%0A%09%3C%21%5BCDATA%5B%0A%09%09.fil0%20%7Bfill%3A%23<?php echo substr( $curr['custom']['outofstock'], 1 ); ?>%3Bfill-rule%3Anonzero%3Bpaint-order%3Astroke%3Bstroke%3A%23<?php echo substr( $curr['custom']['outofstock'], 1 ); ?>%3Bstroke-width%3A5%3Bstroke-linecap%3Abutt%3Bstroke-linejoin%3Amiter%3B%7D%0A%09%5D%5D%3E%0A%09%3C/style%3E%0A%3C/defs%3E%0A%3Cg%3E%0A%09%3Cpolygon%20class%3D%22fil0%22%20points%3D%221175.11%2C1181%20590.5%2C596.391%205.89083%2C1181%20-0%2C1181%20-0%2C1175.11%20584.609%2C590.5%20-0%2C5.89083%20-0%2C0%205.89083%2C0%20590.5%2C584.609%201175.11%2C0%201181%2C0%201181%2C5.89083%20596.391%2C590.5%201181%2C1175.11%201181%2C1181%20%22/%3E%0A%3C/g%3E%0A%3C/svg%3E');
							}
					<?php
						break;


						case 'ivpa_background' :
						case 'ivpa_round' :
					?>
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active {
								background-color:<?php echo $curr['custom']['normal']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked,
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
								background-color:<?php echo $curr['custom']['active']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_disabled {
								background-color:<?php echo $curr['custom']['disabled']; ?>;
							}
							.ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_outofstock {
								background-color:<?php echo $curr['custom']['outofstock']; ?>;
							}
					<?php

						break;

					}

				}

			}

?>
	</style>
<?php

		}

		function utf8_urldecode($str) {
			$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
			return html_entity_decode($str,null,'UTF-8');
		}

		public static function init_globals() {

			global $ivpa_global;

			$ivpa_global['init'] = true;

			$ivpa_product = new WC_Product_Simple( get_the_ID() );
			$ivpa_global['simple'] = esc_html( $ivpa_product->add_to_cart_text() );

			$ivpa_product = new WC_Product_Variable( get_the_ID() );
			$ivpa_global['variable'] = esc_html( $ivpa_product->add_to_cart_text() );

		}

		public static function get_loop() {

			$curr_action_filter = current_filter();

			if ( $curr_action_filter == self::$settings['archive_action'] ) {
				$curr_is_loop = 'loop';
			}
			else if ( $curr_action_filter == self::$settings['single_action'] ) {
				$curr_is_loop = 'single';
			}
			else if ( $curr_action_filter == 'ivpa_get_loop_options' ) {
				$curr_is_loop = 'loop';
			}
			else if ( $curr_action_filter == 'ivpa_get_single_options' ) {
				$curr_is_loop = 'single';
			}
			else {
				$curr_is_loop = 'loop';
			}

			return $curr_is_loop;

		}

		public static function check_visibility( $n, $opt ) {

			$type = isset( $opt['ivpa_limit_type'][$n] ) && !empty( $opt['ivpa_limit_type'][$n] ) ? $opt['ivpa_limit_type'][$n] : '';
			$category = isset( $opt['ivpa_limit_category'][$n] ) && !empty( $opt['ivpa_limit_category'][$n] ) ? $opt['ivpa_limit_category'][$n] : '';
			$products = isset( $opt['ivpa_limit_product'][$n] ) && !empty( $opt['ivpa_limit_product'][$n] ) ? $opt['ivpa_limit_product'][$n] : '';

			if ( empty( $type ) && empty( $category ) && empty( $products ) ) {
				return true;
			}

			global $product;

			$check = strpos( $type, '|' ) ? explode( '|', $type ) : array( $type );
			foreach( $check as $k => $v ) {
				if ( $product->is_type( $v ) ) {
					return true;
				}
			}

			$check = strpos( $category, '|' ) ? explode( '|', $category ) : array( $category );
			$productCat = $product->get_category_ids();
			if ( isset( $productCat[0] ) ) {
				if ( in_array( $productCat[0], $check ) ) {
					return true;
				}

			}

			$check = strpos( $products, '|' ) ? explode( '|', $products ) : array( $products );
			if ( in_array( get_the_ID(), $check ) ) {
				return true;
			}

			return false;

		}

		public static function fix_custom_terms( $terms, $order ) {
			$return = array();

			foreach( $terms as $k => $v ) {
				if ( array_search($v->slug,array_keys($order)) ) {
					$return[array_search($v->slug,array_keys($order))] = $v;
				}
				else {
					$return[] = $v;
				}
			}

			ksort( $return,SORT_REGULAR );
			return $return;
		}

		function ivpa_attributes() {

			global $product, $ivpa_global;

			if ( $product->is_type( apply_filters( 'ivpa_disallow_products_types', array( 'external', 'grouped' ) ) ) ) {
				return '';
			}

			if ( !isset( $ivpa_global['init'] ) ){
				self::init_globals();
			}

			$curr_is_loop = self::get_loop();

			if ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_selection' && !$product->is_in_stock() ) {
				self::$settings['wc_settings_ivpa_archive_mode'] = 'ivpa_showonly';
				$wasInit = true;
			}

			$cached_html = '';
			if ( empty( $_REQUEST ) && self::$settings['wc_settings_ivpa_use_caching'] == 'yes' ) {

				$cached_html = get_post_meta( get_the_ID(), '_ivpa_cached_' . $curr_is_loop . '_' . get_locale(), true );
			}

			if ( !empty( $cached_html ) ) {

				$available_variations = get_post_meta( get_the_ID(), '_ivpa_cached_data', true );

				echo str_replace( '%%%JSON_REPLACE_IVPA%%%', esc_attr( json_encode( $available_variations ) ), $cached_html );

			}
			else {
				if ( self::$settings['wc_settings_ivpa_simple_support'] == 'none' && !$product->is_type( 'variable' ) ) {
					return '';
				}

				$available_attributes = $product->get_attributes();

				$available_variations = array();
				$curr_attributes = array();
				$selected_attributes = array();

				if ( $product->is_type( 'variable' ) ) {

					foreach ( $product->get_children() as $child_id ) {
						$variation = wc_get_product( $child_id );

						if ( empty( $variation ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
							continue;
						}

						if ( apply_filters( 'woocommerce_hide_invisible_variations', false, get_the_ID(), $variation ) && ! $variation->variation_is_visible() ) {
							continue;
						}

						if ( has_post_thumbnail( $variation->get_id() ) ) {
							$attachment_id   = get_post_thumbnail_id( $variation->get_id() );
							$attachment      = wp_get_attachment_image_src( $attachment_id, 'full' );
							$variation_image = str_replace( array( 'http:', 'https:' ), '', $attachment[0] );
						}
						else {
							$variation_image = '';
						}

						$availability_html = '';
						$backorder_required = $variation->backorders_require_notification();
						if ( $curr_is_loop == 'loop' && $backorder_required === true ) {
							$availability      = $variation->get_availability();
							$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . wp_kses_post( $availability['availability'] ) . '</p>';
							$availability_html = apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $variation );
						}

						$available_variations[] = array(
							'variation_id'          => $variation->get_id(),
							'attributes'            => $variation->get_variation_attributes(),
							'price_html'            => apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === "" || $product->get_variation_price( 'min' ) !== $product->get_variation_price( 'max' ), $product, $variation ) ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
							'is_in_stock'           => $variation->is_in_stock(),
							'ivpa_image'            => $variation_image,
							'backorders_allowed'    => $backorder_required,
							'availability_html'     => $availability_html
						);

					}

					$curr_attributes = $product->get_variation_attributes();

					if ( WC_Improved_Variable_Product_Attributes_Init::version_check() ) {
						$selected_attributes = $product->get_default_attributes();
					}
					else {
						$selected_attributes = $product->get_variation_default_attributes();
					}

				}

				$curr_customizations = self::get_custom();

				if ( !isset( $curr_customizations['ivpa_attr'] ) || !is_array( $curr_customizations['ivpa_attr'] ) ) {
					return '';
				}

				$avlb_atts = array();
				foreach ( $available_attributes as $k => $v ) {
					$avlb_atts[] = $k;
				}

				$cstm_atts = array();
				
				foreach( $curr_customizations['ivpa_attr'] as $k => $v ) {
					if ( $v == 'ivpa_custom' ) {
						$check = self::check_visibility( $k, $curr_customizations );
						if ( $check === true ) {
							$cstm_atts[] = array(
								'key' => $k,
								'custom' => isset( $curr_customizations['ivpa_title'][$k] ) ? sanitize_title( $curr_customizations['ivpa_title'][$k]) : 'ivpa_custom'
							);
						}
					}
					else {
						if ( in_array( $v, $avlb_atts ) ) {
							if ( array_key_exists( $v, $available_attributes ) && $available_attributes[$v]['variation'] ) {
								$cstm_atts[] = array(
									'key' => $k,
									'custom' => $v
								);
							}
							else if ( self::$settings['wc_settings_ivpa_simple_support'] !== 'none' && array_key_exists( $v, $available_attributes ) && isset( $curr_customizations['ivpa_svariation'][$k] ) && $curr_customizations['ivpa_svariation'][$k] == 'yes' ) {
								$cstm_atts[] = array(
									'key' => $k,
									'custom' => $v
								);
							}
							unset($avlb_atts[array_search($v, $avlb_atts)]);
						}
					}
				}

				foreach( $avlb_atts as $k => $v ) {
					if ( array_key_exists( $v, $available_attributes ) && $available_attributes[$v]['variation'] ) {
						$cstm_atts[] = array(
							'key' => false,
							'custom' => $v
						);
					}
					else if ( self::$settings['wc_settings_ivpa_simple_support'] !== 'none' && array_key_exists( $v, $available_attributes ) && isset( $curr_customizations['ivpa_svariation'][$k] ) && $curr_customizations['ivpa_svariation'][$k] == 'yes' ) {
						$cstm_atts[] = array(
							'key' => false,
							'custom' => $v
						);
					}
				}

				if ( empty( $cstm_atts ) ) {
					return '';
				}

				if ( $curr_is_loop == 'single' ) {
					$curr_add_elements = 'id="ivpa-content" class="ivpa-register' . ( self::$settings['wc_settings_ivpa_step_selection'] == 'yes' ? ' ivpa-stepped' : '' ) . ( self::$settings['wc_settings_ivpa_disable_unclick'] == 'yes' ? ' ivpa-disableunclick' : '' ) . '"';

					if ( $product->is_type( 'variable' ) ) {
						$curr_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
						if ( !empty( $curr_thumb[0] ) ) {
							$curr_add_elements .= ' data-image="' . str_replace( array( 'http:', 'https:' ), '', $curr_thumb[0] ) . '"';
						}
					}
				}
				else {
					$curr_add_elements = 'class="ivpa-content ivpa-register' . ( self::$settings['wc_settings_ivpa_step_selection'] == 'yes' ? ' ivpa-stepped' : '' ) . ( self::$settings['wc_settings_ivpa_disable_unclick'] == 'yes' ? ' ivpa-disableunclick' : '' ) . ' ' . self::$settings['wc_settings_ivpa_archive_align'] . '"';
					$curr_add_elements .= ' data-url="' . get_permalink() . '"';
					if ( $product->is_type( 'variable' ) && has_post_thumbnail() ) {
						$curr_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
						if ( !empty( $curr_thumb[0] ) ) {
							$curr_add_elements .= ' data-image="' . str_replace( array( 'http:', 'https:' ), '', $curr_thumb[0] ) . '"';
						}
					}
				}

				$curr_add_elements .= ' data-id="' . get_the_ID() . '"';

				ob_start();

			?>
				<div <?php echo $curr_add_elements; ?> data-variations="<?php echo '%%%JSON_REPLACE_IVPA%%%'; ?>"<?php echo ( self::ivpa_wpml_language() !== false ? ' data-lang="' . ICL_LANGUAGE_CODE . '"' : '' ); ?> data-type="<?php echo esc_attr( $product->get_type() ); ?>">
					<div class="ivpa-hidden-price">
					<?php
						$price_html = $product->get_price_html();
						wc_get_template( 'loop/price.php' );
					?>
					</div>
			<?php

				$style = '';
				$willCustom = false;

				foreach ( $cstm_atts as $k => $v ) {

					if ( is_array( $v ) ) {
						$willCustom = $v['custom'] == 'ivpa_custom' ? true : false;
						$k = $v['key'];
						$v = $v['custom'];
					}
					else {
						return '';
					}

					$curr = self::__get_customization( $k, $curr_customizations );

					if ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' && isset( $curr['archive_include'] ) && $curr['archive_include'] == 'no' ) {
						continue;
					}

					$curr_term_sanitized = self::utf8_urldecode( $v );
					$v = sanitize_title( $v );

					if ( $curr['style'] == 'ivpa_text' ) {
						if ( isset( $_REQUEST['ivpa_page'] ) && in_array( $_REQUEST['ivpa_page'], array( 'selectbox' ) ) ) {
							$curr['style'] = 'ivpa_' . $_REQUEST['ivpa_page'];
						}
						if ( isset( $_REQUEST['ivpa_page'] ) && in_array( $_REQUEST['ivpa_page'], array( 'border', 'round' ) ) ) {
							$curr['custom']['style'] = 'ivpa_' . $_REQUEST['ivpa_page'];
						}
					}

					$curr['terms'] = array();
					if ( taxonomy_exists( $curr_term_sanitized ) ) {
						$curr['terms'] = wc_get_product_terms( get_the_ID(), $curr_term_sanitized, array( 'fields' => 'all') );
					}
					else {
						if ( isset( $available_attributes[$v] ) ) {
							$custom_vals = array_map( 'trim', explode( WC_DELIMITER, $available_attributes[$v]['value'] ) );
							foreach ( $custom_vals as $cv ) {
								$curr['terms'][$cv] = new stdClass();
								$curr['terms'][$cv]->name = ucfirst( $cv );
								$curr['terms'][$cv]->slug = $cv;
							}
						}
						else if ( $curr['name'] !== '' && is_array( $curr['name'] ) ) {
							foreach ( $curr['name'] as $cv ) {
								$curr['terms'][$cv] = new stdClass();
								$curr['terms'][$cv]->name = $cv;
								$curr['terms'][$cv]->slug = sanitize_title( $cv );
							}
						}
						else {
							//continue;
						}
					}

					if ( $curr['order'] == 'true' ) {
						$curr['terms'] = self::fix_custom_terms( $curr['terms'], $curr['custom'] );
					}

					if ( $curr_is_loop == 'single' && $curr['style'] == 'ivpa_text' ) {
						$str = ( $curr['attr'] == 'ivpa_custom' || $willCustom === true ? 'ivpa_custom_option' : 'ivpa_attribute' );
						if ( !isset( $curr['custom']['style'] ) || !in_array( $curr['custom']['style'], array( 'ivpa_border', 'ivpa_background', 'ivpa_round' ) ) ) {
							$curr['custom']['style'] = 'ivpa_background';
						}
						switch ( $curr['custom']['style'] ) {

							case 'ivpa_border' :
								ob_start();
						?>
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active {
									border-color:<?php echo $curr['custom']['normal']; ?>;
									color:<?php echo $curr['custom']['normal']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked,
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
									border-color:<?php echo $curr['custom']['active']; ?>;
									color:<?php echo $curr['custom']['active']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_disabled {
									border-color:<?php echo $curr['custom']['disabled']; ?>;
									color:<?php echo $curr['custom']['disabled']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_outofstock {
									border-color:<?php echo $curr['custom']['outofstock']; ?>;
									color:<?php echo $curr['custom']['outofstock']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>.ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_outofstock:after {
									background-image:url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20xml%3Aspace%3D%22preserve%22%20width%3D%2232px%22%20height%3D%2232px%22%20style%3D%22shape-rendering%3AgeometricPrecision%3B%20text-rendering%3AgeometricPrecision%3B%20image-rendering%3AoptimizeQuality%3B%20fill-rule%3Aevenodd%3B%20clip-rule%3Aevenodd%22%20viewBox%3D%220%200%201181%201181%22%20preserveAspectRatio%3D%22none%22%20xmlns%3Axlink%3D%22http%3A//www.w3.org/1999/xlink%22%3E%0A%3Cdefs%3E%0A%09%3Cstyle%20type%3D%22text/css%22%3E%0A%09%3C%21%5BCDATA%5B%0A%09%09.fil0%20%7Bfill%3A%23<?php echo substr( $curr['custom']['outofstock'], 1 ); ?>%3Bfill-rule%3Anonzero%3Bpaint-order%3Astroke%3Bstroke%3A%23<?php echo substr( $curr['custom']['outofstock'], 1 ); ?>%3Bstroke-width%3A5%3Bstroke-linecap%3Abutt%3Bstroke-linejoin%3Amiter%3B%7D%0A%09%5D%5D%3E%0A%09%3C/style%3E%0A%3C/defs%3E%0A%3Cg%3E%0A%09%3Cpolygon%20class%3D%22fil0%22%20points%3D%221175.11%2C1181%20590.5%2C596.391%205.89083%2C1181%20-0%2C1181%20-0%2C1175.11%20584.609%2C590.5%20-0%2C5.89083%20-0%2C0%205.89083%2C0%20590.5%2C584.609%201175.11%2C0%201181%2C0%201181%2C5.89083%20596.391%2C590.5%201181%2C1175.11%201181%2C1181%20%22/%3E%0A%3C/g%3E%0A%3C/svg%3E');
								}
						<?php
								$style .= ob_get_clean();
							break;


							case 'ivpa_background' :
							case 'ivpa_round' :
								ob_start();
						?>
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active {
									background-color:<?php echo $curr['custom']['normal']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked,
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
									background-color:<?php echo $curr['custom']['active']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_disabled {
									background-color:<?php echo $curr['custom']['disabled']; ?>;
								}
								#ivpa-content .<?php echo $str; ?>[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_outofstock {
									background-color:<?php echo $curr['custom']['outofstock']; ?>;
								}
						<?php
								$style .= ob_get_clean();
							break;

						}
					}


					$var_class = null;
					if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
						if ( ( $curr['attr'] == 'ivpa_custom' || $willCustom === true ) === true && in_array( $curr['style'], array( 'ivpa_text', 'ivpa_color', 'ivpa_image', 'ivpa_html', 'ivpa_selectbox' ) ) ) {
							$var_class = 'ivpa-do';
						}
						else if ( $curr['attr'] !== 'ivpa_custom' ) {
							if ( $curr_is_loop == 'single' && !$product->is_type( 'variable' ) ) {
								$var_class = 'ivpa-do';
							}
						}
					}

				?>
					<div class="ivpa-opt <?php echo $curr['attr'] == 'ivpa_custom' || $willCustom === true ? 'ivpa_custom_option' : 'ivpa_attribute'; ?> <?php echo $curr['style'] . ' ' . ( $curr['style'] == 'ivpa_text' ? $curr['custom']['style'] : '' ) . ( $curr_is_loop == 'loop' ? ' ' . self::$settings['wc_settings_ivpa_archive_mode'] : '' ); ?><?php echo $curr['attr'] == 'ivpa_custom' && in_array( $curr['style'], array( 'ivpa_text', 'ivpa_color', 'ivpa_image', 'ivpa_html', 'ivpa_selectbox', 'ivpac_checkbox' ) ) && isset( $curr['multiselect'] ) && $curr['multiselect'] == 'yes' ? ' ivpa_multiselect' : ''; ?> <?php echo isset( $var_class ) ? $var_class : '' ; ?>" data-attribute="<?php echo $v; ?>"<?php echo isset( $curr['required'] ) && $curr['required'] == 'yes' || $product->get_type() == 'variable' && $curr['attr'] !== 'ivpa_custom' ? ' data-required="yes"': ''; ?>>
					<?php
						if ( $curr_is_loop == 'single' && $curr['style'] == 'ivpa_selectbox' ) {
						?>
							<strong class="ivpa_title ivpa_selectbox_title">
								<?php
									if ( $curr['title'] == '' ) {
										echo wc_attribute_label( $curr_term_sanitized );
									}
									else {
										echo stripslashes( $curr['title'] );
									}
									echo isset( $curr['required'] ) && $curr['required'] == 'yes' || $product->get_type() == 'variable' && $curr['attr'] !== 'ivpa_custom' ? apply_filters( 'ivpa_required_field_content', ' <span class="ivpa-required">REQ</span>' ) : '';
								?>
							</strong>
						<?php
							if ( self::$settings['wc_settings_ivpa_single_desc'] == 'ivpa_aftertitle' && $curr_is_loop == 'single' ) {
								if ( $curr['desc'] !== '' ) {
									self::__get_desc( $curr, $curr_is_loop );
								}
							}
						}

						if ( $curr['style'] == 'ivpa_selectbox' ) {
						?>
							<div class="ivpa_select_wrapper">
								<div class="ivpa_select_wrapper_inner">
						<?php
						}

						global $prdctfltr_global;
						if ( $curr_is_loop == 'single' && isset( $_REQUEST[ 'attribute_' . sanitize_title( $v ) ] ) ) {
							$selected_attr = $_REQUEST[ 'attribute_' . sanitize_title( $v ) ];
						}
						else if ( $curr_is_loop == 'loop' && ( isset( $_REQUEST[ sanitize_title( $v ) ] ) || isset( $prdctfltr_global['active_filters'][sanitize_title( $v )][0] ) || isset( $prdctfltr_global['active_permalinks'][sanitize_title( $v )][0] ) ) ) {
							$selected_attr = isset( $_REQUEST[ sanitize_title( $v ) ] ) ? $_REQUEST[ sanitize_title( $v ) ] : ( isset( $prdctfltr_global['active_filters'][sanitize_title( $v )][0] ) ? $prdctfltr_global['active_filters'][sanitize_title( $v )][0] : $prdctfltr_global['active_permalinks'][sanitize_title( $v )][0] );
						}
						else if ( isset( $selected_attributes[ sanitize_title( $v ) ] ) ) {
							$selected_attr = $selected_attributes[ sanitize_title( $v ) ];
						}
						else {
							$selected_attr = '';
						}

						if ( $curr_is_loop == 'single' || $curr['style'] == 'ivpa_selectbox' ) {
						?>
							<strong class="ivpa_title">
								<?php
									if ( $curr['style'] == 'ivpa_selectbox' ) {
										if ( $selected_attr == '' ) {
											if ( $curr_is_loop == 'single' ) {
												esc_html_e( 'Select', 'ivpawoo' );
											}
											else {
												if ( $curr['title'] == '' ) {
													echo wc_attribute_label( $curr_term_sanitized );
												}
												else {
													echo $curr['title'];
												}
											}
										}
										else {
											$term = get_term_by( 'slug', $selected_attr, sanitize_title( $v ) );
											if ( !empty( $term ) ) {
												echo $term->name;
											}
										}
									}
									else {
										if ( $curr['title'] == '' ) {
											echo wc_attribute_label( $curr_term_sanitized );
										}
										else {
											echo $curr['title'];
										}
									}
									echo isset( $curr['required'] ) && $curr['required'] == 'yes' || $product->get_type() == 'variable' && $curr['attr'] !== 'ivpa_custom' ? apply_filters( 'ivpa_required_field_content', ' <span class="ivpa-required"></span>' ) : '';
								?>
							</strong>
						<?php
							if ( self::$settings['wc_settings_ivpa_single_desc'] == 'ivpa_aftertitle' && $curr_is_loop == 'single' && $curr['style'] !== 'ivpa_selectbox' ) {
								if ( $curr['desc'] !== '' ) {
									self::__get_desc( $curr, $curr_is_loop );
								}
							}
						}

						if ( ( $curr['attr'] == 'ivpa_custom' || $willCustom === true ) === true && in_array( $curr['style'], array( 'ivpa_text', 'ivpa_color', 'ivpa_image', 'ivpa_html', 'ivpa_selectbox' ) ) ) {
						?>
							<input type="hidden" name="<?php echo 'ivpac_' . $k; ?>" />
						<?php
						}
						else {
							if ( $curr_is_loop == 'single' && !$product->is_type( 'variable' ) ) {
							?>
								<input type="hidden" name="<?php echo 'attribute_' . $v; ?>" />
							<?php
							}
						}

						$curr_add_class = '';

						switch ( $curr['style'] ) {

							case 'ivpa_text' :

								foreach ( $curr['terms'] as $l => $b ) {

									if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
										continue;

									if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
										$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
									}

								?>
									<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>" >
										<?php
											echo isset( $curr['name'][$b->slug] ) && $curr['name'][$b->slug] !== '' ? $curr['name'][$b->slug] : $b->name;

											self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), false, $curr_is_loop );

											if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
											?>
												<span class="ivpa_tooltip"><span><?php echo stripslashes( $curr['ivpa_tooltip'][$b->slug] ); ?></span></span>
											<?php
											}
										?>
									</span>
								<?php
								}

							break;


							case 'ivpa_color' :

								foreach ( $curr['terms'] as $l => $b ) {

									if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
										continue;

									if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
										$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
									}

								?>
									<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>"<?php echo $curr_is_loop == 'single' && isset( $curr['size'] ) ? ' style="width:' . $curr['size'] . 'px;height:' . $curr['size'] . 'px;"' : '' ; ?>>
										<span style="background-color:<?php echo isset( $curr['custom'], $curr['custom'][$b->slug] ) ? $curr['custom'][$b->slug] : ''; ?>"></span>
									<?php
										self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), false, $curr_is_loop );

										if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
										?>
											<span class="ivpa_tooltip"><span><?php echo stripslashes( $curr['ivpa_tooltip'][$b->slug] ); ?></span></span>
										<?php
										}
									?>
									</span>
								<?php
								}

							break;


							case 'ivpa_image' :

								foreach ( $curr['terms'] as $l => $b ) {

									if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
										continue;

									if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
										$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
									}

								?>
									<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>"<?php echo $curr_is_loop == 'single' && isset( $curr['size'] ) ? ' style="width:' . $curr['size'] . 'px;"' : '' ; ?>>
										<img src="<?php echo isset( $curr['custom'], $curr['custom'][$b->slug] ) ? esc_url( $curr['custom'][$b->slug] ) : ''; ?>" alt="<?php echo esc_attr( $b->name ); ?>" />
									<?php
										self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), false, $curr_is_loop );

										if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
										?>
											<span class="ivpa_tooltip"><span><?php echo stripslashes( $curr['ivpa_tooltip'][$b->slug] ); ?></span></span>
										<?php
										}
									?>
									</span>
								<?php
								}

							break;

							case 'ivpa_html' :

								foreach ( $curr['terms'] as $l => $b ) {

									if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
										continue;

									if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
										$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
									}

								?>
									<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>">
										<?php echo isset( $curr['custom'], $curr['custom'][$b->slug] ) ? stripslashes( $curr['custom'][$b->slug] ) : ''; ?>
										<?php
											self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), false, $curr_is_loop );

											if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
											?>
												<span class="ivpa_tooltip"><span><?php echo stripslashes( $curr['ivpa_tooltip'][$b->slug] ); ?></span></span>
											<?php
											}
										?>
									</span>
								<?php

								}

							break;

							case 'ivpa_selectbox' :

								foreach ( $curr['terms'] as $l => $b ) {

									if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
										continue;

									if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
										$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
									}

								?>
									<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>">
										<?php echo isset( $curr['name'][$b->slug] ) && $curr['name'][$b->slug] !== '' ? $curr['name'][$b->slug] : $b->name; ?> <?php self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), true, $curr_is_loop ); ?>
									</span>
								<?php

								}

							break;

							case 'ivpac_input' :
							case 'ivpac_checkbox' :
							case 'ivpac_textarea' :

								foreach ( $curr['terms'] as $l => $b ) {
									$field = sanitize_title( $curr['title'] );

								?>
									<span class="ivpa_term ivpa_custom" data-term="<?php echo $field; ?>">
									<?php
										if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
											switch( $curr['style'] ) {
												case 'ivpac_input' :
												?>
													<input class="ivpac-change" type="text" name="<?php echo 'ivpac_' . $k . '[' . $b->slug . ']'; ?>" /> 
												<?php
												break;

												case 'ivpac_checkbox' :
												?>
													<input class="ivpac-change" type="checkbox" name="<?php echo 'ivpac_' . $k . '[' . $b->slug . ']'; ?>" value="<?php echo $b->slug; ?>" /> 
												<?php
												break;

												case 'ivpac_textarea' :
												?>
													<textarea class="ivpac-change" name="<?php echo 'ivpac_' . $k . '[' . $b->slug . ']'; ?>"></textarea> 
												<?php
												break;

												default :
												break;
											}
										}
									?>
									<em class="ivpa_name"><?php echo isset( $curr['name'][$b->slug] ) && $curr['name'][$b->slug] !== '' ? $curr['name'][$b->slug] : $b->name; ?> <?php self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), false, $curr_is_loop ); ?></em>
									</span>
								<?php

								}

								if ( self::$settings['wc_settings_ivpa_step_selection'] == 'yes' && $curr['style'] !== 'ivpac_checkbox' ) {
								?>
									<span class="ivpa_term ivpa_custom ivpa_group_custom"><span class="ivpa-notconfirmed"><?php esc_html_e( 'Click to confirm input', 'ivpawoo' ); ?></span><span class="ivpa-confirmed"><?php esc_html_e( 'Input confirmed', 'ivpawoo' ); ?></span></span>
								<?php
								}

							break;

							case 'ivpac_system' :
								$field = sanitize_title( $curr['title'] );
							?>
								<span class="ivpa_term ivpa_custom" data-term="<?php echo $field; ?>">
								<?php
									if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
									?>
										<select class="ivpac-change" name="<?php echo 'ivpac_' . $k; ?>">
										<?php
											foreach ( $curr['terms'] as $l => $b ) {
											?>
												<option value="<?php echo isset( $curr['custom'][$b->slug] ) ? $curr['custom'][$b->slug] : $b->slug; ?>"><?php echo isset( $curr['name'][$b->slug] ) && $curr['name'][$b->slug] !== '' ? $curr['name'][$b->slug] : $b->name; ?> <?php strip_tags( self::add_price_html( $curr['attr'], ( isset( $curr['price'][$b->slug] ) ? $curr['price'][$b->slug] : '' ), true, $curr_is_loop ) ); ?></option>
											<?php
											}
										?>
									</select> 
									<?php
									}
								?>
								</span>
							<?php

							if ( self::$settings['wc_settings_ivpa_step_selection'] == 'yes' && $curr['style'] !== 'ivpac_checkbox' ) {
							?>
								<span class="ivpa_term ivpa_custom ivpa_group_custom"><span class="ivpa-notconfirmed"><?php esc_html_e( 'Click to confirm selection', 'ivpawoo' ); ?></span><span class="ivpa-confirmed"><?php esc_html_e( 'Selection confirmed', 'ivpawoo' ); ?></span></span>
							<?php
							}

							break;

							default :
							break;

						}

						if ( self::$settings['wc_settings_ivpa_single_desc'] == 'ivpa_afterattribute' && $curr_is_loop == 'single' && $curr['style'] !== 'ivpa_selectbox' ) {
							if ( $curr['desc'] !== '' ) {
								self::__get_desc( $curr, $curr_is_loop );
							}
						}
						if ( $curr['style'] == 'ivpa_selectbox' ) {
						?>
								</div>
							</div>
						<?php
						}
						if ( self::$settings['wc_settings_ivpa_single_desc'] == 'ivpa_afterattribute' && $curr_is_loop == 'single' && $curr['style'] == 'ivpa_selectbox' ) {
							if ( $curr['desc'] !== '' ) {
								self::__get_desc( $curr, $curr_is_loop );
							}
						}
						if ( $curr_is_loop == 'loop' ) {
							self::__get_required_short( $curr );
						}
					?>
					</div>
				<?php
					$willCustom = false;
				}

				if ( $curr_is_loop == 'single' ) {
				?>
					<a class="ivpa_reset_variations" href="#reset"><?php esc_html_e( 'Clear selection', 'ivpawoo' ); ?></a>
				<?php
				}

				if ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_quantity'] == 'yes' ) {
				?>
					<div class="ivpa_quantity">
						<small><?php esc_html_e( 'Qty:', 'ivpawoo' ); ?></small>
						<input type="number" class="ivpa_qty" value="1" min="1" />
					</div>
				<?php
				}

				if ( !empty( $style ) ) {
					echo '<style type="text/css">' . $style . '</style>';
				}

				?>
				</div>
			<?php

				$html = trim( ob_get_clean() );

				echo str_replace( '%%%JSON_REPLACE_IVPA%%%', esc_attr( json_encode( $available_variations ) ), $html );

				if ( empty( $_REQUEST ) && self::$settings['wc_settings_ivpa_use_caching'] == 'yes' ) {
					update_post_meta( get_the_ID(), '_ivpa_cached_' . $curr_is_loop . '_' . get_locale(), $html );
					update_post_meta( get_the_ID(), '_ivpa_cached_data', $available_variations );
				}

			}

			if ( isset( $wasInit ) ) {
				self::$settings['wc_settings_ivpa_archive_mode'] = 'ivpa_selection';
			}

		}

		public static function __get_required_short( $curr ) {
			global $product;
			if ( isset( $curr['required'] ) && $curr['required'] == 'yes' || $product->get_type() == 'variable' && $curr['attr'] !== 'ivpa_custom' ) {
				echo '<div class="ivpa-required-short">*</div>';
			}
		}

		public static function __get_desc( $curr, $curr_is_loop ) {
			?>
			<div class="ivpa_desc"><?php echo $curr['desc']; ?>
			<?php
				if ( isset( $curr['addprice'] ) && $curr['addprice'] !== '' ) {
				?>
					<strong>
						<?php self::add_price_html( $curr['attr'], $curr['addprice'], ( in_array( $curr['style'], array( 'ivpa_selectbox', 'ivpac_system' ) ) ? true : false ), $curr_is_loop );?>
					</strong>
				<?php
				}
			?>
			</div>
			<?php
		}

		public static function __get_customization( $k, $curr_customizations ) {
			if ( $k!==false ) {
				return array(
						'name' => ( isset($curr_customizations['ivpa_name'][$k]) ? $curr_customizations['ivpa_name'][$k] : '' ),
						'attr' => ( isset($curr_customizations['ivpa_attr'][$k]) ? $curr_customizations['ivpa_attr'][$k] : '' ),
						'style' => ( isset($curr_customizations['ivpa_style'][$k]) ? $curr_customizations['ivpa_style'][$k] : 'ivpa_text' ),
						'title' => ( isset($curr_customizations['ivpa_title'][$k]) ? $curr_customizations['ivpa_title'][$k] : '' ),
						'desc' => ( isset($curr_customizations['ivpa_desc'][$k]) ? stripslashes( $curr_customizations['ivpa_desc'][$k] ) : '' ),
						'custom' => ( isset($curr_customizations['ivpa_custom'][$k]) ? $curr_customizations['ivpa_custom'][$k] : array( 'style' => 'ivpa_background', 'normal' => '#bbbbbb', 'active' => '#333333', 'disabled' => '#e45050', 'outofstock' => '#e45050' ) ),
						'ivpa_tooltip' => ( isset($curr_customizations['ivpa_tooltip'][$k]) ? $curr_customizations['ivpa_tooltip'][$k] : array() ),
						'size' => ( isset($curr_customizations['ivpa_size'][$k]) ? $curr_customizations['ivpa_size'][$k] : null ),
						'addprice' => ( isset($curr_customizations['ivpa_addprice'][$k]) ? $curr_customizations['ivpa_addprice'][$k] : null ),
						'order' => ( isset($curr_customizations['ivpa_custom_order'][$k]) ? $curr_customizations['ivpa_custom_order'][$k] : null ),
						'multiselect' => ( isset($curr_customizations['ivpa_multiselect'][$k]) ? $curr_customizations['ivpa_multiselect'][$k] : null ),
						'required' => ( isset($curr_customizations['ivpa_required'][$k]) ? $curr_customizations['ivpa_required'][$k] : null ),
						'price' => ( isset($curr_customizations['ivpa_price'][$k]) ? $curr_customizations['ivpa_price'][$k] : null ),
						'archive_include' => ( isset($curr_customizations['ivpa_archive_include'][$k]) ? $curr_customizations['ivpa_archive_include'][$k] : null ),
						'selectable' => ( isset($curr_customizations['ivpa_svariation'][$k]) ? $curr_customizations['ivpa_svariation'][$k] : null ),
				);
			}
			else {
				return array(
						'name' => '',
						'attr' => '',
						'style' => 'ivpa_text',
						'title' => '',
						'desc' => '',
						'custom' => array( 'style' => 'ivpa_background', 'normal' => '#bbbbbb', 'active' => '#333333', 'disabled' => '#e45050', 'outofstock' => '#e45050' ),
						'ivpa_tooltip' => array(),
						'size' => null,
						'addprice' => null,
						'order' => null,
						'multiselect' => null,
						'required' => null,
						'price' => null,
						'archive_include' => null,
				);
			}
		}

		public static function add_price_html( $opt, $price, $select, $loop ) {
			if ( $loop == 'single' && $opt == 'ivpa_custom' ) {
				if ( isset( $price ) && $price !== '' ) {
					$price = strip_tags( wc_price( $price ) );
					?>
						<strong class="ivpa-addprice"><?php echo $select === true ? '(+' . $price . ')' : ' +' . $price; ?></strong>
					<?php
				}
			}
		}

		function ivpa_add_to_cart_callback() {

			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$quantity = isset( $_POST['quantity'] ) && !empty( $_POST['quantity'] ) ? apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] ) : 1;
			$variation_id = isset( $_POST['variation_id'] ) && !empty( $_POST['variation_id'] ) ? $_POST['variation_id'] : '';

			if ( isset( $_POST['variation' ] ) && is_array( $_POST['variation' ] ) ) {
				foreach ( $_POST['variation'] as $k => $v ) {
					$variation[$k] = self::utf8_urldecode($v);
				}
			}
			else {
				$variation = array();
			}

			$product_data = array();
			if ( isset( $_POST['ivpac'] ) ) {
				$ivpac = array();
				parse_str( $_POST['ivpac'], $ivpac );


				foreach ( $ivpac as $k => $v ) {
					if ( substr( $k, 0, 6 ) == 'ivpac_' && !empty( $v ) ) {
						if ( is_array( $v ) ) {
							$v = array_filter( $v );
						}
						if ( !empty( $v ) ) {
							$product_data['ivpac'][substr( $k, 6 )] = is_array( $v ) ? implode( ', ', $v ) : $v;
						}
					}
				}

			}

			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			$id = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $product_data );

			if ( $passed_validation && $id ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
					wc_add_to_cart_message( $product_id );
				}

				$data = WC_AJAX::get_refreshed_fragments();
			}
			else {

					WC_AJAX::json_headers();

					$data = array(
						'error' => true,
						'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
					);

					$data = json_encode( $data );
			}

			wp_die( $data );
			exit();

		}

		function ivpa_repair_cart(){
			if ( defined( 'DOING_AJAX' ) ) {
				wc_setcookie( 'woocommerce_items_in_cart', 1 );
				wc_setcookie( 'woocommerce_cart_hash', md5( json_encode( WC()->cart->get_cart() ) ) );
				do_action( 'woocommerce_set_cart_cookies', true );
			}
		}

		public static function ivpa_wpml_language() {

			if( class_exists( 'SitePress' ) ) {
				global $sitepress;

				if ( method_exists( $sitepress, 'get_default_language' ) ) {

					$default_language = $sitepress->get_default_language();
					$current_language = $sitepress->get_current_language();

					if ( $default_language != $current_language ) {
						return sanitize_title( $current_language );
					}
				}
			}
			return false;

		}

		public static function ivpa_wpml_get_current_slug( $curr_term, $attr ) {

			if( function_exists( 'icl_object_id' ) ) {

				global $sitepress;

				if ( method_exists( $sitepress, 'get_default_language' ) ) {

					$default_language = $sitepress->get_default_language();
					$current_language = $sitepress->get_current_language();

					if ( $default_language != $current_language ) {

						$term_id = icl_object_id( $curr_term->term_id, $attr, false, $default_language );
						$term = get_term( $term_id, $attr );

						return $term->slug;

					}

				}

			}

			return $curr_term->slug;

		}

		public static function prdctfltr_wpml_include_terms( $curr_include, $attr ) {

			if( function_exists( 'icl_object_id' ) ) {
				global $sitepress;

				if ( method_exists( $sitepress, 'get_default_language' ) ) {

					$translated_include = array();

					foreach( $curr_include as $curr ) {
						$current_term = get_term_by( 'slug', $curr, $attr );

						if($current_term) {
							$default_language = $sitepress->get_default_language();
							$current_language = $sitepress->get_current_language();

							$term_id = $current_term->term_id;
							if ( $default_language != $current_language ) {
								$term_id = icl_object_id( $term_id, $attr, false, $default_language );
							}

							$term = get_term( $term_id, $attr );
							$translated_include[] = $term->slug;

						}
					}

					return $translated_include;
				}
			}

			return $curr_include;

		}

	}

	add_action( 'init', array( 'WC_Improved_Variable_Product_Attributes', 'init' ), 998 );

	if ( !function_exists( 'mnthemes_add_meta_information' ) ) {
		function mnthemes_add_meta_information_action() {
			$val = apply_filters( 'mnthemes_add_meta_information_used', array() );
			if ( !empty( $val ) ) {
				echo '<meta name="generator" content="' . implode( ', ', $val ) . '"/>';
			}
		}
		function mnthemes_add_meta_information() {
			add_action( 'wp_head', 'mnthemes_add_meta_information_action', 99 );
		}
		mnthemes_add_meta_information();
	}

?>