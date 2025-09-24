<?php
namespace Nha88;

class Background_Process {

	public static function add_product_order($events) {
		//debug_log($events);
		$order_id = wp_insert_post([
            'post_type' => 'product_order',
            'post_title' => $events['order_data']['phone'],
            'post_name' => 'order-'.current_time( 'U' ),
            'post_status' => 'publish',
            'post_author' => 0
        ]);

        if( !($order_id instanceof \WP_Error) && $order_id>0 ) {
            update_post_meta($order_id, '_events', $events);
            update_post_meta($order_id, '_purchase', 1);
        }
	}

}