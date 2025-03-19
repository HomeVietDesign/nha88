window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$('#tag-slug').prop('disabled', true);
		$('#slug').prop('disabled', true);
		
		$('label[for="tag-name"],label[for="name"]').html('Email');
		$('label[for="tag-slug"],label[for="slug"]').html('Mã khách hàng');
		$('label[for="tag-description"],label[for="description"]').html('Họ tên');
		
		$(document).on('click', '.row-actions .editinline', function(e){
			let $this = $(this);
			setTimeout(function(){
				let $row = $this.closest('tr');
				$('tr#edit-'+$row.find('input[name="delete_tags[]"]').val()).find('input[name="slug"]').prop('disabled', true).parent('.input-text-wrap').prev('.title').text('Mã phần tử');
			}, 50);
		});
	});
});