<?php
namespace Nha88\Admin;

class Customer {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='customer') {
			wp_enqueue_style( 'manage-customer', THEME_URI.'/assets/css/manage-customer.css', [], '' );
			wp_enqueue_script('manage-customer', THEME_URI.'/assets/js/manage-customer.js', array('jquery'), '');
		}
	}

	public static function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => date('Ymd-His', current_time( 'U' ))], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public static function manage_edit_column_header($columns) {
		if(isset($columns['name'])) {
			$columns['name'] = 'Email';
		}
		if(isset($columns['slug'])) {
			$columns['slug'] = 'Mã khách hàng';
		}
		if(isset($columns['description'])) {
			$columns['description'] = 'Họ tên';
		}
		if(isset($columns['posts'])) {
			$columns['posts'] = 'Đếm';
		}
		return $columns;
	}
}