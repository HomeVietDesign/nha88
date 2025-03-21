<?php
/**
 * Theme class
 * 
 */
namespace Nha88;

class Theme {
	use \Nha88\Singleton;

	protected function __construct() {
		add_action('after_switch_theme', [$this, 'theme_activation']);
		add_action('switch_theme', [$this, 'theme_deactivation']);
		//add_action('wp_loaded', [$this, 'init_account'], 0);
		add_action('fw_init', [$this, 'setup_theme'] );
		add_action('fw_init', [$this, 'includes'] );
		add_action('fw_init', [$this, 'hooks'] );
	}

	public static function setting() {
		return \Nha88\Setting::get_instance();
	}

	public function hooks() {
		$this->hooks_custom_types();
		$this->hooks_assets();
		$this->hooks_head();
		$this->hooks_header();
		$this->hooks_body();
		$this->hooks_footer();
		$this->hooks_widget();
		$this->hooks_product();
		$this->hooks_ajax();
		$this->hooks_background_process();
	}

	private function hooks_background_process() {
		add_action('purchase_process', ['\Nha88\Background_Process', 'purchase_process']);
	}

	private function hooks_ajax() {
		add_action( 'wp_ajax_logout_post_password', ['\Nha88\Ajax', 'logout_post_password'] );
		add_action( 'wp_ajax_nopriv_logout_post_password', ['\Nha88\Ajax', 'logout_post_password'] );
	}

	private function hooks_custom_types() {
		if ( is_admin() ) {
			add_action( 'admin_menu', ['\Nha88\Custom_Types', '_admin_action_rename_post_menu' ], 99 );
		}
		add_action( 'init', ['\Nha88\Custom_Types', '_theme_action_change_post_labels'], 9999 );
		add_action( 'init', ['\Nha88\Custom_Types', '_theme_action_register_taxonomy'], 10 );
		add_action( 'init', ['\Nha88\Custom_Types', '_theme_action_register_custom_type'], 10 );
		//add_action( 'init', ['\Nha88\Custom_Types', '_theme_action_register_post_status'], 10 );

		add_action( 'the_post', ['\Nha88\Custom_Types', '_setup_loop_custom_type'], 10 );

		//add_action( 'terms_clauses', ['\Nha88\Custom_Types', '_setup_term_default_sort'], 10, 3 );
	}

	private function hooks_assets() {
		add_action('wp_enqueue_scripts', ['\Nha88\Assets', 'enqueue_styles'], 50);
		add_action('wp_enqueue_scripts', ['\Nha88\Assets', 'enqueue_scripts'], 50);
		add_action('wp_enqueue_scripts', ['\Nha88\Assets', 'recaptcha_script'], 21);
	}

	private function hooks_head() {
		add_action('wp_head', ['\Nha88\Template_Tags', 'head_scripts'], 50);
		add_action('wp_head', ['\Nha88\Template_Tags', 'head_youtube_scripts'], 10);
		add_action('wp_head', ['\Nha88\Template_Tags', 'noindex'], 10);
	}

	private function hooks_header() {
		add_action('template_redirect', ['\Nha88\Template_Tags', 'header_html']);
	}

	private function hooks_body() {
		add_action('wp_body_open', ['\Nha88\Template_Tags', 'body_open_custom_code'], 5);
		add_action('wp_body_open', ['\Nha88\Template_Tags', 'site_body_open'], 30);
		add_action('wp_footer', ['\Nha88\Template_Tags', 'site_body_close'], 5);
	}

	private function hooks_footer() {
		add_action('template_redirect', ['\Nha88\Template_Tags', 'display_footer_html']);
		add_action('wp_footer', ['\Nha88\Template_Tags', 'footer_custom_scripts'], 100);
		add_filter('the_password_form', ['\Nha88\Template_Tags', 'the_password_form'], 10, 2);
		
	}

	private function hooks_widget() {
		add_action('widgets_init', ['\Nha88\Widgets', 'register_sidebars']);
	}

	private function hooks_product() {
		add_filter( 'the_title', ['\Nha88\Products', 'the_title'], 10, 2 );
		add_filter( 'edit_post_link', ['\Nha88\Products', 'edit_post_link'] );
		add_filter( 'posts_clauses', ['\Nha88\Products', 'id_search'], 10, 2 );
	}

