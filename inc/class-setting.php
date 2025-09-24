<?php
namespace Nha88;

final class Setting {
	use \Nha88\Singleton;

	public $recaptcha_keys;

	public $data = [];

	protected function __construct() {
		if(!function_exists('is_plugin_active')) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$sitekey = '';
		$secretkey = '';
		$ctf7_has_recaptcha = false;

		if(is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )) {
			$ctf7_recaptcha = \WPCF7_RECAPTCHA::get_instance();

			if($ctf7_recaptcha->is_active()) {
				$sitekey = $ctf7_recaptcha->get_sitekey();
				$secretkey = $ctf7_recaptcha->get_secret($sitekey);
				$ctf7_has_recaptcha = true;
			}
		}

		if($sitekey=='' || $secretkey=='') {
			$sitekey = $this->get('recaptcha_key');
			$secretkey = $this->get('recaptcha_secret');
		}

		$this->recaptcha_keys = ['sitekey'=>$sitekey,'secretkey'=>$secretkey, 'ctf7'=>$ctf7_has_recaptcha];
	}

	public static function request_type() {
		return [
			'order' => 'Đặt mua hồ sơ',
			//'vfp' => 'Xem mặt bằng'
		];
	}

	public function cf_captcha_verify($token) {
		// Get Turnstile Keys from Settings
		$captcha = self::get_turnstile_keys();

		if ($captcha['sitekey'] && $captcha['secretkey']) {

			$headers = array(
				'body' => [
					'secret' => $captcha['secretkey'],
					'response' => $token
				]
			);
			$verify = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $headers);
			$verify = wp_remote_retrieve_body($verify);
			$response = json_decode($verify);

			//wp_mail( 'qqngoc2988@gmail.com', $_SERVER['HTTP_HOST'].' cf captcha verify', json_encode( $response ), ['Content-Type: text/html; charset=UTF-8'] );

			//debug_log($response);

			if($response->success) {
				return true;
			}
		} else {
			return true;
		}

		return false;
	}

	public static function has_turnstile() {
        $turnstile_keys = self::get_turnstile_keys();

        if ($turnstile_keys['sitekey']!='' && $turnstile_keys['secretkey']!='') {
            return $turnstile_keys;
        }

        return false;
    }

	public static function get_turnstile_keys() {
        if(!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $sitekey = '';
        $secretkey = '';
        $ctf7_has_turnstile = false;

        if(is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )) {
            $ctf7_turnstile = \WPCF7_Turnstile::get_instance();

            if($ctf7_turnstile->is_active()) {
                $sitekey = $ctf7_turnstile->get_sitekey();
                $secretkey = $ctf7_turnstile->get_secret($sitekey);
                $ctf7_has_turnstile = true;
            }
        }

        if($sitekey=='' || $secretkey=='') {
            $sitekey = fw_get_db_settings_option('cf_turnstile_key');
            $secretkey = fw_get_db_settings_option('cf_turnstile_secret');
        }

        return ['sitekey'=>$sitekey,'secretkey'=>$secretkey, 'ctf7'=>$ctf7_has_turnstile];
    }

	public function recaptcha_verify($token, $score=0.5) {
		$check_captcha = wp_remote_post(
			"https://www.google.com/recaptcha/api/siteverify",
			array(
				'body'=>array(
					'secret' => $this->recaptcha_keys['secretkey'],
					'response' => $token
				)
			)
		);

		$recaptcha_verify = json_decode(wp_remote_retrieve_body($check_captcha), true);
		//debug_log($recaptcha_verify);

		if( boolval($recaptcha_verify["success"]) && $recaptcha_verify["score"] >= $score ) {
		//if( (boolval($recaptcha_verify["success"]) && $recaptcha_verify["score"] >= $score) || WP_DEBUG) {
			return true;			
		}

		return false;
	}

	public function get_admin_email_address() {
		$admin_email_address = explode(',',$this->get('admin_email_address'));
		return array_map('sanitize_email', $admin_email_address);
	}

	public function get($setting_id, $default='') {
		if(!isset($this->data[$setting_id])) {
			$this->data[$setting_id] = fw_get_db_settings_option($setting_id, $default);
		}
		return $this->data[$setting_id]; 
	}
}