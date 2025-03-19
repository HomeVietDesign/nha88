<?php
namespace Nha88\Admin;

class Purchase {

	public static function custom_misc_actions($post) {
		
		if($post->post_type=='purchase') {
			$purchase = \Nha88\Purchase::get_instance($post->ID);
			
			?>
			<div class="misc-pub-section misc-pub-date_created">
				<p>Mã đơn: <?php echo $purchase->id; ?></p>
				<p>Ngày tạo: <?php echo esc_html(get_the_date( 'd/m/Y H:i:s', $post )); ?></p>
				<p>Khách hàng: <?php echo esc_html( $purchase->get('email') ); ?></p>
				<p>Giá trị: <?php echo number_format( absint($purchase->get('value')),0,'.',',' ); ?> vnđ</p>
			</div>
			<?php
		}
	}

	public static function meta_boxes() {
		remove_meta_box(
			'postexcerpt' // ID
			,   'purchase'            // Screen, empty to support all post types
			,   'normal'      // Context
		);

		add_meta_box(
			'postexcerpt2'     // Reusing just 'postexcerpt' doesn't work.
			,   'Ghi chú'    // Title
			,   array ( __CLASS__, 'postexcerpt2' ) // Display function
			,   'purchase'              // Screen, we use all screens with meta boxes.
			,   'normal'          // Context
			,   'core'            // Priority
		);
	}

	public static function postexcerpt2( $post ) {
    ?>
        <label class="screen-reader-text" for="excerpt">Ghi chú</label>
        <?php
        // We use the default name, 'excerpt', so we don’t have to care about
        // saving, other filters etc.
        wp_editor(
            unescape( $post->post_excerpt ),
            'excerpt',
            array (
	            'editor_height' => 400,
	            'media_buttons' => false,
	            'teeny'         => true,
	            'tinymce'       => false
            )
        );
    }

	public static function ajax_purchase_urls() {
		$purchase_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		check_ajax_referer( 'purchase-urls-'.$purchase_id, 'nonce', true );

		$purchase = \Nha88\Purchase::get_instance($purchase_id);

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title></title>
			<style type="text/css">
				html, body {
					font-family: arial;
					font-size: 14px;
				}
				body div {
					padding: 5px 0;
					margin: 5px 0;
					border-bottom: rgba(0, 0, 0, .1) 1px solid;
				}
				a {
					color: #00f;
					text-decoration: none;
				}
			</style>
		</head>
		<body>
		<?php
		if($purchase->get_urls()) {
			foreach ($purchase->get_urls() as $url) {
				echo '<div><a href="'.esc_url($url).'">'.esc_html($url).'</a></div>';
			}
		}
		?>	
		</body>
		</html>
		<?php
		exit;
	}

	public static function default_sort($wp_query) {
		
	}

	public static function purchase_before_load() {
		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
		global $google_auth_url;

		if($post_type=='purchase') {

			$clientId = fw_get_db_settings_option('drive_key');
			$clientSecret = fw_get_db_settings_option('drive_secret');
			$redirect_uri = admin_url('edit.php?post_type=purchase');

			//debug_log($redirect_uri);

			$code_verifier = get_option( 'code_verifier', '' );

			$client = new \Google\Client();
			$client->setApplicationName("Nha88");
			$client->setAccessType('offline');
			$client->setClientId($clientId);
			$client->setClientSecret($clientSecret);
			$client->setRedirectUri( $redirect_uri );
			$client->addScope("https://www.googleapis.com/auth/drive");

			if (isset($_REQUEST['logout'])) {
				update_option('google_drive_token', '');
			}

			if ( isset($_GET['code']) ) {
				$token = $client->fetchAccessTokenWithAuthCode($_GET['code'], $code_verifier);
				
				if(isset($token['access_token'])) {

					$client->setAccessToken($token);

					// store in the session also
					update_option('google_drive_token', $token);

					// redirect back to the example
					header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
				}
			}

			$token = get_option('google_drive_token', '');

			// set the access token as part of the client
			if ( !empty($token) ) {

				$client->setAccessToken($token);

				if ($client->isAccessTokenExpired()) {
					//debug_log($token);
					update_option('google_drive_token', '');
					header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
				}

			} else {
				$code_verifier = $client->getOAuth2Service()->generateCodeVerifier();
				update_option('code_verifier', $code_verifier);

				$google_auth_url = $client->createAuthUrl();
				//header('Location: ' . $google_auth_url);
			}
		}
	}

