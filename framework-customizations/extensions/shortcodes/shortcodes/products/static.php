<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-products',
	$shortcodes_extension->locate_URI( '/shortcodes/products/static/css/style.css' ),
	['bootstrap']
);


wp_enqueue_script(
	'fw-shortcode-products',
	$shortcodes_extension->locate_URI( '/shortcodes/products/static/js/script.js' ),
	['jquery'],
	false,
	true
);
