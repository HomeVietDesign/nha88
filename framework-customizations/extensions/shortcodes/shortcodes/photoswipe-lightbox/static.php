<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-photoswipe-lightbox',
	$shortcodes_extension->locate_URI( '/shortcodes/photoswipe-lightbox/static/css/style.css' ),
	['photoswipe'],
	'1.0'
);

wp_enqueue_script(
	'fw-shortcode-photoswipe-lightbox',
	$shortcodes_extension->locate_URI( '/shortcodes/photoswipe-lightbox/static/js/script.js' ),
	['jquery', 'bootstrap', 'photoswipe-lightbox'],
	false,
	true
);