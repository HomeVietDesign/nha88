window.addEventListener('DOMContentLoaded', function(){
	const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
	const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

	jQuery(function($){
		$(document).on('click', 'a.popup', function(e){
			e.preventDefault();

			let $a = $(this),
				i=getParam('popup'),
				$modal = $('#modal-popup-content'),
				src = add_query_url('popup', (i==null)?1:parseInt(i)+1, $a.attr('href'));
			
			$modal.find('.modal-body').html('<iframe src="'+src+'" style="border:0;">');

			$modal.modal('show');

			return false;
		});

		// products
		function load_products($section, paged=1, scrolltop=true) {
			let $list_el = $section.find('.list-products'),
				$pagination_links_el = $section.find('.product-paginate-links'),
				query = JSON.parse($section.find('[name="query"]').val());;

			$.ajax({
				url:theme.ajax_url+'?action=products_paginate',
				method:'GET',
				data:{
					query:query
					,paged:paged
				},
				beforeSend:function(){
					$section.find('.overlay').removeClass('hide');
					
					$offset = 30;
					if($('#site-header').length>0) $offset += $('#site-header').height();
					if($('#wpadminbar').length>0) $offset += $('#wpadminbar').height();

					$('html,body').animate({
						scrollTop: $section.offset().top-$offset
					});
				},
				success:function(response){
					//$paged_el.val(paged);
					//console.log(response);
					$list_el.html(response['items']);
					$pagination_links_el.html(response['paginate_links']);

					$list_el.find(".product-images-slider").owlCarousel({
						items:1,
						lazyLoad:true,
						loop:true,
						autoplay:false,
						// autoHeight:true,
						autoplayTimeout:3000,
						autoplayHoverPause:true,
						nav: true,
						dots: false
					});

					const popoverTriggerList = $list_el.get(0).querySelectorAll('[data-bs-toggle="popover"]')
					const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));


				},
				complete:function() {
					$section.find('.overlay').addClass('hide');
				}
			});
			
		}

		$(document).on('click', '.product-paginate-links button.page-numbers', function(e){
			let $this = $(this),
				$section = $this.closest('.fw-shortcode-products'),
				paged = parseInt($this.data('paged'));

			$this.prop('disabled', true);
			load_products($section, paged);
		});


		let popped_popup_content = getCookie('popped_popup_content');
		if($('#modal-popup').length>0 && theme.preview!='1' && !popped_popup_content) {
			
			const popup_content = new bootstrap.Modal('#modal-popup');
			
			setTimeout(function(){
				popup_content.show();
				setCookie('popped_popup_content', 1, 1);
			}, 1000*parseInt(theme.popup_content_timeout));
			
		}

		function set_vh_size() {
			let vh = $(window).innerHeight();
			if($('#site-header').length>0) {
				vh -= $('#site-header').height();
			}
			if($('#wpadminbar').length>0) {
				vh -= $('#wpadminbar').height();
			}
			if($('#footer-buttons-fixed').length>0) {
				vh -= $('#footer-buttons-fixed').height();
			}
			$('#main-nav ul.sub-menu').css('max-height', `${vh}px`);
		}

		function align_submenu() {
			let win_width = $(window).width();
			$('#main-nav ul.sub-menu').each(function(index){
				let $sub_menu = $(this),
					$wrap_sub = $sub_menu.parent();
				let delta = $wrap_sub.offset().left + $sub_menu.width() - win_width;
				if( delta>0 ) {
					$sub_menu.css('right', '0');
					$sub_menu.css('left', 'auto');
				} else {
					$sub_menu.css('left', '0');
					$sub_menu.css('right', 'auto');
				}
			});
		}

		function calc_clients_sticky() {
			let $clients_sticky = $('.clients.position-sticky');
			if($clients_sticky.length>0) {
				let clients_sticky_top = $('#site-header').height();
				if($('body').hasClass('admin-bar')&&$('body').width()>601) {
					clients_sticky_top += $('#wpadminbar').height();
				}
				$clients_sticky.css('top', clients_sticky_top+'px');

				let scw = 0;
				$clients_sticky.find('a').each(function(index, el){
					//console.log($(el).width());
					scw += $(el).width()+26;
				});
				$clients_sticky.find('.content-scroll').width(scw+'px');
			}
		}

		$(window).on('resize', debounce(function(){
			//console.log('set_vh_size');
			set_vh_size();
			align_submenu();
			calc_clients_sticky();
		})).resize();

		$('a[href$="#"]').on('click', function(e){
			e.preventDefault();
			return false;
		});
		
		// xử lý sub menu
		$('#main-nav a.toggle-sub-menu').on('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			let $this = $(this);
			$this.parent('li').siblings().find('ul.sub-menu').removeClass('open');
			let sub = $this.next('ul.sub-menu');
			sub.toggleClass('open');
		});

		$('body').on('click', function(e){
			$('#main-nav ul.sub-menu').removeClass('open');
		});

		if($('body').hasClass('single-product') || $('body').hasClass('product-template-default')) {

			var sync1 = $(".single-product-images .gallery .slider");
			var sync2 = $(".single-product-images .gallery .navigation-thumbs");

			var thumbnailItemClass = '.owl-item';
			var args = {
				// video:false,
				items:1,
				lazyLoad:true,
				loop:false,
				autoplay:true,
				autoHeight:true,
				autoplayTimeout:3000,
				autoplayHoverPause:true,
				nav: true,
				dots: false
			};
			if(sync1.hasClass('has-video')) {
				args.autoplay = false;
			}
			var slides = sync1.owlCarousel(args).on('changed.owl.carousel', syncPosition).on('loaded.owl.lazy', function(e){
				let owl = $(this);
			});

			function syncPosition(el) {
				$owl_slider = $(this).data('owl.carousel');
				var loop = $owl_slider.options.loop;

				if(loop){
					var count = el.item.count-1;
					var current = Math.round(el.item.index - (el.item.count/2) - .5);
					if(current < 0) {
						current = count;
					}
					if(current > count) {
						current = 0;
					}
				}else{
					var current = el.item.index;
				}

				var owl_thumbnail = sync2.data('owl.carousel');
				var itemClass = "." + owl_thumbnail.options.itemClass;


				var thumbnailCurrentItem = sync2
				.find(itemClass)
				.removeClass("synced")
				.eq(current);

				thumbnailCurrentItem.addClass('synced');

				if (!thumbnailCurrentItem.hasClass('active')) {
					var duration = 300;
					sync2.trigger('to.owl.carousel',[current, duration, true]);
				}   
			}

			var thumbs = sync2.owlCarousel({
				items:4,
				lazyLoad:true,
				loop:false,
				margin:10,
				autoplay:false,
				nav: true,
				dots: false,
				// responsive : {
				// 	0 : {
				// 		items: 2
				// 	},
				// 	768 : {
				// 		items: 4
				// 	}
				// },
				onInitialized: function (e) {
					var thumbnailCurrentItem =  $(e.target).find(thumbnailItemClass).eq(this._current);
					thumbnailCurrentItem.addClass('synced');
				}
			})
			.on('click', thumbnailItemClass, function(e) {
				e.preventDefault();
				var duration = 300;
				var itemIndex =  $(e.target).parents(thumbnailItemClass).index();
				sync1.trigger('to.owl.carousel',[itemIndex, duration, true]);
			}).on("changed.owl.carousel", function (el) {
				var number = el.item.index;
				$owl_slider = sync1.data('owl.carousel');
				$owl_slider.to(number, 100, true);
			});
		}

		var lightbox = new PhotoSwipeLightbox({
			gallery: '.pswp-gallery',
			children: 'a',
			pswpModule: PhotoSwipe 
		});
		lightbox.init();

		/* xử lý form đặt mua hồ sơ */

		function check_input_phone_number(p) {
			const patt = /^(\+?\d{1,3}[-.\s]?)?(\(?\d{3}\)?[-.\s]?)?\d{3}[-.\s]?\d{4}$/;
			return patt.test(p);
		}

		let ajax_rp = null;
		$('#frm-request-product').on('submit', function(event){
			event.preventDefault();
			
			let submit_button = $('#request-product-submit'),
				type = $('#request_type').val(),
				id = parseInt($('#product_id').val()),
				phone = $('#customer_phone').val(),
				phone_number = '',
				name = $('#customer_name').val(),
				token = $('#frm-request-product [name="cf-turnstile-response"]').val(),

				feedback_name = $('#customer_name').next('.invalid-feedback'),
				feedback_phone = $('#customer_phone').closest('.input-group').find('.invalid-feedback'),
				validate_name = validate_phone = false, custom_value = '';

			$('#request-product-message').html('');

			if(id<=0) {
				$('#customer_phone').addClass('is-invalid');
				feedback_phone.html('Chưa lựa chọn sản phẩm!');
			} else {
				if(check_input_phone_number(phone)) {
					validate_phone = true
					$('#customer_phone').removeClass('is-invalid');
					feedback_phone.html('');
				} else {
					$('#customer_phone').addClass('is-invalid');
					feedback_phone.html('Số điện thoại không đúng!');
				}

				if(name!='') {
					validate_name = true
					$('#customer_name').removeClass('is-invalid');
					feedback_name.html('');
				} else {
					$('#customer_name').addClass('is-invalid');
					feedback_name.html('Tên không được trống');
				}

				if(validate_name && validate_phone){

					if(phone.startsWith('0')) {
						phone_number = "+84" + phone.slice(1, phone.length);
					} else if(phone.startsWith('+84')) {
						phone_number = phone;
					} else if(phone.startsWith('84')) {
						phone_number = "+"+phone;
					} else {
						phone_number = "+84" + phone;  
					}

					if(ajax_rp!=null) ajax_rp.abort();

					ajax_rp = $.ajax({
						url: theme.ajax_url,
						type: 'post',
						data: {
							action: 'request_product',
							type:type,
							id:id,
							name:name,
							phone:phone_number,
							url: window.btoa(window.location.href),
							token:token
						},
						dataType: 'json',
						beforeSend: function(xhr) {
							submit_button.text('Đang gửi..');
						},
						success: function(response) {
							
							const eventRequest = new CustomEvent('requestProduct', {
								bubbles: true,
								detail: response
							});

							if(response.code==1) {
								
								event.target.dispatchEvent(eventRequest);
							
								$('#request-product-message').html('<div class="py-3 text-center text-success">'+response.msg+'</div>');

								submit_button.text('Đã gửi');
								submit_button.closest('form').trigger('reset');

							} else {
								submit_button.text('Đồng ý');
								$('#request-product-message').html('<div class="py-3 text-center text-danger">'+response.msg+'</div>');
							}
							
							
						},
						error: function() {
							submit_button.text('Đồng ý');
							$('#request-product-message').html('<div class="py-3 text-center text-danger">Có lỗi khi gửi! Vui lòng tải lại trang rồi thử lại. Hoặc liên hệ với ban quản trị về sự cố này.</div>');
						},
						complete: function() {
							submit_button.prop('disabled', false);
						}
					});
					

				} else {
					submit_button.prop('disabled', false);
				}
			}

			return false;

		}); // submit order

		// chọn mẫu modal
		$('#request-product').on('show.bs.modal', function (event) {
			let modal = $(this),
				button = $(event.relatedTarget),
				type = button.data('type'),
				id = button.data('id'),
				src = button.data('src');

			$('#request-product-label').find('.modal-title-'+type).removeClass('hidden').siblings().addClass('hidden');
			$('#request-product-desc').find('.popup-content-'+type).removeClass('hidden').siblings().addClass('hidden');

			$('#request_type').val(type);
			$('#product_id').val(id);
			
			$('#request-product-preview').html('<img src="'+src+'">');
			
		}).on('hidden.bs.modal', function (e) {
			
			$('#customer_name').removeClass('is-invalid');
			$('#customer_name').next('.invalid-feedback').html('');
			$('#customer_phone').removeClass('is-invalid');
			$('#customer_phone').next('.invalid-feedback').html('');

			let type = $('#request_type').data('default');

			$('#request_type').val(type);
			$('#product_id').val('');

			$('#request-product-label').find('.modal-title-'+type).removeClass('hidden').siblings().addClass('hidden');
			$('#request-product-desc').find('.popup-content-'+type).removeClass('hidden').siblings().addClass('hidden');

			$('#request-product-message').html('');
			$('#request-product-preview').html('');
			$('#request-product-submit').text('Đồng ý');
	
		});


	});
});