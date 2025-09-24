<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-floor-plan',
	$shortcodes_extension->locate_URI( '/shortcodes/floor-plan/static/css/style.css' ),
	['photoswipe'],
	'1.0'
);

wp_enqueue_script(
	'fw-shortcode-floor-plan',
	$shortcodes_extension->locate_URI( '/shortcodes/floor-plan/static/js/script.js' ),
	['jquery', 'bootstrap', 'photoswipe-lightbox'],
	false,
	true
);