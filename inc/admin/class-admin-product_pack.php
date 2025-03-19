<?php
namespace Nha88\Admin;

class Product_Pack {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='product_pack') {
			wp_enqueue_style( 'manage-product_pack', THEME_URI.'/assets/css/manage-product_pack.css', [], '' );
			wp_enqueue_script('manage-product_pack', THEME_URI.'/assets/js/manage-product_pack.js', array('jquery'), '');
		}
	}

	public static function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => date('Ymd-His', current_time( 'U' ))], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public static function manage_edit_column_header($columns) {
		if(isset($columns['slug'])) {
			$columns['slug'] = 'Mã phần tử';
		}
		if(isset($columns['posts'])) {
			$columns['posts'] = 'Đếm';
		}
		return $columns;
	}
}