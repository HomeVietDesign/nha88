<?php
namespace Nha88\Admin;

class Product {

	public static function custom_columns_value($column, $post_id) {
		$nonce = wp_create_nonce('quick_edit_'.$post_id);
		
		switch ($column) {
			case 'sku':
				echo esc_html(fw_get_db_post_option($post_id, 'sku'));
				break;
			case 'id':
				echo $post_id;
				break;
			case 'image':
				if(has_post_thumbnail()) {
					the_post_thumbnail( 'thumbnail' );
				} else {
					echo '<i>(No image)</i>';
				}
				break;
		}
	}

	public static function custom_columns_header($columns) {

		$columns['sku'] = 'Mã hồ sơ';
		$columns['id'] = 'ID';
		$columns['image'] = 'Ảnh';

		return $columns;

	}

	public static function save_product($post_id, $post, $update) {
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => 'bt'.$post_id], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}
	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if(($hook=='edit.php' || $hook=='post.php' || $hook=='post-new.php') && $post_type=='product') {
			//add_thickbox();
			//wp_enqueue_script('jquery-input-number', THEME_URI.'/libs/jquery-input-number/jquery-input-number.js', array('jquery'), '', false);

			wp_enqueue_style('admin-product', THEME_URI.'/assets/css/admin-product.css', [], '');
			wp_enqueue_script('admin-product', THEME_URI.'/assets/js/admin-product.js', array('jquery'), '');
		}

	}
}