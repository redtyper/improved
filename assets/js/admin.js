(function($){
"use strict";

	function _get_style(e) {
		return e.substr(0,5)=='ivpa_'?e.substr(5): e.substr(6);
	}
	function _get_style_back(e) {
		if ( $.inArray( e, [ 'image', 'color', 'text', 'selectbox', 'html' ] ) > -1 ) {
			return 'ivpa_'+e;
		}
		return 'ivpac_'+e;
	}

	function u(e) {
		return typeof e == 'undefined' ? false : e;
	}

	$(document).on( 'svx-wc_ivpa_attribute_customization-load', function(e,f) {

		var r = [];

		$.each( f.val.ivpa_title, function(i,b) {

			var typeC = u(f.val.ivpa_attr[i])!==false?(f.val.ivpa_attr[i]=='ivpa_custom'?'ivpa_custom':'ivpa_attr'):false;
			var getStyle = u(f.val.ivpa_style[i])!==false?_get_style(f.val.ivpa_style[i]):false;

			r.push( {
				type:typeC,
				name:u(f.val.ivpa_title)?u(f.val.ivpa_title[i]):false,
				default_name:u(f.val.ivpa_attr[i])!=='ivpa_custom'?u(svx.extras.product_attributes[f.val.ivpa_attr[i]]):false,
				style:getStyle,
				taxonomy:u(f.val.ivpa_attr[i])=='ivpa_custom'?'meta':u(f.val.ivpa_attr[i]),
				ivpa_addprice:u(f.val.ivpa_addprice)?u(f.val.ivpa_addprice[i]):false,
				ivpa_archive_include:u(f.val.ivpa_archive_include)?u(f.val.ivpa_archive_include[i]):false,
				ivpa_desc:u(f.val.ivpa_desc)?u(f.val.ivpa_desc[i]):false,
				ivpa_limit_category:u(f.val.ivpa_limit_category)?u(f.val.ivpa_limit_category[i]):false,
				ivpa_limit_product:u(f.val.ivpa_limit_product)?u(f.val.ivpa_limit_product[i]):false,
				ivpa_limit_type:u(f.val.ivpa_limit_type)?u(f.val.ivpa_limit_type[i]):false,
				ivpa_multiselect:u(f.val.ivpa_multiselect)?u(f.val.ivpa_multiselect[i]):false,
				ivpa_required:u(f.val.ivpa_required)?u(f.val.ivpa_required[i]):false,
				size:u(f.val.ivpa_size)?u(f.val.ivpa_size[i]):false,
				ivpa_svariation:u(f.val.ivpa_svariation)?u(f.val.ivpa_svariation[i]):false,
				custom_order:u(f.val.ivpa_custom_order)?u(f.val.ivpa_custom_order[i]):false,
				options: []
			} );

			if ( getStyle == 'text' && u(f.val.ivpa_custom) && u(f.val.ivpa_custom[i]) ) {
				r[i].text = {
					style:u(f.val.ivpa_custom[i].style)?f.val.ivpa_custom[i].style.toString().substring(5):'background',
					normal:u(f.val.ivpa_custom[i].normal),
					active:u(f.val.ivpa_custom[i].active),
					disabled:u(f.val.ivpa_custom[i].disabled),
					outofstock:u(f.val.ivpa_custom[i].outofstock),
				}
				delete f.val.ivpa_custom[i].style;
				delete f.val.ivpa_custom[i].normal;
				delete f.val.ivpa_custom[i].active;
				delete f.val.ivpa_custom[i].disabled;
				delete f.val.ivpa_custom[i].outofstock;
			}
			else {
				r[i].text = {
					style:'background',
					normal:'#bbbbbb',
					active:'#333333',
					disabled:'#e45050',
					outofstock:'#e45050',
				}
			}

			if ( u(f.val.ivpa_custom) ) {
				var checkH = u(f.val.ivpa_custom[i]);
				if ( checkH === false ) {
					checkH = u(f.val.ivpa_name)?u(f.val.ivpa_name[i]):[];
				}

				$.each( checkH, function(n,m) {
					r[i].options.push( {
						slug: n,
						name: u(f.val.ivpa_name)?(u(f.val.ivpa_name[i])?u(f.val.ivpa_name[i][n]):false):false,
						value: u(f.val.ivpa_custom)?(u(f.val.ivpa_custom[i])?u(f.val.ivpa_custom[i][n]):false):false,
						tooltip: u(f.val.ivpa_tooltip)?(u(f.val.ivpa_tooltip[i])?u(f.val.ivpa_tooltip[i][n]):false):false,
						price:  u(f.val.ivpa_price)?(u(f.val.ivpa_price[i])?u(f.val.ivpa_price[i][n]):false):false,
					} );
				} );
			}


		});


		f.val = r;

	});

	$(document).on( 'svx-wc_ivpa_attribute_customization-save', function(e,f) {

		var r = {
			ivpa_addprice: {},
			ivpa_archive_include: [],
			ivpa_attr: [],
			ivpa_custom: {},
			ivpa_desc: [],
			ivpa_limit_category: {},
			ivpa_limit_product: {},
			ivpa_limit_type: {},
			ivpa_multiselect: [],
			ivpa_name: {},
			ivpa_price: {},
			ivpa_size: [],
			ivpa_style: [],
			ivpa_svariation: [],
			ivpa_title: [],
			ivpa_tooltip: {},
			ivpa_custom_order: [],
			ivpa_required: []
		};

		$.each( f.val, function(i,b) {

			if ( u(b) ) {
				if ( u(b.name) === false || u(b.type) == 'ivpa_custom' && b.name == '' ) {
					b.name = 'Not set';
				}
				r.ivpa_title[i] = u(b.name);

				r.ivpa_archive_include[i] = u(b.ivpa_archive_include);
				r.ivpa_desc[i] = u(b.ivpa_desc);
				r.ivpa_multiselect[i] = u(b.ivpa_multiselect);
				r.ivpa_required[i] = u(b.ivpa_required);
				r.ivpa_style[i] = u(b.style)?_get_style_back(b.style):'ivpa_text';
				r.ivpa_svariation[i] = u(b.ivpa_svariation);

				if ( !u(r.ivpa_name[i]) ) {
					r.ivpa_name[i] = {};
				}
				if ( !u(r.ivpa_price[i]) ) {
					r.ivpa_price[i] = {};
				}
				if ( !u(r.ivpa_tooltip[i]) ) {
					r.ivpa_tooltip[i] = {};
				}

				if ( u(b.type) == 'ivpa_attr' ) {
					r.ivpa_attr[i] = u(b.taxonomy);
					r.ivpa_custom_order[i] = u(b.custom_order);
				}
				else {
					r.ivpa_attr[i] = 'ivpa_custom';
				}

				if ( !u(r.ivpa_custom[i]) ) {
					r.ivpa_custom[i] = {};
				}
				if ( u(b.options) ) {
					$.each( b.options, function(n,q) {
						r.ivpa_name[i][q.slug] = u(q.name)===false?(u(b.terms)===false?'':b.terms[n].name):q.name;
						r.ivpa_custom[i][q.slug] = u(q.value)===false?'':q.value;
						r.ivpa_price[i][q.slug] = u(q.price)===false?'':q.price;
						r.ivpa_tooltip[i][q.slug] = u(q.tooltip)===false?'':q.tooltip;
					} );
				}

				if ( r.ivpa_style[i] == 'ivpa_text' && u(b.text) ) {
					r.ivpa_custom[i].active = u(b.text.active);
					r.ivpa_custom[i].disabled = u(b.text.disabled);
					r.ivpa_custom[i].normal = u(b.text.normal);
					r.ivpa_custom[i].outofstock = u(b.text.outofstock);
					r.ivpa_custom[i].style = u(b.text.style)===false?'ivpa_background':'ivpa_'+b.text.style;
				}

				r.ivpa_addprice[i] = u(b.ivpa_addprice);
				r.ivpa_limit_category[i] = u(b.ivpa_limit_category);
				r.ivpa_limit_product[i] = u(b.ivpa_limit_product);
				r.ivpa_limit_type[i] = u(b.ivpa_limit_type);

				if ( $.inArray( u(b.style), [ 'image', 'color' ] ) > -1 ) {
					r.ivpa_size[i] = u(b.size)?b.size:32;
				}
			}
		});

		f.save_val = r;

	});

	$(document).on( 'change', '.svx-option-list-item-container select[data-option="taxonomy"]', function() {
		var v = $(this);
		$.each( svx.settings.wc_ivpa_attribute_customization.val, function(i,o) {
			if ( u(o.taxonomy) == v.val() ) {
				alert( 'Already customized!' );
				v.val('');
				return false;
			}
		} );
	} );

	function sanitize_title(s) {
		if ( u(s) ) {
			s = s.toString().replace(/^\s+|\s+$/g, '');
			s = s.toLowerCase();

			var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
			var to   = "aaaaeeeeiiiioooouuuunc------";

			for (var i=0, l=from.length ; i<l ; i++)
			{
				s = s.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
			}

			s = s.replace('.', '-')
				.replace(/[^a-z0-9 -]/g, '')
				.replace(/\s+/g, '-')
				.replace(/-+/g, '-');
		}
		else {
			s = '';
		}

		return s;
	}

})(jQuery);