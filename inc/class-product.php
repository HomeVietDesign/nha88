<?php
namespace Nha88;

class Product extends Post {

	public $type = 'product';

	public function info_html() {
		$size_z = $this->get('size_z', null);
		$size_s = $this->get('size_s', null);
		$description = $this->get('description', '');
		?>
		<div class="mb-2 horizontal-sizes">
			<span>– Kích thước&nbsp;</span>
			<?php if($this->get('size_x', '')) { ?>
			<span>Ngang:&nbsp;<b><?=esc_html($this->get('size_x', ''))?>m</b></span>
			<?php } ?>
			
			<?php if($this->get('size_x', '') && $this->get('size_y', '')) { ?>
			<span>&nbsp;&nbsp;∣&nbsp;&nbsp;</span>
			<?php } ?>

			<?php if($this->get('size_y', '')) { ?>
			<span>Chiều dài:&nbsp;<b><?=esc_html($this->get('size_y', '-'))?>m</b></span>
			<?php } ?>
		</div>
						
		<?php if(!empty($size_z)) { ?>
		<div class="mb-2 vertical-sizes">
			<span>– Chiều cao </span>
			<?php
			foreach ($size_z as $key => $value) {
				if($key>0) {
					?>&nbsp;&nbsp;∣&nbsp;&nbsp;<?php
				}
				echo esc_html($value['name']).':&nbsp;<b>'.esc_html($value['size']).'m</b>';
			}
			?>
		</div>
		<?php } ?>

		<?php if(!empty($size_s)) { ?>
		<div class="mb-2 areas">
			<span>– Diện tích xây dựng </span>
			<?php
			foreach ($size_s as $key => $value) {
				if($key>0) {
					?>&nbsp;&nbsp;∣&nbsp;&nbsp;<?php
				}
				echo esc_html($value['name']).':&nbsp;<b>'.esc_html($value['size']).'m<sup>2</sup></b>';
			}
			?>
		</div>
		<?php } ?>

		<?php if($description!='') { ?>
		<div class="description">
			<?php echo wp_get_the_content($description); ?>
		</div>
		<?php
		}
	}

	public function image_html() {
		$images = $this->get('images', null);
		?>
		<div class="single-product-images mb-3 position-relative">
			<?php if(!empty($images)) { ?>
			<div class="gallery">
				<div class="slider owl-carousel owl-theme">
					<?php
					foreach ($images as $key => $value) {
						$src = wp_get_attachment_image_src( $value['attachment_id'], 'full', false );
						?>
						<img class="owl-lazy" data-src="<?php echo esc_url($src[0]); ?>">
						<?php
					}
					?>
				</div>
				<div class="navigation-thumbs owl-carousel owl-theme">
					<?php
					foreach ($images as $key => $value) {
						$src = wp_get_attachment_image_src( $value['attachment_id'], 'medium', false );
						?>
						<img class="owl-lazy" data-src="<?=esc_url($src[0])?>">
						<?php
					}
					?>
				</div>
			</div>
			<?php } else { ?>
			<div class="image">
				<?php echo get_the_post_thumbnail( $this->id, 'full' ); ?>
			</div>
			<?php } ?>
		</div>
		<?php
	}

	public function get_regular_price() {
		$price = '';
		if($this->id) {
			$price_term = get_the_terms( $this->post, 'product_price' );
			if($price_term && !($price_term instanceof \WP_Error)) {
				$price = absint($price_term[0]->name);
			}
		}
		
		return $price;
	}

	public function get_sale_price() {
		$regular_price = $this->get_regular_price();
		$sale_price = '';
		$sale_off = fw_get_db_settings_option('sale_off', '');
		if($sale_off && $regular_price) {
			$sale_off = floatval($sale_off);
			if($sale_off>100) $sale_off=100;
			$sale_price = floor($regular_price - $sale_off * $regular_price / 100);
		}

		return $sale_price;
	}

