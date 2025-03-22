<?php
/**
 * Facebook Pixel Plugin FacebookRequest class.
 *
 * This file contains the main logic for FacebookRequest.
 *
 * @package FacebookPixelPlugin
 */

/**
 * Define FacebookRequest class.
 *
 * @return void
 */

/*
* Copyright (C) 2017-present, Meta, Inc.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

namespace FacebookPixelPlugin\Integration;

defined( 'ABSPATH' ) || die( 'Direct access not allowed' );

use FacebookPixelPlugin\Core\FacebookPluginUtils;
use FacebookPixelPlugin\Core\FacebookServerSideEvent;
use FacebookPixelPlugin\Core\FacebookWordPressOptions;
use FacebookPixelPlugin\Core\ServerEventFactory;
use FacebookPixelPlugin\Core\PixelRenderer;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;

/**
 * FacebookRequest class.
 */
class FacebookRequest extends FacebookWordpressIntegrationBase {
    const TRACKING_NAME = 'wp-theme';

    /**
     * Add hooks to inject the Contact Form 7 tracking code.
     *
     * Adds the following hooks:
     *  - order_submit: Triggers a server-side event when the form is submitted.
     *  - wp_footer: Injects the mail sent listener.
     */
    public static function inject_pixel_code() {
        add_filter( 'request_submit', array( __CLASS__, 'trackServerEvent' ) );
        
        add_action(
            'wp_footer',
            array( __CLASS__, 'injectMailSentListener' ),
            10
        );
    }

    /**
     * Injects a JavaScript listener for the 'orderProduct' event,
     * which is triggered when a form is submitted.
     *
     * The listener executes the Pixel code sent in the response
     * via the 'fb_pxl_code' key.
     *
     * @return void
     */
    public static function injectMailSentListener() {
        ob_start();
    ?>
    <!-- Meta Pixel Event Code -->
    <script type='text/javascript'>
        document.addEventListener( 'request', function( event ) {
	        if( "fb_pxl_code" in event.detail){
                if(event.detail.fb_pxl_code!='') {
	               eval(event.detail.fb_pxl_code);
                }
	        }
        }, false );
    </script>
    <!-- End Meta Pixel Event Code -->
        <?php
        $listener_code = ob_get_clean();
        echo $listener_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function trackServerEvent( $response ) {
    	
        $is_internal_user = FacebookPluginUtils::is_internal_user();
        
        $is_internal_user = false;
        
        $submit_failed    = (intval($response['code'])<1);
        if ( $is_internal_user || $submit_failed ) {
            return $response;
        }

        $event_name = '';

        switch ($response['type']) {
            case 'request':
                $event_name = 'Purchase';
                break;
            
            case 'floor_plan':
                $event_name = 'Purchase';
                break;

            case 'interior':
                $event_name = 'Purchase';
                break;

            case 'purchase':
                $event_name = 'Purchase';
                break;
        }

        $server_event = ServerEventFactory::safe_create_event(
            $event_name,
            array( __CLASS__, 'readFormData' ),
            array( $response ),
            self::TRACKING_NAME,
            true
        );
        FacebookServerSideEvent::get_instance()->track( $server_event );

        $events = FacebookServerSideEvent::get_instance()->get_tracked_events();
        if ( count( $events ) === 0 ) {
            return $response;
        }
        $event_id  = $events[0]->getEventId();
        $fbq_calls = PixelRenderer::render(
            $events,
            self::TRACKING_NAME,
            false
        );
        $code      = sprintf(
            "
    if( typeof window.pixelLastGeneratedOrderEvent === 'undefined'
    || window.pixelLastGeneratedOrderEvent != '%s' ){
    window.pixelLastGeneratedOrderEvent = '%s';
    %s
    }
        ",
            $event_id,
            $event_id,
            $fbq_calls
        );

        $response['fb_pxl_code'] = $code;

        return $response;
    }

    public static function readFormData( $response ) {
        return $response['data'];
    }

}
FacebookRequest::inject_pixel_code();