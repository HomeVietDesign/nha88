<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	'product_cat_in' => [
		'label' => 'Danh mục lựa chọn',
		'type'  => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'product_cat',
		'limit' => 100,
	],
	'product_cat_ex' => [
		'label' => 'Danh mục loại trừ',
		'type'  => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'product_cat',
		'limit' => 100,
	],
	'items' => [
		'type'  => 'numeric',
	    'value' => -1,
		'integer' => true,
		'negative' => true,
		'label' => 'Số lượng hiển thị',
		'desc' => 'Giá trị "-1" là hiển thị tất cả.',
	],
	'popup' => array(
		'label' => 'Mở popup chi tiết?',
		'desc'  => '',
		'value'  => 'yes',
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
);