	public function single_actions_html() {
		?>
		<div class="single-actions text-center">
			<div class="d-flex justify-content-center justify-content-xl-between align-items-center flex-wrap">
				<div class="mb-2">
					<div class="d-flex align-items-center">
						<?php
						$this->request_buttons(true);
						$this->sample_link(true);
						$this->view_images_button(true);
						?>
					</div>
				</div>
				<div class="mb-2">
				<?php
				
				$this->price_html(true);
				
				?>
				</div>
			</div>
			<div class="d-flex align-items-center justify-content-center flex-wrap">
				<?php $this->popup_sale_button(true); ?>
			</div>
			<?php
			
			$this->view_floor_plan_buttons(true);
			
			?>
			<div class="product-title fw-bold py-2 text-green"><?php echo esc_html(get_the_title($this->post)); ?></div>
			<?php
			
			$this->size_html(true);
			$this->sku_html(true);
			
			?>
		</div>
		<?php
	}

	public function loop_html($type='') {
		
		$this->thumbnail_html($type);

		?>
		<div class="heading text-center p-2 pb-5 position-relative">
			<div class="d-flex justify-content-center justify-content-xl-between align-items-center flex-wrap">
				<div class="mb-2">
					<div class="d-flex align-items-center">
						<?php
						$this->request_buttons();
						$this->sample_link();
						$this->view_images_button();
						?>
					</div>
				</div>
				<div class="mb-2">
				<?php
				
				$this->price_html();
				
				?>
				</div>
			</div>
			<div class="d-flex align-items-center justify-content-center flex-wrap">
				<?php $this->popup_sale_button(); ?>
			</div>
			<?php
			
			$this->view_floor_plan_buttons();
			
			?>
			<div class="product-title fw-bold py-2 text-green"><?php echo esc_html(get_the_title($this->post)); ?></div>
			<?php
			
			$this->size_html();
			$this->sku_html();
			
			$this->button_detail_html($type);
			
			?>
		</div>
		<?php
	}

