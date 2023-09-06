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
    $this->tablename = $wpdb->prefix . "gameface_user";
    $this->limit = 10;

    $this->onActivate();

    add_action('admin_post_createuser', array($this, 'createUser'));
    add_action('admin_post_nopriv_createuser', array($this, 'createUser'));

    add_action('admin_post_deleteuser', array($this, 'deleteUser'));
    add_action('admin_post_nopriv_deleteuser', array($this, 'deleteUser'));

    add_action('admin_post_updateuser', array($this, 'updateUser'));
    add_action('admin_post_nopriv_updateuser', array($this, 'updateUser'));
    
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

  function deleteUser() {
    if(current_user_can('administrator')) {
      $id = sanitize_text_field($_POST['idtodelete']);
      global $wpdb;
      $wpdb->delete($this->tablename, array('id' => $id));
      wp_safe_redirect(site_url('/leagues'));
    } else {
      wp_safe_redirect(site_url());
    }
    exit;
  }

  function createUser() {
    // Clear Error log
    VW::clearErrors();

    /* CREATE USER IN WP for password management */
    $user = array();
    if(isset($_POST['incUsername'])) $user['Username'] = sanitize_text_field($_POST['incUsername']);
    if(isset($_POST['incPass'])) $user['Pass'] = sanitize_text_field($_POST['incPass']);
    if(isset($_POST['incEmail'])) $user['Email'] = sanitize_text_field($_POST['incEmail']);

    // save wpid for our own db
    $wpid = wp_create_user($user['Username'], $user['Pass'], $user['Email']);

    if ( !is_wp_error( $wpid ) ) {
      /* insert into cdub table */
      // grab fields
      $user['WPID'] = $wpid;
      if(isset($_POST['incGamertag'])) $user['Gamertag'] = sanitize_text_field($_POST['incGamertag']);

      // setup db
      global $wpdb;
      // insert
      $is_success = $wpdb->insert($this->tablename, $user);
      // TODO check error
      if( $is_success ) {
        // SUCCESS!!!
        // TODO login user
        wp_clear_auth_cookie();
        wp_set_current_user ( $wpid );
        wp_set_auth_cookie  ( $wpid );
        wp_safe_redirect(site_url('/my-account'));
        exit();
      } else {
        // THROW ERROR
        // Error: problem with inserting into cdubdb
        $cdub_error = "Error entering Your Account (user-functions.php)";
        $cdub_error_desc = "There is a problem registering the details of your account. Please report this bug to the admin. SYSTEM: " . $wpdb->last_query . " : " . $wpdb->last_error;
        $cdub_error_data = $user;
        wp_safe_redirect(site_url('/register'));
      }
    } else {
      // THROW ERROR
      // Error: problem with wp_create_user
      $cdub_error = "Error entering WordPress User (user-functions.php)";
      $cdub_error_desc = "There is a problem registering your account with WordPress. Please report this bug to the admin. SYSTEM: " . $wpid->get_error_message();
      $cdub_error_data = $user;
      wp_safe_redirect(site_url('/register'));
    }
  }
}