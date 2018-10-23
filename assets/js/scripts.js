(function($){

"use strict";

	var ivpa_strings = {};

	ivpa_strings.variable = typeof ivpa !== 'undefined' ? ivpa.localization.variable : '';
	ivpa_strings.simple = typeof ivpa !== 'undefined' ? ivpa.localization.simple : '';
	ivpa_strings.injs = {};
	ivpa_strings.sizes = {};

	if (!Object.keys) {
		Object.keys = function (obj) {
			var keys = [],
				k;
			for (k in obj) {
				if (Object.prototype.hasOwnProperty.call(obj, k)) {
					keys.push(k);
				}
			}
			return keys;
		};
	}

	function getObjects(obj, key, val) {
		var objects = [];
		for (var i in obj) {
			if (!obj.hasOwnProperty(i)) continue;
			if (typeof obj[i] == 'object') {
				objects = objects.concat(getObjects(obj[i], key, val));
			} else if (i == key && obj[key] == val || obj[key] == '' ) {
				objects.push(obj);
			}
		}
		return objects;
	}

	function baseNameHTTP( str ) {
		var base = new String(str);
		if(base.lastIndexOf('.') != -1) {
			base = base.substring(0, base.lastIndexOf('.'));
		}
		return base.replace(/(^\w+:|^)\/\//, '');
	}

	function baseName( str ) {
		var base = new String(str);
		if(base.lastIndexOf('.') != -1) {
			base = base.substring(0, base.lastIndexOf('.'));
		}
		return base;
	}

	var currVariations = {};

	function ivpa_register_310() {

		if ( $('.ivpa-register:not(.ivpa_registered)').length > 0 ) {

			var $dropdowns = $('#ivpa-content .ivpa_term');

			$dropdowns
			.on('mouseover', function()
			{
				var $this = $(this);

				if ($this.prop('hoverTimeout'))
				{
					$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
				}

				$this.prop('hoverIntent', setTimeout(function()
				{
					$this.addClass('ivpa_hover');
				}, 250));
				})
			.on('mouseleave', function()
				{
				var $this = $(this);

				if ($this.prop('hoverIntent'))
				{
					$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
				}

				$this.prop('hoverTimeout', setTimeout(function()
				{
					$this.removeClass('ivpa_hover');
				}, 250));
			});

			$('.ivpa-register:not(.ivpa_registered):visible').each( function() {

				if ( $(this).find('.ivpa_showonly').length == 0 ) {
					var curr_element = $(this);
					var curr_id = curr_element.attr('data-id');

					if ( typeof currVariations[curr_id] == 'undefined' ) {
						currVariations[curr_id] = $.parseJSON( curr_element.attr('data-variations') );
					}

					curr_element.addClass('ivpa_registered');

					if ( curr_element.find('.ivpa_attribute .ivpa_term.ivpa_clicked').length > 0 ) {
						curr_element.find('.ivpa_attribute .ivpa_term.ivpa_clicked').each( function() {
							$(this).closest('.ivpa_attribute').addClass('ivpa_activated').addClass('ivpa_clicked');
							call_ivpa($(this),curr_element,currVariations[curr_id],'register');
							if ($(this).hasClass('ivpa_outofstock')) {
								$(this).removeClass('ivpa_clicked');
							}
						});
					}
					else {
						curr_element.find('.ivpa_attribute .ivpa_term:first').each( function() {
							call_ivpa($(this),curr_element,currVariations[curr_id],'register');
						});
					}
				}

			});

		}
	}

	$(document).ready(function() {
		setTimeout( function(){ ivpa_register_310(); }, 250 );
	});

	if ( ivpa.outofstock == 'clickable' ) {
		var ivpaElements = '.ivpa_attribute:not(.ivpa_showonly) .ivpa_term';
	}
	else {
		var ivpaElements = '.ivpa_attribute:not(.ivpa_showonly) .ivpa_term:not(.ivpa_outofstock)';
	}
	if ( ivpa.disableunclick == 'yes' ) {
		ivpaElements += ':not(.ivpa_clicked)';
	}

	var ivpaProcessing = false;
	$(document).on( 'click', ivpaElements, function() {

		if ( ivpaProcessing === true ) {
			return false;
		}


		ivpaProcessing = true;

		var curr_element = $(this).closest('.ivpa-register');
		var curr_id = curr_element.attr('data-id');

		if ( typeof currVariations[curr_id] == 'undefined' ) {
			currVariations[curr_id] = $.parseJSON( curr_element.attr('data-variations') );
		}

		call_ivpa($(this),curr_element,currVariations[curr_id],'default');

	});

	function call_ivpa(curr_this,curr_element,curr_variations,action) {

		var curr_el = curr_this;
		var curr_el_term = curr_el.attr('data-term');

		var curr = curr_el.closest('.ivpa_attribute');
		var curr_attr = curr.attr('data-attribute');

		var main = curr.closest('.ivpa-register');

		curr_element.attr('data-selected', '');

		if ( ivpa.backorders == 'yes' ) {
			curr_element.find('.ivpa_attribute .ivpa_term.ivpa_backorder').removeClass('ivpa_backorder');
			curr_element.find('.ivpa_attribute .ivpa_term.ivpa_backorder_not').removeClass('ivpa_backorder_not');
		}

		if ( action == 'default' ) {

			if ( !curr.hasClass('ivpa_activated') ) {
				curr.addClass('ivpa_activated');
			}
			else if ( curr.find('.ivpa_term.ivpa_clicked').length == 0 ) {
				curr.removeClass('ivpa_activated');
			}

			var curr_selectbox = $(document.getElementById(curr_attr));
			if ( !curr_el.hasClass('ivpa_clicked') ) {
				curr.find('.ivpa_term').removeClass('ivpa_clicked');
				curr_el.addClass('ivpa_clicked');
				curr.addClass('ivpa_clicked');
				if ( curr_element.attr('id') == 'ivpa-content' ) {
					curr_selectbox.trigger('focusin');
					if ( curr_selectbox.find('option[value="'+curr_el_term+'"]').length > 0 ) {
						curr_selectbox.val(curr_el_term).trigger('change');
					}
					else {
						curr_selectbox.val('').trigger('focusin').trigger('change').val(curr_el_term).trigger('change');
					}
				}
				if ( curr.hasClass('ivpa_selectbox') ) {

					curr.find('.ivpa_select_wrapper_inner').scrollTop(0).removeClass('ivpa_selectbox_opened');
					var sel = curr.find('span[data-term="'+curr_el_term+'"]').text();
					if ( typeof ivpa_strings.injs[curr_attr] == 'undefined' ) {
						ivpa_strings.injs[curr_attr] = curr.find('.ivpa_select_wrapper_inner .ivpa_title').text();
					}
					curr.find('.ivpa_select_wrapper_inner .ivpa_title').text(sel);
				}
			}
			else {
				curr_el.removeClass('ivpa_clicked');
				curr.removeClass('ivpa_clicked');
				if ( curr_element.attr('id') == 'ivpa-content' ) {
					curr_selectbox.find('option:selected').removeAttr('selected').trigger('change');
				}
				if ( curr.hasClass('ivpa_selectbox') ) {

					curr.find('.ivpa_select_wrapper_inner').scrollTop(0).removeClass('ivpa_selectbox_opened');
					if ( typeof ivpa_strings.injs[curr_attr] !== 'undefined' ) {
						curr.find('.ivpa_select_wrapper_inner .ivpa_title').text(ivpa_strings.injs[curr_attr]);
					}
					else {
						curr.find('.ivpa_select_wrapper_inner .ivpa_title').text(ivpa.localization.select);
					}
				}
			}
		}



		/*if ( action !== 'register' ) {*/
			check_selections(curr_element);
		/*}*/


		$.each( main.find('.ivpa_attribute'), function() {

			var curr_keys = [];
			var curr_vals = [];
			var curr_objects = {};

			var ins_curr = $(this);
			var ins_curr_attr = ins_curr.attr('data-attribute');

			var ins_curr_par = ins_curr.closest('.ivpa-register');

			var m=0;

			$.each( ins_curr_par.find('.ivpa_attribute:not([data-attribute="'+ins_curr_attr+'"]) .ivpa_term.ivpa_clicked'), function() {

				var sep_curr = $(this);
				var sep_curr_par = sep_curr.closest('.ivpa_attribute');

				var a = sep_curr_par.attr('data-attribute');
				var t = sep_curr.attr('data-term');

				curr_keys.push( a );
				curr_vals.push( t );

				m++;

			});

			$.each(curr_variations, function(vrl_curr_index, vrl_curr) {

				var found = false;

				var p=0;

				$.each(curr_keys, function(l,b) {

					var curr_set = getObjects(vrl_curr.attributes, 'attribute_'+b, curr_vals[l]);
					if ( $.isEmptyObject(curr_set) === false ) {
						p++;
					}
				});

				if ( p === m ) {
					found = true;
				}

				if ( found === true && vrl_curr.is_in_stock === true ) {
					$.each(vrl_curr.attributes , function(hlp_curr_index, hlp_curr_item) {

						var hlp_curr_attr = hlp_curr_index.replace('attribute_', '');

						if ( ins_curr_attr == hlp_curr_attr ) {

							if ( typeof curr_objects[hlp_curr_attr] == 'undefined' ) {
								curr_objects[hlp_curr_attr] = [];
							}

							if ( $.inArray(hlp_curr_item, curr_objects[hlp_curr_attr]) == -1 ) {
								curr_objects[hlp_curr_attr].push(hlp_curr_item);
							}

						}

					} );

				}

			} );

			if ( $.isEmptyObject(curr_objects) === false ) {
				$.each(curr_objects , function(curr_stock_attr, curr_stock_item) {
					curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term').removeClass('ivpa_instock').removeClass('ivpa_outofstock');
					if ( curr_stock_item.length == 1 && curr_stock_item[0] == '' ) {
						curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term').addClass('ivpa_instock');
					}
					else {
						$.each( curr_stock_item, function(curr_stock_id, curr_stock_term) {
							if ( curr_stock_term !== '' ) {
								curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term[data-term="'+curr_stock_term+'"]').addClass('ivpa_instock');
							}
							else {
								curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term:not(.ivpa_instock)').addClass('ivpa_instock');
							}
						});
						curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term:not(.ivpa_instock)').addClass('ivpa_outofstock');
					}
				});
			}

		} );

		if ( curr_element.hasClass('ivpa-stepped') ) {
			curr_element.find('.ivpa_attribute, .ivpa_custom_option').eq(0).show();
			check_steps(curr);
		}

		if ( ivpa.backorders == 'yes' ) {

			if ( curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').length < 2 ) {

				if ( curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').length == 0 ) {
					var activeElements = curr_element.find('.ivpa_attribute.ivpa_clicked:not([data-attribute="'+curr_attr+'"])');
					var activeLook = '.ivpa_attribute[data-attribute="'+curr_attr+'"]';
				}
				else {
					var activeElements = curr_element.find('.ivpa_attribute.ivpa_clicked');
					var activeLook = '.ivpa_attribute:not(.ivpa_clicked)';
				}

				var activeVar = {};

				var activeCount = 0;
				$.each( activeElements, function() {
					activeVar['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
					activeCount++;
				});

				$.each(curr_variations, function(vrl_curr_index, vrl_curr) {

					if ( $.isEmptyObject( vrl_curr.attributes ) === false ) {
						var cNt = 0;
						$.each( activeVar, function(u3,o5) {
							if ( typeof vrl_curr.attributes[u3] !== 'undefined' && vrl_curr.attributes[u3] == o5 || typeof vrl_curr.attributes[u3] !== 'undefined'  && vrl_curr.attributes[u3] == '' ) {
								cNt++;
							}
						});

						if ( activeCount == cNt ) {
							if ( vrl_curr.backorders_allowed === true && vrl_curr.is_in_stock === true ) {
								var attrChek = 'attribute_'+curr_element.find(activeLook).attr('data-attribute');
								if ( typeof vrl_curr.attributes[attrChek] !== 'undefined' ) {
									if ( vrl_curr.attributes[attrChek] == '' ) {
										curr_element.find(activeLook+' .ivpa_term:not(.ivpa_backorder)').addClass('ivpa_backorder');
									}
									else {
										curr_element.find(activeLook+' .ivpa_term[data-term="'+vrl_curr.attributes[attrChek]+'"]:not(.ivpa_backorder)').addClass('ivpa_backorder');
									}
								}
							}
							else {
								var attrChek = 'attribute_'+$(activeLook).attr('data-attribute');
								if ( typeof vrl_curr.attributes[attrChek] !== 'undefined' ) {
									if ( vrl_curr.attributes[attrChek] == '' ) {
										curr_element.find(activeLook+' .ivpa_term:not(.ivpa_backorder_not)').addClass('ivpa_backorder_not');
									}
									else {
										curr_element.find(activeLook+' .ivpa_term[data-term="'+vrl_curr.attributes[attrChek]+'"]:not(.ivpa_backorder_not)').addClass('ivpa_backorder_not');
									}
								}
								$.each( vrl_curr.attributes, function(j3,i6) {
									if ( j3 !== attrChek ) {
										if ( i6 == '' ) {
											curr_element.find('.ivpa_attribute[data-attribute="'+j3.replace('attribute_','')+'"] .ivpa_term:not(.ivpa_backorder_not)').addClass('ivpa_backorder_not');
										}
										else {
											curr_element.find('.ivpa_attribute[data-attribute="'+j3.replace('attribute_','')+'"] .ivpa_term[data-term="'+i6+'"]:not(.ivpa_backorder_not)').addClass('ivpa_backorder_not');
										}
									}
								});
							}
						}
					}
				});
			}

		}

		if ( curr_element.attr('id') !== 'ivpa-content' ) {
			var container = curr_element.closest(ivpa.settings.archive_selector);

			if ( curr_element.find('.ivpa_attribute').length > 0 && curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').length == 0 ) {

				var curr_elements = curr_element.find('.ivpa_attribute.ivpa_clicked');
				var curr_var = {};

				curr_elements.each( function() {
					curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
				});

				var i = curr_element.find('.ivpa_attribute').length;

				$.each( curr_variations, function(t,f) {

					var o = 0;
					var found = false;

					$.each( curr_var, function(w,c) {
						var curr_set = getObjects(f.attributes, w, c);
						if ( $.isEmptyObject(curr_set) === false ) {
							o++;
						}
					});

					if ( o === i ) {
						found = true;
					}

					if ( found === true && f.is_in_stock === true ) {

						curr_element.attr('data-selected', f.variation_id);

						var image = f.ivpa_image;

						if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

							if ( image != '' ) {

								var imgPreload = new Image();
								$(imgPreload).attr({
									src: image
								});

								if (imgPreload.complete || imgPreload.readyState === 4) {

								}
								else {

									container.addClass('ivpa-image-loading');
									container.fadeTo( 100, 0.7 );

									$(imgPreload).load(function (response, status, xhr) {
										if (status == 'error') {
											console.log('101 Error!');
										}
										else {
											container.removeClass('ivpa-image-loading');
											container.fadeTo( 100, 1 );
										}
									});
								}

								if ( container.find('img[data-default-image]').length > 0 ) {
									var archive_image = container.find('img[data-default-image]');
								}
								else {
									var archive_image = container.find('img.wp-post-image:first');

									if ( archive_image.next().is('img') ) {
										archive_image.push(archive_image.next());
									}
								}

								var rmbrSet = '';
								$.each( archive_image, function(i,e) {

									var defaultImg = curr_element.attr('data-image');
									var newImg = image;
									var srcset = $(this).attr('srcset');

									if ( !$(this).attr('data-default-image') ) {
										$(this).attr('data-default-image',(i==0?defaultImg:$(this).attr('src')));
									}

									var thisRc = $(this).attr('src');
									var thisRcFixed =  getUrlNoSuffix(thisRc);
									$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );
									//$(this).attr('src',newImg);

									var shopKitSupport = $(this).parent();
									if ( shopKitSupport.is('.shopkit-loop-image-inner') ) {
										if ( !shopKitSupport.attr('data-default-bg') ) {
											shopKitSupport.attr('data-default-bg',shopKitSupport.css('background'));
										}
										var stringRplc = shopKitSupport.css('background').split('"');
										shopKitSupport.css({background:stringRplc[0]+newImg+stringRplc[2]});
									}

									if ( typeof srcset != 'undefined' ) {

										var defaultSrc = $(this).attr('data-default-srcset');
										if ( typeof defaultSrc == 'undefined' ) {
											$(this).attr('data-default-srcset',srcset);
											defaultSrc = srcset;
										}

										if ( i==0 ) {

											var re = new RegExp(baseNameHTTP(defaultImg), 'g');
											srcset = defaultSrc.replace(re, baseNameHTTP(newImg));
											$(this).attr('srcset', srcset);
											rmbrSet= srcset;
										}
										else {
											$(this).attr('srcset', rmbrSet);
										}

									}

								});

							}
							else {

								var archive_image = container.find('img[data-default-image]');
								if ( archive_image.length > 0 ) {
									archive_image.each( function(i,e) {

										var defaultImg = $(this).attr('src');
										var newImg = $(this).attr('data-default-image');

										var thisRc = $(this).attr('src');
										var thisRcFixed =  getUrlNoSuffix(thisRc);
										$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );
										//$(this).attr('src', newImg);

										var shopKitSupport = $(this).parent();
										if ( shopKitSupport.attr('data-default-bg') ) {
											shopKitSupport.css({background:shopKitSupport.attr('data-default-bg')});
										}

										var srcset = $(this).attr('srcset');
										if ( typeof srcset != 'undefined' ) {
											var re = new RegExp(defaultImg, 'g');
											srcset = srcset.replace(re, baseNameHTTP(newImg));
											$(this).attr('srcset', $(this).attr('data-default-srcset')).removeAttr('data-default-srcset');
										}

									});

								}
							}

						}

						if ( ivpa.backorders == 'yes' ) {
							if ( curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').length == 0 && curr_element.find('.ivpa_attribute[data-attribute="'+curr_attr+'"] .ivpa_term.ivpa_clicked.ivpa_backorder:not(.ivpa_backorder_not)').length > 0 &&  f.availability_html !== '' && curr_element.find('.ivpa_backorder_allow').length == 0 ) {
								var avaHtml = '<div class="ivpa_backorder_allow">'+f.availability_html+'</div>';
								curr_element.append($(avaHtml).fadeIn());
							}
							else {
								if ( curr_element.find('.ivpa_attribute[data-attribute="'+curr_attr+'"] .ivpa_term.ivpa_clicked.ivpa_backorder:not(.ivpa_backorder_not)').length == 0 ) {
									if ( curr_element.find('.ivpa_backorder_allow').length > 0 ) {
										curr_element.find('.ivpa_backorder_allow').remove();
									}
								}
							}
						}

						var price = f.price_html;

						if ( price != '' ) {
							container.find(ivpa.settings.price_selector+':not(.ivpa-hidden-price '+ivpa.settings.price_selector+')').each( function() {
								if ( $(this).parents('.ivpa-content').length > 0 ) {
									return true;
								}
								$(this).replaceWith(price);
							});
						}

						ivpaProcessing= false;
						return false;


					}
				});

			}
			else {

				if ( curr_element.find('.ivpa_attribute.ivpa_clicked').length > 0 ) {

					var curr_elements = curr_element.find('.ivpa_attribute.ivpa_clicked');
					var curr_var = {};

					var vL = 0;
					curr_elements.each( function() {
						curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
						vL++;
					});

					var i = curr_element.find('.ivpa_attribute').length;
					var curr_variations_length = curr_variations.length;
					var found = [];
					var iL = 0;

					var hasCount = 0;
					curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').each( function() {
						hasCount = $(this).find('.ivpa_term').length*(hasCount==0?1:hasCount);
					});

					$.each( curr_variations, function(t,f) {

						var o = 0;
						$.each( curr_var, function(w,c) {
							var curr_set = getObjects(f.attributes, w, c);
							if ( $.isEmptyObject(curr_set) === false ) {
								o++;
							}
						});

						if ( vL == o ) {
							if ( $.inArray( f.ivpa_image, found ) < 0 ) {
								found.push(f.ivpa_image);
								iL++;
							}
						}

						if ( !--curr_variations_length ) {

							if ( typeof found[0] !== "undefined" &&  ( hasCount !== iL || curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').length == 1 ) !== false ) {

								var image = found[0];

								if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

									if ( image != '' ) {

										var imgPreload = new Image();
										$(imgPreload).attr({
											src: image
										});

										if (imgPreload.complete || imgPreload.readyState === 4) {

										}
										else {

											container.addClass('ivpa-image-loading');
											container.fadeTo( 100, 0.7 );

											$(imgPreload).load(function (response, status, xhr) {
												if (status == 'error') {
													console.log('101 Error!');
												}
												else {
													container.removeClass('ivpa-image-loading');
													container.fadeTo( 100, 1 );
												}
											});
										}

								if ( container.find('img[data-default-image]').length > 0 ) {
									var archive_image = container.find('img[data-default-image]');
								}
								else {
									var archive_image = container.find('img.wp-post-image:first');
									if ( archive_image.next().is('img') ) {
										archive_image.push(archive_image.next());
									}
								}

								var rmbrSet = '';
								$.each( archive_image, function(i,e) {

									var defaultImg = curr_element.attr('data-image');
									var newImg = image;
									var srcset = $(this).attr('srcset');

									if ( !$(this).attr('data-default-image') ) {
										$(this).attr('data-default-image',defaultImg);
									}
									var thisRc = $(this).attr('src');
									var thisRcFixed =  getUrlNoSuffix(thisRc);
									$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );

									var shopKitSupport = $(this).parent();
									if ( shopKitSupport.is('.shopkit-loop-image-inner') ) {
										if ( !shopKitSupport.attr('data-default-bg') ) {
											shopKitSupport.attr('data-default-bg',shopKitSupport.css('background'));
										}
										var stringRplc = shopKitSupport.css('background').split('"');
										shopKitSupport.css({background:stringRplc[0]+newImg+stringRplc[2]});
									}

									if ( typeof srcset != 'undefined' ) {

										var defaultSrc = $(this).attr('data-default-srcset');
										if ( typeof defaultSrc == 'undefined' ) {
											$(this).attr('data-default-srcset',srcset);
											defaultSrc = srcset;
										}

										if ( i==0 ) {

											var re = new RegExp(baseNameHTTP(defaultImg), 'g');
											srcset = defaultSrc.replace(re, baseNameHTTP(newImg));
											$(this).attr('srcset', srcset);
											rmbrSet= srcset;
										}
										else {
											$(this).attr('srcset', rmbrSet);
										}

									}

								});

							}
							else {

								var archive_image = container.find('img[data-default-image]');
								if ( archive_image.length > 0 ) {
									archive_image.each( function(i,e) {

										var defaultImg = $(this).attr('src');
										var newImg = $(this).attr('data-default-image');

										var thisRc = $(this).attr('src');
										var thisRcFixed =  getUrlNoSuffix(thisRc);
										$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );

										var shopKitSupport = $(this).parent();
										if ( shopKitSupport.attr('data-default-bg') ) {
											shopKitSupport.css({background:shopKitSupport.attr('data-default-bg')});
										}

										var srcset = $(this).attr('srcset');
										if ( typeof srcset != 'undefined' ) {

											var re = new RegExp(defaultImg, 'g');
											srcset = srcset.replace(re, baseNameHTTP(newImg));
											$(this).attr('srcset', $(this).attr('data-default-srcset')).removeAttr('data-default-srcset');

										}

									});

								}

									}

								}

							}

							var curr_price = container.find('.ivpa-hidden-price').html();
							container.find(ivpa.settings.price_selector+':not(.ivpa-hidden-price '+ivpa.settings.price_selector+')').replaceWith(curr_price);

						}

					});

					ivpaProcessing= false;
					return false;

				}
				else {

					if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

						var archive_image = container.find('img[data-default-image]');
						if ( archive_image.length > 0 ) {
							archive_image.each( function(i,e) {

								var defaultImg = $(this).attr('src');
								var newImg = $(this).attr('data-default-image');

								var thisRc = $(this).attr('src');
								var thisRcFixed =  getUrlNoSuffix(thisRc);
								$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );

								var shopKitSupport = $(this).parent();
								if ( shopKitSupport.attr('data-default-bg') ) {
									shopKitSupport.css({background:shopKitSupport.attr('data-default-bg')});
								}

								var srcset = $(this).attr('srcset');
								if ( typeof srcset != 'undefined' ) {

									var re = new RegExp(defaultImg, 'g');
									srcset = srcset.replace(re, baseNameHTTP(newImg));
									$(this).attr('srcset', $(this).attr('data-default-srcset')).removeAttr('data-default-srcset');

								}

							});
						}

					}

					var curr_price = container.find('.ivpa-hidden-price').html();
					container.find(ivpa.settings.price_selector+':not(.ivpa-hidden-price '+ivpa.settings.price_selector+')').replaceWith(curr_price);

					ivpaProcessing= false;
					return false;

				}

				if ( ivpa.backorders == 'yes' && curr_element.find('.ivpa_backorder_allow').length > 0 ) {
					curr_element.find('.ivpa_backorder_allow').remove();
				}

			}

			ivpaProcessing= false;
			return false;

		}
		else {

			if ( ivpa.imageswitch == 'no' ) {
				ivpaProcessing= false;
				return false;
			}

			if ( curr_element.find('.ivpa_attribute.ivpa_clicked').length > 0 ) {

				var curr_elements = curr_element.find('.ivpa_attribute.ivpa_clicked');
				var curr_var = {};

				var vL = 0;
				curr_elements.each( function() {
					curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
					vL++;
				});

				var i = curr_element.find('.ivpa_attribute').length;
				var curr_variations_length = curr_variations.length;
				var found = [];
				var iL = 0;

				var hasCount = 0;
				curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').each( function() {
					hasCount = $(this).find('.ivpa_term').length*(hasCount==0?1:hasCount);
				});

				$.each( curr_variations, function(t,f) {

					var o = 0;
					$.each( curr_var, function(w,c) {
						var curr_set = getObjects(f.attributes, w, c);
						if ( $.isEmptyObject(curr_set) === false ) {
							o++;
						}
					});

					if ( vL == o ) {
						if ( $.inArray( f.ivpa_image, found ) < 0 ) {
							found.push(f.ivpa_image);
							iL++;
						}
					}

					if ( !--curr_variations_length ) {

						if ( ivpa.settings.single_selector == '' ) {
							var container = curr_element.closest(ivpa.settings.archive_selector).find('.product-gallery');
							if ( container.length == 0 ) {
								container = curr_element.closest(ivpa.settings.archive_selector).find('.images');
							}
						}
						else {
							var container = $(ivpa.settings.single_selector);
						}

						if ( typeof found[0] !== "undefined" &&  ( hasCount !== iL || curr_element.find('.ivpa_attribute:not(.ivpa_clicked)').length == 1 ) !== false ) {

							var image = found[0];

							if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

								if ( image != '' ) {

									var imgPreload = new Image();
									$(imgPreload).attr({
										src: image
									});

									if (imgPreload.complete || imgPreload.readyState === 4) {

									}
									else {

										container.addClass('ivpa-image-loading');
										container.fadeTo( 100, 0.7 );

										$(imgPreload).load(function (response, status, xhr) {
											if (status == 'error') {
												console.log('101 Error!');
											}
											else {
												container.removeClass('ivpa-image-loading');
												container.fadeTo( 100, 1 );
											}
										});
									}

									var defaultImg = curr_element.attr('data-image');

									if ( container.find('img[data-default-image]').length > 0 ) {
										var archive_image = container.find('img[data-default-image]');
									}
									else {
										var archive_image = container.find('img[src*="'+baseNameHTTP(defaultImg)+'"]');
										if ( archive_image.length == 0 ) {
											archive_image = container.find('img:first');
										}
									}

									var productGallery = $('.woocommerce-product-gallery');
									if ( productGallery.length>0 ) {
										var flex = productGallery.data('flexslider');
										if ( typeof flex != 'undefined' && typeof flex.currentSlide != 'undefined' && flex.currentSlide > 0 ) {
											productGallery.flexslider( 0 );
										}
									}

									var rmbrSet = '';
									$.each( archive_image, function(i,e) {

										var newImg = image;
										var srcset = $(this).attr('srcset');

										if ( !$(this).attr('data-default-image') ) {
											$(this).attr('data-default-image',(i==0?defaultImg:$(this).attr('src')));
										}
										if ( i==0 ) {

											if ( typeof flex != 'undefined' && productGallery.find('.flex-viewport').length>0 && parseInt( productGallery.find('.flex-viewport').css('height'), 10 ) > 0 ) {
												var rmbrHeight = parseInt( productGallery.find('.flex-viewport').css('height'), 10 );
											}

											$(this).attr('data-src',newImg);
											$(this).attr('data-large-image',newImg);

											var thisRc = $(this).attr('src');
											var thisRcFixed =  getUrlNoSuffix(thisRc);
											$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );

										}
										else {
											var thisRc = $(this).attr('src');
											var thisRcFixed =  getUrlNoSuffix(thisRc);

											$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );
										}

										if ( typeof srcset != 'undefined' ) {

											var defaultSrc = $(this).attr('data-default-srcset');
											if ( typeof defaultSrc == 'undefined' ) {
												$(this).attr('data-default-srcset',srcset);
												defaultSrc = srcset;
											}

											if ( i==0 ) {

												var re = new RegExp(baseNameHTTP(defaultImg), 'g');
												srcset = defaultSrc.replace(re, baseNameHTTP(newImg));
												$(this).attr('srcset', srcset);
												rmbrSet= srcset;
											}
											else {
												$(this).attr('srcset', rmbrSet);
											}

										}

									});

								}

							}

						}

					}

				});

			}
			else {

				if ( ivpa.settings.single_selector == '' ) {
					var container = curr_element.closest(ivpa.settings.archive_selector).find('.product-gallery');
					if ( container.length == 0 ) {
						container = curr_element.closest(ivpa.settings.archive_selector).find('.images');
					}
				}
				else {
					var container = $(ivpa.settings.single_selector);
				}

				if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

					var archive_image = container.find('img[data-default-image]');
					if ( archive_image.length > 0 ) {
						archive_image.each( function(i,e) {

							var defaultImg = $(this).attr('src');
							var newImg = $(this).attr('data-default-image');

							var thisRc = $(this).attr('src');
							var thisRcFixed =  getUrlNoSuffix(thisRc);
							$(this).attr('src',thisRc.replace( thisRcFixed, getUrlNoSuffix(newImg) ) );

							if ( i==0 ) {
								$(this).attr('data-src',newImg);
								$(this).attr('data-large-image',newImg);
							}

							var srcset = $(this).attr('srcset');
							if ( typeof srcset != 'undefined' ) {
								var re = new RegExp(defaultImg, 'g');
								srcset = srcset.replace(re, baseNameHTTP(newImg));
								$(this).attr('srcset', $(this).attr('data-default-srcset')).removeAttr('data-default-srcset');
							}

						});
					}

				}

			}

			ivpaProcessing= false;
			return false;

		}

	}

	function getUrlNoSuffix(theUrl) {
		theUrl = theUrl.substring(0,theUrl.lastIndexOf('.'));
		var x = theUrl.lastIndexOf('x');
		var y = theUrl.lastIndexOf('-');
		if ( x > y ) {
			var s = theUrl.substring(y);
			s = s.split('x');
			$.each( s, function(i,e) {
				var test = /^\d+$/.test(s[i]);
				if ( !test ) {
					var notWell = true;
				}
			});
			if ( typeof notWell == 'undefined' ) {
				return theUrl.substring(0,y);
			}
		}
		return theUrl;
	}

	function __get_attr(g) {
		var item = {};

		g.find('.ivpa-content .ivpa_attribute').each( function() {
			var attribute = $(this).attr('data-attribute');
			var attribute_value = $(this).find('.ivpa_term.ivpa_clicked').attr('data-term');
			item['attribute_'+attribute] = attribute_value;
		});

		return item;
	}

	function __adding_to_cart(e,f,g) {
		var container = f.closest(ivpa.settings.archive_selector);
		var find = container.find('#ivpa-content').length>0 ? '#ivpa-content' : '.ivpa-content' ;

		var var_id = container.find(find).attr('data-selected');

		if ( typeof var_id == 'undefined' || var_id == '' ) {
			var_id = container.find('[name="variation_id"]').val();
		}

		if ( typeof var_id == 'undefined' || var_id == '' ) {
			var_id = container.find(find).attr('data-id');
		}

		var item = {};

		container.find(find+' .ivpa_attribute').each( function() {
			var attribute = $(this).attr('data-attribute');
			var attribute_value = $(this).find('.ivpa_term.ivpa_clicked').attr('data-term');

			item['attribute_'+attribute] = attribute_value;
		});

		var ivpac = container.find(find+' .ivpa_custom_option').length>0 ? container.find(find+' .ivpa_custom_option [name^="ivpac_"]').serialize() : '';

		var quantity = container.find('input.ivpa_qty');
		if ( quantity.length > 0 ) {
			var qty = quantity.val();
		}
		var quantity = ( typeof qty !== "undefined" ? qty : $(this).attr('data-quantity') );

		g.variation_id = var_id;
		g.variation = item;
		g.quantity = quantity;
		g.ivpac = ivpac;
	}

	$(document).on( 'product_loops_add_to_cart', function(e,f,g) {
		__adding_to_cart(e,f,g);
	} );

	$(document).on( 'adding_to_cart', function(e,f,g) {
		__adding_to_cart(e,f,g);
	} );

	$(document).on( 'click', ivpa.settings.addcart_selector+'.product_type_variable.is-addable', function() {

		var container = $(this).closest(ivpa.settings.archive_selector);
		var var_id = container.find('.ivpa-content').attr('data-selected');

		if ( typeof var_id == 'undefined' || var_id == '' ) {
			var_id = container.find('[name="variation_id"]').val();
		}

		if ( typeof var_id == 'undefined' || var_id == '' ) {
			var_id = container.find('.ivpa-content').attr('data-id');
		}

		if ( var_id !== undefined && var_id !== '' ) {

			var product_id = $(this).attr('data-product_id');

			var quantity = container.find('input.ivpa_qty');
			if ( quantity.length > 0 ) {
				var qty = quantity.val();
			}
			var quantity = ( typeof qty !== "undefined" ? qty : $(this).attr('data-quantity') );

			var $thisbutton = $( this );

			if ( $thisbutton.is( ivpa.settings.addcart_selector ) ) {

				$thisbutton.removeClass( 'added' );
				$thisbutton.addClass( 'loading' );

				var data = {
					action: 'ivpa_add_to_cart_callback',
					product_id: product_id,
					quantity: quantity
				};

				$( 'body' ).trigger( 'adding_to_cart', [ $thisbutton, data ] );

				$.post( ivpa.ajax, data, function( response ) {

					if ( ! response )
						return;

					var this_page = window.location.toString();

					this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );

					$thisbutton.removeClass('loading');

					if ( response.error && response.product_url ) {
						window.location = response.product_url;
						return;
					}

					var fragments = response.fragments;
					var cart_hash = response.cart_hash;

					$thisbutton.addClass( 'added' );

					if ( ! ivpa.add_to_cart.is_cart && $thisbutton.parent().find( '.added_to_cart' ).size() === 0 ) {
						$thisbutton.after( ' <a href="' + ivpa.add_to_cart.cart_url + '" class="added_to_cart wc-forward" title="' + 
						ivpa.add_to_cart.i18n_view_cart + '">' + ivpa.add_to_cart.i18n_view_cart + '</a>' );
					}

					if ( fragments ) {
						$.each( fragments, function( key ) {
							$( key )
								.addClass( 'updating' )
								.fadeTo( '400', '0.6' )
								.block({
									message: null,
									overlayCSS: {
										opacity: 0.6
									}
								});
						});

						$.each( fragments, function( key, value ) {
							$( key ).replaceWith( value );
							$( key ).stop( true ).css( 'opacity', '1' ).unblock();
						});

						$( document.body ).trigger( 'wc_fragments_loaded' );
					}

					$('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
				});

				return false;

			} else {
				return true;
			}

		}

	});


	$(document).ajaxComplete( function() {
		ivpa_register_310();
	});


	$(document).on('click', '.ivpa_selectbox .ivpa_title', function() {
		var el = $(this).closest('.ivpa_select_wrapper_inner');

		if ( el.hasClass('ivpa_selectbox_opened') ) {
			el.removeClass('ivpa_selectbox_opened');
		}
		else {
			el.addClass('ivpa_selectbox_opened').delay(200).queue(function(next){
			});
		}

	});

	$('#ivpa-content .ivpa_selectbox, .ivpa-content .ivpa_selectbox').each(function(i,c){
		$(c).css('z-index',99-i);
	});

	if ( ivpa.singleajax == 'yes' ) {

		$(document).on( 'click', '.single_add_to_cart_button', function() {

			var item = {};

			var $thisbutton = $( this );
			var form = $(this).closest('form');
			var product_id = parseInt(form.find('input[name=product_id]').length>0 ? form.find('input[name=product_id]').val() : form.find('button.single_add_to_cart_button[type="submit"]').val(), 10);
			var quantity = parseInt(form.find('input[name=quantity]').val(), 10);

			if ( product_id < 1 ) {
				return false;
			}

			var data = {
				action: 'ivpa_add_to_cart_callback',
				product_id: product_id,
				quantity: quantity
			};

			$thisbutton.removeClass('added');
			$thisbutton.addClass('loading');

			$('body').trigger( 'adding_to_cart', [ $thisbutton, data ] );

			$.post( ivpa.ajax, data, function( response ) {

				if ( ! response )
					return;

				var this_page = window.location.toString();

				this_page = this_page.replace('add-to-cart', 'added-to-cart');

				$thisbutton.removeClass('loading');

				if ( response.error && response.product_url ) {
					window.location = response.product_url;
					return;
				}

				var fragments = response.fragments;
				var cart_hash = response.cart_hash;

				$thisbutton.addClass( 'added' );

				if ( ! ivpa.add_to_cart.is_cart && $thisbutton.parent().find( '.added_to_cart' ).size() === 0 ) {
					$thisbutton.after( ' <a href="' + ivpa.add_to_cart.cart_url + '" class="added_to_cart button wc-forward" title="' + 
					ivpa.add_to_cart.i18n_view_cart + '">' + ivpa.add_to_cart.i18n_view_cart + '</a>' );
				}

				if ( fragments ) {
					$.each( fragments, function( key ) {
						$( key )
							.addClass( 'updating' )
							.fadeTo( '400', '0.6' )
							.block({
								message: null,
								overlayCSS: {
									opacity: 0.6
								}
							});
					});

					$.each( fragments, function( key, value ) {
						$( key ).replaceWith( value );
						$( key ).stop( true ).css( 'opacity', '1' ).unblock();
					});

					$( document.body ).trigger( 'wc_fragments_loaded' );
				}

				$('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
			});

			return false;

		});

	}

	$(document).on( 'change', '.ivpac-change', function() {

		if ( $(this).closest('.ivpa-opt').is('[data-required="yes"]') ) {
			var doIt = false;

			if ( $(this).attr('type') == 'checkbox' ) {
				if ( $(this).is(':checked') ) {
					doIt = true;
				}
			}
			else {
				if ( $(this).val() !== '' ) {
					doIt = true;
				}
			}

			if ( doIt ) {
				$(this).closest('.ivpa-opt').addClass('ivpa-required');
			}
			else {
				$(this).closest('.ivpa-opt').removeClass('ivpa-required');
			}
			check_selections($(this).closest('.ivpa-register'));
		}
	} );

	$(document).on( 'click', '.ivpa_term.ivpa_custom[data-term="checkbox"] .ivpa_name', function() {
		$(this).closest('.ivpa_term').find('input[type="checkbox"]:first').trigger('click');
	} );

	$(document).on( 'click', '.ivpa-do span.ivpa_term', function() {

		var wrp = $(this).closest('div[data-attribute]');
		var str = [];
		var clk = $(this).hasClass('ivpa_clicked');

		if ( ivpa.disableunclick != 'no' && clk ) {
			return false;
		}

		if ( !wrp.hasClass('ivpac_input') && !wrp.hasClass('ivpac_textarea') && !wrp.hasClass('ivpac_system') && !wrp.hasClass('ivpa_attribute') || $(this).hasClass('ivpa_group_custom') ) {
			if ( !wrp.hasClass('ivpa_multiselect') ) {
				if ( clk ) {
					$(this).removeClass('ivpa_clicked');
				}
				else {
					wrp.find('.ivpa_clicked').removeClass('ivpa_clicked');
					$(this).addClass('ivpa_clicked');
				}
			}
			else {
				if ( clk ) {
					$(this).removeClass('ivpa_clicked');
				}
				else {
					$(this).addClass('ivpa_clicked');
				}
			}
		}

		if ( wrp.closest('.ivpa-register').hasClass('ivpa-stepped') ) {
			check_steps(wrp);
		}

		wrp.find('span.ivpa_clicked').each(function(){
			str.push($(this).attr('data-term'));
		});

		wrp.find('input[type="hidden"]:first').val(str.join(', ')).trigger('change');

		if ( wrp.hasClass('ivpa_selectbox') ) {
			wrp.find('.ivpa_select_wrapper_inner').scrollTop(0).removeClass('ivpa_selectbox_opened');
			var sel = wrp.find('span[data-term="'+$(this).attr('data-term')+'"]').text();
			wrp.find('.ivpa_select_wrapper_inner .ivpa_title').text(clk==true?ivpa.localization.select:sel);
		}

		check_selections(wrp.closest('.ivpa-register'));

	});

	function check_selections(e) {

		$.each( e.find('.ivpa-opt'), function(i,f) {
			f = $(f);
			if ( f.find('.ivpac-change').length == 0 ) {
				if ( f.find('.ivpa_clicked').length>0 ) {
					f.addClass('ivpa-required');
				}
				else {
					f.removeClass('ivpa-required');
				}
			}
		} );

		if ( e.attr('id') !== 'ivpa-content' ) {
			var c = e.closest(ivpa.settings.archive_selector);
			var btn = c.find('[data-product_id="'+e.attr('data-id')+'"]');

			if ( btn.hasClass('product_type_simple') || btn.hasClass('product_type_variable') || btn.hasClass('pl-product-type-simple') || btn.hasClass('pl-product-type-variable') ) {

				if ( e.find('.ivpa-opt[data-required="yes"]:not(.ivpa-required)').length == 0 ) {
					if ( !$.isEmptyObject(ivpa_strings) && btn.text().indexOf( ivpa_strings.variable ) > -1 ) {
						btn.html( btn.html().replace(ivpa_strings.variable, ivpa_strings.simple) );
					}
					btn.addClass('is-addable');
					var quantity = c.find('.ivpa_quantity');
					if ( quantity.length > 0 ) {
						quantity.stop(true,true).slideDown();
					}
				}
				else if ( e.find('.ivpa-opt[data-required="yes"]:not(.ivpa-required)').length > 0 ) {
					if ( !$.isEmptyObject(ivpa_strings) && btn.text().indexOf( ivpa_strings.simple ) > -1 ) {
						btn.html( btn.html().replace(ivpa_strings.simple, ivpa_strings.variable) );
						if ( btn.hasClass('product_type_simple') ) {
							btn.removeClass('product_type_simple').removeClass('ajax_add_to_cart').attr('href', e.attr('data-url')).addClass('product_type_variable');
						}
					}
					btn.removeClass('is-addable');
					var quantity = c.find('.ivpa_quantity');
					if ( quantity.length > 0 ) {
						quantity.stop(true,true).slideUp();
					}
				}
			}

		}
		else {
			var c = e.closest('form').length>0 ? e.closest('form') : e.closest(ivpa.settings.archive_selector);
			var btn = c.find('.single_add_to_cart_button');

			if ( e.attr('data-type') == 'simple' || e.attr('data-type') == 'variable' ) {
				if ( e.find('.ivpa-opt[data-required="yes"]:not(.ivpa-required)').length == 0 ) {
					btn.removeClass('disabled');
					btn.addClass('is-addable');
				}
				else if ( e.find('.ivpa-opt[data-required="yes"]:not(.ivpa-required)').length > 0 ) {
					btn.addClass('disabled');
					btn.removeClass('is-addable');
				}
			}
		}

	}

	function check_steps(curr) {
		if ( !curr.hasClass('ivpa-step') ) {
			curr.addClass('ivpa-step');
		}

		if ( curr.find('.ivpa_clicked').length==0 ) {
			curr.removeClass('ivpa-step').nextUntil('a').each( function() {
				$(this).removeClass('ivpa-step');
				$(this).find('.ivpa_clicked').trigger('click');
			} );
		}
	}

})(jQuery);