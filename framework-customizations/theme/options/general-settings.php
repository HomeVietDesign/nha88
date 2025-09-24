<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'general' => array(
		'type' => 'tab',
		'title' => 'Cài đặt chung',
		'options' => array(
			'hotline' => array(
				'label' => 'Số hotline',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'hotline_label' => array(
				'label' => 'Nhãn hotline',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'zalo' => array(
				'label' => 'Số zalo',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'zalo_label' => array(
				'label' => 'Nhãn zalo',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'admin_email_address' => array(
				'label' => 'Admin email address',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),

			'divider_footer_link' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Các nút link cố định cuối trang, bên trên nút gọi.</strong>',
				'size' => 'large',
			),
			'footer_links' => array(
				'type' => 'addable-popup',
				'value' => array(),
				'label' => 'Nút link cuối trang',
				'desc'  => '',
				'template' => '{{=name}}',
				'popup-title' => 'Thêm link',
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
					'url' => array(
						'label' => 'URL',
						'type' => 'text',
						'desc' => 'Đường dẫn chuyển đến khi click vào nút.',
						'value' => '',
					),
				),
			),
			'cf_turnstile_key' => array(
				'label' => __( 'Turnstile key' ),
				'type'  => 'text',
			),
			'cf_turnstile_secret' => array(
				'label' => __( 'Turnstile secret' ),
				'type'  => 'text',
			),
		),
	),
);