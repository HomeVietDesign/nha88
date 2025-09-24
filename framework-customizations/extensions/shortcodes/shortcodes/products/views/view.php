<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
$ex_id = 0;
if(is_singular( 'product' )) $ex_id = get_the_ID();

$product_cat_in = isset($atts['product_cat_in']) ? array_map('absint', $atts['product_cat_in']) : [];
$product_cat_ex = isset($atts['product_cat_ex']) ? array_map('absint', $atts['product_cat_ex']) : [];

$shortcode_html_id = uniqid('fw-shortcode-products-');

$items = intval($atts['items']);

$args = [
	'post_type' => 'product',
	'posts_per_page' => $items,
	'post_status' => 'publish',
];

if($ex_id) $args['post__not_in'] = [$ex_id];

$tax_query = [];

if($product_cat_in) {
	$tax_query['product_cat_in'] = [
		'taxonomy' => 'product_cat',
		'field' => 'term_id',
		'terms' => $product_cat_in
	];
}

if($product_cat_ex) {
	$tax_query['product_cat_ex'] = [
		'taxonomy' => 'product_cat',
		'field' => 'term_id',
		'terms' => $product_cat_ex,
		'operator' => 'NOT IN'
	];
}

//debug($args);

$meta_query = [];

$orderby = [
    'date' => 'DESC',
    'ID' => 'DESC',
];

if(!empty($tax_query)) {
	$args['tax_query'] = $tax_query;
}
if(!empty($meta_query)) {
	$args['meta_query'] = $meta_query;
}
if(!empty($orderby)) {
	$args['orderby'] = $orderby;
}

$query = new \WP_Query($args);

//debug($query->request);
//debug($atts);

?>
<div id="<?=$shortcode_html_id?>" class="fw-shortcode-products">
	<div class="fw-shortcode-products-inner">
		<input type="hidden" name="query" value="<?=esc_attr(json_encode($query->query))?>">
		<div class="media-container">
			<?php \FW_Shortcode_Products::products($query, $atts['popup']); ?>
		</div>
		<?php
		if($items > 0 && $query->max_num_pages > 1) {
		?>
		<div class="fw-shortcode-products-paginate-links paginate-links d-flex justify-content-center align-items-center">
			<?php echo \FW_Shortcode_Products::pagination($query); ?>
		</div>
		<?php
		} // if pagination
		?>
	</div>
</div>
<?php