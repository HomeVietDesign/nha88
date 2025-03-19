<?php
namespace Nha88\Admin;

class Product_Cat {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='product_cat') {
			wp_enqueue_style( 'manage-product_cat', THEME_URI.'/assets/css/manage-product_cat.css', [], '' );
			wp_enqueue_script('manage-product_cat', THEME_URI.'/assets/js/manage-product_cat.js', array('jquery'), '');
		}
	}

	public static function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => date('Ymd-His', current_time( 'U' ))], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public static function manage_edit_column_header($columns) {
		if(isset($columns['slug'])) {
			$columns['slug'] = 'URL';
		}

		//$columns['term_id'] = 'ID';
		//$columns['page'] = 'Cấp tài khoản';
		$columns['order'] = 'STT';

		if(isset($columns['posts'])) {
			$columns['posts'] = 'Đếm';
		}
		return $columns;
	}

	public static function manage_edit_columns_value($row, $column_name, $term_id) {
		$nonce = wp_create_nonce('quick_edit_'.$term_id);

		switch ($column_name) {
			case 'order':
				echo intval(get_term_meta($term_id, 'order', true));
				break;
			case 'page':
				$product_page_id = absint(get_term_meta($term_id, '_product_page', true));
				echo esc_html(get_post_field( 'post_title', $product_page_id ));
				break;
			case 'term_id':
				echo esc_html($term_id);
				break;
		}
	}
}