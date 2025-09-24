<?php
namespace Nha88;

class Custom_Types {

	public static function product_front_page_template($template) {
		global $post;
		if (is_front_page()) {
			if ($post && $post->post_type === 'product') {
				return locate_template("single-product.php");
			}
		}

		return $template;
	}

	public static function pre_get_posts($query) {
		if ( !is_admin() // Only target the front end
		//&& $query->is_front_page() 
		&& $query->is_main_query() // Only target the main query
		&& 'page' === get_option( 'show_on_front' ) // Only target the static front page
		) {

			$query->set( 'post_type', ['page', 'product'] );
		}
	}

	public static function wp_dropdown_pages_args($args) {
		global $pagenow;
		if(is_admin() && 'options-reading.php'==$pagenow) {
			$args['post_type'] = ['page', 'product'];
		}
		return $args;
	}

	public static function hide_tags_from_quick_edit($show_in_quick_edit, $taxonomy_name, $post_type) {

		if($taxonomy_name=='customer' && $post_type=='product') {
			$show_in_quick_edit = false;
		}

		return $show_in_quick_edit;
	}

	public static function _theme_action_register_post_status() {
		register_post_status( 'cancel', array(
			'label'                     => 'Đã hủy',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Đã hủy (%s)', 'Đã hủy (%s)' ),
		) );
	}

