<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class UserDB {
  function __construct() {

    // add-on database containing complex data
    // my account
    // profile
    // roles

    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cdub_user";
    $this->limit = 10;

    $this->onActivate();

    add_action('rest_api_init', array($this, 'actionRoutes'));
  }

  function actionRoutes($request) {
    // Register WP User Route
    register_rest_route('cdub/v1', 'user/register', array(
      'methods' => 'POST',
      'callback' => array($this, 'registerUser')
    ));

    register_rest_route('cdub/v1', 'test/test', array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => array($this, 'test')
    ));
  }

  function test() {
    return 'test';
  }

  function registerUser( $request = null ) {
    $response = array();
    $username = sanitize_user( $request['username'] );
    $email = sanitize_email( $request['email'] );
    $password = sanitize_text_field( $request['password'] );
  
    $error = new WP_Error();
    if ( empty( $username ) ) {
      $error->add( 400, __( "Username field 'username' is required.", 'wp-rest-user' ), array( 'status' => 400 ) );
      return $error;
    }
    if ( empty( $email ) ) {
      $error->add(401, __( "Email field 'email' is required.", 'wp-rest-user' ), array('status' => 400 ) );
      return $error;
    }
    if ( empty( $password ) ) {
      $error->add( 404, __( "Password field 'password' is required.", 'wp-rest-user' ), array( 'status' => 400 ) );
      return $error;
    }
    $user_id = username_exists( $username );
    if ( ! $user_id && email_exists( $email ) == false ) {
      $user_id = wp_create_user( $username, $password, $email );
      if ( ! is_wp_error( $user_id ) ) {
        // Get User Meta Data (Sensitive, Password included. DO NOT pass to front end.)
        $user = get_user_by('id', $user_id);
        // $user->set_role( $role );
        $user->set_role('subscriber');
        // WooCommerce specific code
        if ( class_exists( 'WooCommerce' ) ) {
          $user->set_role( 'customer' );
        }
        // Get User Data (Non-Sensitive, Pass to front end.)
        $response['code'] = 200;
        $response['message'] = sprintf( __( "User '%s' Registration was Successful", 'wp-rest-user' ), $username );
      } else {
        return $user_id;
      }
    } else {
      $error->add( 406, __( "Email already exists, please try 'Reset Password'", 'wp-rest-user' ), array( 'status' => 400 ));
      return $error;
    }
    return new WP_REST_Response( $response, 123 );
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      WPID bigint(20) NOT NULL DEFAULT 0,
      Username varchar(60) NOT NULL DEFAULT '',
      Email varchar(60) NOT NULL DEFAULT '',
      Gamertag varchar(60) NOT NULL DEFAULT '',
      Roles varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY (id)
    ) $this->charset;");
  }
}