	public static function purchase_google_drive_status($views) {
		//debug_log($views);
		global $google_auth_url;

		if($google_auth_url) {
			$views['google_drive'] = '<a style="vertical-align:middle;" href="'.esc_url($google_auth_url).'" class="button button-secondary">Kết nối Google Drive</a>';
		} else {
			$views['google_drive'] = '<a style="vertical-align:middle;" href="'.admin_url('edit.php?post_type=purchase&logout=1').'" class="button button-primary">Ngắt kết nối Google Drive</a>';
		}

		return $views;
	}

	public static function purchase_before_delete($post_id, $post) {
		if($post->post_type=='purchase') {
			// delete bank trans file
			$purchase = \Nha88\Purchase::get_instance($post_id);
			$purchase->unlink_bank_file();
		}
	}

	public static function ajax_transaction_cancel() {
		$response = [
			'code' => 0,
			'msg' => ''
		];
		$purchase_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		check_ajax_referer( 'edit-purchase-'.$purchase_id, 'nonce', true );
		
		$purchase = \Nha88\Purchase::get_instance($purchase_id);

		//debug_log($purchase);
		if($purchase->id>0) {
			
			// cập nhật trạng thái giao dịch
			$purchase->cancel();

			$response['code'] = 1;
			$response['msg'] = '<div style="font-weight:bold;color:#999;">Đã hủy</div>';
			ob_start();
			?>
			<p>GIAO DỊCH ĐÃ HỦY TẠI WEBSITE NHA88!</p>
			<p></p>
			<p style='font-weight:bold;'>Mã giao dich tham chiếu: <span style="color:#f00;"><?=esc_html($purchase->id)?></span></p>
			<p style='font-weight:bold;'>Mã sản phẩm tham chiếu: <span style="color:#f00;"><?=esc_html($purchase->get('product'))?></span></p>
			<p></p>
			<p style='font-weight:bold; font-size: 16px; color:#f00;'>Giao dịch của bạn đã không được chấp thuận.</p>
			<p></p>
			<p>Liên hệ với ban quản trị để biết thêm chi tiết.</p>
			<p></p>
			<p></p>
			<p>-------------</p>
			<p>Email gửi từ website: <?=esc_url(home_url())?></p>
			<?php
			$mail_body = ob_get_clean();

			wp_mail( $purchase->get('email'), '[Nha88] Đã hủy giao dịch', $mail_body, ['Content-Type: text/html; charset=UTF-8'] );
				
		}

		wp_send_json($response);

	}

