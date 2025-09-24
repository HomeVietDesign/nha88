<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Buynow extends FW_Shortcode
{	

	const RESPONSE_CODE = -1;
	const RESPONSE_TOKEN_CODE = -2;
	const RESPONSE_PRODUCT_CODE = -3;
	const RESPONSE_PHONE_NUMBER_CODE = -4;
	const RESPONSE_SEND_FAILED_CODE = -5;

	public function _init() {
		add_action( 'wp_ajax_buynow', [$this, 'ajax_buynow'] );
		add_action( 'wp_ajax_nopriv_buynow', [$this, 'ajax_buynow'] );
	}

	public function ajax_buynow() {
		global $theme_setting;

		$id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		
		$phone_number = isset($_POST['phone_number']) ? phone_0284(sanitize_phone_number($_POST['phone_number'])) : '';
		$cf_turnstile_response = isset($_POST['cf-turnstile-response']) ? $_POST['cf-turnstile-response'] : '';
		$ref = isset($_POST['ref'])?$_POST['ref']:'';
		$url = isset($_POST['url']) ? urldecode($_POST['url']) : '';
		$referrer = urldecode(base64_decode($ref).','.$url);
		
		$token_ok = false;
		$product_ok = false;
		$phone_number_ok = false;

		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [
				'id' => $id,
				'phone' => $phone_number,
				'name' => '',
				'ref' => $ref,
				'url' => $url,
			],
			'fb_pxl_code' => '',
			'tt_pxl_code' => '',
		];

		if($theme_setting->cf_captcha_verify($cf_turnstile_response)) {
			$token_ok = true;
		} else {
			$response['code'] = self::RESPONSE_TOKEN_CODE;
			$response['msg'] = 'Lỗi chưa xác minh...';
		}

		if($id) {
			$product_ok = true;
		} else {
			$response['code'] = self::RESPONSE_PRODUCT_CODE;
			$response['msg'] = 'Sản phẩm không hợp lệ.';
		}

		if(''!=$phone_number) {
			$phone_number_ok = true;
		} else {
			$response['code'] = self::RESPONSE_PHONE_NUMBER_CODE;
			$response['msg'] = 'Số điện thoại của bạn không hợp lệ.';
		}

		if($token_ok && $product_ok && $phone_number_ok) {
			$mail_to = get_bloginfo('admin_email');

			$admin2_email = $theme_setting->get_admin_email_address();
			$mail_headers = array('Content-Type: text/html; charset=UTF-8');

			if(!empty($admin2_email)) {
				foreach ($admin2_email as $key => $value) {
					$mail_headers[] = 'Bcc: '.$value;
				}
			}

			//$mail_to = 'qqngochv@gmail.com';


			ob_start();

			$subject = '[ '.$phone_number.' ] Mua ngay hồ sơ';

			$src = '';
			if(has_post_thumbnail( $id )) {
				$srcs = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'large' );
				$src = $srcs[0];
			}
			?>
			<p style='font-weight:bold;'>THÔNG TIN ĐẶT MUA HỒ SƠ</p>
			<p>Số điện thoại: <?=esc_html($phone_number)?></p>
			<p>ID: <?=esc_html($id)?></p>
			<?php if($src) { ?>
			<p>Ảnh hồ sơ:</p>
			<p><img src="<?=esc_url($src)?>" style='max-width:100%;height:auto;'></p>
			<?php } ?>
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
			} elseif (strpos($referrer, 'tiktok')!==false) {
				echo 'Tiktok';
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
			
			$sent = wp_mail( $mail_to, $subject, $body, $mail_headers );

			//$sent = true;
			
			if($sent) {
				
				$response['code'] = 1;
				$response['msg'] = '<p><strong>Yêu cầu của Quý khách đã được gửi đi.</strong> Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất.</p><p>Xin cảm ơn!</p>';
			} else {
				$response['code'] = self::RESPONSE_SEND_FAILED_CODE;
				$response['msg'] = 'Yêu cầu chưa được gửi đi! Vui lòng liên hệ với ban quản trị về sự cố này.';
			}
		}

		$response = apply_filters( 'buynow', $response );

		wp_send_json( $response );

	}


}