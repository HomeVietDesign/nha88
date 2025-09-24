<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

?>
<div class="fw-shortcode-container fw-shortcode-floor-plan">
	<div class="row">
		<div class="col-md-7 floor-plan-thumbnail mb-3">
			<?php
			if($atts['thumbnail']) {
				echo wp_get_attachment_image( $atts['thumbnail']['attachment_id'], 'full' );
			}
			?>
		</div>
		<div class="col-md-5 plan-items mb-3">
			<?php
			if(!empty($atts['title'])) {
				?>
				<div class="fw-bold mb-2"><?=esc_html($atts['title'])?></div>
				<?php
			}

			if(!empty($atts['items'])) {
			?>
			<ul class="items list-unstyled">
			<?php
			foreach ($atts['items'] as $item) {
				?>
				<li class="item mb-2<?php echo (!empty($item['images']))?' fp-pswp-gallery':''; ?>">
					<span><?php echo esc_html($item['title']); ?></span>
					<?php
					if(!empty($item['images'])) {
						foreach ($item['images'] as $key => $value) {
							$src_full = wp_get_attachment_image_src( $value['attachment_id'], 'full' );
							if($key==0) {
								?>
								&nbsp;⇒&nbsp;<a class="fw-bold" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>">Xem nội thất</a>
								<?php
							} else {
								?>
								<a class="d-none" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>"></a>
								<?php
							}
						}
					}
					?>
				</li>
				<?php
			}
			?>
			</ul>
			<?php
			}

			if(!empty($atts['images'])) {
				?>
				<div class="has-zoom-icon fp-pswp-gallery position-relative">
					<?php
					if(!empty($atts['images'])) {
						foreach ($atts['images'] as $key => $value) {
							$src_full = wp_get_attachment_image_src( $value['attachment_id'], 'full' );
							if($key==0) {
								?>
								<a class="fw-bold text-white" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>">
									<?php echo wp_get_attachment_image( $value['attachment_id'], 'large' ); ?>
									<span class="zoom-icon position-absolute start-0 top-0 w-100 h-100 d-flex justify-content-center align-items-center">
										<img src="<?=THEME_URI?>/assets/img/iconmonstr-zoom-in-thin-48.png">
									</span>
								</a>
								<?php
							} else {
								?>
								<a class="d-none" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>"></a>
								<?php
							}
						}
					}
					?>
					
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<?php