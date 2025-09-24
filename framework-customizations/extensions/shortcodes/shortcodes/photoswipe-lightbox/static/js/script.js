window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		var lightbox = new PhotoSwipeLightbox({
			gallery: '.shortcode-photoswipe-lightbox',
			children: 'a',
			pswpModule: PhotoSwipe 
		});
		lightbox.init();
	});
});