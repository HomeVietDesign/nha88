<?php
namespace Nha88;

/**
 * 
 */
abstract class Term {

	public $id = 0;

	public $taxonomy = 'category';

	public $term = null;

	protected $data = [];

	private static $instances = [];

	public function __construct($term, $taxonomy) {
		$_term = get_term($term, $taxonomy);
		if( $_term && !($_term instanceof \WP_Error) && $_term->taxonomy==$this->taxonomy) {
			$this->id = $_term->term_id;
			$this->term = $_term;
		}
	}

	public static function get_instance($id, $tax) {

		if(!isset(self::$instances['t'.$id])) {
			$object = new static($id, $tax);
			if($object->id) {
				self::$instances['t'.$id] = $object;
			}
		}

		return isset(self::$instances['t'.$id]) ? self::$instances['t'.$id] : null;
	}

	public function set($meta_key, $meta_value) {
		if($this->id==0) {
			throw new \Exception('Term not exists.');
		}

		// xoa du lieu meta cu
		if(isset($this->data[$meta_key])) unset($this->data[$meta_key]);

		if(function_exists('fw_get_db_term_option')) {
			return fw_set_db_term_option($this->id, $this->taxonomy, $meta_key, $meta_value);
		} else {
			return update_term_meta($this->id, $meta_key, $meta_value);
		}

	}

	public function get($meta_key, $default = '') {
		if($this->id==0) {
			throw new \Exception('Term not exists.');
		}

		if(!isset($data[$meta_key])) {
			if(function_exists('fw_get_db_term_option')) {
				$this->data[$meta_key] = fw_get_db_term_option($this->id, $this->taxonomy, $meta_key, $default);
			} else {
				$this->data[$meta_key] = get_term_meta($this->id, $meta_key, true);
			}
		}

		return $this->data[$meta_key];
	}

	public function refresh() {
		$this->term = null;
		//$this->type = '';
		$this->data = [];
		if($this->id) {
			$this->__construct($this->id, $this->taxonomy);
			if(isset(self::$instances['t'.$this->id])) {
				unset(self::$instances['t'.$this->id]);
			}
		}
	}

}