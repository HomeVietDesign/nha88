<?php
namespace Nha88;

class Custom_Types {

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
			'menu_icon'           => 'dashicons-admin-post',
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
			'name'               => 'Sản phẩm',
			'singular_name'      => 'Sản phẩm',
			'add_new'            => 'Thêm mới Sản phẩm',
			'add_new_item'       => 'Thêm mới Sản phẩm',
			'edit_item'          => 'Sửa Sản phẩm',
			'new_item'           => 'Sản phẩm mới',
			'view_item'          => 'Xem Sản phẩm',
			'search_items'       => 'Tìm Sản phẩm',
			'not_found'          => 'Không có Sản phẩm nào',
			'not_found_in_trash' => 'Không có Sản phẩm nào trong Thùng rác',
			'parent_item_colon'  => 'Sản phẩm cha:',
			'menu_name'          => 'Sản phẩm',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			//'description'         => 'description',
			//'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			//'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false, // ẩn bài viết ở front-end
			'exclude_from_search' => false, // loại khỏi kết quả tìm kiếm
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				//'editor',
				//'thumbnail',
			),
		);
	
		register_post_type( 'product', $args );

		$labels = array(
			'name'               => 'Giao dịch',
			'singular_name'      => 'Giao dịch',
			'menu_name'          => 'Giao dịch',
			'all_items'          => 'Tất cả giao dịch',
			'view_item'          => 'Xem giao dịch',
			'add_new_item'       => 'Thêm giao dịch mới',
			'edit_item'          => 'Chỉnh sửa giao dịch',
		);
		$args = array(
			'labels'             => $labels,
			'public'             => false,    // Ẩn khỏi front-end, chỉ quản trị.
			'show_ui'            => true,     // Hiển thị trong admin.
			'supports'           => array(
				'title',
				'excerpt',
				//'thumbnail'
			), // Bạn có thể thêm các trường khác nếu cần.
			'menu_icon'          => 'dashicons-money-alt',
		);
		register_post_type( 'purchase', $args );
	}

	public static function _theme_action_register_taxonomy() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Chuyên mục',
			'singular_name'     => 'Chuyên mục',
			'search_items'      => 'Tìm Chuyên mục',
			'all_items'         => 'Tất cả Chuyên mục',
			'edit_item'         => 'Sửa Chuyên mục',
			'update_item'       => 'Cập nhật Chuyên mục',
			'add_new_item'      => 'Thêm Chuyên mục mới',
			'new_item_name'     => 'Chuyên mục mới',
			'menu_name'         => 'Chuyên mục',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => ['slug'=>'chuyen-muc'],
			//'rewrite'           => false,
			'public' 			=> true,
			'show_in_nav_menus' => true,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'product_cat', 'product', $args ); // our new 'format' taxonomy

		$labels = array(
			'name'              => 'Giá sản phẩm',
			'singular_name'     => 'Giá sản phẩm',
			'search_items'      => 'Tìm Giá sản phẩm',
			'all_items'         => 'Tất cả Giá sản phẩm',
			'edit_item'         => 'Sửa Giá sản phẩm',
			'update_item'       => 'Cập nhật Giá sản phẩm',
			'add_new_item'      => 'Thêm Giá sản phẩm mới',
			'new_item_name'     => 'Giá sản phẩm mới',
			'menu_name'         => 'Giá sản phẩm',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			//'rewrite'           => ['slug'=>'danh-muc'],
			'rewrite'           => false,
			'public' 			=> false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		// register_taxonomy( 'product_price', 'product', $args ); // our new 'format' taxonomy

		$labels = array(
			'name'              => 'Định dạng file',
			'singular_name'     => 'Định dạng file',
			'search_items'      => 'Tìm Định dạng file',
			'all_items'         => 'Tất cả Định dạng file',
			'edit_item'         => 'Sửa Định dạng file',
			'update_item'       => 'Cập nhật Định dạng file',
			'add_new_item'      => 'Thêm Định dạng file mới',
			'new_item_name'     => 'Định dạng file mới',
			'menu_name'         => 'Định dạng file',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			//'rewrite'           => ['slug'=>'danh-muc'],
			'rewrite'           => false,
			'public' 			=> false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'product_file_type', 'product', $args ); // our new 'format' taxonomy

		$labels = array(
			'name'              => 'Khách hàng',
			'singular_name'     => 'Khách hàng',
			'search_items'      => 'Tìm Khách hàng',
			'all_items'         => 'Tất cả Khách hàng',
			'edit_item'         => 'Sửa Khách hàng',
			'update_item'       => 'Cập nhật Khách hàng',
			'add_new_item'      => 'Thêm Khách hàng mới',
			'new_item_name'     => 'Khách hàng mới',
			'menu_name'         => 'Khách hàng',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => false,
			'public' 			=> false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'customer', 'product', $args );
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
			$wp_taxonomies['category']->public = false;
			$wp_taxonomies['category']->show_ui = false;
			$wp_taxonomies['category']->rewrite = false;
		}

		if( isset($wp_taxonomies['post_tag']) ) {
			$wp_taxonomies['post_tag']->public = false;
			$wp_taxonomies['post_tag']->show_ui = false;
			$wp_taxonomies['post_tag']->rewrite = false;
		}
	}

	public static function _admin_action_rename_post_menu() {
		global $menu, $submenu;

		if ( isset( $menu[5] ) ) {
			unset($menu[5]);
		}

		if ( isset( $submenu['edit.php'] ) ) {
			unset($submenu['edit.php']);
		}
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