<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['images'])) {
	?>
	<div class="fw-shortcode-photoswipe-lightbox">
		<div class="shortcode-photoswipe-lightbox pswp-gallery">
		<?php
		foreach ($atts['images'] as $key => $image) {
			$src_full = wp_get_attachment_image_src( $image['attachment_id'], 'full' );
			if($key==0) {
				?>
				<a class="d-block position-relative" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>">
					<?php echo wp_get_attachment_image( $image['attachment_id'], $atts['size'] ); ?>
					<span class="zoom-icon position-absolute start-0 top-0 w-100 h-100 d-flex justify-content-center align-items-center">
						<img src="<?=fw_ext( 'shortcodes' )->locate_URI( '/shortcodes/photoswipe-lightbox/static/img/mag-plus-white.png' )?>" width="150" height="150">
					</span>
				</a>
				<?php
			} else {
				?>
				<a class="d-none" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>"></a>
				<?php
			}
		}
		?>
		</div>
	</div>
	<?php
}
