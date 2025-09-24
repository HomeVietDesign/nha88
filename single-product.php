<?php
get_header();

while (have_posts()) {
	the_post();
	global $post, $product;
	
	?>
	<div class="heading text-center container py-3">
		<h1 class="fs-3 text-uppercase">
		<?php the_title(); ?>
		</h1>
		<?php $product->sku_html(); ?>
	</div>
	<div class="topinfo container">
		<div class="row">
			<div class="col-lg-8 images">
				<div class="inner h-100">
					<div class="position-sticky z-1">
						<?php $product->image_html(); ?>
					</div>
				</div>
			</div>
			<div class="col-lg-4 desc">
				<div class="inner h-100">
					<div class="position-sticky z-1">
						<div class="fw-bold mb-3">GIỚI THIỆU VỀ HỒ SƠ</div>
						<?php $product->info_html(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	the_content();
}

get_footer();