<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'google' => array(
    'type' => 'tab',
		'title' => __('Cài đặt google'),
		'options' => array(
			'recaptcha_key' => array(
				'label' => __( 'Recaptcha key' ),
				'type'  => 'text',
			),
			'recaptcha_secret' => array(
				'label' => __( 'Recaptcha secret' ),
				'type'  => 'text',
			),
			'drive_key' => array(
				'label' => __( 'Drive key' ),
				'type'  => 'text',
			),
			'drive_secret' => array(
				'label' => __( 'Drive secret' ),
				'type'  => 'text',
			),
		),
	),
);