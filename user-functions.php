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

    /* CREATE USER IN WP for password management */
    $username = "";
    $password = "";
    $email = "";
    if(isset($_POST['incUsername'])) $username = sanitize_text_field($_POST['incUsername']);
    if(isset($_POST['incPass'])) $password = sanitize_text_field($_POST['incPass']);
    if(isset($_POST['incEmail'])) $email = sanitize_text_field($_POST['incEmail']);

    // save wpid for our own db
    $wpid = wp_create_user($username, $password, $email);

    // create user object to insert into our db
    $user = array();
    // if POST value has a value, add to user array
    // field name = post value
    $user['WPID'] = $wpid;
    $user['Email'] = $email;
    $user['Username'] = $username;
    if(isset($_POST['incGamertag'])) $user['Gamertag'] = sanitize_text_field($_POST['incGamertag']);

    global $wpdb;
    $wpdb->insert($this->tablename, $user);

    if ( !is_wp_error( $wpid ) ) {
      wp_safe_redirect(site_url('/my-account'));
    } else {
      wp_safe_redirect(site_url('/register'));
    }
  }
}