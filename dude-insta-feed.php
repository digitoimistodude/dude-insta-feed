<?php
/**
 * Plugin Name: Dude Insta feed
 * Plugin URI: https://github.com/digitoimistodude/dude-insta-feed
 * Description: Fetches the latest images for user from Instagram
 * Version: 0.1.0
 * Author: Digitoimisto Dude Oy, Timi Wahalahti
 * Author URI: https://www.dude.fi
 * Requires at least: 4.4.2
 * Tested up to: 4.7.2
 *
 * Text Domain: dude-insta-feed
 * Domain Path: /languages
 */

if( !defined( 'ABSPATH' )  )
	exit();

Class Dude_Insta_Feed {
  private static $_instance = null;

  /**
   * Construct everything and begin the magic!
   *
   * @since   0.1.0
   * @version 0.1.0
   */
  public function __construct() {
    // Add actions to make magic happen
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
  } // end function __construct

  /**
   *  Prevent cloning
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-insta-feed' ) );
	} // end function __clone

  /**
   *  Prevent unserializing instances of this class
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __wakeup() {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-insta-feed' ) );
  } // end function __wakeup

  /**
   *  Ensure that only one instance of this class is loaded and can be loaded
   *
   *  @since   0.1.0
   *  @version 0.1.0
	 *  @return  Main instance
   */
  public static function instance() {
    if( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  } // end function instance

  /**
   *  Load plugin localisation
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function load_plugin_textdomain() {
    load_plugin_textdomain( 'dude-insta-feed', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
  } // end function load_plugin_textdomain

	public function get_user_images( $userid = '' ) {
		if( empty( $userid ) )
			return;

		$transient_name = apply_filters( 'dude-insta-feed/user_images_transient', 'dude-insta-user-'.$userid, $userid );
		$images = get_transient( $transient_name );
	  if( !empty( $images ) || false != $images )
	    return $images;

		$parameters = array(
			'access_token'	=> apply_filters( 'dude-insta-feed/access_token/user='.$userid, '', $userid ),
			'count'					=> '5',
		);

		$response = self::_call_api( $userid, apply_filters( 'dude-insta-feed/user_images_parameters', $parameters ) );
		if( $response === FALSE )
			return;

		$response = apply_filters( 'dude-insta-feed/user_images', json_decode( $response['body'], true ) );
		set_transient( $transient_name, $response, apply_filters( 'dude-insta-feed/user_images_lifetime', '600' ) );

		return $response;
	} // end function get_user_images

	private function _call_api( $userid = '', $parameters = array() ) {
		if( empty( $userid ) )
			return false;

		if( empty( $parameters ) )
			return false;

		$parameters = http_build_query( $parameters );
		$response = wp_remote_get( 'https://api.instagram.com/v1/users/'.$userid.'/media/recent/?'.$parameters );
		return $response;

		if( $response['response']['code'] !== 200 ) {
			self::_write_log( 'response status code not 200 OK, user: '.$userid );
			return false;
		}

		return $response;
	} // end function _call_api

	private function _write_log ( $log )  {
    if( true === WP_DEBUG ) {
      if( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
      } else {
        error_log( $log );
      }
    }
  } // end _write_log
} // end class Dude_Insta_Feed

function dude_insta_feed() {
  return new Dude_Insta_Feed();
} // end function dude_insta_feed
