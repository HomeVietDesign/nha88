<?php
namespace Nha88;

class Ajax {
	
	public static function logout_post_password() {
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
		if($url) wp_remote_request($url, ['method'=>'PURGE']);
		die;
	}

}
