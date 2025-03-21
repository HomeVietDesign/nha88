window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){

		$(document).on('change', 'input.input-dimension', function(e){
			let $input = $(this),
				nonce = $input.data('nonce'),
				id = $input.data('id'),
				dimension = $input.data('dimension'),
				val = $input.val();

			$input.prop('disabled', true);

			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {action:'change_product_dimension',id: id, nonce: nonce, dimension: dimension, val: val},
				success: function(response) {
					$input.val(response);
				},
				complete: function() {
					$input.prop('disabled', false);
				}
			});
		});

		$(document).on('change', 'input.combo', function(e){
			let $this = $(this),
				nonce = $this.data('nonce'),
				id = $this.data('id'),
				combo = $this.prop('checked');
			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {id: id, nonce: nonce, action:'change_product_combo', combo: combo},
				beforeSend: function() {
					$this.prop('disabled', true);
				},
				success: function(response) {
					//$this.val(response);
					if(response=='yes') {
						$this.prop('checked', true);
					} else {
						$this.prop('checked', false);
					}
				},
				complete: function() {
					$this.prop('disabled', false);
				}
			});
		});

		$(document).on('change', 'input.has_file', function(e){
			let $this = $(this),
				nonce = $this.data('nonce'),
				id = $this.data('id'),
				has_file = $this.prop('checked');
			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {id: id, nonce: nonce, action:'change_product_has_file', has_file: has_file},
				beforeSend: function() {
					$this.prop('disabled', true);
				},
				success: function(response) {
					//$this.val(response);
					if(response=='yes') {
						$this.prop('checked', true);
					} else {
						$this.prop('checked', false);
					}
				},
				complete: function() {
					$this.prop('disabled', false);
				}
			})
		});

		$(document).on('change', 'input.url_data_file', function(e){
			let $this = $(this),
				nonce = $this.data('nonce'),
				id = $this.data('id'),
				url_data_file = $this.val();
			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {id: id, nonce: nonce, action:'change_product_url_data_file', url_data_file: url_data_file},
				beforeSend: function() {
					$this.prop('readonly', true);
				},
				success: function(response) {
					$this.val(response);
					//console.log(response);
				},
				complete: function() {
					$this.prop('readonly', false);
				}
			})
		});
	});
});