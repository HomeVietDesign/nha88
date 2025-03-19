<?php
namespace Nha88;

class Purchase_Process extends \WP_Background_Process {

    protected $action = 'purchase_process';

    protected function task( $item ) {
		$retry_count = isset( $item['retry_count'] ) ? intval( $item['retry_count'] ) : 0;
		try {
			myplugin_process_order( $item );
		} catch ( Exception $e ) {
			error_log( sprintf( 'Lỗi xử lý đơn hàng cho %s: %s', json_encode( $item ), $e->getMessage() ) );

			if ( $retry_count < 3 ) {
				// Tăng số lần retry và đẩy lại vào queue
				$item['retry_count'] = $retry_count + 1;
				$this->push_to_queue( $item );
			} else {
				// Sau 3 lần retry, thông báo lỗi cho admin
				myplugin_notify_admin_error( sprintf( 'Đơn hàng thất bại sau 3 lần retry: %s', json_encode( $item ) ) );
			}
		}
		return false;
    }

    protected function complete() {
        parent::complete();
        do_action( 'purchase_process_complete' );
    }
}