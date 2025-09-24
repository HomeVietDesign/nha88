<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Products extends FW_Shortcode
{

	public function _init() {
	}

	public static function products($query, $popup='yes') {
		//debug($query);
		if($query->have_posts()) {
			?>
			<div class="list-products row">
			<?php self::loop_products($query, $popup); ?>
			</div>
			<?php
			wp_reset_postdata();
		} else {
			echo '<div class="text-center py-3">Chưa có dữ liệu.</div>';
		}
	}

	public static function loop_products($query, $popup='yes') {
		while ($query->have_posts()) {
			$query->the_post();
			if($popup!='yes') {
				get_template_part( 'loop', 'product-no-content' );
			} else {
				get_template_part( 'loop', 'product' );
			}
		}

	}

	public static function pagination($wp_query, $end_size=3, $mid_size=2) {
		// Get max pages and current page out of the current query, if available.
		$total   = (int)$wp_query->max_num_pages;
		$current = ($wp_query->query_vars['paged']==0)?1:(int)$wp_query->query_vars['paged'];

		// Who knows what else people pass in $args.
		if ( $total < 2 ) {
			return;
		}

		if ( $end_size < 1 ) {
			$end_size = 1;
		}

		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}

		$r          = '';
		$page_links = array();
		$dots       = false;

		if ( $current && 1 < $current ): 
			$page_links[] = '<button class="prev page-numbers ms-1 btn btn-sm btn-outline-dark" data-paged="'.($current - 1).'" type="button"><span class="dashicons dashicons-arrow-left"></span></button>';
		endif;

		for ( $n = 1; $n <= $total; $n++ ) :
			if ( $n == $current ) :
				$page_links[] = '<span class="page-numbers ms-1 current  btn btn-sm btn-danger" data-paged="'.$n.'">'.$n.'</span>';

				$dots = true;
			else :
				if ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) :
					$page_links[] = '<button class="page-numbers ms-1 btn btn-sm btn-outline-dark" data-paged="'.$n.'" type="button">'.$n.'</button>';

					$dots = true;
				elseif ( $dots ) :
					if($total>2*$end_size+1) {
						$page_links[] = '<span class="page-numbers ms-1 dots btn btn-sm">&hellip;</span>';
					} else {
						$page_links[] = '<button class="page-numbers ms-1 btn btn-sm btn-outline-dark" data-paged="'.$n.'" type="button">'.$n.'</button>';
					}
					$dots = false;
				endif;
			endif;
		endfor;

		if ( $current && $current < $total ) :
			$page_links[] = '<button class="next page-numbers ms-1 btn btn-sm btn-outline-dark" data-paged="'.($current + 1).'" type="button"><span class="dashicons dashicons-arrow-right"></span></button>';
		endif;

		$r = implode( "\n", $page_links );

		return $r;
	}
}