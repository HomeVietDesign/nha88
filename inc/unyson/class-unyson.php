<?php
namespace Nha88;

class Unyson {
	use \Nha88\Singleton;

	protected function __construct() {
		add_action('fw_option_types_init', [$this, '_action_theme_include_custom_option_types']);
		add_action('after_setup_theme', [$this, 'on_after_setup_theme']);
		add_action('template_redirect', [$this, 'required_unyson'], -99);
	}

	public function required_unyson() {
		if ( !defined('FW') ) {
			$this->admin_notice_missing_main_plugin();
			exit;
		}
	}

	public function on_after_setup_theme() {
		if ($this->is_compatible()) {
			add_action('wp_loaded', [$this, 'remove_try_brizy_notice']);
			add_filter('fw_use_sessions', '__return_false');
		}
	}

	public function _action_theme_include_custom_option_types() {
		require_once THEME_DIR.'/framework-customizations/option-types/code-editor/class-fw-option-type-code-editor.php';
		require_once THEME_DIR.'/framework-customizations/option-types/numeric/class-fw-option-type-numeric.php';
	}

	public function remove_try_brizy_notice() {
		remove_action('admin_notices', [fw()->theme, '_action_admin_notices']);
	}

	public function is_compatible() {
		// Check if Unyson installed and activated
		if ( !defined('FW') ) {
			add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
			return false;
		}

		return true;

	}

	public function admin_notice_missing_main_plugin() {

		$message = '"<strong>Unyson Test Extension</strong>" requires "<strong>Unyson</strong>" to be installed and activated.';

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

}
Unyson::get_instance();