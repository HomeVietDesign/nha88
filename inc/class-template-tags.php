<?php
/**
 * 
 * 
 */
namespace Nha88;

class Template_Tags {

	public static function display_footer_html() {
		global $popup;
		if( !$popup ) {
			add_action('wp_footer', [__CLASS__, 'site_footer'], 10);
			add_action('wp_footer', [__CLASS__, 'footer_fixed'], 20);
		}
		add_action('wp_footer', [__CLASS__, 'modals'], 20);
		add_action('wp_footer', [__CLASS__, 'request_product_modal']);
	}

	public static function request_product_modal() {
		$turnstile_keys = \Nha88\Setting::get_turnstile_keys();
		$request_type = array_keys(\Nha88\Setting::request_type());
		?>
		<div class="modal fade" id="request-product" tabindex="-1" role="dialog" aria-labelledby="request-product-label">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="request-product-label"><?php
						foreach ($request_type as $key => $type) {
							?>
							<span class="modal-title-<?=esc_attr($type)?><?php echo ($key>0) ? ' hidden':''; ?>"><?=esc_html(fw_get_db_settings_option($type.'_popup_title', ''))?></span>
							<?php
						}
						?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form id="frm-request-product" class="modal-body" method="POST" action="">
						<input type="hidden" id="request_type" name="request_type" data-default="<?php echo esc_html($request_type[0]); ?>" value="<?php echo esc_html($request_type[0]); ?>" required>
						<input type="hidden" id="product_id" name="product_id" value="" required>
						<div class="mb-3 popup-content" id="request-product-desc">
						<?php
						foreach ($request_type as $key => $type) {
							?>
							<div class="popup-content-<?=esc_attr($type)?><?php echo ($key>0) ? ' hidden':''; ?>"><?=wp_get_the_content(fw_get_db_settings_option($type.'_popup_content', ''))?></div>
							<?php
						}
						?>
						</div>
						
						<div class="mb-3">
							<input type="text" id="customer_name" name="customer_name" maxlength="60" class="form-control" placeholder="Họ tên" required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="mb-3">
							<input type="text" id="customer_phone" name="customer_phone" placeholder="Số điện thoại của bạn" class="form-control" aria-label="Số điện thoại của bạn" required>
						</div>
						
						<?php if($turnstile_keys['sitekey']!='' && $turnstile_keys['secretkey']!='') { ?>
						<div id="cf-turnstile-rp" class="cf-turnstile" data-sitekey="<?=esc_attr($turnstile_keys['sitekey'])?>" data-callback="cf_turnstile_rp_callback" data-error-callback="cf_turnstile_rp_error_callback" data-expired-callback="cf_turnstile_rp_expired_callback"></div>
						<?php } ?>

						<div class="mb-3">
							<?php if($turnstile_keys['sitekey']!='' && $turnstile_keys['secretkey']!='') { ?>
							<button type="submit" class="btn btn-lg btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100" id="request-product-submit" disabled>Kiểm tra bảo mật...</button>
							<?php } else { ?>
							<button type="submit" class="btn btn-lg btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100" id="request-product-submit">Bấm gửi đi</button>
							<?php } ?>
							
							<div class="invalid-feedback"></div>
						</div>
						<div id="request-product-message"></div>
						<div id="request-product-preview" class="position-relative"></div>
					</form>
				</div>
			</div>
		</div>
		
		<?php
	}

