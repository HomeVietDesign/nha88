window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){

		function checkPurchaseFormValid() {
			let formData = new FormData($('#frm-purchase').get(0));
			//const imgRegex = /image\/(gif|jpe?g|png|webp)$/i;
			let valid = true;
			for (const pair of formData.entries()) {
				//console.log(pair[0], pair[1]);
				valid = (valid && document.getElementById(pair[0]).validity.valid);
				//console.log(document.getElementById(pair[0]).validity.valid);
			}

			return valid;
			
		}

		$(document).on('input', '#customer_email, #customer_bank', function() {
			if(checkPurchaseFormValid()) {
				$('#purchase-submit').prop('disabled', false);
			} else {
				$('#purchase-submit').prop('disabled', true);
			}
		});

		$(document).on('input', '#customer_bank', function() {
			let $input = $(this);
			$input.closest('[for="customer_bank"]').find('.form-control').text($input.val().split('\\').pop());
		});

		$('#purchase-popup').on('show.bs.modal', function (event) {
			let $modal = $(this),
				$button = $(event.relatedTarget),
				$form = $('#frm-purchase'),
				id = parseInt($button.data('id')),
				type = $button.data('type');

			//console.log($button);
			switch(type) {
				case 'combo':
					$('#purchase-popup-label').text(fw_shortcode_products.purchase_combo_popup_title);
					break;
				default:
					$('#purchase-popup-label').text(fw_shortcode_products.purchase_popup_title);
					break;
			}

			$.ajax({
				url: theme.ajax_url+'?action=purchase_load_form',
				type: 'GET',
				data: {'id': id, 'type': type},
				beforeSend: function() {
					$form.html('<p class="text-primary">Đang tải...</p>');
				},
				success: function(response) {
					$form.html(response);
					if(checkPurchaseFormValid()) {
						$('#purchase-submit').prop('disabled', false);
					} else {
						$('#purchase-submit').prop('disabled', true);
					}
				},
				error: function(xhr) {
					$form.html('<p class="text-danger">Có lỗi xảy ra. Xin vui lòng thử lại.</p>');
				},
				complete: function() {
					
				}
			});
			
		}).on('hidden.bs.modal', function (e) {

			$('#purchase_content').html('');
			$('#product_id').val('');
			
			$('#purchase-product-image').html('');
			$('#purchase-product-qrbank').html('');

		});

		let ajax_purchase = null;
		function submit_purchase(event, token='') {
			let $form = $(event.currentTarget)
				,formData = new FormData($form[0])
				,$button = $form.find('[type="submit"]')
				,$response = $('#purchase-response')
				//,ref = getCookie('_ref')
				;
			
			$button.prop('disabled', true);

			formData.append('token', token);
			//formData.append('ref', ref);
			formData.append('url', window.location.href);

			if(ajax_purchase!=null) ajax_purchase.abort();

			ajax_purchase = $.ajax({
				url: theme.ajax_url+'?action=purchase',
				type: 'POST',
				processData: false,
				contentType: false,
				data: formData,
				dataType: 'json',
				cache: false,
				beforeSend: function() {
					$response.html('<p class="text-primary">Đang gửi giao dịch...</p>');
				},
				success: function(response) {
					const eventPurchase = new CustomEvent('purchase', {
						bubbles: true,
						detail: { id:response.data.id, email:response.data.email, fb_pxl_code:response.fb_pxl_code }
					});

					if(response['code']>0) {
						event.target.dispatchEvent(eventPurchase);
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

		$('#frm-purchase').on('submit', function(event){
			event.preventDefault();
			let $form = $(this);
			$form.find('[type="submit"]').prop('disabled', true);

			if(typeof grecaptcha != 'undefined') {
				grecaptcha.ready(function() {
					grecaptcha.execute(theme.sitekey, {action: 'purchase'}).then(function(token) {
						submit_purchase(event, token);
					}); // recaptcha execute
				}); // recaptcha ready
			} else {
				submit_purchase(event, '');
			}

			return false;
		});
		
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

	});
});