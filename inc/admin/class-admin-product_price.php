<?php
namespace Nha88\Admin;

class Product_Price {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='product_price') {
			wp_enqueue_style( 'manage-product_price', THEME_URI.'/assets/css/manage-product_price.css', [], '' );
			//wp_enqueue_script('manage-product_price', THEME_URI.'/assets/js/manage-product_price.js', array('jquery'), '');
		}
	}

	public static function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => date('Ymd-His', current_time( 'U' ))], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public static function manage_edit_column_header($columns) {
		if(isset($columns['slug'])) {
			unset($columns['slug']);
		}

		if(isset($columns['description'])) {
			unset($columns['description']);
		}

		if(isset($columns['posts'])) {
			unset($columns['posts']);
		}

		$columns['value'] = 'Giá trị';

		$columns['qrbank'] = 'Ảnh QR';
		return $columns;
	}

	public static function manage_edit_columns_value($row, $column_name, $term_id) {
		//$nonce = wp_create_nonce('quick_edit_'.$term_id);

		switch ($column_name) {
			case 'qrbank':
				$qrbank = fw_get_db_term_option($term_id, 'product_price', 'qrbank');
				if($qrbank){
					echo wp_get_attachment_image($qrbank['attachment_id'], 'large');
				}
				break;
			case 'value':
				echo number_format(absint(get_term_field( 'name', $term_id, 'product_price' )), 0, '.', ',');
				break;
		}
	}

	public static function register_column_for_sortable($columns) {
		//debug_log($columns);
		if(isset($columns['name'])) unset($columns['name']);

		$columns['value'] = 'name';

  		return $columns;
	}
}