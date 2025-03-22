<?php
global $post, $product, $theme_setting;

//debug($product);

?>
<div class="product col-md-6">
	<div class="inner d-flex flex-column bg-black">
		<div class="product-images position-relative ratio-1x1 bg-dark">
			<?php
			if(!empty($product->get('images'))) {
			?>
			<div class="position-absolute w-100 h-100 start-0 top-0">
				<div class="product-images-slider owl-carousel owl-theme">
					<?php
					foreach ($product->get('images') as $key => $value) {
						?>
						<div class="ratio ratio-1x1">
							<?php
							if($key==0) {
							echo wp_get_attachment_image($value['attachment_id'], 'medium_large', false, ['class'=>'object-fit-cover']);
							} else {
								$src = wp_get_attachment_image_src( $value['attachment_id'], 'medium_large', false );
								?>
								<img class="owl-lazy object-fit-cover" data-src="<?php echo esc_url($src[0]); ?>">
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
			}

			$file_types = get_the_terms( $product->id, 'product_file_type' );

			if ( !is_wp_error( $file_types ) && !empty( $file_types ) ) {
				?>
				<div class="file-type position-absolute start-0 top-0 z-3 d-flex">
				<?php
				foreach ($file_types as $key => $value) {
					echo '<span class="d-block ms-1">'.esc_html($value->name).'</span>';
				}
				?>
				</div>
				<?php
			}
			?>
			<div class="position-absolute top-0 end-0 z-3">
				<button type="button" class="btn btn-sm btn-danger text-yellow fw-bold m-1" data-bs-toggle="modal" data-bs-target="#request-popup" data-src="<?=esc_url($product->get_image_src('large'))?>" data-id="<?=$product->post->ID?>" data-type="request" data-popup-title="<?php echo esc_attr($theme_setting->get('request_popup_title', 'XEM HỒ SƠ MẪU')); ?>"><?php echo esc_html($theme_setting->get('request_button_text', 'ĐĂNG KÝ NHẬN HỒ SƠ MẪU')); ?></button>
			</div>
			<?php
			if(has_role('administrator')) {
				?>
				<div class="position-absolute bottom-0 end-0 z-3 p-1 d-flex align-items-center">
					<div class="me-1"><?php echo $product->id; ?></div>
					<?php
					edit_post_link( '<span class="dashicons dashicons-edit"></span>' );
					?>
				</div>
				<?php
			}

			if($product->get('frontage','')!=''&&$product->get('depth','')!='') {
				?>
				<div class="position-absolute bottom-0 start-0 z-3 px-2">
					<div class="product-dimension fw-bold text-yellow mx-1">Kích thước: <?=esc_html($product->get('frontage',''))?>m x <?=esc_html($product->get('depth',''))?>m</div>
				</div>
				<?php
			}
			?>
		</div>
		<div class="product-info text-center p-2 position-relative">
			<div class="d-flex flex-wrap justify-content-center justify-content-lg-between align-items-center">
				<div class="m-1"><?php echo $product->get_price_html(); ?></div>
				<div class="product-actions d-flex flex-wrap justify-content-center">
					<?php
					if(has_role('administrator') && $product->get('url_data_file')!='') {
						?>
						<a href="<?php echo esc_url($product->get('url_data_file')); ?>" class="btn btn-sm btn-secondary fw-bold m-1" target="_blank"><?php echo esc_html($theme_setting->get('upload_button_text', 'UP FILE 3D')); ?></a>
						<?php
					}

					?>
					<button type="button" class="btn btn-sm btn-success fw-bold m-1 floor_plan_button" data-bs-target="#request-popup" data-bs-toggle="modal" data-src="<?=esc_url($product->get_image_src('large'))?>" data-id="<?=$product->post->ID?>" data-type="floor_plan" data-popup-title="<?php echo esc_attr($theme_setting->get('floor_plan_popup_title', 'ĐĂNG KÝ NHẬN MẶT BẰNG')); ?>"><?php echo esc_html($theme_setting->get('floor_plan_button_text', 'MẶT BẰNG')); ?></button>
				
					<button type="button" class="btn btn-sm btn-success fw-bold m-1 interior_button" data-bs-target="#request-popup" data-bs-toggle="modal" data-src="<?=esc_url($product->get_image_src('large'))?>" data-id="<?=$product->post->ID?>" data-type="interior" data-popup-title="<?php echo esc_attr($theme_setting->get('interior_popup_title', 'ĐĂNG KÝ XEM NỘI THẤT')); ?>"><?php echo esc_html($theme_setting->get('interior_button_text', 'NỘI THẤT')); ?></button>
				
					<?php if($product->get_price()>0) { ?>
						<button type="button" class="btn btn-sm btn-primary fw-bold m-1" data-bs-target="#request-popup" data-bs-toggle="modal" data-src="<?=esc_url($product->get_image_src('large'))?>" data-id="<?=$product->post->ID?>" data-type="purchase" data-popup-title="<?php echo esc_attr($theme_setting->get('purchase_popup_title', 'MUA HỒ SƠ ĐÃ LỰA CHỌN')); ?>"><?php echo esc_html($theme_setting->get('purchase_button_text', 'ĐẶT MUA')); ?></button>
					<?php } elseif($product->get_price()===0) { ?>
						
					<?php } else { ?>
						<span class="btn btn-sm btn-warning text-red fw-bold m-1">SẮP RA MẮT</span>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>