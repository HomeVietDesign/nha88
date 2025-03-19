<?php
namespace Nha88;

class Official_Facebook_Pixel {
	use \Nha88\Singleton;

	private function __construct() {
		add_action( 'init', [$this, 'remove_ofp_wpcf7_integration'], 10 );
		
		include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-purchase.php';
		include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-wpcf7.php';
	}

	public function remove_ofp_wpcf7_integration() {
		// global $wp_filter;
		// debug_log($wp_filter);

		remove_action(
            'wpcf7_submit',
            array( 'FacebookPixelPlugin\Integration\FacebookWordpressContactForm7', 'trackServerEvent' ),
            10,
            2
        );

        remove_action(
            'wp_footer',
            array( 'FacebookPixelPlugin\Integration\FacebookWordpressContactForm7', 'injectMailSentListener' ),
            10,
            2
        );
	}

}
Official_Facebook_Pixel::get_instance();
