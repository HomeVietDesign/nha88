<?php
namespace Nha88;

class Account {

	private $id = 0;

	public $code = '';

	public $name = '';

	public $email = '';

	public function __construct($term_account) {

		if(is_int($term_account)) $term_account = get_term_by( 'term_id', $term_account, 'customer' );

		if($term_account instanceof \WP_Term && $term_account->taxonomy=='customer') {
			$this->id = $term_account->term_id;
			$this->code = $term_account->slug;
			$this->name = sanitize_text_field($term_account->description);
			$this->email = sanitize_email($term_account->name);
		}
	}

	public function can_download($post) {
		if(has_term( $this->id, 'customer', $post )) {
			return true;
		}
		return false;
	}

	public function get_id() {
		return $this->id;
	}
}