<?php
namespace Nha88;

class Ajax {

	public static function request_product() {
		global $theme_setting;

		$type = isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : '';
		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$name = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : '';
		$phone = isset($_REQUEST['phone']) ? phone_0284(sanitize_phone_number($_REQUEST['phone'])) : '';
		$url = isset($_REQUEST['url']) ? esc_url(base64_decode($_REQUEST['url'])) : '';
		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';
		$urls = urldecode(base64_decode($ref).','.$url);
		$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';

		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [
				'type' => $type,
				'id' => $id,
				'phone' => $phone,
				'name' => $name,
				'url' => $url, // encoded
				'ref' => $ref // base64 và encoded
			],
			'fb_pxl_code' => '',
			'tt_pxl_code' => '',
		];

		//debug_log($response);

		if( $theme_setting->cf_captcha_verify($token) ) {

			if($id && ''!=$name && ''!=$phone) {

				$attachment_img = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
				$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'large' );

				$mail_to = get_bloginfo('admin_email');
				$mail_headers = array('Content-Type: text/html; charset=UTF-8');

				$admin2_email = $theme_setting->get_admin_email_address();
				if(!empty($admin2_email)) {
					foreach ($admin2_email as $key => $value) {
						if($key==0) {
							$mail_headers[] = 'Bcc: '.$mail_to;
							$mail_to = $value;

						} else {
							$mail_headers[] = 'Bcc: '.$value;
						}
						
					}
				}

				//$mail_to = 'qqngoc2988@gmail.com';

				ob_start();

				$subject = '[ '.phone_8420($phone).' ] '.\Nha88\Setting::request_type()[$type];

				?>
				<p style='font-weight:bold;'>YÊU CẦU <?=mb_strtoupper(\Nha88\Setting::request_type()[$type])?></p>
				<p>Họ tên: <?=esc_html($name)?></p>
				<p>Số điện thoại: <?=esc_html(phone_8420($phone))?></p>
				<p>Mã hồ sơ: <?=esc_html(fw_get_db_post_option($id, 'sku'))?></p>
				<p>ID: <?=esc_html($id)?></p>
				<p>Ảnh hồ sơ:</p>
				<p><img src="<?=(($image_src)?esc_url($image_src[0]):$attachment_img)?>" style='width:100%;height:auto;'></p>
				<p>-------------</p>
				<p>Email gửi từ website: <?=esc_url(home_url())?></p>
				
				<p>Nguồn: 
				<?php
				if(strpos($urls, 'facebook')!==false || strpos($urls, 'fbclid')!==false) {
					echo 'Facebook';
				} elseif (strpos($urls, 'google')!==false || strpos($urls, 'gclid')!==false) {
					echo 'Google';
				} elseif (strpos($urls, 'youtube')!==false) {
					echo 'Youtube';
				} elseif (strpos($urls, 'zalo')!==false) {
					echo 'Zalo';
				} elseif (strpos($urls, 'tiktok')!==false) {
					echo 'Tiktok';
				} else {
					echo '(Không xác định)';
				}
				?>
				</p>
				<p>Quảng cáo: 
				<?php
				if(preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $urls, $matches)) {
					echo esc_html(str_replace('+', ' ', $matches[1]));
				}
				?>
				</p>
				<p>Thiết bị: <?=esc_html($_SERVER['HTTP_USER_AGENT'])?></p>
				<?php
				$body = ob_get_clean();
				
				$send = wp_mail( $mail_to, $subject, $body, $mail_headers );
				
				//$send = true;

				if($send) {
					
					$response['data']['title'] = get_the_title($id);
					$response['data']['image'] = $attachment_img;
					
					$response['code'] = 1;
					$response['msg'] = '<p><strong>Yêu cầu của Quý khách đã được gửi đi.</strong> Trợ lý của KTS. Trần Sơn sẽ liên hệ tư vấn trong thời gian sớm nhất.</p><p>Xin cảm ơn!</p>';

				} else {
					$response['code'] = -3;
					$response['msg'] = 'Yêu cầu chưa được gửi đi! Vui lòng liên hệ với ban quản trị về sự cố này.';
				}
			} else {
				$response['code'] = -2;
				$response['msg'] = 'Thông tin đã nhập không hợp lệ! Xin thử lại.';
			}
			
		} else {
			$response['code'] = -1;
			$response['msg'] = 'Chưa xác minh! Xin thử lại.';
		}
		
		$response = apply_filters( 'request_product', $response );
		
		wp_send_json($response);
		
		die;
	}
	
	public static function logout_post_password() {
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
		if($url) wp_remote_request($url, ['method'=>'PURGE']);
		die;
	}

}
