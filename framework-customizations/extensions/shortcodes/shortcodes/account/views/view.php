<?php
global $account, $post;

?>
<div class="fw-shortcode-account">
	<div class="row justify-content-center">
		<div class="col-lg-6">
			<div class="row">
				<div class="logged-on col-md-6">
					<div class="mb-3 text-uppercase">Tài khoản</div>
				<?php
				if($account) {
					?>
					<div>Họ tên: <?php echo esc_html($account->name); ?></div>
					<div class="mb-3">Email: <?php echo esc_html($account->email); ?></div>
					<?php
				} else {
					?>
					<div class="mb-3 text-secondary">Chưa đăng nhập hệ thống.</div>
					<?php
				}
				?>
				</div>
				<div class="login-form col-md-6">
					<?php echo get_the_password_form( $post ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	if($account) {
	?>
	<div class="account-packages border-top border-dark py-3">
		
	</div>
	<?php
	}
	?>
</div>