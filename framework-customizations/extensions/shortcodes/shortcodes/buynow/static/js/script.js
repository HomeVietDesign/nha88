window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		
		$('.frm-buynow').on('submit', function(e){
			e.preventDefault();
			let $frm = $(this),
				data = $frm.serialize(),
				$response = $frm.find('.buynow-response'),
				$submit_button = $frm.find('.submit-button'),
				submit_button_text = $submit_button.text();

			$submit_button.prop('disabled', true);

			$.ajax({
				url: theme.ajax_url,
				type: 'post',
				data: data,
				dataType: 'json',
				beforeSend: function(xhr) {
					$response.html('<div class="py-5 px-3 text-center text-info">Đang gửi yêu cầu...</div');
				},
				success: function(response) {
					//console.log(response);
					
					const newEvent = new CustomEvent('buynow', {
						bubbles: true,
						detail: response
					});

					if(response.code==1) {
						
						e.target.dispatchEvent(newEvent);

						$response.html('<div class="p-3 text-center text-green">'+response.msg+'</div>');
						$submit_button.closest('form').trigger('reset');

					} else {
						$response.html('<div class="p-3 text-center text-warning">'+response.msg+'</div>');
					}
					
				},
				error: function() {
					$response.html('<div class="text-warning p-3">Có lỗi khi gửi! Vui lòng tải lại trang rồi thử lại. Hoặc liên hệ với ban quản trị về sự cố này.</div>');
				},
				complete: function() {
					$submit_button.prop('disabled', false);
				}
			});

			return false;
		});
	});
});