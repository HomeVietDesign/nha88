window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){

		$('.fw-shortcode-products .list-products').imagesLoaded(function(){
			$('.fw-shortcode-products .list-products').masonry();
			$('.fw-shortcode-products .list-products').masonry('layout');
		});
		
	});
});