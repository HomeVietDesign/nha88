window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		var lightbox = new PhotoSwipeLightbox({
			gallery: '.fp-pswp-gallery',
			children: 'a',
			pswpModule: PhotoSwipe 
		});
		lightbox.init();
	});
});