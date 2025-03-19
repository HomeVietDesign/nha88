<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Account extends FW_Shortcode
{
	
	public function _init()
	{
		//add_filter( 'the_password_form', [$this, 'the_password_form'], 10, 2 );
	}

	public function the_password_form($output, $post) {
		ob_start();
		?>
		<form action="<?=home_url( 'wp-login.php?action=postpass' )?>" class="post-password-form" method="post">
			<div class="d-inline-block">
				<div class="mb-3 text-uppercase">Đăng nhập bằng email của bạn</div>
				<div class="input-group mb-3">
					<input name="post_password" type="text" class="form-control" spellcheck="false">
					<button class="btn btn-primary" type="submit">Nhập</button>
				</div>
			</div>
		</form>
		<?php
		$output = ob_get_clean();
		return $output;
	}
}