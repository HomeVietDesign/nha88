<?php
if(is_singular( 'product' )) {
global $theme_setting, $product;

$product->single_actions_html();
?>
<form class="frm-buynow" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post">
	<input type="hidden" name="id" value="<?php echo $product->id; ?>">
	<input type="hidden" name="action" value="buynow">
	<input type="hidden" name="ref" value="<?=(isset($_COOKIE['_ref'])?esc_attr(json_encode($_COOKIE['_ref'])):'')?>">
	<input type="hidden" name="url" value="<?=esc_url(fw_current_url())?>">
	<div class="cf-turnstile" data-sitekey="<?=esc_attr($theme_setting->get('cf_turnstile_key'))?>"></div>
	<div class="row g-2 align-items-end">
		<div class="mb-2 col-sm-6">
			<div class="form-label mb-1">Số điện thoại liên hệ</div>
			<input type="tel" name="phone_number" class="form-control w-100" value="" required>
		</div>
		<div class="mb-2 col-sm-6">
			<button type="submit" class="submit-button btn btn-danger w-100">Gửi yêu cầu tư vấn mua hồ sơ</button>
		</div>
	</div>
	<div class="buynow-response text-center"></div>
</form>
<?php
}