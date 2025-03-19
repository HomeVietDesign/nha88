<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['images'])) {
	// data-bs-ride="carousel"
	$carousel_id = wp_unique_id('carousel-');

	$interval = isset($atts['interval']) ? 1000*absint($atts['interval']) : 5000;
	?>
	<div class="fw-shortcode-carousel">
		<div id="<?=esc_attr($carousel_id)?>" class="carousel slide"<?php echo (isset($atts['autoplay'])&&$atts['autoplay']=='yes')?' data-bs-ride="carousel"':''; ?>>
			
			<div class="carousel-inner">
				<?php
				foreach ($atts['images'] as $key => $image) {
					?>
					<div class="carousel-item<?php echo ($key==0)?' active': ''; ?>" data-bs-interval="<?=$interval?>">
						<?php echo wp_get_attachment_image( $image['attachment']['attachment_id'], $atts['size'], false, ['class'=>'d-block w-100'] ); ?>
						<div class="carousel-caption">
							<?=wp_format_content($image['text'])?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<button class="carousel-control-prev d-none d-md-block" type="button" data-bs-target="#<?=esc_attr($carousel_id)?>" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Trước</span>
			</button>
			<button class="carousel-control-next d-none d-md-block" type="button" data-bs-target="#<?=esc_attr($carousel_id)?>" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Sau</span>
			</button>
		</div>
	</div>
	<?php
}
