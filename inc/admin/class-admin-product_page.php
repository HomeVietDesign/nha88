<?php
namespace Nha88\Admin;

class Product_Page {

	public static function save_product_page($post_id, $post, $update) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/*
		$term_id = absint(get_post_meta( $post_id, '_product_cat', true ));
		$term = get_term_by( 'term_id', $term_id, 'product_cat' );
		if($term) {
			global $wpdb;
			
			if($term->name!=$post->post_title) {
				$wpdb->update( $wpdb->terms, ['name' => $post->post_title], ['term_id' => $term_id] );
			}

			$parent = ($post->post_parent==0)?0:absint(get_post_meta($post->post_parent, '_product_cat', true));
			
			$tt_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id = %d", $term->taxonomy, $term_id ) );
		
			$wpdb->update( $wpdb->term_taxonomy, ['parent' => $parent], ['term_taxonomy_id' => $tt_id] );

			wp_cache_delete( $term_id, 'terms' );
		}
		*/
	}

	public static function custom_columns_value($column, $post_id) {
		$nonce = wp_create_nonce('quick_edit_'.$post_id);

		switch ($column) {
			/*
			case 'product_cat':
				$product_cat_id = absint( get_post_meta( $post_id, '_product_cat', true ) );
				echo esc_html(get_term_field( 'name', $product_cat_id, 'product_cat' ));
				break;
			case 'menu_order':
				$post = get_post($post_id);
				echo $post->menu_order;
				break;
			case 'page_id':
				echo $post_id;
				break;
			*/
		}
	}

	public static function custom_columns_header($columns) {

		//$columns['page_id'] = 'ID';
		//$columns['product_cat'] = 'Cấp sản phẩm';
		//$columns['menu_order'] = 'STT';

		return $columns;

	}

	public static function disable_months_dropdown($disabled, $post_type) {
		if($post_type=='product_page') {
			$disabled = true;
		}
		return $disabled;
	}

	public static function enqueue_scripts($hook) {
		global $post_type;
		if(($hook=='edit.php' || $hook=='post.php') && $post_type=='product_page') {
			wp_enqueue_style( 'admin-product_page', THEME_URI.'/assets/css/admin-product_page.css', [], '' );
			wp_enqueue_script('admin-product_page', THEME_URI.'/assets/js/admin-product_page.js', array('jquery'), '');
		}
	}
}