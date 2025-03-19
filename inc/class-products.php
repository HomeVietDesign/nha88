<?php
namespace Nha88;

class Products {

	public static function id_search($pieces, $wp_query) {
		if($wp_query->is_main_query()) {
			if( $wp_query->get('post_type', '')=='product' && $wp_query->get('s')!='' ) {
				global $wpdb;

				$keywords        = explode(' ', $wp_query->get('s'));
				$escaped_percent = $wpdb->placeholder_escape();
				$query           = "";

				if($keywords) {
					$query = "(";
					foreach ($keywords as $key => $word) {
						if($key>0) {
							$query .= " OR {$wpdb->posts}.ID = '{$word}' OR {$wpdb->posts}.post_name LIKE '{$escaped_percent}{$word}{$escaped_percent}'";
							
						} else {
							$query .= "{$wpdb->posts}.ID = '{$word}' OR {$wpdb->posts}.post_name LIKE '{$escaped_percent}{$word}{$escaped_percent}'";
						}
						
					}

					$query .= ") OR ";
				}

				if ( ! empty( $query ) ) { // append necessary WHERE and JOIN options.
					$pieces['where'] = str_replace( "((({$wpdb->posts}.post_title LIKE '{$escaped_percent}", "(({$query} ({$wpdb->posts}.post_title LIKE '{$escaped_percent}", $pieces['where'] );

					$pieces['orderby'] = "{$wpdb->posts}.post_name DESC,".$pieces['orderby'];
				}

				//debug_log($pieces);
			}
		}

		return $pieces;
	}

	public static function edit_post_link($link) {
		$link = str_replace('<a', '<a target="_blank"', $link);
		$link = str_replace('post-edit-link', 'post-edit-link position-absolute end-0 bottom-0 z-3 py-1 px-2 text-white', $link);

		return $link;
	}

	public static function the_title($title, $post_id) {
		$_post = get_post( $post_id );
		if($_post && $_post->post_type=='product') {
			$title = $_post->ID;
		}

		return $title;
	}
}