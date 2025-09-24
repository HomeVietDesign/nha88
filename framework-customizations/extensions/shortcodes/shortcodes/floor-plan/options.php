<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = [
	'thumbnail' => array(
		'label' => 'Ảnh mặt bằng',
		'desc'  => '',
		'type'  => 'upload',
		'images_only' => true,
	),
	'title' => array(
		'label' => 'Tiêu đề công năng',
		'type' => 'text',
		'value' => '',
	),
	'items' => array(
		'type' => 'addable-popup',
		'value' => array(),
		'label' => 'Các thành phần công năng',
		'desc'  => '',
		'template' => '{{=title}}',
		'popup-title' => 'Thêm phần tử',
		'size' => 'large', // small, medium, large
		'limit' => 0, // limit the number of popup`s that can be added
		'add-button-text' => 'Thêm',
		'sortable' => true,
		'popup-options' => array(
			'title' => array(
				'label' => 'Tiêu đề',
				'type' => 'text',
				'value' => '',
			),
			'images' => [
				'type' => 'multi-upload',
				'label' => 'Các hình ảnh',
				'images_only' => true,
				'files_ext' => ['png', 'jpg', 'jpeg'],
			],
		),
	),
	'images' => [
		'type' => 'multi-upload',
		'label' => 'Các hình ảnh',
		'images_only' => true,
		'files_ext' => ['png', 'jpg', 'jpeg'],
	],
];
