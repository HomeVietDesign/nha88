<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-products',
	$shortcodes_extension->locate_URI( '/shortcodes/products/static/css/style.css' ),
	['owlcarousel'],
	'0.1'
);

wp_enqueue_script(
	'fw-shortcode-products',
	$shortcodes_extension->locate_URI('/shortcodes/products/static/js/scripts.js'),
	array('jquery', 'owlcarousel'),
	'0.1',
	true
);

$data = [
	'purchase_popup_title' => fw_get_db_settings_option('purchase_popup_title', 'MUA SẢN PHẨM'),
	'purchase_combo_popup_title' => fw_get_db_settings_option('purchase_combo_popup_title', 'MUA COMBO SẢN PHẨM'),
];
wp_localize_script( 'jquery', 'fw_shortcode_products', $data );
