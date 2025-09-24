<?php
namespace Nha88\Admin;

class Main {
	use \Nha88\Singleton;

	protected function __construct() {
		$this->includes();
		$this->hooks_page();
		$this->hooks_product();
		$this->hooks_product_order();
		$this->hooks_product_price();
	}

	private function hooks_product_order() {
		if(is_admin()) {
			add_action( 'admin_enqueue_scripts', [ '\Nha88\Admin\Product_Order', 'enqueue_scripts' ] );
			add_action( 'manage_product_order_posts_custom_column', [ '\Nha88\Admin\Product_Order', 'custom_columns_value' ], 2, 2 );
			add_filter( 'manage_product_order_posts_columns', [ '\Nha88\Admin\Product_Order', 'add_custom_columns_header' ] );
			add_action( 'add_meta_boxes', ['\Nha88\Admin\Product_Order', 'meta_boxes'] );

			add_filter( 'quick_edit_enabled_for_post_type', ['\Nha88\Admin\Product_Order', 'quick_edit_enabled_for_post_type'], 10, 2 );

			add_action('wp_ajax_send_purchase', ['\Nha88\Admin\Product_Order', 'ajax_send_purchase']);
			add_action('wp_ajax_cancel_purchase', ['\Nha88\Admin\Product_Order', 'ajax_cancel_purchase']);
		}
	}

	private function hooks_product_price() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product_Price', 'enqueue_scripts'] );
		add_action( 'created_product_price', ['\Nha88\Admin\Product_Price', 'auto_slug'] );
		add_action( 'manage_edit-product_price_columns', ['\Nha88\Admin\Product_Price', 'manage_edit_column_header'] );
		add_action( 'manage_product_price_custom_column', ['\Nha88\Admin\Product_Price', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_product_cat() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product_Cat', 'enqueue_scripts'] );
		add_action( 'created_product_cat', ['\Nha88\Admin\Product_Cat', 'auto_slug'] );
		add_action( 'manage_edit-product_cat_columns', ['\Nha88\Admin\Product_Cat', 'manage_edit_column_header'] );
		add_action( 'manage_product_cat_custom_column', ['\Nha88\Admin\Product_Cat', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_product() {
		if(is_admin()) {
			add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product', 'enqueue_scripts'] );
			add_action( 'save_post_product', ['\Nha88\Admin\Product', 'save_product'], 15, 3 );
		}
	}

	private function hooks_page() {
		//add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Page', 'enqueue_scripts'] );
		add_action( 'save_post_page', ['\Nha88\Admin\Page', 'save_page'], 15, 3 );
		add_filter( 'manage_product_posts_columns', ['\Nha88\Admin\Product', 'custom_columns_header'] );
		add_action( 'manage_product_posts_custom_column', ['\Nha88\Admin\Product', 'custom_columns_value'], 2, 2 );
	}

	// private function hooks_product() {
	// 	add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product', 'enqueue_scripts'] );
	// 	add_action( 'save_post_product', ['\Nha88\Admin\Product', 'save_product'], 15, 3 );
	// 	add_filter( 'disable_months_dropdown', ['\Nha88\Admin\Product', 'disable_months_dropdown'], 10, 2 );
	// 	add_filter( 'manage_product_posts_columns', ['\Nha88\Admin\Product', 'custom_columns_header'] );
	// 	add_action( 'manage_product_posts_custom_column', ['\Nha88\Admin\Product', 'custom_columns_value'], 2, 2 );
	// 	add_action( 'wp_ajax_change_product_url_data_file', ['\Nha88\Admin\Product', 'ajax_change_product_url_data_file'] );
	// 	add_action( 'wp_ajax_change_product_has_file', ['\Nha88\Admin\Product', 'ajax_change_product_has_file'] );
	// 	add_action( 'wp_ajax_change_product_combo', ['\Nha88\Admin\Product', 'ajax_change_product_combo'] );
	// 	add_action( 'wp_ajax_change_product_dimension', ['\Nha88\Admin\Product', 'ajax_change_product_dimension'] );
	// 	add_action( 'restrict_manage_posts', ['\Nha88\Admin\Product', 'filter_by_taxonomy'] );
	// 	add_filter( 'parse_query', ['\Nha88\Admin\Product', 'taxonomy_parse_filter'] );
	// 	add_action( 'add_meta_boxes', ['\Nha88\Admin\Product', 'meta_boxes'] );
	// 	add_action( 'edit_form_after_title', ['\Nha88\Admin\Product', 'display_id'] );
	// }

	private function includes() {
		// if( ! class_exists( 'WP_List_Table' ) ) {
		// 	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		// }

		include_once THEME_DIR.'/inc/admin/class-admin-page.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product_order.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product_cat.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product_price.php';
		//include_once THEME_DIR.'/inc/admin/class-admin-google-drive.php';
	}

}
Main::get_instance();