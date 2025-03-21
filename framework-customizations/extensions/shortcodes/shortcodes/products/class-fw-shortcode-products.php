<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Products extends FW_Shortcode
{
	
	public function _init()
	{
		add_action('wp_ajax_products_paginate', [$this, 'ajax_products_paginate']);
		add_action('wp_ajax_nopriv_products_paginate', [$this, 'ajax_products_paginate']);

		add_action('wp_footer', [$this, 'purchase_popup_html'], 99);

		add_action('wp_ajax_purchase_load_form', [$this, 'ajax_purchase_load_form']);
		add_action('wp_ajax_nopriv_purchase_load_form', [$this, 'ajax_purchase_load_form']);

		add_action('wp_ajax_purchase', [$this, 'ajax_purchase']);
		add_action('wp_ajax_nopriv_purchase', [$this, 'ajax_purchase']);

		add_action('wp_ajax_request', [$this, 'ajax_request']);
		add_action('wp_ajax_nopriv_request', [$this, 'ajax_request']);
	}

	public function ajax_request() {
		global $theme_setting;

		$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
		$customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
		$customer_tel = isset($_POST['customer_tel']) ? phone_0284(sanitize_phone_number($_POST['customer_tel'])) : '';
		$token = isset($_POST['token']) ? $_POST['token'] : '';
		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';
		$url = isset($_POST['url']) ? urldecode($_POST['url']) : '';
		$referrer = urldecode(base64_decode($ref).','.$url);
		
		$token_ok = false;
		$product_ok = false;
		$customer_name_ok = false;
		$customer_tel_ok = false;

		$response = [
			'code' => 0,
			'msg' => '',
			'id' => $product_id,
			'data' => [ // fb pixel event data
				'phone' => $customer_tel,
				'name' => $customer_name,
				'content_type' => 'product',
			],
			'fb_pxl_code' => ''
		];

		$product = new \Nha88\Product($product_id);

		if($theme_setting->recaptcha_verify($token, 0.5)) {
			$token_ok = true;
		} else {
			$response['code'] = -1;
			$response['msg'] = '<p class="text-red">Lỗi captcha.</p>';
		}

		if($product->id) {
			$product_ok = true;
		} else {
			$response['code'] = -2;
			$response['msg'] = '<p class="text-red">Sản phẩm không hợp lệ.</p>';
		}

		if(''!=$customer_name) {
			$customer_name_ok = true;
		} else {
			$response['code'] = -3;
			$response['msg'] = '<p class="text-red">Tên của bạn không hợp lệ.</p>';
		}

		if(''!=$customer_tel) {
			$customer_tel_ok = true;
		} else {
			$response['code'] = -4;
			$response['msg'] = '<p class="text-red">Số điện thoại của bạn không hợp lệ.</p>';
		}

		if($product_ok && $customer_name_ok && $customer_tel_ok && $token_ok) {

			$mail_to = [
				get_bloginfo('admin_email'),
			];

			$admin2_email = $theme_setting->get_admin_email_address();

			if(!empty($admin2_email)) {
				$mail_to = array_merge($mail_to, $admin2_email);
			}

			//$mail_to = 'qqngochv@gmail.com';

			$mail_headers = array('Content-Type: text/html; charset=UTF-8');

			ob_start();

			$subject = '[ '.$customer_tel.' ] '.$theme_setting->get('request_button_text', 'XEM HỒ SƠ MẪU');

			?>
			<p style='font-weight:bold;'><?php echo esc_html(get_option('request_popup_title', 'ĐĂNG KÝ NHẬN HỒ SƠ MẪU')); ?></p>
			<p>Họ tên: <?=esc_html($customer_name)?></p>
			<p>Số điện thoại: <?=esc_html($customer_tel)?></p>
			<p>Mã SP: <?=esc_html($product->id)?></p>
			<p>Ảnh SP:</p>
			<p><img src="<?=esc_url($product->get_image_src('large'))?>" style='max-width:100%;height:auto;'></p>
			<p>-------------</p>
			<p>Email gửi từ website: <?=esc_url(home_url())?></p>
			
			<p>Nguồn: 
			<?php
			if(strpos($referrer, 'facebook')!==false || strpos($referrer, 'fbclid')!==false) {
				echo 'Facebook';
			} elseif (strpos($referrer, 'google')!==false || strpos($referrer, 'gclid')!==false) {
				echo 'Google';
			} elseif (strpos($referrer, 'zalo')!==false) {
				echo 'Zalo';
			} else {
				echo '(Không xác định)';
			}
			?>
			</p>
			<p>Quảng cáo: 
			<?php
			if(preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches)) {
				echo esc_html(str_replace('+', ' ', $matches[1]));
			}
			?>
			</p>
			<p>Thiết bị: <?=esc_html($_SERVER['HTTP_USER_AGENT'])?></p>
			<?php
			$body = ob_get_clean();

			//$response['msg'] = $body;
			
			$send = wp_mail( $mail_to, $subject, $body, $mail_headers );
			
			//$send = true;

			if($send) {
				
				//if(function_exists('as_enqueue_async_action')) {
					//as_enqueue_async_action('add_customer_order', [['id'=>$id, 'title'=>get_the_title($id), 'image'=>$attachment_img, 'phone'=>$phone, 'name'=>$name, 'type'=>$type, 'url'=>$url, 'ref'=>$ref, 'user_agent'=>$_SERVER['HTTP_USER_AGENT']]], 'order');
				//}
				
				$response['code'] = 1;
				$response['msg'] = '<p class="text-success"><strong>Yêu cầu của Quý khách đã được gửi đi.</strong> Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất.</p><p class="text-success text-end">Xin cảm ơn!</p>';
			} else {
				$response['code'] = -5;
				$response['msg'] = '<p class="text-red">Yêu cầu chưa được gửi đi! Vui lòng liên hệ với ban quản trị về sự cố này.</p>';
			}
		} else {
			$response['code'] = -6;
			//$response['msg'] = 'Thông tin đã nhập không hợp lệ! Xin thử lại.';
		}

		$response = apply_filters( 'request_submit', $response );

		wp_send_json($response);
	}


	public function ajax_purchase() {
		global $theme_setting;

		$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
		$customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
		$token = isset($_POST['token']) ? $_POST['token'] : '';
		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';
		$url = isset($_POST['url']) ? urldecode($_POST['url']) : '';
		$purchase_type = isset($_POST['purchase_type']) ? $_POST['purchase_type'] : 'normal';
		$customer_bank = isset($_FILES['customer_bank']) ? $_FILES['customer_bank'] : null;
		
		$token_ok = false;
		$product_ok = false;
		$customer_email_ok = false;
		$customer_bank_ok = false;

		$response = [
			'code' => 0,
			'msg' => '',
			'id' => $product_id,
			'data' => [ // fb pixel event data
				'email' => $customer_email,
				'value' => 0,
				'currency' => 'VND',
				'content_type' => 'product',
				'purchase_type' => $purchase_type,
			],
			'fb_pxl_code' => ''
		];

		$product = new \Nha88\Product($product_id);

		if($theme_setting->recaptcha_verify($token, 0.5)) {
			$token_ok = true;
		} else {
			$response['code'] = -1;
			$response['msg'] = '<p class="text-red">Lỗi captcha.</p>';
		}

		if($product->id) {
			$product_ok = true;
		} else {
			$response['code'] = -2;
			$response['msg'] = '<p class="text-red">Sản phẩm không hợp lệ.</p>';
		}

		if(''!=$customer_email) {
			$customer_email_ok = true;
		} else {
			$response['code'] = -3;
			$response['msg'] = '<p class="text-red">Email của bạn không hợp lệ.</p>';
		}

		if($product_ok && $customer_email_ok && $token_ok) {
			
			// $relative_target_file = '';
			// $target_file = '';

			$bank_trans_file = '';

			if($product->get_price()>0) {
			
				// tải lên ảnh chuyển khoản thành công
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				}

				$upload_overrides = array(
					'test_form' => false
				);

				$bank_trans_file = wp_handle_upload( $customer_bank, $upload_overrides );

				if ( $bank_trans_file && ! isset( $bank_trans_file['error'] ) ) {
					$customer_bank_ok = true;
				} else {
					/*
					* Error generated by _wp_handle_upload()
					* @see _wp_handle_upload() in wp-admin/includes/file.php
					*/
					$response['code'] = -4;
					$response['msg'] = '<p class="text-red">Lỗi tải lên ảnh chuyển khoản.</p>';
				}

			} elseif($product->get_price()===0) {
				$customer_bank_ok = true;
			} else {
				$response['code'] = -5;
				$response['msg'] = '<p class="text-red">Sản phẩm không thể giao dịch.</p>';
			}

			if($customer_bank_ok) {

				$response['data']['value'] = $product->get_price();

				$referrers = ($ref!='')?urldecode(base64_decode($ref)):'';

				if(function_exists('as_enqueue_async_action')) {
					
					// Xử lý đơn hàng dưới nền
					as_enqueue_async_action('purchase_process', [['date'=>current_time( 'mysql' ), 'type'=>$purchase_type, 'email'=>$customer_email, 'item_id'=>$product_id, 'item_value'=>$product->get_price(), 'bank_trans_file'=>$bank_trans_file, 'url'=>$url, 'ref'=>$ref, 'user_agent'=>$_SERVER['HTTP_USER_AGENT']]], 'purchase');

					self::login_customer($customer_email);

					$response['code'] = 1;
					$response['msg'] = '<p class="text-success">Gửi giao dịch thành công. Vui lòng đợi email xác nhận, sau đó bạn có thể tải file. Xin cảm ơn!</p>';

				} else {
					$response['code'] = -6;
					$response['msg'] = '<p class="text-red">Giao dịch chưa được tạo. Vui lòng báo với chúng tôi về sự cố này để được khắc phục.</p>';

					if(WP_DEBUG) {
						debug_log('Hàm as_enqueue_async_action để thực hiện tác vụ mua hàng dưới nền.');
					} else {
						wp_mail( get_bloginfo('admin_email'), 'Lỗi xử lý nền', 'Hàm as_enqueue_async_action để thực hiện tác vụ mua hàng dưới nền.', ['Content-Type: text/html; charset=UTF-8'] );
					}
				}

			}
		}

		$response = apply_filters( 'purchase', $response );

		wp_send_json( $response );
	}

	public static function login_customer($customer_email) {

		require_once ABSPATH . WPINC . '/class-phpass.php';
		$hasher = new PasswordHash( 8, true );

		/**
		 * Filters the life span of the post password cookie.
		 *
		 * By default, the cookie expires 10 days from creation. To turn this
		 * into a session cookie, return 0.
		 *
		 * @since 3.7.0
		 *
		 * @param int $expires The expiry time, as passed to setcookie().
		 */
		$expire  = apply_filters( 'post_password_expires', time() + 10 * DAY_IN_SECONDS );
		$referer = wp_get_referer();

		if ( $referer ) {
			$secure = ( 'https' === parse_url( $referer, PHP_URL_SCHEME ) );
		} else {
			$secure = false;
		}

		setcookie( 'wp-postpass_' . COOKIEHASH, $hasher->HashPassword( wp_unslash( $customer_email ) ), $expire, COOKIEPATH, COOKIE_DOMAIN, $secure );
	}

	public function ajax_purchase_load_form() {
		$id = isset($_GET['id']) ? absint($_GET['id']) : 0;
		$type = isset($_GET['type']) ? $_GET['type'] : 'normal';
		$product = new \Nha88\Product($id);
		if($product->post && $product->get_price()!==null) {
			global $theme_setting;
			?>
			<input type="hidden" id="product_id" name="product_id" value="<?=$id?>" required>
			<input type="hidden" id="purchase_type" name="purchase_type" value="<?=esc_attr($type)?>" required>
			<div class="row">
				<div class="purchase-form-fields col-lg-5">
					<?php
					switch ($type) {
						case 'combo':
							if($theme_setting->get('purchase_combo_popup_desc', '')!='') {
								?>
								<div class="mb-3 purchase-combo-popup-desc"><?php echo wp_get_the_content($theme_setting->get('purchase_combo_popup_desc', '')); ?></div>
								<?php
							}
							break;
						
						default:
							if($theme_setting->get('purchase_popup_desc', '')!='') {
								?>
								<div class="mb-3 purchase-popup-desc"><?php echo wp_get_the_content($theme_setting->get('purchase_popup_desc', '')); ?></div>
								<?php
							}
							break;
					}
					
					?>
					<div class="mb-3">
						<div class="form-label mb-1">Email của bạn</div>
						<input type="email" id="customer_email" name="customer_email" maxlength="60" class="form-control" value="" required>
					</div>
					<?php if($product->get_price() > 0) { ?>
					<div class="mb-3">
						<div class="form-label mb-1">Ảnh chuyển khoản thành công</div>
						<label class="d-block" for="customer_bank">
							<span class="input-group">
								<span class="form-control overflow-hidden"></span>
								<span class="input-group-text">Bấm tải lên</span>
							</span>
							<div style="width: 0;height: 0;overflow: hidden;"><input type="file" id="customer_bank" name="customer_bank" accept="image/*" class="form-control" required></div>
						</label>
					</div>
					<?php } ?>
					<div class="mb-3">
						<div id="purchase-response"></div>
						<button type="submit" id="purchase-submit" class="w-100 btn btn-danger rounded-0 text-uppercase text-yellow fw-bold" disabled>Bấm gửi đi</button>
					</div>
					<?php if($product->get_price() > 0) { ?>
					<div class="mb-3 d-none d-lg-block text-center">
					<?php
					switch ($type) {
						case 'combo':
							?>
							<div class="my-1">Mã thanh toán nhanh</div>
							<?php
							if($theme_setting->get('bank_qr')) {
								echo wp_get_attachment_image( $theme_setting->get('bank_qr')['attachment_id'], 'large' );
							}
							?>
							<div class="my-1">Đồng giá <strong><?php echo number_format(fw_get_db_settings_option('purchase_combo_price'),0,'.',','); ?></strong> vnđ/hồ sơ - Tổng giá trị sẽ theo số lượng thư viện Bạn mua.</div>
							<?php
							break;
						
						default:
							?>
							<div class="my-1">Mã quét thanh toán</div>
							<?php
							if($product->get_price_qrbank()!='') {
								echo $product->get_price_qrbank();
							}
							elseif($theme_setting->get('bank_qr')) {
								echo wp_get_attachment_image( $theme_setting->get('bank_qr')['attachment_id'], 'full' );
							}
							?>
							<div class="my-1">Giá mua 1 hồ sơ: <?php echo $product->get_price_html_short(); ?></div>
							<?php
							break;
					}
					
					?>
					</div>
					<?php } ?>
				</div>
				<div class="purchase-product-image col-lg-7">
					<div id="purchase-product-image"><?php echo $product->get_image('large'); ?></div>
				</div>
			</div>
			<?php
		} else {
			?>
			<p class="text-danger">Sản phẩm không hợp lệ.</p>
			<?php
		}
		die;
	}

	public function purchase_popup_html() {
		global $theme_setting;

		?>
		<div class="modal fade" id="purchase-popup" tabindex="-1" role="dialog" aria-labelledby="purchase-popup-label">
			<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="purchase-popup-label">
							<?php //echo esc_html($theme_setting->get('purchase_popup_title', 'MUA SẢN PHẨM')); ?>
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form id="frm-purchase" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" class="modal-body" enctype="multipart/form-data"></form>
				</div>
			</div>
		</div>

		<div class="modal fade" id="request-popup" tabindex="-1" role="dialog" aria-labelledby="request-popup-label">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="request-popup-label">
							<?php echo esc_html($theme_setting->get('request_popup_title', 'ĐĂNG KÝ NHẬN HỒ SƠ MẪU')); ?>
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form id="frm-request" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" class="modal-body">
						<input type="hidden" id="request-product-id" name="product_id" value="" required>
						<div class="mb-3">
							<div class="form-label mb-1">Tên khách hàng</div>
							<input type="text" name="customer_name" maxlength="60" class="form-control" value="" required>
						</div>
						<div class="mb-3">
							<div class="form-label mb-1">Điện thoại liên hệ</div>
							<input type="tel" name="customer_tel" class="form-control" value="" required>
						</div>
						<div class="mb-3">
							<div id="request-response"></div>
							<button type="submit" id="request-submit" class="w-100 btn btn-danger rounded-0 text-uppercase text-yellow fw-bold" disabled>Bấm gửi đi</button>
						</div>
						<div class="mb-3">
							<div id="request-product-image"></div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php

	}

	public function ajax_products_paginate() {

		$response = [
			'items' => '',
			'paginate_links' => ''
		];

		$paged = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 1;
    	$args = isset($_REQUEST['query']) ? $_REQUEST['query'] : [];

		$args['paged'] = $paged;

		$query = new \WP_Query($args);

		if($query->have_posts()) {

			ob_start();
				self::loop_products($query);
			$response['items'] = ob_get_clean();

			$response['paginate_links'] = self::pagination($query, 3, 2);

		}
		
		wp_send_json($response);
	}

	public static function products($query) {
		if($query->have_posts()) {

			?>
			<div class="list-products row justify-content-center">
			    <?php self::loop_products($query); ?>
			</div>
			<?php

		}
	}

	public static function loop_products($query) {
		while ($query->have_posts()) {
			$query->the_post();
			//self::display_product();
			get_template_part( 'product', 'loop' );
		}
		wp_reset_postdata();
	}

	public static function pagination($wp_query, $end_size=3, $mid_size=2) {

		// Get max pages and current page out of the current query, if available.
		$total   = (int)$wp_query->max_num_pages;
		$current = ($wp_query->query_vars['paged']==0)?1:(int)$wp_query->query_vars['paged'];

		// Who knows what else people pass in $args.
		if ( $total < 2 ) {
			return;
		}

		if ( $end_size < 1 ) {
			$end_size = 1;
		}

		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}

		$r          = '';
		$page_links = array();
		$dots       = false;

		if ( $current && 1 < $current ): 
			$page_links[] = '<button class="prev page-numbers" data-paged="'.($current - 1).'" type="button"><span class="dashicons dashicons-arrow-left"></span></button>';
		endif;

		for ( $n = 1; $n <= $total; $n++ ) :
			if ( $n == $current ) :
				$page_links[] = '<span class="page-numbers current">'.$n.'</span>';

				$dots = true;
			else :
				if ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) :
					$page_links[] = '<button class="page-numbers" data-paged="'.$n.'" type="button">'.$n.'</button>';

					$dots = true;
				elseif ( $dots ) :
					//$page_links[] = '<span class="page-numbers dots">&hellip;</span>';
					if($total>2*$end_size+1) {
						$page_links[] = '<span class="page-numbers dots">&hellip;</span>';
					} else {
						$page_links[] = '<button class="page-numbers" data-paged="'.$n.'" type="button">'.$n.'</button>';
					}
					$dots = false;
				endif;
			endif;
		endfor;

		if ( $current && $current < $total ) :
			$page_links[] = '<button class="next page-numbers" data-paged="'.($current + 1).'" type="button"><span class="dashicons dashicons-arrow-right"></span></button>';
		endif;

		$r = implode( "\n", $page_links );

		return $r;
	}
}