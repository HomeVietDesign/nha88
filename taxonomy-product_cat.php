<?php
get_header();

?>
<div class="products-container container-xxl">
	<div class="heading text-center py-3 fs-4 fw-bold text-uppercase"><?php the_archive_title(); ?></div>
	<?php
		
	if(!empty(get_the_archive_description())) {
		?>
		<div class="description mb-3"><?php the_archive_description(); ?></div>
		<?php
	}

	?>
	<div class="list-products row justify-content-center">
	<?php
	while (have_posts()) {
		the_post();
		get_template_part( 'product', 'loop' );
	}
	?>
	</div>
	<?php
	$paginate_links = paginate_links([
		'end_size'           => 3,
		'mid_size'           => 2,
		'prev_text'          => '<span class="dashicons dashicons-arrow-left"></span>',
		'next_text'          => '<span class="dashicons dashicons-arrow-right"></span>',
	]);

	if($paginate_links) {
		?>
		<div class="paginate-links product-paginate-links d-flex justify-content-center align-items-stretch p-2 mb-3">
			<?php echo $paginate_links; ?>
		</div>
		<?php
	}
	?>
</div>
<?php
get_footer();