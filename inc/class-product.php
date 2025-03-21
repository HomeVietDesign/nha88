<?php
namespace Nha88;

/**
 * 
 */
class Product extends Post {
	
	public $type = 'product';

	private $price = null;

	private $qrbank_src = null;

	private $qrbank = null;

	public function get_image($size='full') {
		$image = '';

		if($this->get('images')) {
			$image = wp_get_attachment_image( $this->get('images')[0]['attachment_id'], $size );
		}

		return $image;
	}

	public function get_image_src($size='full') {
		$image_src = '';

		if($this->get('images')) {
			$image_srcs = wp_get_attachment_image_src( $this->get('images')[0]['attachment_id'], $size, false );
			$image_src = $image_srcs[0];
		}

		return $image_src;
	}

	public function get_price_qrbank() {
		global $theme_setting;

		if($theme_setting->get('bank_qr')) {
			return wp_get_attachment_image( $theme_setting->get('bank_qr')['attachment_id'], 'large' );
		}
		// if($this->get_object_price()) {
		// 	if($this->get_object_price()->get_qrbank_src()) {
		// 		return '<img src="'.esc_url($this->get_object_price()->get_qrbank_src()).'">';
		// 	}
		// }

		return '';
	}

	public function get_price_html() {
		global $theme_setting;

		ob_start();
		?>
		<div class="product-price-html">
			<span class="product-price-label"><?php echo esc_html($theme_setting->get('product_price_label', 'Giá bán:')); ?></span>
			<?php echo self::get_price_html_short(); ?>
		</div>
		<?php
		return ob_get_clean();
	}

	public function get_price_html_short() {
		global $theme_setting;
		$price = $this->get_price();
		ob_start();
		?>
		<span class="product-price">
		<?php
		if($price) {
			?>
			<strong><?=number_format($price, 0, '.', ',')?></strong>
			<span class="product-price-unit"><?php echo esc_html($theme_setting->get('product_price_unit', 'vnđ')); ?></span>
			<?php
		} elseif ($price===0) {
			?><strong class="no-price"><?php echo esc_html($theme_setting->get('product_price_0', 'Miễn phí')); ?></strong><?php
		} else {
			?><strong class="no-price"><?php echo esc_html($theme_setting->get('product_price_empty', 'Liên hệ')); ?></strong><?php
		}
		?>
		</span>
		<?php
		return ob_get_clean();
	}

	public function get_price() {
		global $theme_setting;

		$area = floatval($this->get('frontage',0)) * floatval($this->get('depth',0));
		$price = absint($theme_setting->get('product_price',0));
		$sale = $this->get('sale', 'normal');
		$value = $area*$price;

		if($value === 0) $sale = 'comingsoon';

		switch ($sale) {
			case 'free':
				return 0;
				break;
			case 'comingsoon':
				return '';
				break;
			default:
			case 'normal':
				return $value;
				break;
		}

		// if($this->get_object_price()) {
		// 	return absint( $this->get_object_price()->term->name );
		// }
		// return '';
	}

	public function get_object_price() {
		if($this->post && $this->price===null) {
			$term_price = get_the_terms( $this->post, 'product_price' );
			
			if($term_price) {
				//$this->price = $term_price[0];
				$this->price = \Nha88\Product_Price::get_instance($term_price[0]->term_id, 'product_price');
			}
		}
		return $this->price;
	}
}