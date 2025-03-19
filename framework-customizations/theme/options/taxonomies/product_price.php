<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	'qrbank' => array(
		'type'  => 'upload',
		'value' => '',
		'label' => 'Ảnh QR',
		'images_only' => true
	),
);