	public function includes() {

		include_once THEME_DIR.'/inc/class-custom-types.php';

		if(is_admin()) {
			include_once THEME_DIR.'/inc/admin/class-admin.php';
		}

		include_once THEME_DIR.'/inc/class-setting.php';
		include_once THEME_DIR.'/inc/class-assets.php';
		include_once THEME_DIR.'/inc/class-post.php';
		include_once THEME_DIR.'/inc/class-term.php';
		//include_once THEME_DIR.'/inc/class-account.php';
		include_once THEME_DIR.'/inc/class-product.php';
		include_once THEME_DIR.'/inc/class-product-price.php';
		include_once THEME_DIR.'/inc/class-template-tags.php';
		include_once THEME_DIR.'/inc/class-walker-primary-menu.php';
		include_once THEME_DIR.'/inc/class-walker-secondary-menu.php';
		include_once THEME_DIR.'/inc/class-widgets.php';
		include_once THEME_DIR.'/inc/class-products.php';
		include_once THEME_DIR.'/inc/class-ajax.php';
		include_once THEME_DIR.'/inc/class-purchase.php';
		include_once THEME_DIR.'/inc/class-background-process.php';
		//include_once THEME_DIR.'/inc/class-purchase-process.php';
		
		$GLOBALS['theme_setting'] = \Nha88\Setting::get_instance();
	}

	public function setup_theme() {
		// không dùng block editor
		add_filter('use_widgets_block_editor', '__return_false');
		add_filter('use_block_editor_for_post_type', '__return_false', 10);

		add_filter('image_size_names_choose', [$this, 'image_sizes_choose']);

		add_theme_support( 'title-tag' );
		
		add_theme_support( 'post-thumbnails' );

		remove_image_size( '1536x1536' );
		remove_image_size( '2048x2048' );

		register_nav_menus(
			array(
				'primary' => 'Menu chính',
				'secondary' => 'Menu phụ',
			)
		);

		add_theme_support('custom-background');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		// Add support for Block Styles.
		//add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		add_filter('get_the_archive_title_prefix', '__return_empty_string');

	}

	public function image_sizes_choose($size_names) {
		$new_sizes = array(
			'medium_large' => 'Medium Large',
		);
		return array_merge( $size_names, $new_sizes );
	}

	/*
	public function init_account() {
		global $account;
		
		if(isset($_COOKIE[ 'wp-postpass_' . COOKIEHASH ])) {
			$accounts = get_terms(['taxonomy'=>'customer', 'hide_empty'=>false]);
			if(is_array($accounts) && !empty($accounts)) {
			    require_once ABSPATH . WPINC . '/class-phpass.php';
			    $hasher = new \PasswordHash( 8, true );
			    $hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
			    if ( str_starts_with( $hash, '$P$B' ) ) {
			        foreach ($accounts as $key => $value) {
			            if($hasher->CheckPassword( $value->name, $hash )) {
			                $account = new \Nha88\Account($value);
			                break;
			            }
			        }
			    }
			}
		}
	}
	*/

	public function theme_activation() {
		/*
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $table_prefix, $wpdb;

		$purchase_table = $table_prefix . 'purchase';
		$charset_collate = $wpdb->get_charset_collate();

		if( $wpdb->get_var( "SHOW TABLES LIKE '{$purchase_table}'" ) != $purchase_table ) {
			$sql = "CREATE TABLE {$purchase_table} (
				purchase_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				customer_id bigint(20) UNSIGNED NOT NULL,
				item_id bigint(20) NULL DEFAULT NULL,
				item_value int(10) UNSIGNED NOT NULL DEFAULT '0',
				bank_trans_file text NULL,
				status tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
				utm_source varchar(20) NULL,
				utm_medium varchar(20) NULL,
				url text NULL,
				referrer text NULL,
				user_agent text NULL,
				note text NULL,
				date_created datetime NULL DEFAULT NULL,
				date_created_gmt datetime NULL DEFAULT NULL,
				PRIMARY KEY (purchase_id),
				KEY (customer_id),
				KEY (item_id)
			) {$charset_collate};";

			dbDelta( $sql );
		}
		*/
	}

	public function theme_deactivation() {
		
	}
}