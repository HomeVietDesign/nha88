<?php
namespace Nha88\Admin;

class Main {
	use \Nha88\Singleton;

	protected function __construct() {
		$this->includes();
		$this->hooks_page();
		$this->hooks_product();
		$this->hooks_product_cat();
		$this->hooks_product_price();
		$this->hooks_product_file_type();
		//$this->hooks_product_page();
		$this->hooks_customer();
		$this->hooks_purchase();
		//$this->hooks_google_drive();
	}

	private function hooks_page() {
		//add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Page', 'enqueue_scripts'] );
		add_action( 'save_post_page', ['\Nha88\Admin\Page', 'save_page'], 15, 3 );
		//add_filter( 'the_title', ['\Nha88\Admin\Page', 'product_title'], 10, 2 );
	}

	private function hooks_product() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product', 'enqueue_scripts'] );
		add_action( 'save_post_product', ['\Nha88\Admin\Product', 'save_product'], 15, 3 );
		add_filter( 'disable_months_dropdown', ['\Nha88\Admin\Product', 'disable_months_dropdown'], 10, 2 );
		add_filter( 'manage_product_posts_columns', ['\Nha88\Admin\Product', 'custom_columns_header'] );
		add_action( 'manage_product_posts_custom_column', ['\Nha88\Admin\Product', 'custom_columns_value'], 2, 2 );
		add_action( 'wp_ajax_change_product_url_data_file', ['\Nha88\Admin\Product', 'ajax_change_product_url_data_file'] );
		add_action( 'wp_ajax_change_product_has_file', ['\Nha88\Admin\Product', 'ajax_change_product_has_file'] );
		add_action( 'wp_ajax_change_product_combo', ['\Nha88\Admin\Product', 'ajax_change_product_combo'] );
		add_action( 'wp_ajax_change_product_dimension', ['\Nha88\Admin\Product', 'ajax_change_product_dimension'] );
		add_action( 'restrict_manage_posts', ['\Nha88\Admin\Product', 'filter_by_taxonomy'] );
		add_filter( 'parse_query', ['\Nha88\Admin\Product', 'taxonomy_parse_filter'] );
	}

	private function hooks_product_price() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product_Price', 'enqueue_scripts'] );
		add_action( 'created_product_price', ['\Nha88\Admin\Product_Price', 'auto_slug'] );
		add_action( 'manage_edit-product_price_columns', ['\Nha88\Admin\Product_Price', 'manage_edit_column_header'] );
		add_action( 'manage_product_price_custom_column', ['\Nha88\Admin\Product_Price', 'manage_edit_columns_value'], 15, 3 );

		add_filter( 'manage_edit-product_price_sortable_columns', ['\Nha88\Admin\Product_Price', 'register_column_for_sortable'] );

	}

	private function hooks_product_file_type() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product_File_Type', 'enqueue_scripts'] );
		add_action( 'created_product_file_type', ['\Nha88\Admin\Product_File_Type', 'auto_slug'] );
		add_action( 'manage_edit-product_file_type_columns', ['\Nha88\Admin\Product_File_Type', 'manage_edit_column_header'] );
		//add_action( 'manage_product_file_type_custom_column', ['\Nha88\Admin\Product_File_Type', 'manage_edit_columns_value'], 15, 3 );

	}

	private function hooks_product_cat() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product_Cat', 'enqueue_scripts'] );
		add_action( 'created_product_cat', ['\Nha88\Admin\Product_Cat', 'auto_slug'] );
		add_action( 'manage_edit-product_cat_columns', ['\Nha88\Admin\Product_Cat', 'manage_edit_column_header'] );
		add_action( 'manage_product_cat_custom_column', ['\Nha88\Admin\Product_Cat', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_product_page() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Product_Page', 'enqueue_scripts'] );
		add_filter( 'disable_months_dropdown', ['\Nha88\Admin\Product_Page', 'disable_months_dropdown'], 10, 2 );
		add_filter( 'manage_product_page_posts_columns', ['\Nha88\Admin\Product_Page', 'custom_columns_header'] );
		add_action( 'manage_product_page_posts_custom_column', ['\Nha88\Admin\Product_Page', 'custom_columns_value'], 2, 2 );
		add_action( 'save_post_product_page', ['\Nha88\Admin\Product_Page', 'save_product_page'], 10, 3 );
	}

	private function hooks_customer() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Customer', 'enqueue_scripts'] );
		add_action( 'created_customer', ['\Nha88\Admin\Customer', 'auto_slug'] );
		add_action( 'manage_edit-customer_columns', ['\Nha88\Admin\Customer', 'manage_edit_column_header'] );
	}

	/*
	private function hooks_purchase() {
		add_action( 'admin_menu', ['\Nha88\Admin\Purchase', 'admin_menu'] );
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Purchase', 'enqueue_scripts'] );
		add_action( 'wp_ajax_transaction_approval', ['\Nha88\Admin\Purchase', 'ajax_transaction_approval'] );
		add_action( 'wp_ajax_transaction_cancel', ['\Nha88\Admin\Purchase', 'ajax_transaction_cancel'] );

		// lưu các trường tùy biến cho trang quản trị (screen là đối tượng trang quản trị)
		add_filter('set_screen_option_'.\Nha88\Admin\Purchase::ADMIN_SLUG.'_per_page', ['\Nha88\Admin\Purchase', 'set_screen_option'], 10, 3 );
	}
	*/

	private function hooks_google_drive() {
		//add_action( 'admin_menu', ['\Nha88\Admin\Google_Drive', 'admin_menu'] );
		// add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Google_Drive', 'enqueue_scripts'] );
		//add_action( 'wp_ajax_google_drive_auth', ['\Nha88\Admin\Google_Drive', 'ajax_google_drive_auth'] );

	}

	private function hooks_purchase() {
		add_action( 'admin_enqueue_scripts', ['\Nha88\Admin\Purchase', 'enqueue_scripts'] );
		//add_action( 'save_post_purchase', ['\Nha88\Admin\Purchase', 'save_purchase'], 15, 3 );
		//add_filter( 'disable_months_dropdown', ['\Nha88\Admin\Purchase', 'disable_months_dropdown'], 10, 2 );
		add_filter( 'quick_edit_enabled_for_post_type', ['\Nha88\Admin\Purchase', 'quick_edit_disable'], 10, 2 );
		add_filter( 'post_row_actions', ['\Nha88\Admin\Purchase', 'purchase_row_actions'], 10, 2 );
		add_filter( 'the_title', ['\Nha88\Admin\Purchase', 'purchase_title'], 10, 2 );
		add_filter( 'display_post_states', ['\Nha88\Admin\Purchase', 'purchase_display_post_states'], 10, 2 );
		add_filter( 'manage_purchase_posts_columns', ['\Nha88\Admin\Purchase', 'custom_columns_header'] );
		add_action( 'manage_purchase_posts_custom_column', ['\Nha88\Admin\Purchase', 'custom_columns_value'], 2, 2 );
		add_action( 'wp_ajax_transaction_approval', ['\Nha88\Admin\Purchase', 'ajax_transaction_approval'] );
		add_action( 'wp_ajax_transaction_cancel', ['\Nha88\Admin\Purchase', 'ajax_transaction_cancel'] );

		add_action( 'before_delete_post', ['\Nha88\Admin\Purchase', 'purchase_before_delete'], 10, 2 );

		add_action( 'load-edit.php', ['\Nha88\Admin\Purchase', 'purchase_before_load'], 10 );
		add_filter( 'views_edit-purchase', ['\Nha88\Admin\Purchase', 'purchase_google_drive_status'], 10 );

		//add_action( 'pre_get_posts', ['\Nha88\Admin\Purchase', 'default_sort'] );
		add_action( 'wp_ajax_purchase_urls', ['\Nha88\Admin\Purchase', 'ajax_purchase_urls'] );

		add_action( 'add_meta_boxes', ['\Nha88\Admin\Purchase', 'meta_boxes'] );
		add_action( 'post_submitbox_misc_actions', ['\Nha88\Admin\Purchase', 'custom_misc_actions'] );
	}

	private function includes() {
		if( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		include_once THEME_DIR.'/inc/admin/class-admin-page.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product_cat.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product_price.php';
		include_once THEME_DIR.'/inc/admin/class-admin-product_file_type.php';
		//include_once THEME_DIR.'/inc/admin/class-admin-product_page.php';
		include_once THEME_DIR.'/inc/admin/class-admin-customer.php';
		include_once THEME_DIR.'/inc/admin/class-admin-purchase.php';
		//include_once THEME_DIR.'/inc/admin/class-admin-google-drive.php';
	}

}
Main::get_instance();