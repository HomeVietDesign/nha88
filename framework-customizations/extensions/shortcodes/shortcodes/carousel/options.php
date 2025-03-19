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
		'type' => 'addable-popup',
		'label' => 'Ảnh',
		'width'  => 'full',
		'size' => 'large', // small, medium, large
		'template'      => 'Phần tử slide',
		'popup-title' => 'Thêm ảnh',
		'popup-options' => array(
			'attachment' => array(
				'type'  => 'upload',
				'label' => 'Hình ảnh',
				'images_only' => true,
			),
			'text' => array(
				'label' => 'Văn bản',
				'desc'  => '',
				'type'  => 'wp-editor',
				'editor_height' => 200,
				'size' => 'large', // small, large
			),
		),
		'sortable' => true,
	),
	'size' => array(
		'type' => 'select',
		'label' => 'Kích thước ảnh hiển thị',
		'choices' => $sizes
	),
	'autoplay' => array(
		'label' => 'Tự động chạy?',
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
	'interval' => array(
		'label' => 'Thời gian chuyển ảnh',
		'value' => 5,
		'desc'  => 'Thời lượng tính bằng giây chuyển ảnh. Chỉ có tác dụng khi cho tự động chuyển ảnh.',
		'type'  => 'numeric'
	),
);
