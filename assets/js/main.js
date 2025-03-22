window.addEventListener('DOMContentLoaded', function(){
	const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
	const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

	jQuery(function($){
		// products
		function check_input_phone_number(p) {
			const patt = /^(\+?\d{1,3}[-.\s]?)?(\(?\d{3}\)?[-.\s]?)?\d{3}[-.\s]?\d{4}$/;
			return patt.test(p);
		}

		$('#request-popup').on('show.bs.modal', function (event) {
			let $modal = $(this),
				$button = $(event.relatedTarget),
				$form = $('#frm-request'),
				id = parseInt($button.data('id')),
				title = $button.data('popup-title'),
				type = $button.data('type');
			
			$('#request-popup-label').text(title);
			$('#request-product-id').val(id);
			$('#request-type').val(type);
			$('#request-product-image').html('<img src="'+$button.data('src')+'">');
			
		}).on('hidden.bs.modal', function (event) {

			$('#request-popup-label').text('');
			$('#request-product-id').val('');
			$('#request-type').val('');
			$('#request-response').html('');
			$('#request-product-image').html('');
			$('#request-submit').prop('disabled', true);


		});

		function checkRequestFormValidity() {
			let valid = true;
			$('#request-popup').find('input').each(function(index, el){
				let $el = $(el);
				switch(el.type) {
					case 'text':
						if(el.validity.valueMissing || el.validity.tooLong) {
							valid = false;
						}
						break;
					case 'tel':
						if(el.validity.valueMissing || !check_input_phone_number(el.value)) {
							valid = false;
						}
						break;
				}

			});

			return valid;
		}

		$(document).on('input', '#request-popup input', function() {
			if(checkRequestFormValidity()) {
				$('#request-submit').prop('disabled', false);
			} else {
				$('#request-submit').prop('disabled', true);
			}
		});

		let ajax_request = null;
		function submit_request(token='') {
			let $form = $('#frm-request')
				,data = $form.serializeArray()
				,$button = $form.find('[type="submit"]')
				,$response = $('#request-response')
				;
			
			$button.prop('disabled', true);

			data.push({name:'token', value:token});
			data.push({name:'url', value:window.location.href});

			$.ajax({
				url: theme.ajax_url+'?action=request',
				type: 'POST',
				data: data,
				dataType: 'json',
				beforeSend: function() {
					$response.html('<p class="text-primary">Đang gửi yêu cầu...</p>');
				},
				success: function(response) {
					const eventRequest = new CustomEvent('request', {
						bubbles: true,
						detail: { id:response.data.id, name:response.data.name, content_type:response.data.content_type, phone:response.data.phone, fb_pxl_code:response.fb_pxl_code }
					});

					if(response['code']==1) {
						event.target.dispatchEvent(eventRequest);
					} else {
						$button.prop('disabled', false);
					}

					$response.html(response['msg']);
				},
				error: function(xhr) {
					$response.html('<p class="text-danger">Có lỗi xảy ra. Xin vui lòng thử lại.</p>');
					$button.prop('disabled', false);
				},
				complete: function() {
					$form.trigger('reset');
				}
			});
		
		}

		$('#frm-request').on('submit', function(event){
			event.preventDefault();
			let $form = $(this);
			$form.find('[type="submit"]').prop('disabled', true);

			if(typeof grecaptcha != 'undefined') {
				grecaptcha.ready(function() {
					grecaptcha.execute(theme.sitekey, {action: 'request'}).then(function(token) {
						submit_request(token);
					}); // recaptcha execute
				}); // recaptcha ready
			} else {
				submit_request('');
			}

			return false;
		});

		/*
		$(document).on('click', '.floor_plan_button, .interior_button', function(e){
			let pswp = new PhotoSwipe({
				dataSource: $(this).data('images'),
				//showHideAnimationType: 'none',
				index: 0
			});
			pswp.init();
		});
		*/
		
		$(".product-images-slider").owlCarousel({
			items:1,
			lazyLoad:true,
			loop:true,
			autoplay:false,
			// autoHeight:true,
			autoplayTimeout:3000,
			autoplayHoverPause:true,
			nav:true,
			dots:false
		});
		
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

		// -----------------------------------------------------------
		$('.logout-post-password').on('click', function(e){
			e.preventDefault();
			let $this = $(this),
				url = $this.data('url');

			$.ajax({
				url:theme.ajax_url+'?action=logout_post_password',
				method:'GET',
				data:{url:url},
				beforeSend:function(){
					$this.prop('disabled', true);
				},
				success:function(){
					deleteCookie('wp-postpass_'+$this.data('hash'));
					//$this.remove();
					location.href = url;
				}
			});
			
		});

		let popped_popup_content = getCookie('popped_popup_content');
		if($('#modal-popup').length>0 && theme.preview!='1' && !popped_popup_content) {
			
			const popup_content = new bootstrap.Modal('#modal-popup');
			
			setTimeout(function(){
				popup_content.show();
				setCookie('popped_popup_content', 1, 1);
			}, 1000*parseInt(theme.popup_content_timeout));
			
		}

		function check_validity($form) {
			let valid = true;
			$form.find('input.wpcf7-form-control').each(function(index, el){
				switch(el.type) {
					case 'text':
						if(el.validity.valueMissing || el.validity.tooLong) {
							valid = false;
						}
						break;
					case 'tel':
						if(!check_input_phone_number(el.value)) {
							valid = false;
						}
						break;
				}
			});

			return valid;
		}

		$(document).on('keyup', 'input.wpcf7-form-control', function(e){
			let $form = $(this).closest('form'),
				$submit_button = $form.find('[type="submit"]');

			if(check_validity($form)) {
				$submit_button.prop('disabled', false);
			} else {
				$submit_button.prop('disabled', true);
			}

		});

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

	});
});