	public function view_images_button($is_singular = false) {
		$images = fw_get_db_post_option($this->id, 'images', null);
		?>
		<div class="loop-product-images pswp-gallery">
		<?php
		if(!empty($images)) {
			foreach ($images as $key => $value) {
				$src_full = wp_get_attachment_image_src( $value['attachment_id'], 'full' );
				if($key==0) {
					?>
					<a class="btn btn-sm btn-primary ms-1" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>" title="Các hình ảnh">Slice</a>
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

	public function popup_sale_button($is_singular = false) {
		global $theme_setting;
		if($this->get('popup_sale')=='yes' && $theme_setting->get('popup_sale','')!='') {
			?>
			<button type="button" class="btn btn-sm btn-danger text-yellow m-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modal-popup-sale"><?=esc_html($theme_setting->get('popup_sale_button_text',''))?></button>
			<?php
		}
	}

	public function button_detail_html($type='') {
		if($type!='coming_soon') {
		?>
		<a class="btn btn-sm btn-primary popup" href="<?php echo esc_url(get_permalink($this->post)); ?>">Xem chi tiết</a>
		<?php
		}
		if(current_user_can( 'edit_posts' )) {
			?>
			<span class="position-absolute bottom-0 start-0 p-2"><?=$this->id?></span>
			<a href="<?php echo esc_url(get_edit_post_link($this->post)); ?>" target="_blank" class="btn btn-sm text-white position-absolute bottom-0 end-0"><span class="dashicons dashicons-edit"></span></a>
			<?php
		}
	}
 	public function sku_html($is_singular = false) {
		$sku = fw_get_db_post_option($this->id, 'sku', '');

		if($sku) echo '<div class="product-sku mb-2">(Mã hiệu: <strong>'.esc_html($sku).'</strong>)</div>';
	}

	public function size_html($is_singular = false) {
		$size_x = fw_get_db_post_option($this->id, 'size_x', true);
		$size_y = fw_get_db_post_option($this->id, 'size_y', true);

		if($size_x && $size_y) {
			?>
			<div class="mb-2 horizontal-sizes text-yellow"><span>Kích thước:</span>&nbsp;<span><?=esc_html($size_x)?></span>x<span><?=esc_html($size_y)?>m</span></div>
			<?php
		}
	}

	public function sample_link($is_singular = false) {
		global $theme_setting;
		if($theme_setting->get('sample_link')) {
			?>
			<a class="btn btn-sm btn-primary m-1 popup" href="<?=esc_url($theme_setting->get('sample_link'))?>"><?=esc_html($theme_setting->get('sample_link_label'))?></a>
			<?php
		}
	}

	public function price_html($is_singular = false) {
		$regular_price = $this->get_regular_price();
		$sale_price = $this->get_sale_price();
		?>
		<div class="product-price-wrap<?php echo ($sale_price)?' has-sale':''; ?> m-1">
		<?php
		if($regular_price!='') {
			?>
			<div class="d-flex product-regular-price align-items-center justify-content-end">
				<div class="text-uppercase me-2">Giá gốc:</div>
				<div class="price"><?=number_format($regular_price, 0, ',', '.')?></div>
				<div class="ms-1 unit">vnđ</div>
			</div>
			<?php
			if($sale_price) {
			?>
			<div class="d-flex product-sale-price align-items-center justify-content-end">
				<div class="text-uppercase me-2">Giá ưu đãi:</div>
				<div class="price"><?=number_format($sale_price, 0, ',', '.')?></div>
				<div class="ms-1 unit">vnđ</div>
			</div>
			<?php
			}
		}
		?>
		</div>
		<?php
	}

	public function view_floor_plan_buttons($is_singular = false) {
		$fp_options = fw_get_db_post_option($this->id, 'fp_options', []);
		if($fp_options) {
			?>
			<div class="floor-plan-buttons d-flex justify-content-center align-items-center flex-wrap">
			<?php
			foreach ($fp_options as $op) {
				if($op['images']) {
				?>
				<div class="m-1 pswp-gallery">
					<?php
					foreach ($op['images'] as $key => $value) {
						$src_full = wp_get_attachment_image_src( $value['attachment_id'], 'full' );
						if($key==0) {
						?>
						<a class="btn btn-sm btn-primary" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>"><?=esc_html($op['name'])?></a>
						<?php
						} else {
						?>
						<a class="d-none" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>"></a>
						<?php	
						}
					}
					?>		
				</div>
				<?php
				}
			}
			?>
			</div>
			<?php
		}
		
	}

	public function request_buttons($is_singular = false) {
		global $theme_setting;

		$request_type = array_keys(\Nha88\Setting::request_type());
		if($request_type) {
			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id($this->post), 'large' );
			$src = ($image_src) ? $image_src[0] : '';

			foreach ($request_type as $key => $type) {
				?>
				<button type="button" class="btn btn-sm btn-primary m-1" data-bs-toggle="modal" data-bs-target="#request-product" data-id="<?=$this->post->ID?>" data-src="<?=esc_url($src)?>" data-type="<?=esc_attr($type)?>"><?=esc_html(fw_get_db_settings_option($type.'_button_text',''))?></button>
				<?php
			}
		}
		
	}

	public function thumbnail_html($type='') {
		global $theme_setting;
		?>
		<div class="product-thumbnail position-relative">
			<!-- popup sale button -->
			<div class="d-flex position-absolute top-0 end-0 p-1">
				<?php
				
				?>
			</div>
			<?php if($type=='coming_soon') {
				echo get_the_post_thumbnail( $this->post, 'medium_large', ['alt'=>esc_attr(get_the_title($this->post))] );
			} else { ?>
			<a class="popup" href="<?php echo esc_url(get_permalink($this->id)); ?>">
				<?php
				echo get_the_post_thumbnail( $this->post, 'medium_large', ['alt'=>esc_attr(get_the_title($this->post))] );
				?>
			</a>
			<?php } ?>
		</div>
		<?php
	}
}