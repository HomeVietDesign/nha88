window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$(window).on('resize', function(){
			$('.view_urls a.thickbox').each(function(){
				let $link = $(this),
					w = $(window).width(),
					h = $(window).height(),
					href = $link.attr('href');

				if(w>800) {
					w = 800;
				} else {
					w -= 60;
				}
				if(h>600) {
					h = 600;
				} else {
					h -= 60;
				}

				href = href.replace(/width=\d+/, 'width='+w);
				href = href.replace(/height=\d+/, 'height='+h);
				$link.attr('href', href);
			});
		}).resize();
		
		$('.transaction-cancel').on('click', function(e) {
			let $this = $(this);

			if(confirm($this.attr('title'))) {
				$this.prop('disabled', true);
				let id = $this.data('id'), nonce = $this.data('nonce');
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action: 'transaction_cancel', id:id, nonce:nonce},
					dataType: 'json',
					beforeSend: function() {

					},
					success: function(response) {
						if(response.code==1) {
							$this.parent().html(response.msg);
						} else {
							$this.prop('disabled', false);
							alert(response.msg);
						}
					},
					error: function() {
						$this.prop('disabled', false);
					},
					complete: function() {
						
					}
				})
			}
		});
		
		$('.transaction-approval').on('click', function(e) {
			let $this = $(this);

			if(confirm($this.attr('title'))) {
				$this.prop('disabled', true);
				let id = $this.data('id'), nonce = $this.data('nonce');
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action: 'transaction_approval', id:id, nonce:nonce},
					dataType: 'json',
					beforeSend: function() {

					},
					success: function(response) {

						if(response.code==1) {
							$this.parent().html(response.msg);
						} else {
							$this.prop('disabled', false);
							alert(response.msg);
						}
						
					},
					error: function() {
						$this.prop('disabled', false);
					},
					complete: function() {
						
					}
				})
			}
		});
	});
});