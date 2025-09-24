<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$all_sizes = wp_get_registered_image_subsizes();
$medium_large = $all_sizes['medium_large'];
unset($all_sizes['medium_large']);
//debug_log($all_sizes);
$sizes = [];
foreach ($all_sizes as $key => $value) {
	$size_name = preg_replace('/[-|_]+/', ' ', $key);
	$width = absint( $value['width'] ); $width = ($width===0)?'Auto':$width;
	$height = absint( $value['height'] ); $height = ($height===0)?'Auto':$height;

	$sizes[$key] = $size_name.' - '.$width.' x '.$height;
	if($key=='large') {
		$width = absint( $medium_large['width'] ); $width = ($width===0)?'Auto':$width;
		$height = absint( $medium_large['height'] ); $height = ($height===0)?'Auto':$height;
		$sizes['medium_large'] = 'medium large - '.$width.' x '.$height;
	}
}
$sizes['full'] = 'Ảnh gốc';

$options = array(
	'images' => array(
		'type' => 'multi-upload',
		'label' => 'Danh sách ảnh',
		'images_only' => true,
		'files_ext' => array( 'png', 'jpg', 'jpeg' ),
	),
	'size' => array(
		'type' => 'select',
		'value' => 'large',
		'label' => 'Kích thước thumbnail',
		'choices' => $sizes
	),
);
