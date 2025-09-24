<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-buynow',
	$shortcodes_extension->locate_URI( '/shortcodes/buynow/static/css/style.css' ),
	['bootstrap'],
	''
);

wp_enqueue_script(
	'fw-shortcode-buynow',
	$shortcodes_extension->locate_URI('/shortcodes/buynow/static/js/script.js'),
	array('bootstrap'),
	false,
	true
);
