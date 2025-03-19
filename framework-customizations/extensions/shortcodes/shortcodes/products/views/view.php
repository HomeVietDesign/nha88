<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
global $customer;

$product_cat_ids = isset($atts['product_cat']) ?  $atts['product_cat'] : [];
$product_cat_exclude_ids = isset($atts['product_cat_exclude']) ?  $atts['product_cat_exclude'] : [];

// if ( ! empty( $atts['product_cat'] ) ) {
// 	$product_cat_id = $atts['product_cat'][0];
// }
//$product_cat = get_term_by( 'term_id', $product_cat_id, 'product_cat' );

$number = !empty($atts['number']) ? intval($atts['number']) : 8;

$meta_query = [];
$tax_query = [];
$orderby = [
	'date' => 'DESC',
	'ID' => 'DESC'
];

$args = [
	'post_type' => 'product',
	'posts_per_page' => $number,
	'post_status' => 'publish'
];

if(!empty($product_cat_ids)) {
	$tax_query['product_cat'] = [
		'taxonomy' => 'product_cat',
		'field' => 'term_id',
		'terms' => $product_cat_ids,
	];
}

if(!empty($product_cat_exclude_ids)) {
	$tax_query['product_cat_exclude'] = [
		'taxonomy' => 'product_cat',
		'field' => 'term_id',
		'terms' => $product_cat_exclude_ids,
		'operator' => 'NOT IN'
	];
}

if($tax_query) {
	$args['tax_query'] = $tax_query;
}

if($meta_query) {
	$args['meta_query'] = $meta_query;
}

if($orderby) {
	$args['orderby'] = $orderby;
}

$query = new \WP_Query($args);

//debug($query);

$shortcode_html_id = uniqid('fw-shortcode-products-');
if($query->have_posts()) {
?>
<div id="<?=$shortcode_html_id?>" class="fw-shortcode-products position-relative">
	<div class="fw-shortcode-products-inner">
		<input type="hidden" name="query" value="<?=esc_attr(json_encode($query->query))?>">
        <div class="products-container container-xxl p-0 position-relative">
		<?php

			\FW_Shortcode_Products::products($query);
		?>
		</div>
		<?php
		if($query->max_num_pages>1) {
		?>
		<div class="paginate-links product-paginate-links d-flex justify-content-center align-items-stretch p-2">
		<?php echo \FW_Shortcode_Products::pagination($query, 3, 2); ?>
		</div>
		<?php
		} // if pagination
		?>
	</div>
	<div class="overlay hide position-absolute w-100 h-100 bg-light opacity-25 start-0 top-0 z-3"></div>
</div>
<?php
}

?>
