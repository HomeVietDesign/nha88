<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'product' => array(
		'type' => 'tab',
		'title' => 'Sản phẩm',
		'options' => array(
			
			'request_button_text' => array(
				'label' => 'Nhãn nút gửi yêu cầu',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'XEM HỒ SƠ MẪU'
			),
			'request_popup_title' => array(
				'label' => 'Tiêu đề popup xem hồ sơ mẫu',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'ĐĂNG KÝ NHẬN HỒ SƠ MẪU'
			),

			'floor_plan_button_text' => array(
				'label' => 'Nhãn nút xem mặt bằng',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'MẶT BẰNG'
			),
			'floor_plan_popup_title' => array(
				'label' => 'Tiêu đề popup xem mặt bằng',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'ĐĂNG KÝ XEM MẶT BẰNG MẪU'
			),

			'interior_button_text' => array(
				'label' => 'Nhãn nút xem nội thất',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'NỘI THẤT'
			),
			'interior_popup_title' => array(
				'label' => 'Tiêu đề popup xem nội thất',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'ĐĂNG KÝ XEM NỘI THẤT MẪU'
			),

			'purchase_button_text' => array(
				'label' => 'Nhãn nút đặt mua',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'ĐẶT MUA'
			),
			'purchase_popup_title' => array(
				'label' => 'Tiêu đề popup đặt mua',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'ĐẶT MUA HỒ SƠ ĐÃ LỰA CHỌN'
			),

			'purchase_guide_link' => array(
				'label' => 'Link hướng dẫn',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'purchase_guide_link_label' => array(
				'label' => 'Nhãn nút hướng dẫn',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			
			// 'product_popup_instruction' => array(
			// 	'label' => 'Hướng dẫn sử dụng',
			// 	'desc'  => '',
			// 	'type'  => 'wp-editor',
			// 	'value' => '',
			// 	'size' => 'large',
			// 	'editor_height' => '400'
			// ),

			// 'download_button_text' => array(
			// 	'label' => 'Nhãn nút tải file',
			// 	'desc'  => '',
			// 	'type'  => 'text',
			// 	'value' => 'TẢI FILE 3D'
			// ),
			'upload_button_text' => array(
				'label' => 'Nhãn nút up file',
				'desc'  => 'Dành cho quản trị',
				'type'  => 'text',
				'value' => 'UP FILE 3D'
			),
			'product_price' => array(
				'label' => 'Đơn giá',
				'desc'  => '',
				'type'  => 'numeric',
				'value' => 100000
			),
			'product_price_label' => array(
				'label' => 'Nhãn giá bán',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'Giá bán:'
			),
			'product_price_unit' => array(
				'label' => 'Đơn vị giá',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'vnđ'
			),
			'product_price_0' => array(
				'label' => 'Nhãn giá = 0',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'Miễn phí'
			),
			'product_price_empty' => array(
				'label' => 'Nhãn không có giá',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'Liên hệ'
			),
			
		),
	),
);