	public static function modals() {
		global $popup;

		$close_button_left = $popup*30;
		?>
		<div class="modal" id="modal-popup-content" tabindex="-1">
			<div class="modal-dialog m-0">
				<div class="modal-content rounded-0 border-0">
					<button type="button" class="p-0 position-absolute close-button text-red z-3" data-bs-dismiss="modal" aria-label="Đóng lại" style="left:<?=$close_button_left?>px;"><span class="dashicons dashicons-no-alt"></span></button>
					<div class="modal-body p-0"></div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function footer_fixed() {
		global $theme_setting, $post;

		$footer_links = $theme_setting->get('footer_links');
		?>
		<div id="footer-buttons-fixed" class="position-fixed d-flex justify-content-center">
			<div class="w-100">
				<?php if($footer_links) {

					$link_last = array_pop($footer_links);

					if(!empty($footer_links)) {
						foreach ($footer_links as $link) {
						?>
						<div class="my-1">
							<a class="btn btn-danger d-block w-100 fw-bold text-nowrap" style="color:#ff0;" href="<?=esc_url($link['url'])?>"><?=esc_html($link['name'])?></a>
						</div>
						<?php
						}
					}
					?>
					<div class="row g-2">
						<div class="col">
							<div class="my-1">
								<a class="btn btn-danger d-block w-100 fw-bold text-nowrap" style="color:#ff0;" href="<?=esc_url($link_last['url'])?>"><?=esc_html($link_last['name'])?></a>
							</div>
						</div>
						<?php
						if($theme_setting->get('purchase_guide_link', false)) {
						?>
						<div class="col">
							<div class="my-1">
							
								<a class="d-block w-100 btn btn-success fw-bold text-nowrap" href="<?=esc_url($theme_setting->get('purchase_guide_link', '#'))?>"><?=esc_html($theme_setting->get('purchase_guide_link_label', 'Hướng dẫn'))?></a>
								
							</div>
						</div>
						<?php
						}
						?>
					</div>
				<?php } else { ?>
					<div class="my-2">
					<?php
					if($theme_setting->get('purchase_guide_link', false)) {
						?>
						<a class="d-block w-100 btn btn-success fw-bold text-nowrap" href="<?=esc_url($theme_setting->get('purchase_guide_link', '#'))?>"><?=esc_html($theme_setting->get('purchase_guide_link_label', 'Hướng dẫn'))?></a>
						<?php
					}
					?>
					</div>
				<?php }

				if($theme_setting->get('hotline','')!='' || $theme_setting->get('zalo','')!='' || ($theme_setting->get('popup_content','')!='' && $theme_setting->get('popup_content_button_text','') != '')) {
					?>
					<div class="hotline d-flex align-items-center mt-1 justify-content-end">
						<?php if($theme_setting->get('popup_content','')!='' && $theme_setting->get('popup_content_button_text','') != '') { ?>
							<button type="button" class="btn-popup-open btn-popup-content-open btn btn-lg btn-danger d-block flex-grow-1 fw-bold" style="color:#ff0;" data-bs-toggle="modal" data-bs-target="#modal-popup"><?=esc_html($theme_setting->get('popup_content_button_text',''))?></button>
						<?php } ?>

						<?php if($theme_setting->get('zalo','')!='') { ?>
						<a class="zalo-button btn btn-danger d-block btn-lg fw-bold text-yellow <?php //echo (!$has_contractor_actions)?'flex-grow-1':''; ?>flex-grow-1" href="https://zalo.me/<?=esc_attr($theme_setting->get('zalo',''))?>"><?=esc_html($theme_setting->get('zalo_label',''))?></a>
						<?php } ?>
						<?php if($theme_setting->get('hotline','')!='') { ?>
						<a class="alo-phone-img-circle d-block" href="tel:<?php echo esc_attr($theme_setting->get('hotline','')); ?>" title="<?php echo esc_attr($theme_setting->get('hotline_label','')); ?>"></a>
						<?php } ?>
					</div>
					<?php
				}
				
				?>
			</div>
		</div>
		<!-- modal -->
		<?php
		if($theme_setting->get('popup_content','')!='') {
			?>
			<div class="modal fade" id="modal-popup" tabindex="-1">
				<div class="modal-dialog modal-lg modal-dialog-centered">
					<div class="modal-content rounded-0">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						<div class="modal-body"><?php echo wp_format_content($theme_setting->get('popup_content','')); ?></div>
					</div>
				</div>
			</div>
			<?php
		}
		if($theme_setting->get('popup_sale','')!='') {
			?>
			<div class="modal fade" id="modal-popup-sale" tabindex="-1">
				<div class="modal-dialog modal-lg modal-dialog-centered">
					<div class="modal-content rounded-0">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						<div class="modal-body"><?php echo wp_format_content($theme_setting->get('popup_sale','')); ?></div>
					</div>
				</div>
			</div>
			<?php
		}
		
	}

	public static function the_password_form($output, $post) {
		ob_start();
		?>
		<form action="<?=home_url( 'wp-login.php?action=postpass' )?>" class="post-password-form" method="post">
			<div class="mb-3 text-uppercase">Đăng nhập bằng email của bạn</div>
			<div class="input-group mb-3">
				<input name="post_password" type="text" class="form-control" spellcheck="false">
				<button class="btn btn-primary" type="submit">Nhập</button>
			</div>
		</form>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	public static function display_widgets() {
		// Phân tích domain từ URL (loại bỏ schema, path)
		$host = parse_url(home_url(), PHP_URL_HOST);

		// Regex: bắt domain và loại trừ /wp-admin
		$regex = '/^https?:\/\/(?:www\.)?' . preg_quote($host, '/') . '(?!\/(wp-admin|wp-content)).*$/i';

		ob_start();
		?>
		<div class="site-footer-inner container-xl">
			<div class="row">
				<?php if(is_active_sidebar( 'footer-1' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-1'); ?></div>
				</div>
				<?php } ?>
				<?php if(is_active_sidebar( 'footer-2' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-2'); ?></div>
				</div>
				<?php } ?>
				<?php if(is_active_sidebar( 'footer-3' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-3'); ?></div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php
		$dom_content = str_get_html(ob_get_clean());
		if($dom_content) {
			foreach($dom_content->find('a') as $element) {
				//if(preg_match($regex, $element->href)) {
					$element->setAttribute('class', trim($element->class . ' popup'));
				//}
			}
		}
		echo (string)$dom_content;

	}

	public static function youtube_api() {
		?>
		<script type="text/javascript" id="youtube-video-api-scripts" data-no-optimize="1">
			// This code loads the IFrame Player API code asynchronously.
			var tag = document.createElement('script');

			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
			function onShortcodeYTPlayerReady(event) {
				let settings = JSON.parse(event.target.g.dataset.settings);
				if(settings.autoplay) {
					event.target.mute();
					event.target.playVideo();
				}
			}

			function onShortcodeYTPlayerStateChange(event) {
				//console.log(event);
				let settings = JSON.parse(event.target.g.dataset.settings);
				if(settings.loop && event.data == YT.PlayerState.ENDED) {
					//event.target.mute();
					event.target.playVideo();
				}
				if(event.data == YT.PlayerState.PAUSED || event.data == YT.PlayerState.UNSTARTED || event.data == YT.PlayerState.CUED) {
					event.target.g.closest('.shortcode-youtube-video').classList.add('paused');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('playing');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('buffering');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('ended');
				} else if(event.data == YT.PlayerState.PLAYING) {
					event.target.g.closest('.shortcode-youtube-video').classList.remove('paused');
					event.target.g.closest('.shortcode-youtube-video').classList.add('playing');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('buffering');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('ended');
				} else if(event.data == YT.PlayerState.BUFFERING) {
					event.target.g.closest('.shortcode-youtube-video').classList.remove('paused');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('playing');
					event.target.g.closest('.shortcode-youtube-video').classList.add('buffering');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('ended');
				} else if(event.data == YT.PlayerState.ENDED) {
					event.target.g.closest('.shortcode-youtube-video').classList.remove('paused');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('playing');
					event.target.g.closest('.shortcode-youtube-video').classList.remove('buffering');
					event.target.g.closest('.shortcode-youtube-video').classList.add('ended');
				}
			}

			function onYouTubeIframeAPIReady() {

				let yt_players = [],
					yt_frames = document.querySelectorAll('.yt-video-iframe');

				if(yt_frames.length>0) {
					yt_frames.forEach(function(el){
						//console.log(JSON.parse(el.dataset.settings));
						let player = new YT.Player(el.id, {
							height: '1080',
							width: '1920',
							videoId: el.dataset.id,
							playerVars: JSON.parse(el.dataset.settings),
							events: {
								'onReady': onShortcodeYTPlayerReady,
								'onStateChange': onShortcodeYTPlayerStateChange
							}
						});
						yt_players.push(player);
					});
				}

				let shortcodePlays = document.querySelectorAll('.shortcode-youtube-video .play');
				let shortcodePauses = document.querySelectorAll('.shortcode-youtube-video .pause');
				shortcodePlays.forEach(function(play) {
					play.addEventListener("click", function() {
						//console.log('play');
						//console.log(yt_players[play.dataset.index]);
						yt_players[play.dataset.index].playVideo();
					});
				});
				shortcodePauses.forEach(function(pause) {
					pause.addEventListener("click", function() {
						//console.log('pause');
						//console.log(yt_players[pause.dataset.index]);
						yt_players[pause.dataset.index].pauseVideo();
					});
				});

			}
		</script>
		<?php
	}

	public static function footer_custom_scripts() {
		global $theme_setting;
		$custom_script = $theme_setting->get('footer_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}

	public static function site_footer() {
		
		?>
		<footer id="site-footer" class="py-5">
		<?php self::display_widgets(); ?>
		</footer>
		<?php
	}

	public static function site_body_close() {
		?>
		</div><!-- /#site-body -->
		<?php
	}

	public static function site_body_open() {
		?>
		<div id="site-body">
		<?php
	}

	public static function body_open_custom_code() {
		global $theme_setting;

		$custom_script = $theme_setting->get('body_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}

	public static function body_class($classes) {
		global $popup;
		if ( $popup ) {
			$classes[] = 'has-popup';
			$classes[] = 'popup-'.$popup;
		}

		return $classes;
	}

	public static function header_html() {
		global $popup;
		if( !$popup ) {
			add_action('wp_body_open', [__CLASS__, 'site_header'], 10);
		}
	}

	public static function primary_menu() {
		$object = get_queried_object();
		$display_menu = 'yes';
		$menu = false;
		$nav_menu = '';
		if(is_page()) {
			$display_menu = fw_get_db_post_option($object->ID, 'display_menu', 'yes');
			$menu = fw_get_db_post_option($object->ID, 'apply_menu');
		} elseif(is_category() || is_tax()) {
			$display_menu = fw_get_db_term_option($object->term_id, $object->taxonomy, 'display_menu', 'yes');
			$menu = fw_get_db_term_option($object->term_id, $object->taxonomy, 'apply_menu');
		}

		if($display_menu=='yes') {

			$obj_menu = ($menu) ? wp_get_nav_menu_object( $menu[0] ): false;
			if($obj_menu) {
				$nav_menu = wp_nav_menu([
					'menu' => $obj_menu,
					'container' => false,
					'echo' => false,
					'fallback_cb' => '',
					'depth' => 2,
					'walker' => new \Nha88\Walker_Primary_Menu(),
					'items_wrap' => '<ul class="%2$s">%3$s</ul>',
				]);

			} else if(has_nav_menu('primary')) {
				$nav_menu = wp_nav_menu([
					'theme_location' => 'primary',
					'container' => false,
					'echo' => false,
					'fallback_cb' => '',
					'depth' => 2,
					'walker' => new \Nha88\Walker_Primary_Menu(),
					'items_wrap' => '<ul class="%2$s">%3$s</ul>',
				]);
			}
			
			if($nav_menu!='') {
				?>
				<nav id="main-nav">
					<div class="main-nav-inner"><?php echo $nav_menu; ?></div>
				</nav>
				<?php
			}
			
		}
	}

	public static function secondary_menu() {
		if( has_nav_menu('secondary') ) {
			?>
			<nav id="secondary-nav" class="">
				<div class="container p-0">
					<div class="d-flex flex-wrap justify-content-center overflow-hidden">
						<div class="secondary-menu">
							<?php
							wp_nav_menu([
								'theme_location' => 'secondary',
								'container' => false,
								'echo' => true,
								'fallback_cb' => '',
								'depth' => 1,
								'walker' => new \Nha88\Walker_Secondary_Menu(),
								'items_wrap' => '<ul class="menu list-unstyled p-0 m-0 d-flex">%3$s</ul>',
							]);
							?>
						</div>
					</div>
				</div>
			</nav>
			<?php
		}
	}

	public static function site_header() {
		
		?>
		<header id="site-header" class="position-sticky">
		<?php
		ob_start();
		self::primary_menu();
		self::secondary_menu();

		$html = str_get_html(ob_get_clean());
		if($html) {
			// Phân tích domain từ URL (loại bỏ schema, path)
			$host = parse_url(home_url(), PHP_URL_HOST);

			// Regex: bắt domain và loại trừ /wp-admin
			$regex = '/^https?:\/\/(?:www\.)?' . preg_quote($host, '/') . '(?!\/(wp-admin|wp-content)).*$/i';

			foreach($html->find('a') as $element) {
				//if(preg_match($regex, $element->href)) {
					$element->setAttribute('class', trim($element->class . ' popup'));
				//}
			}
		}
		echo (string)$html;

		?>
		</header>
		<?php
		
	}

	public static function noindex() {
		if(is_single()) {
		?>
		<meta name="robots" content="noindex, nofollow" />
		<?php
		}
	}

	public static function head_product_scripts() {
		$captcha = \Nha88\Setting::has_turnstile();
		if($captcha) {
		?>
		<script>
			function cf_turnstile_rp_callback(token) { // rp = request product
				let $submit_button = jQuery('#request-product-submit');
				$submit_button.prop('disabled', false);
				$submit_button.text('Bấm gửi đi');
			}

			function cf_turnstile_rp_error_callback() {
				let $submit_button = jQuery('#request-product-submit');
				// alert('Kiểm tra SPAM thất bại!');
				// window.location.reload();
				$submit_button.prop('disabled', true);
			}

			function cf_turnstile_rp_expired_callback() {
				let $submit_button = jQuery('#request-product-submit');
				// alert('Kiểm tra SPAM hết hạn!');
				// window.location.reload();
				$submit_button.prop('disabled', true);
			}

			document.addEventListener('requestProduct', function(e){
				turnstile.reset();
			});
		</script>
		<?php
		}
	}

	public static function head_scripts() {
		global $theme_setting;
		?>
		<style type="text/css">
			.grecaptcha-badge {
				right: -999999px!important;
			}
			/* PART 1 - Before Lazy Load */
			img[data-lazyloaded]{
				opacity: 0;
			}
			/* PART 2 - Upon Lazy Load */
			img.litespeed-loaded{
				-webkit-transition: opacity .3s linear 0.1s;
				-moz-transition: opacity .3s linear 0.1s;
				transition: opacity .3s linear 0.1s;
				opacity: 1;
			}
			/*@media (min-width: 576px) {
				
			}*/
		</style>
		<script type="text/javascript">
			window.addEventListener('DOMContentLoaded', function(){
				const root = document.querySelector(':root');
				root.style.setProperty('--footer-buttons-fixed--height', document.getElementById('footer-buttons-fixed').clientHeight+'px');
				root.style.setProperty('--site-header--height', document.getElementById('site-header').clientHeight+'px');
				window.addEventListener('resize', function(){
					root.style.setProperty('--footer-buttons-fixed--height', document.getElementById('footer-buttons-fixed').clientHeight+'px');
					root.style.setProperty('--site-header--height', document.getElementById('site-header').clientHeight+'px');
				});
			});
		</script>
		<?php
		$captcha = \Nha88\Setting::has_turnstile();
		if($captcha) {
			?>
			<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
			<?php
		}
		$custom_script = $theme_setting->get('head_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}
}