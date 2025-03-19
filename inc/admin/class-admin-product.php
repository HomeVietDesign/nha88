<?php
namespace Nha88\Admin;

class Product {

	public static function taxonomy_parse_filter($query) {
		//modify the query only if it admin and main query.
		if( !(is_admin() AND $query->is_main_query()) ){ 
			return $query;
		}

		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	
		if($post_type=='product') {
			$tax_query = [];

			$pc = isset($_GET['pc']) ? intval($_GET['pc']) : 0;
			if($pc!=0) {
				$tax_query['pc'] = ['taxonomy' => 'product_cat'];
				if($pc>0) {
					$tax_query['pc']['field'] = 'term_id';
					$tax_query['pc']['terms'] = $pc;
				} else {
					$tax_query['pc']['operator'] = 'NOT EXISTS';
				}

			}

			if(!empty($tax_query)) {
				$query->set('tax_query', $tax_query);
			}
		}

		return $query;
	}

	public static function filter_by_taxonomy($post_type) {
		if ($post_type == 'product') {
			wp_dropdown_categories(array(
				'show_option_all' => '- Hạng mục -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'product_cat',
				'name'            => 'pc',
				'orderby'         => 'name',
				'selected'        => isset($_GET['pc']) ? intval($_GET['pc']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));
		};
	}

	public static function ajax_change_product_url_data_file() {
		$post_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$url_data_file = isset($_REQUEST['url_data_file']) ? sanitize_url($_REQUEST['url_data_file']) : '';

		$response = fw_get_db_post_option($post_id, 'url_data_file');

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			
			fw_set_db_post_option($post_id, 'url_data_file', $url_data_file);
			
			wp_cache_delete($post_id, 'posts');

			$response = $url_data_file;
		}
		wp_send_json($response);
	}

	public static function ajax_change_product_combo() {
		$post_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$combo = isset($_REQUEST['combo']) ? $_REQUEST['combo'] : '';

		$response = fw_get_db_post_option($post_id, 'combo');

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {

			fw_set_db_post_option($post_id, 'combo', ($combo=='true')?'yes':'no');
			
			wp_cache_delete($post_id, 'posts');

			$response = fw_get_db_post_option($post_id, 'combo');
		}

		wp_send_json($response);
	}

	public static function ajax_change_product_has_file() {
		$post_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$has_file = isset($_REQUEST['has_file']) ? $_REQUEST['has_file'] : '';

		$response = fw_get_db_post_option($post_id, 'has_file');

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {

			fw_set_db_post_option($post_id, 'has_file', ($has_file=='true')?'yes':'no');
			
			wp_cache_delete($post_id, 'posts');

			$response = fw_get_db_post_option($post_id, 'has_file');
		}

		wp_send_json($response);
	}

	public static function custom_columns_value($column, $post_id) {
		$nonce = wp_create_nonce('quick_edit_'.$post_id);
		global $product;
		
		switch ($column) {
			case 'id':
				echo $post_id;
				break;
			case 'image':
				$images = fw_get_db_post_option($post_id, 'images', []);
				if($images) {
					echo wp_get_attachment_image( $images[0]['attachment_id'], 'thumbnail', false );
				} else {
					echo '<i>(No image)</i>';
				}
				break;
			case 'url_data_file':
				//echo $product->get('url_data_file');
				?>
				<div style="display: flex;align-items: center;">
				<input type="text" class="url_data_file" data-nonce="<?=esc_attr($nonce)?>" data-id="<?=$post_id?>" value="<?=esc_attr($product->get('url_data_file'))?>" style="width: 100%;">
				<?php
				if($product->get('url_data_file')) {
					?>
					<a href="<?=esc_url($product->get('url_data_file'))?>" target="_blank" style="margin-left: 10px;"><span class="dashicons dashicons-external"></span></a>
					<?php
				}
				?>
				</div>
				<?php
				break;
			case 'actions':
				$has_file = fw_get_db_post_option($post_id, 'has_file', 'no');
				$combo = fw_get_db_post_option($post_id, 'combo', 'no');
				?>
				<label><input type="checkbox" class="has_file" data-nonce="<?=esc_attr($nonce)?>" <?php checked( $has_file, 'yes', true ); ?> data-id="<?=$post_id?>"> Đã có file 3D?</label>
				<label><input type="checkbox" class="combo" data-nonce="<?=esc_attr($nonce)?>" <?php checked( $combo, 'yes', true ); ?> data-id="<?=$post_id?>"> Bán combo?</label>
				<?php
				break;
		}
	}

	public static function custom_columns_header($columns) {

		$columns['id'] = 'ID';
		$columns['url_data_file'] = 'URL thư mục';
		$columns['actions'] = 'Lựa chọn';
		$columns['image'] = 'Ảnh';

		return $columns;

	}

	public static function disable_months_dropdown($disabled, $post_type) {
		if($post_type=='product') {
			$disabled = true;
		}
		return $disabled;
	}

	public static function save_product($post_id, $post, $update) {
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => date('Ymd-His', strtotime($post->post_date))], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}
	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if(($hook=='edit.php' || $hook=='post.php') && $post_type=='product') {
			//add_thickbox();
			//wp_enqueue_script('jquery-input-number', THEME_URI.'/libs/jquery-input-number/jquery-input-number.js', array('jquery'), '', false);

			wp_enqueue_style('admin-product', THEME_URI.'/assets/css/admin-product.css', [], '');
			wp_enqueue_script('admin-product', THEME_URI.'/assets/js/admin-product.js', array('jquery'), '');
		}

	}
}