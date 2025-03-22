<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	fw()->theme->get_options( 'general-settings' ),
	fw()->theme->get_options( 'google-settings' ),
	fw()->theme->get_options( 'custom-script-settings' ),
	fw()->theme->get_options( 'product-settings' ),
	//fw()->theme->get_options( 'purchase-settings' ),
);