	public static function ajax_transaction_approval() {
		$response = [
			'code' => 0,
			'msg' => ''
		];
		$purchase_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		check_ajax_referer( 'edit-purchase-'.$purchase_id, 'nonce', true );

		$purchase = \Nha88\Purchase::get_instance($purchase_id);

		//debug_log($purchase);
		if($purchase->id>0) {
			$continue = false;
			
			try {
				// chia sẻ google drive cho khách hàng
				$google_client = new \Google\Client();
				$google_client->setAccessToken(get_option('google_drive_token', ''));
				$drive_service = new \Google\Service\Drive($google_client);

				//$file_id = fw_get_db_post_option(absint($purchase->get('product')), 'file_id', '');
				$file_id = '';

				if(empty($file_id)) {
					try {
						// chưa có file_id thì tìm và lưu file_id;
						$pageToken = null;
						do {
							$listFiles = $drive_service->files->listFiles(array(
								'q' => "name='".$purchase->get('product')."' and trashed=false and mimeType='application/vnd.google-apps.folder'",
								'spaces' => 'drive',
								'pageToken' => $pageToken,
								'fields' => 'nextPageToken, files(id, name)',
							));

							$pageToken = $listFiles->pageToken;
							
							//debug_log($listFiles);
							
							if($listFiles->files)  {
								$file_id = $listFiles->files[0]->id;
								//fw_set_db_post_option(absint($purchase->get('product')), 'file_id', $file_id);
								$pageToken = null;
							}
							
						} while ($pageToken != null);

					} catch (\Exception $e) {
						throw new \Exception('Lỗi: '.$e->getMessage());
					}
				}

				if(!empty($file_id)) {
					
					$drive_service->getClient()->setUseBatch(true);
					
					try {
						$batch = $drive_service->createBatch();

						$userPermission = new \Google\Service\Drive\Permission(array(
							'type' => 'user',
							'role' => 'reader',
							'emailAddress' => $purchase->get('email')
						));

						$request = $drive_service->permissions->create($file_id, $userPermission, array('fields' => 'id'));
						$batch->add($request, 'user');
					
						$results = $batch->execute();

						//debug_log($results);

						foreach ($results as $result) {
							if ($result instanceof \Google_Service_Exception) {
								// Handle error
								// debug_log($result);
								throw new \Exception('Lỗi: Không thể chia sẻ.');
							} else {
								$continue = true;
							}
						}
					} catch (\Exception $e) {
						throw new \Exception('Lỗi: '.$e->getMessage());
					} finally {
						$drive_service->getClient()->setUseBatch(false);
					}
				} else {
					throw new \Exception('File không tồn tại.');
				}

			} catch (\Exception $e) {
				$response['msg'] = $e->getMessage();
			}

			if($continue) {
				// set quyền hiển thị link file trên web
				$approval = wp_set_post_terms( absint($purchase->get('product')), [absint($purchase->get('customer'))], 'customer', true );
				
				// gửi email thông báo
				if(is_array($approval)) {

					// cập nhật trạng thái giao dịch
					$purchase->publish();
		
					$response['code'] = 1;
					$response['msg'] = '<div style="font-weight:bold;color:#f00;">Đã duyệt</div>';

					$product = \Nha88\Product::get_instance(absint($purchase->get('product')));

					// gửi email thông báo cho khách hàng
					ob_start();
					?>
					<p>GIAO DỊCH ĐÃ ĐƯỢC DUYỆT TẠI WEBSITE NHA88!</p>
					<p></p>
					<p style='font-weight:bold;'>Mã giao dịch tham chiếu: <span style="color:#f00;"><?=esc_html($purchase->id)?></span></p>
					<p style='font-weight:bold;'>Mã sản phẩm tham chiếu: <span style="color:#f00;"><?=esc_html($purchase->get('product'))?></span></p>
					<p></p>
					<p style='font-weight:bold; font-size: 16px; color:#f00;'><a href="<?=esc_url($product->get('url_data_file'))?>">Tải File 3D</a>. Hoặc bạn có thể truy cập website đăng nhập bằng email của bạn và tải File 3D đã mua.</p>
					<p></p>
					<p>Nếu có vấn đề gì vui lòng liên hệ với ban quản trị để được hỗ trợ.</p>
					<p></p>
					<p></p>
					<p>-------------</p>
					<p>Email gửi từ website: <?=esc_url(home_url())?></p>
					<?php
					$mail_body = ob_get_clean();

					wp_mail( $purchase->get('email'), '[Nha88] Giao dịch đã duyệt', $mail_body, ['Content-Type: text/html; charset=UTF-8'] );
					
				} else {
					$response['msg'] = 'Lỗi: add term.';
				}
			} else {
				$response['msg'] = 'Lỗi: chia sẻ google drive.';
			}
		} else {
			$response['msg'] = 'Lỗi: giao dịch không tồn tại.';
		}

		wp_send_json($response);

	}

	public static function purchase_display_post_states($states, $post) {
		if($post->post_type=='purchase') {
			$states = [];
		}
		return $states;
	}

	public static function purchase_title($post_title, $post_id) {
		$_post = get_post($post_id);
		//debug_log($_post);
		if($_post->post_type=='purchase') {
			$post_title = $_post->ID;
		}
		return $post_title;
	}

	public static function purchase_row_actions( $actions, $post ) {
		if($post->post_type=='purchase') {

			$nonce = wp_create_nonce( 'purchase-urls-'.$post->ID );
			$view_urls = admin_url( 'admin-ajax.php?action=purchase_urls&id='.$post->ID.'&nonce='.$nonce.'&TB_iframe=true&width=800&height=600' );

			$actions['view_urls'] = '<a href="'.esc_url($view_urls).'" class="thickbox">Quá trình truy cập</a>';
			if(isset($actions['edit'])) {
				unset($actions['edit']);
			}
		}
		return $actions;
	}

