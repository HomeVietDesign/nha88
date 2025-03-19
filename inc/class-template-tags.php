<?php
/**
 * 
 * 
 */
namespace Nha88;

class Template_Tags {

	public static function display_footer_html() {
		add_action('wp_footer', [__CLASS__, 'site_footer'], 10);
		add_action('wp_footer', [__CLASS__, 'footer_fixed'], 20);
	}

	public static function footer_fixed() {
		global $theme_setting, $account, $post;

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
						<div class="col">
							<div class="my-1">
							<?php
							if($account) {
							?>
								<button type="button" class="logout-post-password d-block w-100 btn btn-success fw-bold text-nowrap" data-hash="<?=esc_attr(COOKIEHASH)?>" data-url="<?=esc_url(fw_current_url())?>" title="Đăng xuất">Đăng xuất</button>
							<?php } else { ?>
								
								<button type="button" class="d-block w-100 btn btn-success fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#modal-account">Đăng nhập</button>
							<?php } ?>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="my-2">
					<?php
					if($account) {
					?>
						<button type="button" class="logout-post-password d-block w-100 btn btn-success fw-bold text-nowrap" data-hash="<?=esc_attr(COOKIEHASH)?>" data-url="<?=esc_url(fw_current_url())?>" title="Đăng xuất">Đăng xuất</button>
					<?php } else { ?>
						
						<button type="button" class="d-block w-100 btn btn-success fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#modal-account">Đăng nhập</button>
					<?php } ?>
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
		?>
		<div class="modal fade" id="modal-account" tabindex="-1">
			<div class="modal-dialog modal-md modal-dialog-centered">
				<div class="modal-content rounded-0">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					<div class="modal-body">
					<?php if($account) {
						?>
						<div><button type="button" class="btn btn-danger">Đăng xuất</button></div>
						<?php
					} else {
						echo get_the_password_form();
					} ?>
					</div>
				</div>
			</div>
		</div>
		<?php
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

	public static function header_html() {
		add_action('wp_body_open', [__CLASS__, 'site_header'], 10);
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
		<?php self::primary_menu(); ?>
		<?php self::secondary_menu(); ?>
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

	public static function head_youtube_scripts() {
		?>
		<script>
			// This code loads the IFrame Player API code asynchronously.
			var tag = document.createElement('script');
			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		 </script>
		<?php
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
		$custom_script = $theme_setting->get('head_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}
}