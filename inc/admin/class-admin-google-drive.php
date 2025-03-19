<?php
namespace Nha88\Admin;

class Google_Drive {

	const ADMIN_SLUG = 'admin_google_drive';

	public static function enqueue_scripts($hook) {
		if($hook=='toplevel_page_'.self::ADMIN_SLUG) {
			wp_enqueue_style( self::ADMIN_SLUG, THEME_URI.'/assets/css/admin-google-drive.css' );
			wp_enqueue_script( self::ADMIN_SLUG, THEME_URI.'/assets/js/admin-google-drive.js', array('jquery'), '', false );
		}
	}

	public static function google_drive_load_admin() {
		global $google_auth_url;

		$clientId = fw_get_db_settings_option('drive_key');
		$clientSecret = fw_get_db_settings_option('drive_secret');
		$redirect_uri = admin_url('admin.php?page='. self::ADMIN_SLUG);

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
			
			if($token) {

				$client->setAccessToken($token);

				// store in the session also
				$google_drive_token = $token;
				update_option('google_drive_token', $google_drive_token);

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

	public static function admin_google_drive_page() {
		global $google_auth_url;

		?>
		<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<p>
				<?php if($google_auth_url) { ?>
				<a href="<?=esc_url($google_auth_url)?>" class="button button-primary">Connect Google Account</a>
				<?php } else { ?>
				<a href="<?=admin_url('admin.php?page='. self::ADMIN_SLUG.'&logout=1')?>" class="button button-primary">Disconnect Google Account</a>
				<?php } ?>
			</p>
			<div>
			<?php
			if(empty($google_auth_url)) {
				
				$google_client = new \Google\Client();
				$google_client->setAccessToken(get_option('google_drive_token', ''));
				
				$drive_service = new \Google\Service\Drive($google_client);

				$file = null;
				$pageToken = null;
				do {
					$response = $drive_service->files->listFiles(array(
						'q' => "name='119875' and mimeType='application/vnd.google-apps.folder'",
						'spaces' => 'drive',
						'pageToken' => $pageToken,
						'fields' => 'nextPageToken, files(id, name)',
					));

					$pageToken = $response->pageToken;
					
					if($response->files)  {
						$file = $response->files[0];
						$pageToken = null;
					}

					// foreach ($response->files as $f) {
					// 	$file = $f;
					// 	printf("<p>Found file: %s (%s)</p>", $file->name, $file->id);
					// }
					
				} while ($pageToken != null);

				//debug_log($file);

				if($file) {
					// $realFileId = readline("Enter File Id: ");
					// $realUser = readline("Enter user email address: ");
					// $realDomain = readline("Enter domain name: ");
					// $ids = array();
					// $fileId = '1sTWaJ_j7PkjzaBWtNc3IzovK5hQf21FbOw9yLeeLPNQ';
					// $fileId = $realFileId;
					
					$drive_service->getClient()->setUseBatch(true);
					
					try {
						$batch = $drive_service->createBatch();

						$userPermission = new \Google\Service\Drive\Permission(array(
							'type' => 'user',
							'role' => 'reader',
							'emailAddress' => 'qqngochv@gmail.com'
						));

						$request = $drive_service->permissions->create($file->id, $userPermission, array('fields' => 'id'));
						$batch->add($request, 'user');
					
						$results = $batch->execute();

						foreach ($results as $result) {
							if ($result instanceof \Google_Service_Exception) {
								// Handle error
								debug_log($result);
							} else {
								debug_log("Permission ID: ".$result->id);
								//array_push($ids, $result->id);
							}
						}
					} finally {
						$drive_service->getClient()->setUseBatch(false);
					}
					
					//return $ids;

				}
			}
			?>
			</div>
		</div>
		<?php
	
	}

	public static function admin_menu() {
		$admin_page = add_menu_page( 'Google Drive', 'Google Drive', 'manage_options', self::ADMIN_SLUG, [__CLASS__, 'admin_google_drive_page'], 'dashicons-category', 80 );

		add_action( 'load-' . $admin_page, [__CLASS__, 'google_drive_load_admin'], 10, 0 );
	}
}