<?php
namespace Nha88\Admin;

class Product_File_Type {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='product_file_type') {
			wp_enqueue_style( 'manage-product_file_type', THEME_URI.'/assets/css/manage-product_file_type.css', [], '' );
			//wp_enqueue_script('manage-product_file_type', THEME_URI.'/assets/js/manage-product_file_type.js', array('jquery'), '');
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
		return $columns;
	}

	public static function manage_edit_columns_value($row, $column_name, $term_id) {
		switch ($column_name) {
			case 'term_id':
				echo esc_html($term_id);
				break;
		}
	}
}