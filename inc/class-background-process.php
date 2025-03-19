<?php
namespace Nha88;

class Background_Process {

	public static function purchase_process($data) {
		/*
		[
			'type'=>$purchase_type, 
			'email'=>$customer_email, 
			'item_id'=>$product_id, 
			'item_value'=>$price, 
			'bank_trans_file'=>$bank_trans_file, 
			'url'=>$url, 
			'ref'=>$ref, 
			'user_agent'=>$_SERVER['HTTP_USER_AGENT']
		]
		*/

		global $theme_setting;

		$retry_count = isset( $data['retry_count'] ) ? intval( $data['retry_count'] ) : 0;

		try {

			$product = new \Nha88\Product($data['item_id']);
			if($product->id==0) {
				throw new \Exception("Sản phẩm không tồn tại.");
			}

			// lấy hoặc tạo mới thông tin khách hàng
			$term_customer = get_term_by( 'name', $data['email'], 'customer' );
			if($term_customer==false) {
				require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
				$new_customer = wp_create_term( $data['email'], 'customer' );
				if(is_array($new_customer) && isset($new_customer['term_id'])) {
					$term_customer = get_term_by( 'term_id', absint($new_customer['term_id']), 'customer' );
				} else {
					throw new \Exception("Không thể tạo khách hàng.");
				}
			}
				
			// tạo đơn hàng trong csdl
			$purchase_data = array(
				//'post_title'   => $data['item_id'] . ' - ' . $data['email'],
				'post_title'   => current_time('U'),
				'post_type'    => 'purchase',
				'post_date'    => $data['date'],
				'post_status'  => 'publish',  // Trạng thái ban đầu là pending
			);
			$purchase_id = wp_insert_post( $purchase_data );

			if(!$purchase_id instanceof \WP_Error) {
				// Lưu thông tin bổ sung dưới dạng meta
				fw_set_db_post_option($purchase_id, 'status', 'pending');
				fw_set_db_post_option($purchase_id, 'email', $data['email']);
				fw_set_db_post_option($purchase_id, 'customer', $term_customer->term_id);
				fw_set_db_post_option($purchase_id, 'product', $product->id);
				fw_set_db_post_option($purchase_id, 'value', $data['item_value']);
				fw_set_db_post_option($purchase_id, 'url', $data['url']);
				fw_set_db_post_option($purchase_id, 'ref', $data['ref']); // base64
				fw_set_db_post_option($purchase_id, 'user_agent', $data['user_agent']);
				fw_set_db_post_option($purchase_id, 'bank_trans_file', $data['bank_trans_file']);
				
			} else {
				throw new \Exception("Không thể tạo đơn hàng.");
			}

			// gửi email tới admin
			ob_start();
			?>
			<p style='font-weight:bold;'>
			<?php
			switch ($data['type']) {
				case 'combo':
					echo 'Giao dịch COMBO sản phẩm tại website Nha88';
					break;
				
				default:
					echo 'Giao dịch sản phẩm tại website Nha88';
					break;
			}
			?>
				
			</p>
			<p></p>
			<p style='font-weight:bold;'>Email: <?=esc_html($data['email'])?></p>
			<p style='font-weight:bold;'>Mã giao dịch: <span style="color:#f00;"><?=esc_html($purchase_id)?></span></p>
			<p style='font-weight:bold;'>Mã sản phẩm: <span style="color:#f00;"><?=esc_html($data['item_id'])?></span></p>
			<p style='font-weight:bold;'>Giá sản phẩm: <span style="color:#f00;"><?=esc_html(number_format($data['item_value'], 0, '.', ','))?>vnđ</span></p>
			<p style='font-weight:bold;'>Ảnh sản phẩm:</p>
			<p><?php echo $product->get_image('large'); ?></p>
			<p></p>
			<?php if($data['item_value']>0) { ?>
			<p>Xem ảnh chuyển khoản đính kèm</p>
			<p></p>
			<p></p>
			<?php } ?>
			<p>-------------</p>
			<p>Email gửi từ website: <?=esc_url(home_url())?></p>
			<p>Nguồn: 
			<?php
			
			$referrers = urldecode(base64_decode($data['ref']).','.$data['url']);

			if(strpos($referrers, 'facebook')!==false || strpos($referrers, 'fbclid')!==false) {
				echo 'Facebook';
			} elseif (strpos($referrers, 'google')!==false || strpos($referrers, 'gclid')!==false) {
				echo 'Google';
			} elseif (strpos($referrers, 'zalo')!==false) {
				echo 'Zalo';
			} else {
				echo '(Không xác định)';
			}
			?>
			</p>
			<p>Quảng cáo: 
			<?php
			if(preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrers, $matches)) {
				echo esc_html(str_replace('+', ' ', $matches[1]));
			}
			?>
			</p>
			<p>URL cuối: <?=esc_url($data['url'])?></p>
			<p>Thiết bị: <?=esc_html($data['user_agent'])?></p>
			<?php

			$mail_body = ob_get_clean();

			$attachments = (!empty($data['bank_trans_file'])) ? [$data['bank_trans_file']['file']] : null;

			$admin_email = [
				get_bloginfo('admin_email'),
			];

			$admin2_email = $theme_setting->get_admin_email_address();

			if(!empty($admin2_email)) {
				$admin_email = array_merge($admin_email, $admin2_email);
			}

			$admin_sent = wp_mail( $admin_email, '[Nha88] Giao dịch', $mail_body, ['Content-Type: text/html; charset=UTF-8'], $attachments );

			// debug
			#$admin_sent = true;

			if($admin_sent) {
				
				// gửi email tới khách hàng
				ob_start();
				?>
				<p>
					<?php
					switch ($data['type']) {
						case 'combo':
							echo 'Giao dịch COMBO sản phẩm tại website Nha88';
							break;
						
						default:
							echo 'Giao dịch sản phẩm tại website Nha88';
							break;
					}
					?>
				</p>
				<p></p>
				<p style='font-weight:bold; font-size: 16px; color:#f00;'>Giao dịch đang chờ duyệt. Chúng tôi sẽ gửi thông báo tới email của bạn, vui lòng chờ!</p>
				<p></p>
				<p style='font-weight:bold;'>Mã giao dịch: <span style="color:#f00;"><?=esc_html($purchase_id)?></span></p>
				<p style='font-weight:bold;'>Mã sản phẩm tham chiếu: <span style="color:#f00;"><?=esc_html($data['item_id'])?></span></p>
				<p style='font-weight:bold;'>Giá sản phẩm: <span style="color:#f00;"><?=esc_html(number_format($data['item_value'], 0, '.', ','))?>vnđ</span></p>
				<p style='font-weight:bold;'>Ảnh sản phẩm tham chiếu:</p>
				<p><?php echo $product->get_image('large'); ?></p>
				<p></p>
				<p></p>
				<p>Nếu có vấn đề gì liên hệ với ban quản trị để được hỗ trợ. Xin cảm ơn!</p>
				<p></p>
				<p></p>
				<p>-------------</p>
				<p>Email gửi từ website: <?=esc_url(home_url())?></p>
				<?php
				$mail_body = ob_get_clean();

				$customer_sent = wp_mail( $data['email'], '[Nha88] Giao dịch', $mail_body, ['Content-Type: text/html; charset=UTF-8'] );

				// debug
				#$customer_sent = true;

				if(!$customer_sent) {
					if(WP_DEBUG) {
						debug_log('Lỗi gửi email thông báo đơn hàng "'.$purchase_id.'" cho khách hàng.');
					} else {
						wp_mail( get_bloginfo('admin_email'), 'Lỗi gửi email thông báo đơn hàng "'.$purchase_id.'" cho khách hàng', json_encode( $data ), ['Content-Type: text/html; charset=UTF-8'] );
					}
					//throw new \Exception("Lỗi gửi email thông tin giao dịch cho khách hàng.");
				}
				
			} else {
				if(WP_DEBUG) {
					debug_log('Lỗi gửi email thông báo đơn hàng "'.$purchase_id.'" cho admin.');
				} else {
					wp_mail( get_bloginfo('admin_email'), 'Lỗi gửi email thông báo đơn hàng "'.$purchase_id.'"', json_encode( $data ), ['Content-Type: text/html; charset=UTF-8'] );
				}
				//throw new \Exception("Lỗi gửi email thông tin giao dịch cho admin.");
			}

		} catch (\Exception $e) {

			if ( $retry_count < 3 ) {
				// Tăng số lần retry và đẩy lại vào queue
				$data['retry_count'] = $retry_count + 1;
				as_enqueue_async_action('purchase_process', [$data], 'purchase');
			} else {
				if(WP_DEBUG) {
					debug_log('Giao dịch xử lý lỗi quá 3 lần: '.$e->getMessage());
					debug_log($data);
				} else {
					// Sau 3 lần retry, thông báo lỗi cho admin
					wp_mail( get_bloginfo('admin_email'), '[Nha88] Giao dịch xử lý lỗi quá 3 lần', json_encode( $data ), ['Content-Type: text/html; charset=UTF-8'] );
				}
			}

		}
	}
	
}