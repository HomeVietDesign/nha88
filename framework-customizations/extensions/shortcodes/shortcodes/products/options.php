<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = [
	'number' => [
		'label' => 'Số lượng hiển thị',
		'desc' => 'Số sản phẩm hiển thị trên 1 phân trang',
		'value' => 8,
		'type' => 'numeric',
		'negative' => true,
	],
	'product_cat' => array(
		'label' => 'Danh mục',
		'desc' => '',
		'type' => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'product_cat',
		'limit' => 100,
	),

	'product_cat_exclude' => array(
		'label' => 'Danh mục loại trừ',
		'desc' => '',
		'type' => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'product_cat',
		'limit' => 100,
	),

];
