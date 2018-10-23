<?php

	class WC_Ivpa_Settings {

		public static function init() {
			//add_filter( 'svx_plugins_settings', __CLASS__ . '::get_settings', 50 );
			add_action( 'svx_ajax_saved_settings_improved_options', __CLASS__ . '::delete_cache' );
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::ivpa_settings_scripts', 9 );

			add_action( 'save_post', __CLASS__ . '::delete_post_cache', 10, 3 );
		}

		public static function ivpa_settings_scripts( $settings_tabs ) {
			if ( isset($_GET['page'], $_GET['tab']) && ($_GET['page'] == 'wc-settings' ) && $_GET['tab'] == 'improved_options' ) {
				wp_enqueue_script( 'ivpa-admin', Wcmnivpa()->plugin_url() . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), WC_Improved_Variable_Product_Attributes_Init::$version, true );
			}
		}

		public static function get_settings() {

			$attributes = get_object_taxonomies( 'product' );
			$ready_attributes = array();
			if ( !empty( $attributes ) ) {
				foreach( $attributes as $k ) {
					if ( substr($k, 0, 3) == 'pa_' ) {
						$ready_attributes[$k] =  wc_attribute_label( $k );
					}
				}
			}

			$plugins['improved_options'] = array(
				'slug' => 'improved_options',
				'name' => esc_html__( 'Improved Product Options for WooCommerce', 'ivpawoo' ),
				'desc' => esc_html__( 'Settings page for Improved Product Options for WooCommerce!', 'ivpawoo' ),
				'link' => 'https://mihajlovicnenad.com/product/improved-product-options-woocommerce/',
				'ref' => array(
					'name' => esc_html__( 'More plugins and themes?', 'ivpawoo' ),
					'url' => 'https://mihajlovicnenad.com/shop/'
				),
				'doc' => array(
					'name' => esc_html__( 'Documentation and Plugin Guide', 'ivpawoo' ),
					'url' => 'http://mihajlovicnenad.com/improved-variable-product-attributes/documentation-and-guide/'
				),
				'sections' => array(
					'options' => array(
						'name' => esc_html__( 'Product Options', 'ivpawoo' ),
						'desc' => esc_html__( 'Product Options', 'ivpawoo' ),
					),
					'general' => array(
						'name' => esc_html__( 'General', 'ivpawoo' ),
						'desc' => esc_html__( 'General Options', 'ivpawoo' ),
					),
					'product' => array(
						'name' => esc_html__( 'Product Page', 'ivpawoo' ),
						'desc' => esc_html__( 'Product Page Options', 'ivpawoo' ),
					),
					'shop' => array(
						'name' => esc_html__( 'Shop/Archives', 'ivpawoo' ),
						'desc' => esc_html__( 'Shop/Archives Options', 'ivpawoo' ),
					),
					'installation' => array(
						'name' => esc_html__( 'Installation', 'ivpawoo' ),
						'desc' => esc_html__( 'Installation Options', 'ivpawoo' ),
					),
				),
				'extras' => array(
					'product_attributes' => $ready_attributes
				),
				'settings' => array(

					'wc_ivpa_attribute_customization' => array(
						'name' => esc_html__( 'Options Manager', 'ivpawoo' ),
						'type' => 'list-select',
						'desc' => esc_html__( 'Use the manager to customize your attributes or add custom product options!', 'ivpawoo' ),
						'id'   => 'wc_ivpa_attribute_customization',
						'default' => array(),
						'autoload' => false,
						'section' => 'options',
						//'title' => esc_html__( 'Option', 'wfsm' ),
						'supports' => array( 'customizer' ),
						'options' => 'list',
						'translate' => true,
						'selects' => array(
							'ivpa_attr' => esc_html__( 'Attribute Swatch', 'wfsm' ),
							'ivpa_custom' => esc_html__( 'Custom Option', 'wfsm' )
						),
						'settings' => array(
							'ivpa_attr' => array(
								'taxonomy' => array(
									'name' => esc_html__( 'Select Attribute', 'ivpawoo' ),
									'type' => 'select',
									'desc' => esc_html__( 'Select attribute to customize', 'ivpawoo' ),
									'id'   => 'taxonomy',
									'options' => 'ajax:product_attributes:has_none',
									'default' => '',
									'class' => 'svx-update-list-title'
								),
								'name' => array(
									'name' => esc_html__( 'Name', 'ivpawoo' ),
									'type' => 'text',
									'desc' => esc_html__( 'Use alternative name for the attribute', 'ivpawoo' ),
									'id'   => 'name',
									'default' => '',
								),
								'ivpa_desc' => array(
									'name' => esc_html__( 'Description', 'ivpawoo' ),
									'type' => 'textarea',
									'desc' => esc_html__( 'Enter description for current attribute', 'ivpawoo' ),
									'id'   => 'ivpa_desc',
									'default' => ''
								),
								'ivpa_svariation' => array(
									'name' => esc_html__( 'Attribute is Selectable (Simple Products)', 'ivpawoo' ),
									'type' => 'checkbox',
									'desc' => esc_html__( 'This option is in use only with Simple Products and General>Attribute Selection Support set to All Products', 'ivpawoo' ),
									'id'   => 'ivpa_svariation',
									'default' => false
								),
								'ivpa_archive_include' => array(
									'name' => esc_html__( 'Shop Display Mode', 'ivpawoo' ),
									'type' => 'checkbox',
									'desc' => esc_html__( 'Show on Shop Pages (Works with Shop Display Mode set to Show Available Options Only)', 'ivpawoo' ),
									'id'   => 'ivpa_archive_include',
									'default' => 'yes'
								),
								'ivpa_required' => array(
									'name' => esc_html__( 'Required', 'ivpawoo' ),
									'type' => 'checkbox',
									'desc' => esc_html__( 'This option is required (Only works on simple products, variable product attributes are required always)', 'ivpawoo' ),
									'id'   => 'ivpa_required',
									'default' => 'no'
								),
							),
							'ivpa_custom' => array(
								'name' => array(
									'name' => esc_html__( 'Name', 'ivpawoo' ),
									'type' => 'text',
									'desc' => esc_html__( 'Use alternative name for the attribute', 'ivpawoo' ),
									'id'   => 'name',
									'default' => ''
								),
								'ivpa_desc' => array(
									'name' => esc_html__( 'Description', 'ivpawoo' ),
									'type' => 'textarea',
									'desc' => esc_html__( 'Enter description for current attribute', 'ivpawoo' ),
									'id'   => 'ivpa_desc',
									'default' => ''
								),
								'ivpa_addprice' => array(
									'name' => esc_html__( 'Add Price', 'ivpawoo' ),
									'type' => 'text',
									'desc' => esc_html__( 'Add-on price if option is used by customer', 'ivpawoo' ),
									'id'   => 'ivpa_addprice',
									'default' => ''
								),
								'ivpa_limit_type' => array(
									'name' => esc_html__( 'Limit to Product Type', 'ivpawoo' ),
									'type' => 'text',
									'desc' => esc_html__( 'Enter product types separated by | Sample: &rarr; ', 'ivpawoo' ) . '<code>simple|variable</code>',
									'id'   => 'ivpa_limit_type',
									'default' => ''
								),
								'ivpa_limit_category' => array(
									'name' => esc_html__( 'Limit to Product Category', 'ivpawoo' ),
									'type' => 'text',
									'desc' => esc_html__( 'Enter product category IDs separated by | Sample: &rarr; ', 'ivpawoo' ) . '<code>7|55</code>',
									'id'   => 'ivpa_limit_category',
									'default' => ''
								),
								'ivpa_limit_product' => array(
									'name' => esc_html__( 'Limit to Products', 'ivpawoo' ),
									'type' => 'text',
									'desc' => esc_html__( 'Enter product IDs separated by | Sample: &rarr; ', 'ivpawoo' ) . '<code>155|222|333</code>',
									'id'   => 'ivpa_limit_product',
									'default' => ''
								),
								'ivpa_multiselect' => array(
									'name' => esc_html__( 'Multiselect', 'ivpawoo' ),
									'type' => 'checkbox',
									'desc' => esc_html__( 'Use multi select on this option', 'ivpawoo' ),
									'id'   => 'ivpa_multiselect',
									'default' => 'yes'
								),
								'ivpa_archive_include' => array(
									'name' => esc_html__( 'Shop Display Mode', 'ivpawoo' ),
									'type' => 'checkbox',
									'desc' => esc_html__( 'Show on Shop Pages (Works with Shop Display Mode set to Show Available Options Only)', 'ivpawoo' ),
									'id'   => 'ivpa_archive_include',
									'default' => 'yes'
								),
								'ivpa_required' => array(
									'name' => esc_html__( 'Required', 'ivpawoo' ),
									'type' => 'checkbox',
									'desc' => esc_html__( 'This option is required', 'ivpawoo' ),
									'id'   => 'ivpa_required',
									'default' => 'no'
								),
							)
						)
					),

					'wc_settings_ivpa_single_selectbox' => array(
						'name' => esc_html__( 'Hide WooCommerce Select Boxes', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide default WooCommerce select boxes in Product Pages.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_single_selectbox',
						'default' => 'yes',
						'autoload' => false,
						'section' => 'product'
					),
					'wc_settings_ivpa_single_addtocart' => array(
						'name' => esc_html__( 'Hide Add To Cart Before Selection', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide the Add To Cart button in Product Pages before the selection is made.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_single_addtocart',
						'default' => 'yes',
						'autoload' => false,
						'section' => 'product'
					),
					'wc_settings_ivpa_single_desc' => array(
						'name' => esc_html__( 'Select Descriptions Position', 'ivpawoo' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select where to show descriptions.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_single_desc',
						'options' => array(
							'ivpa_aftertitle' => esc_html__( 'After Title', 'ivpawoo' ),
							'ivpa_afterattribute' => esc_html__( 'After Attributes', 'ivpawoo' )
						),
						'default' => 'ivpa_afterattribute',
						'autoload' => false,
						'section' => 'product'
					),
					'wc_settings_ivpa_single_ajax' => array(
						'name' => esc_html__( 'AJAX Add To Cart', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable AJAX add to cart in Product Pages.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_single_ajax',
						'default' => 'no',
						'autoload' => false,
						'section' => 'product'
					),
					'wc_settings_ivpa_single_image' => array(
						'name' => esc_html__( 'Use Advanced Image Switcher', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable advanced image switcher in Single Product Pages. This option enables image switch when a single attribute is selected.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_single_image',
						'default' => 'no',
						'autoload' => false,
						'section' => 'product'
					),

					'wc_settings_ivpa_archive_quantity' => array(
						'name' => esc_html__( 'Show Quantities', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable product quantity in Shop.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_archive_quantity',
						'default' => 'no',
						'autoload' => false,
						'section' => 'shop'
					),
					'wc_settings_ivpa_archive_mode' => array(
						'name' => esc_html__( 'Shop Display Mode', 'ivpawoo' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select how to show the options in Shop Pages.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_archive_mode',
						'options' => array(
							'ivpa_showonly' => esc_html__( 'Only Show Available Options', 'ivpawoo' ),
							'ivpa_selection' => esc_html__( 'Enable Selection and Add to Cart', 'ivpawoo' )
						),
						'default' => 'ivpa_selection',
						'autoload' => false,
						'section' => 'shop'
					),
					'wc_settings_ivpa_archive_align' => array(
						'name' => esc_html__( 'Options Alignment', 'ivpawoo' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select options alignment in Shop Pages.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_archive_align',
						'options' => array(
							'ivpa_align_left' => esc_html__( 'Left', 'ivpawoo' ),
							'ivpa_align_right' => esc_html__( 'Right', 'ivpawoo' ),
							'ivpa_align_center' => esc_html__( 'Center', 'ivpawoo' )
						),
						'default' => 'ivpa_align_left',
						'autoload' => false,
						'section' => 'shop'
					),

					'wc_settings_ivpa_single_action' => array(
						'name' => esc_html__( 'Single Product Init Action', 'ivpawoo' ),
						'type' => 'text',
						'desc' => esc_html__( 'Use custom initialization action for Single Product Pages. Use actions initiated in your content-single-product.php template. Please enter action name in following format action_name:priority', 'ivpawoo' ) . '( default: woocommerce_before_add_to_cart_button )',
						'id'   => 'wc_settings_ivpa_single_action',
						'default' => '',
						'autoload' => true,
						'section' => 'installation'
					),
					'wc_settings_ivpa_archive_action' => array(
						'name' => esc_html__( 'Shop Init Action', 'ivpawoo' ),
						'type' => 'text',
						'desc' => esc_html__( 'Use custom initialization action for Shop/Product Archive Pages. Use actions initiated in your content-single-product.php template. Please enter action name in following format action_name:priority', 'ivpawoo' ) . ' ( default: woocommerce_after_shop_loop_item:999 )',
						'id'   => 'wc_settings_ivpa_archive_action',
						'default' => '',
						'autoload' => true,
						'section' => 'installation'
					),

					'wc_settings_ivpa_single_selector' => array(
						'name' => esc_html__( 'Single Product Image jQuery Selector', 'ivpawoo' ),
						'type' => 'text',
						'desc' => esc_html__( 'Change default image wrapper selector in Single Product pages.', 'ivpawoo' ) . ' (default: .type-product .images )',
						'id'   => 'wc_settings_ivpa_single_selector',
						'default' => '',
						'autoload' => false,
						'section' => 'installation'
					),
					'wc_settings_ivpa_archive_selector' => array(
						'name' => esc_html__( 'Shop Product jQuery Selector', 'ivpawoo' ),
						'type' => 'text',
						'desc' => esc_html__( 'Change default product selector in Shop.', 'ivpawoo' ) . ' (default: .type-product )',
						'id'   => 'wc_settings_ivpa_archive_selector',
						'default' => '',
						'autoload' => false,
						'section' => 'installation'
					),
					'wc_settings_ivpa_addcart_selector' => array(
						'name' => esc_html__( 'Shop Add To Cart jQuery Selector', 'ivpawoo' ),
						'type' => 'text',
						'desc' => esc_html__( 'Change default add to cart selector in Shop.', 'ivpawoo' ) . ' (default: .add_to_cart_button )',
						'id'   => 'wc_settings_ivpa_addcart_selector',
						'default' => '',
						'autoload' => false,
						'section' => 'installation'
					),
					'wc_settings_ivpa_price_selector' => array(
						'name' => esc_html__( 'Shop Price jQuery Selector', 'ivpawoo' ),
						'type' => 'text',
						'desc' => esc_html__( 'Change default price selector in Shop.', 'ivpawoo' ) . ' (default: .price )',
						'id'   => 'wc_settings_ivpa_price_selector',
						'default' => '',
						'autoload' => false,
						'section' => 'installation'
					),


					'wc_settings_ivpa_single_enable' => array(
						'name' => esc_html__( 'Use Plugin In Product Pages', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use the plugin selectors in Single Product Pages.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_single_enable',
						'default' => 'yes',
						'autoload' => false,
						'section' => 'general'
					),
					'wc_settings_ivpa_archive_enable' => array(
						'name' => esc_html__( 'Use Plugin In Shop', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use the plugin styled selectors in Shop Pages.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_archive_enable',
						'default' => 'no',
						'autoload' => false,
						'section' => 'general'
					),

					'wc_settings_ivpa_simple_support' => array(
						'name' => esc_html__( 'Attribute Selection Support', 'ivpawoo' ),
						'type' => 'select',
						'desc' => esc_html__( 'Set this option to enable selection of attributes for products that are not variable.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_simple_support',
						'options' => array(
							'none' => esc_html__( 'Variable Products', 'ivpawoo' ),
							'full' => esc_html__( 'All Products (Simple Products)', 'ivpawoo' )
						),
						'default' => 'none',
						'autoload' => false,
						'section' => 'general'
					),

					'wc_settings_ivpa_outofstock_mode' => array(
						'name' => esc_html__( 'Out Of Stock Display', 'ivpawoo' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select how the to display the Out of Stock options.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_outofstock_mode',
						'options' => array(
							'default' => esc_html__( 'Shown but not clickable', 'ivpawoo' ),
							'clickable' => esc_html__( 'Shown and clickable', 'ivpawoo' ),
							'hidden' => esc_html__( 'Hidden from pages', 'ivpawoo' )
						),
						'default' => 'default',
						'autoload' => false,
						'section' => 'general'
					),

					'wc_settings_ivpa_image_attributes' => array(
						'name' => esc_html__( 'Image Switching Attributes', 'ivpawoo' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select attributes that will switch the product image. Available in Shop Pages and in Single Product Pages if Advanced Image Switcher option is used.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_image_attributes',
						'options' => 'ajax:product_attributes',
						'default' => '',
						'autoload' => false,
						'section' => 'general'
					),

					'wc_settings_ivpa_step_selection' => array(
						'name' => esc_html__( 'Step Selection', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use stepped selection.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_step_selection',
						'default' => 'no',
						'autoload' => false,
						'section' => 'general'
					),
					'wc_settings_ivpa_disable_unclick' => array(
						'name' => esc_html__( 'Disable Option Deselection', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option not to allow option deselection/unchecking.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_disable_unclick',
						'default' => 'no',
						'autoload' => false,
						'section' => 'general'
					),
					'wc_settings_ivpa_backorder_support' => array(
						'name' => esc_html__( 'Backorder Notifications', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable backorders support and show notifications about them.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_backorder_support',
						'default' => 'no',
						'autoload' => false,
						'section' => 'general'
					),
					'wc_settings_ivpa_force_scripts' => array(
						'name' => esc_html__( 'Plugin Scripts', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to load plugin scripts in all pages. This option fixes issues in Quick Views.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_force_scripts',
						'default' => 'no',
						'autoload' => false,
						'section' => 'installation'
					),
					'wc_settings_ivpa_use_caching' => array(
						'name' => esc_html__( 'Use Caching', 'ivpawoo' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use product caching for better performance.', 'ivpawoo' ),
						'id'   => 'wc_settings_ivpa_use_caching',
						'default' => 'no',
						'autoload' => false,
						'section' => 'installation'
					),

				)
			);

			foreach ( $plugins['improved_options']['settings'] as $k => $v ) {
				$get = isset( $v['translate'] ) ? $v['id'] . SevenVX()->language() : $v['id'];
				$std = isset( $v['default'] ) ?  $v['default'] : '';
				$set = ( $set = get_option( $get, false ) ) === false ? $std : $set;
				$plugins['improved_options']['settings'][$k]['val'] = SevenVX()->stripslashes_deep( $set );
			}

			return apply_filters( 'wc_ivpa_settings', $plugins );

		}

		public static function delete_cache( $id = '' ) {
			global $wpdb;
			if ( empty( $id ) ) {
				$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta WHERE meta.meta_key LIKE '_ivpa_cached_%';" );
			}
			else {
				$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta WHERE meta.post_id = {$id} AND meta.meta_key LIKE '_ivpa_cached_%';" );
			}
		}

		public static function delete_post_cache( $id, $post, $update ) {
			if ( get_option( 'wc_settings_ivpa_use_caching', 'no' ) == 'yes' ) {
				if ( $post->post_type != 'product' ) {
					return;
				}
				self::delete_cache( $id );
			}
		}

	}

	add_action( 'init', array( 'WC_Ivpa_Settings', 'init' ), 100 );
	if ( isset($_GET['page'], $_GET['tab']) && ($_GET['page'] == 'wc-settings' ) && $_GET['tab'] == 'improved_options' ) {
		add_filter( 'svx_plugins_settings', array( 'WC_Ivpa_Settings', 'get_settings' ), 50 );
	}

?>