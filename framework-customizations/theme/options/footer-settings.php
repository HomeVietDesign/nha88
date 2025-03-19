<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'footer' => array(
		'type' => 'tab',
		'title' => __('Footer'),
		'options' => array(
			'footer_bg_color' => [
				'type'  => 'color-picker',
				'value' => '#000000',
				'label' => __('Footer background'),
			],
			'footer_color' => [
				'type'  => 'color-picker',
				'value' => '#ffffff',
				'label' => __('Footer text color'),
			]
		),
	),
);