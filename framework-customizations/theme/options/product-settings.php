<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$request_type = \Nha88\Setting::request_type();

$options = array(
	'product' => array(
		'type' => 'tab',
		'title' => 'Sản phẩm',
		'options' => array(
			'sale_off' => array(
				'label' => 'Giảm giá',
				'desc'  => '%',
				'type'  => 'numeric',
				'integer'  => false,
				'value' => ''
			),
		),
	),
);

foreach ($request_type as $type => $label) {
	$options['product']['options'][$type.'_button_text'] = [
		'label' => 'Nhãn nút '.$label,
		'desc'  => '',
		'type'  => 'text',
		'value' => $label
	];
	$options['product']['options'][$type.'_popup_title'] = [
		'label' => 'Tiêu đề popup '.$label,
		'desc'  => '',
		'type'  => 'text',
		'value' => ''
	];
	$options['product']['options'][$type.'_popup_content'] = [
		'label' => 'Nội dung mô tả popup '.$label,
		'desc'  => '',
		'type'  => 'wp-editor',
		'value' => '',
		'size' => 'large',
		'editor_height' => '400'
	];
}

$options['product']['options']['vfp_button_text'] = [
	'label' => 'Nhãn nút xem mặt bằng',
	'desc'  => '',
	'type'  => 'text',
	'value' => 'Mặt bằng'
];

$options['product']['options']['sample_link_label'] = [
	'label' => 'Nhãn link hồ sơ mẫu',
	'desc'  => '',
	'type'  => 'text',
	'value' => ''
];

$options['product']['options']['sample_link'] = [
	'label' => 'Link hồ sơ mẫu',
	'desc'  => '',
	'type'  => 'text',
	'value' => ''
];