	public static function quick_edit_disable($allowed, $post_type) {
		if('purchase'==$post_type) {
			$allowed = false;
		}

		return $allowed;
	}

	public static function custom_columns_value($column, $post_id) {
		global $google_auth_url;
		$nonce = wp_create_nonce('edit-purchase-'.$post_id);

		$purchase = \Nha88\Purchase::get_instance($post_id);

		switch ($column) {
			case 'actions':
				if($purchase->get('status')=='pending') {
					?>
					<!-- <button type="button" class="button button-primary transaction-share-file" data-id="<?=$purchase->id?>" data-nonce="<?=esc_attr($nonce)?>" title="Chia sẻ google drive giao dịch #<?=$purchase->id?> ?">Chia sẻ file</button> -->

					<button type="button" class="button button-primary transaction-approval" data-id="<?=$purchase->id?>" data-nonce="<?=esc_attr($nonce)?>" title="Duyệt giao dịch #<?=$purchase->id?> ?"<?php echo ($google_auth_url!='')?' disabled':''; ?>>Bấm duyệt</button>

					<button type="button" class="button button-secondary transaction-cancel" data-id="<?=$purchase->id?>" data-nonce="<?=esc_attr($nonce)?>" title="Hủy giao dịch #<?=$purchase->id?> ?">Bấm hủy</button>
					
					<?php
				} elseif($purchase->get('status')=='publish') {
					echo '<div style="font-weight:bold;color:#f00;">Đã duyệt</div>';
				} elseif($purchase->get('status')=='cancel') {
					echo '<div style="font-weight:bold;color:#999;">Đã hủy</div>';
				}

				break;
			case 'email':
				echo '<strong>'.fw_get_db_post_option( $purchase->id, 'email' ).'</strong>';
				break;
			case 'image':
				$product = \Nha88\Product::get_instance(absint(fw_get_db_post_option($purchase->id, 'product')));
				echo $product->get_image('medium');
				break;
			case 'product':
				echo '<strong>'.esc_html(fw_get_db_post_option($purchase->id, 'product')).'</strong>';
				
				break;
			case 'value':
				echo '<strong>'.number_format(absint(fw_get_db_post_option($purchase->id, 'value')), 0, '.', ',').'</strong> vnđ';
				break;
			case 'bank':
				$bank = fw_get_db_post_option($purchase->id, 'bank_trans_file');
				if($bank) {
					$bank_url = parse_url($bank['url']);

					echo '<img src="'.esc_url(home_url($bank_url['path'])).'" width="320">';
				}
				break;
			case 'utm_source':
				echo esc_html($purchase->get_utm_source());
				break;
			case 'utm_content':
				echo esc_html($purchase->get_utm_content());
				break;
			case 'urls':
				if($purchase->get_urls()) {
					foreach ($purchase->get_urls() as $url) {
						echo '<div>'.esc_html($url).'</div>';
					}
				}
				break;
		}
	}

	public static function custom_columns_header($columns) {

		$columns['title'] = 'ID';
		$columns['email'] = 'Khách hàng';
		$columns['product'] = 'Mã SP';
		$columns['image'] = 'Ảnh SP';
		$columns['value'] = 'Giá trị';
		$columns['actions'] = 'Hành động';
		$columns['bank'] = 'Ảnh chuyển khoản';
		$columns['utm_source'] = 'Nguồn';
		$columns['utm_content'] = 'Quảng cáo';
		//$columns['urls'] = 'URLs';

		return $columns;

	}

	public static function disable_months_dropdown($disabled, $post_type) {
		if($post_type=='purchase') {
			$disabled = true;
		}
		return $disabled;
	}

	public static function save_purchase($post_id, $post, $update) {
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => date('Ymd-His', strtotime($post->post_date))], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}
	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if(($hook=='edit.php' || $hook=='post.php') && $post_type=='purchase') {
			add_thickbox();
			//wp_enqueue_script('jquery-input-number', THEME_URI.'/libs/jquery-input-number/jquery-input-number.js', array('jquery'), '', false);

			wp_enqueue_style('admin-purchase', THEME_URI.'/assets/css/admin-purchase.css', [], '');
			wp_enqueue_script('admin-purchase', THEME_URI.'/assets/js/admin-purchase.js', array('jquery'), '');
		}

	}
}
