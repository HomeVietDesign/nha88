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
			'images_floor_plan' => [
				'type' => 'multi-upload',
				'label' => 'Ảnh mặt bằng',
				'images_only' => true,
				'files_ext' => ['png', 'jpg', 'jpeg'],
			],
			'images_interior' => [
				'type' => 'multi-upload',
				'label' => 'Ảnh nội thất',
				'images_only' => true,
				'files_ext' => ['png', 'jpg', 'jpeg'],
			],
		]
	],
    'box_fields' => [
    	'context' => 'advanced',
		'title'   => 'Trường dữ liệu',
		'type'    => 'box',
        'options' => [
        	'total_area' => array(
				'label' => 'Tổng diện tích',
				'desc'  => 'm2',
				'type'  => 'numeric',
				//'value' => 0
			),
        	'url_data_file' => array(
				'type' => 'text',
				'desc' => 'URL thư mục google drive chứa file cần tải về, và được chia sẻ với email khách hàng đã mua.',
				'value' => '',
				'label' => 'URL thư mục file',
				
			),
			'has_file' => array(
				'label' => 'Đã có file 3D?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
					'value' => 'no',
					'label' => 'Chưa có',
				),
				'right-choice' => array(
					'value' => 'yes',
					'label' => 'Có rồi',
				),
			),
			'sale' => array(
				'label' => 'Kinh doanh',
				'desc'  => '',
				'value'  => 'normal',
				'type'  => 'select',
				'choices' => array(
					'nomal' => 'Bình thường',
					'free' => 'Miễn phí',
					'comingsoon' => 'Sắp ra mắt',
				),
				
			),
			'combo' => array(
				'label' => 'Bán combo?',
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
        ]
    ],
   
];