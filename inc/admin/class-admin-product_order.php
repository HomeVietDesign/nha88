<?php
namespace Nha88\Admin;

class Product_Order {

	public static function ajax_cancel_purchase() {
		$response = [
			'code' => 0,
			'data' => ''
		];

		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;

		if(current_user_can('publish_posts') && check_ajax_referer( 'quick_edit_'.$id, 'nonce', false )) {
			$order = get_post($id);
			
			$purchase = absint(get_post_meta($order->ID, 'purchase', true));

			if($order && $purchase==0 && $order->post_status=='pending') {
				$datetime = get_the_date( 'Y-m-d H:i:s', $order );
				wp_update_post([
					'ID' => $order->ID,
					'post_status' => 'publish',
					'edit_date' => $datetime
				]);
				update_post_meta( $order->ID, '_purchase', 0 );
				$response['code'] = 1;
			}
		}
	
		wp_send_json($response);
		exit;
	}

	public static function ajax_send_purchase() {
		$response = [
			'code' => 0,
			'data' => ''
		];

		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;

		if(current_user_can('publish_posts') && check_ajax_referer( 'quick_edit_'.$id, 'nonce', false )) {
			$order = get_post($id);

			$purchase = absint(get_post_meta($order->ID, 'purchase', true));
			
			if($order && $purchase==0 && $order->post_status=='pending') {
				do_action('purchase', ['id'=>$order->ID]);
				$response['code'] = 1;
			}
		}
	
		wp_send_json($response);

		exit;
	}

	public static function meta_boxes() {

		remove_meta_box(
			'submitdiv',
			'product_order',
			'side'
		);

		add_meta_box(
            'order_data'     // Reusing just 'postexcerpt' doesn't work.
        ,   'Thông tin'    // Title
        ,   array ( __CLASS__, 'order_data' ) // Display function
        ,   'product_order'              // Screen, we use all screens with meta boxes.
        ,   'normal'          // Context
        ,   'core'            // Priority
        );

	}

	public static function quick_edit_enabled_for_post_type($quick_edit, $post_type) {

		if($post_type=='product_order') {
			$quick_edit = false;
		}

		return $quick_edit;
	}

	public static function order_data($post) {
		$events = get_post_meta($post->ID, '_events', true);
		//debug($events);
		?>
		<table class="product-order-info">
			<tr>
				<th>Số điện thoại</th>
				<td><?=esc_html($events['order_data']['phone'])?></td>
			</tr>
			<tr>
				<th>IP</th>
				<td><?=esc_html($events['order_data']['ip_address'])?></td>
			</tr>
			<tr>
				<th>Agent</th>
				<td><?=esc_html($events['order_data']['user_agent'])?></td>
			</tr>
			<tr>
				<th>URL</th>
				<td><?=esc_html($events['order_data']['url'])?></td>
			</tr>
			<tr>
				<th>URLs</th>
				<td>
				<?php
				//debug($order_data);
				if($events['order_data']['referrer']) {
					$referrer = explode(',', $events['order_data']['referrer']);
					foreach ($referrer as $key => $value) {
						echo '<p>'.esc_html(urldecode($value)).'</p>';
					}
				}
				?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * giá trị các cộng thông tin mởi rộng cho đối tượng(post)
	 */
	public static function custom_columns_value( $column, $post_id ) {
		$quick_edit_nonce = wp_create_nonce('quick_edit_'.$post_id);
		$post = get_post($post_id);

    	$events = get_post_meta($post_id, '_events', true);

    	$referrer = isset($events['order_data']['url'])?$events['order_data']['url']:'';
    	$referrer .= (isset($events['order_data']['referrer']) && ''!=$events['order_data']['referrer'])?','.$events['order_data']['referrer']:'';
    	$referrer = ($referrer!='')?urldecode($referrer):'';
    	

		switch ($column) {
			case 'ID':
				echo esc_html($post_id);
				break;

			case 'image':
				//echo esc_html($post_id);
				break;

			case 'name':
				if(isset($events['order_data']['name'])) {
					?><div><?=esc_html($events['order_data']['name'])?></div><?php
				}
				break;

			case 'source':

				if(strpos($referrer, 'facebook')!==false || strpos($referrer, 'fbclid')!==false) {
					echo 'Facebook';
				} elseif (strpos($referrer, 'google')!==false || strpos($referrer, 'gclid')!==false) {
					echo 'Google';
				} elseif (strpos($referrer, 'youtube')!==false) {
					echo 'Youtube';
				} elseif (strpos($referrer, 'zalo')!==false) {
					echo 'Zalo';
				} elseif (strpos($referrer, 'tiktok')!==false) {
					echo 'Tiktok';
				} else {
					echo '(Không xác định)';
				}
				break;

			case 'ads':
				if($referrer!='' && preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches) ) {
					echo esc_html(str_replace('+', ' ', $matches[1]));
				}
				break;

			case 'product':
				if(isset($events['order_data']['id'])) {
					echo '<a href="'.esc_url(get_permalink( $events['order_data']['id'] )).'" target="_blank">'.esc_html(get_the_title($events['order_data']['id'])).' ('.$events['order_data']['id'].')</a>';
				}
				break;

			case 'tasks':
				$purchase = absint(get_post_meta($post_id, '_purchase', true));
				?>
				<div class="product-order-tasks">
				<?php if($post->post_status=='pending' && $purchase==0) { ?>
					<button type="button" class="button button-primary send-purchase" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>">Duyệt đơn</button>
					<button type="button" class="button button-secondary cancel-purchase" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>">Hủy đơn</button>
				<?php } else if($post->post_status=='publish' && $purchase==0) { ?>
					<span class="canceled">Đã hủy</span>
				<?php } else if($post->post_status=='publish' && $purchase==1) { ?>
					<span class="purchased">Đã duyệt</span>
				<?php } ?>
				</div>
				<?php
				break;
			
		}
		
	}

	/**
	 * Tiêu đề các cột thông tin mở rộng cho đối tượng(post)
	 */
	public static function add_custom_columns_header( $columns ) {
		
		$cb = $columns['cb'];
		unset($columns['cb']);

		$title = $columns['title'];
		unset($columns['title']);

		if(isset($columns['thumbnail'])) {
			unset($columns['thumbnail']);
		}

		$new_columns = ['cb' => $cb, 'title' => $title];
		
		$new_columns['name'] = 'Tên';
		$new_columns['ID'] = 'ID';
		$new_columns['source'] = 'Nguồn';
		$new_columns['ads'] = 'Quảng cáo';
		$new_columns['product'] = 'Phần tử';
		$new_columns['tasks'] = 'Tác vụ';

		$columns = array_merge($new_columns, $columns);
		return $columns;

	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if($post_type=='product_order' && ($hook=='edit.php' || $hook=='post.php')) {
			//add_thickbox();
			wp_enqueue_style('edit-product-order', THEME_URI.'/assets/css/admin-product-order.css', array(), '');
			wp_enqueue_script('edit-product-order', THEME_URI.'/assets/js/admin-product-order.js', array('jquery'), '', false);
			
		}
	}

}