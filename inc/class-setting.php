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

		if(boolval($recaptcha_verify["success"]) && $recaptcha_verify["score"] >= $score || WP_DEBUG) {
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