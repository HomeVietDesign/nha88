<?php
namespace Nha88;

/**
 * 
 */
class WPCF7 {

	public static function wpcf7_convert_phone_number($value, $value_orig, $tag) {
		$value = phone_0284(sanitize_phone_number($value));

		return $value;
	}

	public static function wpcf7_before_send_mail($contact_form) {
		$submission = \WPCF7_Submission::get_instance();

		$props = $contact_form->get_properties();

		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';
		$referrer = urldecode(base64_decode($ref).','.$submission->get_meta('url'));
		ob_start();
		?>
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
		if( preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches) ) {
				echo esc_html(str_replace('+', ' ', $matches[1]));
			}
		?>
		</p>
		<?php
		$props['mail']['body'] .= ob_get_clean();
		
		$contact_form->set_properties($props);
	}

}
