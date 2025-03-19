<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['images'])) {
	$owlcarousel_id = wp_unique_id('owlcarousel-');

	$interval = isset($atts['interval']) ? 1000*absint($atts['interval']) : 5000;
	$autoplay = isset($atts['autoplay']) ? $atts['autoplay'] : 'no';
	?>
	<div id="<?=esc_attr($owlcarousel_id)?>" class="fw-shortcode-owlcarousel">
		<div class="owl-carousel" data-options="<?php echo esc_attr(json_encode(['autoplay'=>($autoplay=='yes')?1:0,'autoplayTimeout'=>$interval])); ?>">
			<?php
			foreach ($atts['images'] as $key => $image) {
				?>
				<div class="slide">
					<img class="owl-lazy" data-src="<?php echo esc_url(wp_get_attachment_url( $image['attachment']['attachment_id'] )); ?>">
					<?php if($image['text']!='') { ?>
  					<div class="caption">
						<?=wp_format_content($image['text'])?>
					</div>
					<?php } ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
