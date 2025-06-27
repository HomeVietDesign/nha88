<?php
namespace Nha88;

class API {
	
	public static function rest_api_init(){
		register_rest_route('theme-api', '/detail_product', [
			'methods' => 'POST',
			'callback' => [__CLASS__, 'detail_product'],
			'permission_callback' => '__return_true',
		]);
	}

	public static function detail_product($request) {
		$data = json_decode($request->get_body(), true);

		$response = [
			'slider' => '',
			'info'=>''
		];

		$product = \Nha88\Product::get_instance($data['id']);

		if($product->id) {
			ob_start();
			if($product->get('images')) {
				foreach ($product->get('images') as $key => $value) {
					?>
					<div class="img-wrap"><?php echo wp_get_attachment_image( $value['attachment_id'], 'full' ); ?></div>
					<?php
				}
			}
			$response['slider'] = ob_get_clean();
			$response['info'] = wp_format_content($product->post->post_content);
		}

		return new \WP_REST_Response($response, 200);
	}
}