	public static function _theme_action_register_custom_type() {
		$labels = array(
			'name'               => 'Nội dung',
			'singular_name'      => 'Nội dung',
			'add_new'            => 'Thêm mới Nội dung',
			'add_new_item'       => 'Thêm mới Nội dung',
			'edit_item'          => 'Sửa Nội dung',
			'new_item'           => 'Nội dung mới',
			'view_item'          => 'Xem Nội dung',
			'search_items'       => 'Tìm Nội dung',
			'not_found'          => 'Không có Nội dung nào',
			'not_found_in_trash' => 'Không có Nội dung nào trong Thùng rác',
			'parent_item_colon'  => 'Nội dung cha:',
			'menu_name'          => 'Nội dung',
		);
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			//'description'         => 'description',
			//'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			//'menu_position'       => 5,
			'menu_icon'           => 'dashicons-media-text',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false, // ẩn bài viết ở front-end
			'exclude_from_search' => true, // loại khỏi kết quả tìm kiếm
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'editor',
				'revisions',
			),
		);
		register_post_type( 'content_builder', $args );

		$labels = array(
			'name'               => 'Hồ sơ bản vẽ',
			'singular_name'      => 'Hồ sơ bản vẽ',
			'add_new'            => 'Thêm mới Hồ sơ bản vẽ',
			'add_new_item'       => 'Thêm mới Hồ sơ bản vẽ',
			'edit_item'          => 'Sửa Hồ sơ bản vẽ',
			'new_item'           => 'Hồ sơ bản vẽ mới',
			'view_item'          => 'Xem Hồ sơ bản vẽ',
			'search_items'       => 'Tìm Hồ sơ bản vẽ',
			'not_found'          => 'Không có Hồ sơ bản vẽ nào',
			'not_found_in_trash' => 'Không có Hồ sơ bản vẽ nào trong Thùng rác',
			'parent_item_colon'  => 'Hồ sơ bản vẽ cha:',
			'menu_name'          => 'Hồ sơ bản vẽ',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			//'description'         => 'description',
			//'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			//'menu_position'       => 5,
			'menu_icon'           => 'dashicons-book',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true, // ẩn bài viết ở front-end
			'exclude_from_search' => false, // loại khỏi kết quả tìm kiếm
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => ['slug' => 'ho-so'],
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				//'page-attributes',
			),
		);
	
		register_post_type( 'product', $args );

		// product order
		$labels = array(
			'name'               => 'Đơn hàng',
			'singular_name'      => 'Đơn hàng',
			'add_new'            => 'Thêm mới Đơn hàng',
			'add_new_item'       => 'Thêm mới Đơn hàng',
			'edit_item'          => 'Sửa Đơn hàng',
			'new_item'           => 'Đơn hàng mới',
			'view_item'          => 'Xem Đơn hàng',
			'search_items'       => 'Tìm Đơn hàng',
			'not_found'          => 'Không có Đơn hàng nào',
			'not_found_in_trash' => 'Không có Đơn hàng nào trong Thùng rác',
			'parent_item_colon'  => 'Đơn hàng cha:',
			'menu_name'          => 'Đơn hàng',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			//'description'         => 'description',
			//'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			//'menu_position'       => 22,
			'menu_icon'           => 'dashicons-cart',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false, // ẩn bài viết ở front-end
			'exclude_from_search' => true, // loại khỏi kết quả tìm kiếm
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'map_meta_cap'     => true,
			'supports'            => array(
				'title',
				//'editor',
				//'author',
				//'thumbnail',
				//'excerpt',
				//'custom-fields',
				//'trackbacks',
				//'comments',
				// 'revisions',
				// 'page-attributes',
				//'post-formats',
			),
		);
	
		register_post_type( 'product_order', $args );
	}

	public static function _theme_action_register_taxonomy() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Danh mục',
			'singular_name'     => 'Danh mục',
			'search_items'      => 'Tìm Danh mục',
			'all_items'         => 'Tất cả Danh mục',
			'edit_item'         => 'Sửa Danh mục',
			'update_item'       => 'Cập nhật Danh mục',
			'add_new_item'      => 'Thêm Danh mục mới',
			'new_item_name'     => 'Danh mục mới',
			'menu_name'         => 'Danh mục',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'rewrite'           => ['slug'=>'danh-muc'],
			//'rewrite'           => false,
			'public' 			=> false,
			'show_in_nav_menus' => true,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'product_cat', 'product', $args ); // our new 'format' taxonomy

		$labels = array(
			'name'              => 'Giá hồ sơ',
			'singular_name'     => 'Giá hồ sơ',
			'search_items'      => 'Tìm Giá hồ sơ',
			'all_items'         => 'Tất cả Giá hồ sơ',
			'edit_item'         => 'Sửa Giá hồ sơ',
			'update_item'       => 'Cập nhật Giá hồ sơ',
			'add_new_item'      => 'Thêm Giá hồ sơ mới',
			'new_item_name'     => 'Giá hồ sơ mới',
			'menu_name'         => 'Giá hồ sơ',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'rewrite'           => false,
			'public' 			=> false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'product_price', 'product', $args );
	}

	public static function _theme_action_change_post_labels() {
		global $wp_post_types, $wp_taxonomies;
		
		/*
		// Someone has changed this post type, always check for that!
		if( isset($wp_post_types['post']) ) {
			$wp_post_types['post']->label = 'Bài viết';
			$wp_post_types['post']->labels->name               = 'Bài viết';
			$wp_post_types['post']->labels->singular_name      = 'Bài viết';
			$wp_post_types['post']->labels->add_new            = 'Thêm Bài viết';
			$wp_post_types['post']->labels->add_new_item       = 'Thêm mới Bài viết';
			$wp_post_types['post']->labels->all_items          = 'Tất cả Bài viết';
			$wp_post_types['post']->labels->edit_item          = 'Chỉnh sửa Bài viết';
			$wp_post_types['post']->labels->name_admin_bar     = 'Bài viết';
			$wp_post_types['post']->labels->menu_name          = 'Bài viết';
			$wp_post_types['post']->labels->new_item           = 'Bài viết mới';
			$wp_post_types['post']->labels->not_found          = 'Không có Bài viết nào';
			$wp_post_types['post']->labels->not_found_in_trash = 'Không có Bài viết nào';
			$wp_post_types['post']->labels->search_items       = 'Tìm Bài viết';
			$wp_post_types['post']->labels->view_item          = 'Xem Bài viết';
		}
		*/

		if( isset($wp_taxonomies['category']) ) {
			/*
			$wp_taxonomies['category']->label = 'Chuyên mục';
			$wp_taxonomies['category']->labels->name = 'Chuyên mục';
			$wp_taxonomies['category']->labels->singular_name = 'Chuyên mục';
			$wp_taxonomies['category']->labels->add_new = 'Thêm chuyên mục';
			$wp_taxonomies['category']->labels->add_new_item = 'Thêm chuyên mục';
			$wp_taxonomies['category']->labels->edit_item = 'Sửa chuyên mục';
			$wp_taxonomies['category']->labels->new_item = 'Chuyên mục';
			$wp_taxonomies['category']->labels->view_item = 'Xem chuyên mục';
			$wp_taxonomies['category']->labels->search_items = 'Tìm chuyên mục';
			$wp_taxonomies['category']->labels->not_found = 'Không có chuyên mục nào được tìm thấy';
			$wp_taxonomies['category']->labels->not_found_in_trash = 'Không có chuyên mục nào trong thùng rác';
			$wp_taxonomies['category']->labels->all_items = 'Tất cả chuyên mục';
			$wp_taxonomies['category']->labels->menu_name = 'Chuyên mục';
			$wp_taxonomies['category']->labels->name_admin_bar = 'Chuyên mục';
			*/
			// $wp_taxonomies['category']->public = false;
			// $wp_taxonomies['category']->show_ui = false;
			// $wp_taxonomies['category']->show_in_nav_menus = false;
			// $wp_taxonomies['category']->rewrite = false;
		}

		if( isset($wp_taxonomies['post_tag']) ) {
			// $wp_taxonomies['post_tag']->public = false;
			// $wp_taxonomies['post_tag']->show_ui = false;
			// $wp_taxonomies['post_tag']->show_in_nav_menus = false;
			// $wp_taxonomies['post_tag']->rewrite = false;
		}
	}

	public static function _admin_action_rename_post_menu() {
		global $menu, $submenu;

		remove_menu_page( 'edit-comments.php' ); // ẩn menu Comments
		remove_menu_page( 'edit.php' ); // ẩn menu Blog posts
		//remove_menu_page( 'fw-extensions' ); // ẩn menu Unyson
		remove_menu_page( 'separator1' );
	}

	public static function _setup_loop_custom_type($post) {
		global $product;
		$product = \Nha88\Product::get_instance($post->ID);
	}

	public static function _setup_term_default_sort($pieces, $taxonomies, $args) {
		
		if(isset($taxonomies[0]) && 'product_price' == $taxonomies[0] ) {

			$orderby = isset($_REQUEST['orderby']) ? trim(wp_unslash($_REQUEST['orderby'])) : 'name';
			$order   = isset($_REQUEST['order'])   ? trim(wp_unslash($_REQUEST['order']))   : 'ASC';

			if($orderby=='name') {
				$pieces['orderby'] = "ORDER BY name+0";
			}

			$pieces['order']   = $order;
		}

		return $pieces;
	}
}