<?php
namespace Nha88;

class Ads {
/*
sự kiện gửi wpcf7 là SubmitApplication (Gửi đơn đăng ký)
xác nhận sự kiện gửi wpcf7 là CompleteRegistration (Đăng ký)
*/
	public static function track_purchase($data) {
		global $theme_setting;
		$events = get_post_meta($data['id'], '_events', true);

		if($theme_setting->get('fb_pixel')!='' && $theme_setting->get('fb_pixel_at')!='') {
			$event_name = '';
			switch ($events['order_data']['type']) {
				case 'wpcf7':
					$event_name = 'CompleteRegistration';
					break;
				
				default:
					$event_name = 'Purchase';
					break;
			}
			self::fb_send_event($event_name, $events['event_data']['fb'], $theme_setting->get('fb_test_event_code'));
		}
		
		$datetime = get_the_date( 'Y-m-d H:i:s', $data['id'] );

		wp_update_post([
			'ID' => $data['id'],
			'post_status' => 'publish',
			'edit_date' => $datetime
		]);

		update_post_meta( $data['id'], '_purchase', 1 );
	}

	public static function track_wpcf7_submit( $form, $result ) {
		global $theme_setting;

		$submit_failed    = ('mail_sent' !== $result['status']);
		if ( $submit_failed ) {
			return;
		}

		$submission = \WPCF7_Submission::get_instance();
		
		$form_tags = $form->scan_form_tags();
        $form_data      = self::wpcf7_read_form_data( $form_tags );

		$events = [
			'order_data' => [
				'type' => 'wpcf7',
				'id' => $form->id(),
				'name' => $form_data['name'],
				'phone' => $form_data['phone'],
				'url' => $submission->get_meta('url'),
				'referrer' => base64_decode(isset($_COOKIE['_ref'])?$_COOKIE['_ref']:''),
				'user_agent' => get_user_agent(),
				'ip_address' => get_user_ip_address(),
			],
			'event_data' => [
				'fb' => [
					'event_name' => 'CompleteRegistration',
					// Lấy cookie _fbc và _fbp
					'phone' => $form_data['phone'],
					'fbc' => isset($_COOKIE['_fbc']) ? sanitize_text_field($_COOKIE['_fbc']) : '',
					'fbp' => isset($_COOKIE['_fbp']) ? sanitize_text_field($_COOKIE['_fbp']) : '',
					'user_agent' => get_user_agent(),
					'ip_address' => get_user_ip_address(),
					'url' => $submission->get_meta('url'),
				],
				'tt' => []
			]
		];


		if($theme_setting->get('fb_pixel')!='' && $theme_setting->get('fb_pixel_at')!='') {
			self::fb_send_event('CompleteRegistration', $events['event_data']['fb'], $theme_setting->get('fb_test_event_code'));
		}

		if(function_exists('as_enqueue_async_action')) {
			as_enqueue_async_action('add_product_order', [$events], 'order');
		}

	}

	public static function track_request_product($response) {
		global $theme_setting;

		if($response['code']==1) {
			$events = [
				'order_data' => [
					'type' => $response['data']['type'],
					'id' => $response['data']['id'],
					'name' => $response['data']['name'],
					'phone' => $response['data']['phone'],
					'url' => $response['data']['url'],
					'referrer' => base64_decode($response['data']['ref']),
					'user_agent' => get_user_agent(),
					'ip_address' => get_user_ip_address(),
				],
				'event_data' => [
					'fb' => [
						'event_name' => 'Purchase',
						// Lấy cookie _fbc và _fbp
						'phone' => $response['data']['phone'],
						'fbc' => isset($_COOKIE['_fbc']) ? sanitize_text_field($_COOKIE['_fbc']) : '',
						'fbp' => isset($_COOKIE['_fbp']) ? sanitize_text_field($_COOKIE['_fbp']) : '',
						'user_agent' => get_user_agent(),
						'ip_address' => get_user_ip_address(),
						'url' => $response['data']['url'],
					],
					'tt' => []
				]
			];


			if($theme_setting->get('fb_pixel')!='' && $theme_setting->get('fb_pixel_at')!='') {
				self::fb_send_event('Purchase', $events['event_data']['fb'], $theme_setting->get('fb_test_event_code'));
			}

			if(function_exists('as_enqueue_async_action')) {
				as_enqueue_async_action('add_product_order', [$events], 'order');
			}
		}

		return $response;
	}

	public static function fb_send_event($event_name, $event_data = [], $test_event_code='') {
		global $theme_setting;

		$event = [
			'event_name'       => $event_name,
			'event_time'       => time(),
			'action_source'    => 'website',
			'event_source_url' => $event_data['url'],
			'user_data' => [
				'ph' => ($event_data['phone']!='') ? [ hash('sha256', $event_data['phone']) ] : [],
				'client_user_agent' => $event_data['user_agent'],
				'client_ip_address' => $event_data['ip_address'],
				'fbc' => $event_data['fbc'],
				'fbp' => $event_data['fbp'],
			],
		];

		if ($event_name === 'Purchase' || $event_name === 'Subscribe' || $event_name === 'StartTrial') {
			$event['custom_data'] = [
				'currency' => 'VND',
				'value'    => 1000000, // fallback nếu không có value
			];
		}

		$payload = [ 'data' => [$event] ];

		if (!empty($test_event_code)) {
			$payload['test_event_code'] = $test_event_code;
		}

		$res = wp_remote_post(
			'https://graph.facebook.com/v20.0/'.$theme_setting->get('fb_pixel').'/events?access_token='.$theme_setting->get('fb_pixel_at'),
			[
				'method'  => 'POST',
				'body'    => wp_json_encode($payload),
				'headers' => ['Content-Type' => 'application/json'],
			]
		);

		//debug_log($res);

		return !is_wp_error($res);
	}

	public static function wpcf7_read_form_data( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return array();
        }

        $name      = self::wpcf7_get_name( $form_tags );

        return array(
            'name'      => self::wpcf7_get_name( $form_tags ),
            'email'      => self::wpcf7_get_email( $form_tags ),
            'phone'      => self::wpcf7_get_phone( $form_tags ),
        );
    }

    public static function wpcf7_get_phone( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return null;
        }

        foreach ( $form_tags as $tag ) {
            if ( 'tel' === $tag->basetype ) {
                return isset( $_POST[ $tag->name ] ) ? // phpcs:ignore WordPress.Security.NonceVerification.Missing
                phone_0284(sanitize_phone_number(
                    wp_unslash( $_POST[ $tag->name ] )) // phpcs:ignore WordPress.Security.NonceVerification.Missing
                ) : null;
            }
        }

        return null;
    }

    public static function wpcf7_get_email( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return null;
        }

        foreach ( $form_tags as $tag ) {
            if ( 'email' === $tag->basetype && isset( $_POST[ $tag->name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
                return sanitize_text_field( wp_unslash( $_POST[ $tag->name ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
            }
        }

        return null;
    }

	public static function wpcf7_get_name( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return null;
        }

        foreach ( $form_tags as $tag ) {
            if ( 'text' === $tag->basetype
            && strpos( strtolower( $tag->name ), 'your_name' ) !== false ) {
                return sanitize_text_field(
                        wp_unslash( $_POST[ $tag->name ] ?? null ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
                );
            }
        }

        return null;
    }
	
	public static function inject_ads_listener() {
		?>
		<script type='text/javascript'>
		document.addEventListener("DOMContentLoaded", function() {
			if(theme.is_user_logged_in=='0') {
				document.addEventListener( 'orderProduct', function( event ) {

				}, false );

	        } // user logged in
	    });
	    </script>
		<?php
	}

}
