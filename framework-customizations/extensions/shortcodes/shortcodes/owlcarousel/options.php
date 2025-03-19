<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'images' => array(
		'type' => 'addable-popup',
		'label' => 'Ảnh',
		'width'  => 'full',
		'size' => 'large', // small, medium, large
		'template'      => 'Phần tử slide',
		'popup-title' => 'Thêm ảnh',
		'popup-options' => array(
			'attachment' => array(
				'type'  => 'upload',
				'label' => 'Hình ảnh',
				'images_only' => true,
			),
			'text' => array(
				'label' => 'Văn bản',
				'desc'  => '',
				'type'  => 'wp-editor',
				'editor_height' => 200,
				'size' => 'large', // small, large
			),
		),
		'sortable' => true,
	),
	'autoplay' => array(
		'label' => 'Tự động chạy?',
		'desc'  => '',
		'value'  => 'no',
		'type'  => 'switch',
		'left-choice' => array(
			'value' => 'no',
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 'yes',
			'label' => 'Có',
		),
	),
	'interval' => array(
		'label' => 'Thời gian chuyển ảnh',
		'value' => 5,
		'desc'  => 'Thời lượng tính bằng giây chuyển ảnh. Chỉ có tác dụng khi cho tự động chuyển ảnh.',
		'type'  => 'numeric'
	),
);
