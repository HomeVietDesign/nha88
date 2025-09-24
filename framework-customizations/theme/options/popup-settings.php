<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'popup' => array(
		'type' => 'tab',
		'title' => 'Cài đặt Popup',
		'options' => array(
			
			'divider_popup_content' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt popup nội dung tùy biến</strong>',
				'size' => 'large',
			),
			'popup_content' => array(
				'label' => 'Nội dung popup',
				'desc'  => '',
				'type'  => 'wp-editor',
				'value' => '',
				'size' => 'large',
				'editor_height' => '300'
			),
			'popup_content_timeout' => array(
				'label' => 'Thời gian mở popup (giây)',
				'desc'  => '',
				'type'  => 'numeric',
				'value' => 120
			),
			'popup_content_button_text' => array(
				'label' => 'Nhãn nút mở popup',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			
			'divider_popup_sale' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt popup SALE</strong>',
				'size' => 'large',
			),
			'popup_sale' => array(
				'label' => 'Nội dung popup SALE',
				'desc'  => '',
				'type'  => 'wp-editor',
				'value' => '',
				'size' => 'large',
				'editor_height' => '300'
			),
			'popup_sale_button_text' => array(
				'label' => 'Nhãn nút mở popup SALE',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'SALE 50%'
			),
		),
	),
);