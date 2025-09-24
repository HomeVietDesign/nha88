<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = [
	'box_images' => [
		'context' => 'advanced',
		'title'   => 'Hình ảnh',
		'type'    => 'box',
		'options' => [
			'images' => [
				'type' => 'multi-upload',
				'label' => 'Ảnh sản phẩm',
				'images_only' => true,
				'files_ext' => ['png', 'jpg', 'jpeg'],
			],
			'fp_options' => [
				'type' => 'addable-popup',
				'value' => [],
				'label' => 'Các phiên bản mặt bằng',
				'desc'  => '',
				'template' => '{{=name}}',
				'popup-title' => 'Thêm phiên bản',
				'size' => 'small', // small, medium, large
				'limit' => 0, // limit the number of popup`s that can be added
				'add-button-text' => 'Thêm',
				'sortable' => true,
				'popup-options' => array(
					'name' => array(
						'label' => 'Nhãn nút',
						'type' => 'text',
						'value' => '',
					),
					'images' => [
						'type' => 'multi-upload',
						'label' => 'Ảnh mặt bằng',
						'images_only' => true,
						'files_ext' => ['png', 'jpg', 'jpeg'],
					],
				),
			],
		],
	],
    'box_fields' => [
    	'context' => 'side',
		'title'   => 'Trường dữ liệu',
		'type'    => 'box',
        'options' => [
        	'sku' => array(
				'label' => 'Mã hồ sơ',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			// 'regular_price' => array(
			// 	'label' => 'Giá hồ sơ (vnđ)',
			// 	'desc'  => '',
			// 	'type'  => 'numeric',
			// 	'integer'  => true,
			// 	'value' => ''
			// ),
        	'size_x' => array(
				'label' => 'Chiều ngang (m)',
				'desc'  => '',
				'type'  => 'numeric',
				'integer'  => false,
				//'value' => 0
			),
			'size_y' => array(
				'label' => 'Chiều dài (m)',
				'desc'  => '',
				'type'  => 'numeric',
				'integer'  => false,
				//'value' => 0
			),
        	'size_z' => array(
				'type'  => 'addable-popup',
				'value' => [],
				'label' => 'Chiều cao các tầng',
				'popup-title' => 'Thêm tầng',
				'popup-options' => array(
					'name' => array(
						'type' => 'text',
						'label' => 'Tên tầng',
					),
					'size' => array(
						'label' => 'Chiều cao (m)',
						'desc'  => '',
						'type'  => 'numeric',
						'integer'  => false,
						//'value' => 0
					),
				),
				'template' => '{{- name }} : {{- size}}m', // box title
				'add-button-text' => 'Thêm tầng',
				'sortable' => true,
				'limit' => 0,
			),
			'size_s' => array(
				'type'  => 'addable-popup',
				'value' => [],
				'label' => 'Các diện tích',
				'popup-title' => 'Thêm diện tích',
				'popup-options' => array(
					'name' => array(
						'type' => 'text',
						'label' => 'Tên không gian',
					),
					'size' => array(
						'label' => 'Diện tích (m2)',
						'desc'  => '',
						'type'  => 'numeric',
						'integer'  => false,
						//'value' => 0
					),
				),
				'template' => '{{- name }} : {{- size}}m2', // box title
				'add-button-text' => 'Thêm diện tích',
				'sortable' => true,
				'limit' => 0,
			),
        ]
    ],
   	'box_mode' => [
		'context' => 'side',
		'title'   => 'Các chế độ hiển thị',
		'type'    => 'box',
		'options' => [
			'popup_sale' => array(
				'label' => 'Hiển thị nút mở poup sale ?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
			    'right-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			),
		]
	],
   	'box_content' => [
		'context' => 'advanced',
		'title'   => 'Mô tả thêm',
		'type'    => 'box',
		'options' => [
			'description' => [
				'type' => 'wp-editor',
				'label' => 'Nội dung mô tả',
				'size' => 'large',
				'editor_height' => '600'
			],
			
		]
	],
];