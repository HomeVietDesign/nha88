<?php
define('THEME_DIR', get_stylesheet_directory());
define('THEME_URI', get_stylesheet_directory_uri());

require_once THEME_DIR.'/vendor/autoload.php';

require_once THEME_DIR.'/inc/trait-singleton.php';
require_once THEME_DIR.'/inc/unyson/class-unyson.php';

if(class_exists('\\FacebookPixelPlugin\\FacebookForWordpress')) {
	require_once THEME_DIR.'/inc/official-facebook-pixel/class-official-facebook-pixel.php';
}

require_once THEME_DIR.'/inc/global-functions.php';
require_once THEME_DIR.'/inc/class-theme.php';

\Nha88\Theme::get_instance();