<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'purchase' => array(
		'type' => 'tab',
		'title' => 'Mua hàng',
		'options' => array(
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
			'purchase_button_text' => array(
				'label' => 'Nhãn nút mua hàng',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'ĐẶT MUA'
			),
			'purchase_free_button_text' => array(
				'label' => 'Nhãn nút mua hàng miễn phí',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'GỬI EMAIL XÁC NHẬN'
			),
			'purchase_popup_title' => array(
				'label' => 'Tiêu đề popup mua hàng',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'MUA SẢN PHẨM'
			),
			'purchase_popup_desc' => array(
				'label' => 'Nội dung miêu tả form mua hàng',
				'desc'  => '',
				'type'  => 'wp-editor',
				'size' => 'large',
				'editor_height' => '300',
				'value' => '',
			),
			'divider_bank_info' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt tài khoản chuyển khoản</strong>',
				'size' => 'large',
			),
			'bank_qr' => array(
				'label' => 'Ảnh QR thanh toán chung',
				'desc'  => '',
				'type'  => 'upload',
				'images_only' => true,
			),
			'divider_combo' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt combo</strong>',
				'size' => 'large',
			),
			'purchase_combo_button_text' => array(
				'label' => 'Nhãn nút mua combo',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'MUA COMBO'
			),
			'purchase_combo_price' => array(
				'label' => 'Giá mua combo(vnđ)',
				'desc'  => '',
				'type'  => 'number',
				'value' => 200000
			),
			'purchase_combo_popup_title' => array(
				'label' => 'Tiêu đề popup mua combo',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'MUA COMBO SẢN PHẨM'
			),
			'purchase_combo_popup_desc' => array(
				'label' => 'Nội dung miêu tả form mua combo',
				'desc'  => '',
				'type'  => 'wp-editor',
				'size' => 'large',
				'editor_height' => '300',
				'value' => '',
			),
		),
	),
);