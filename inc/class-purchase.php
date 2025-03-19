<?php
namespace Nha88;

/**
 * 
 */
class Purchase extends Post {

	public $type = 'purchase';

	public function publish() {
		$this->set('status', 'publish');
	}

	public function cancel() {
		$this->set('status', 'cancel');
	}

	public function unlink_bank_file() {
		if($this->get('bank_trans_file')) {
			@unlink($this->get('bank_trans_file')['file']);
		}
	}

	public function get_urls() {
		if(!isset($this->data['urls'])) {
			$this->data['urls'] = [];
			if($this->get_referers()) {
				$this->data['urls'] = array_filter(explode(',', $this->get_referers()));
			}
		}
		
		return $this->data['urls'];
	}

	public function get_referers() {
		if(!isset($this->data['referers'])) {
			$this->data['referers'] = urldecode(base64_decode($this->get('ref')).','.$this->get('url'));
		}
		return $this->data['referers'];
	}

	public function get_utm_source() {
		$referrers = $this->get_referers();
		$utm_source = '';
		if(strpos($referrers, 'facebook')!==false || strpos($referrers, 'fbclid')!==false) {
			$utm_source = 'Facebook';
		} elseif (strpos($referrers, 'google')!==false || strpos($referrers, 'gclid')!==false) {
			$utm_source = 'Google';
		} elseif (strpos($referrers, 'zalo')!==false) {
			$utm_source = 'Zalo';
		}

		return $utm_source;
	}

	public function get_utm_content() {
		$referrers = $this->get_referers();

		$utm_content = '';
		if(preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrers, $matches)) {
			$utm_content = str_replace('+', ' ', $matches[1]);
		}

		return $utm_content;
	}


}