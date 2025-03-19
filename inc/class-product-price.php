<?php
namespace Nha88;

/**
 * 
 */
class Product_Price extends Term {
	
	public $taxonomy = 'product_price';

	public function get_qrbank_src() {
		if($this->get('qrbank')) {
			return wp_get_attachment_image_url( $this->get('qrbank')['attachment_id'], 'full' );
		}
		return '';
	}
}