<?php
namespace Nha88;

/**
 * 
 */
class Post {

	public $id = 0;

	public $type = 'post';

	public $post = null;

	protected $data = [];

	private static $instances = [];

	public function __construct($post) {
		$_post = get_post($post);
		//debug_log($this);
		if($_post && $_post->post_type==$this->type) {
			$this->id = $_post->ID;
			$this->post = $_post;
		}
	}

	public static function get_instance($id) {

		if(!isset(self::$instances['p'.$id])) {
			$object = new static($id);
			if($object->id) {
				self::$instances['p'.$id] = $object;
			}
		}

		return isset(self::$instances['p'.$id])?self::$instances['p'.$id]:null;

	}

	public function set($meta_key, $meta_value) {
		if($this->id==0) {
			throw new \Exception('Post not exists.');
		}

		// xoa du lieu meta cu
		if(isset($this->data[$meta_key])) unset($this->data[$meta_key]);

		if(function_exists('fw_get_db_post_option')) {
			return fw_set_db_post_option($this->id, $meta_key, $meta_value);
		} else {
			return update_post_meta($this->id, $meta_key, $meta_value);
		}

	}

	public function get($meta_key, $default = '') {
		if($this->id==0) {
			throw new \Exception('Post not exists.');
		}

		if(!isset($data[$meta_key])) {
			if(function_exists('fw_get_db_post_option')) {
				$this->data[$meta_key] = fw_get_db_post_option($this->id, $meta_key, $default);
			} else {
				$this->data[$meta_key] = get_post_meta($this->id, $meta_key, true);
			}
		}

		return $this->data[$meta_key];
	}

	public function refresh() {
		$this->post = null;
		//$this->type = '';
		$this->data = [];
		if($this->id) {
			$this->__construct($this->id);
			//wp_cache_delete( $this->id, 'posts' );
			// $_post = get_post($this->id);
			// if($_post) {
			// 	//$this->type = $_post->post_type;
			// 	$this->post = $_post;
			// } else {
			// 	$this->id = 0;
			// }
			if(isset(self::$instances['p'.$this->id])) {
				unset(self::$instances['p'.$this->id]);
			}
		}
	}

}