<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
$id = absint( $atts['content_id'][0] );

if($id) {
   
    $content_post = get_post( $id );
    if($content_post instanceof \WP_Post && $content_post->post_status=='publish') {
        $content = $content_post->post_content;
        if ( function_exists('fw_ext_page_builder_get_post_content') ) {
            $content = fw_ext_page_builder_get_post_content($content_post);
        }
        echo '<div class="content-builder">';
        echo wp_get_the_content( $content );
        echo '</div>';

    }
}
