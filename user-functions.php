<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class UserDB {
  function __construct() {

    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->limit = 10;

    // two tables, one for account, one for profile
    $this->ACCTtablename = $wpdb->prefix . "cw_user_acct";
    $this->PROFtablename = $wpdb->prefix . "cw_user_prof";
    
    $this->onActivate();

    // ACTION HOOK SETUP
    // register WP user action hook
    add_action('admin_post_registeruser', array($this, 'registerUser'));
    add_action('admin_post_nopriv_registeruser', array($this, 'registerUser'));

    // edit profile action hook
    add_action('admin_post_updateprofile', array($this, 'updateProfile'));
    add_action('admin_post_nopriv_updateprofile', array($this, 'updateProfile'));

    // edit account action hook
    add_action('admin_post_updateaccount', array($this, 'updateAccount'));
    add_action('admin_post_nopriv_updateaccount', array($this, 'updateAccount'));
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->ACCTtablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      WPID bigint(20) NOT NULL DEFAULT 0,
      fname varchar(60) NOT NULL DEFAULT '',
      lname varchar(60) NOT NULL DEFAULT '',
      address1 varchar(60) NOT NULL DEFAULT '',
      address2 varchar(60) NOT NULL DEFAULT '',
      city varchar(60) NOT NULL DEFAULT '',
      state varchar(60) NOT NULL DEFAULT '',
      zip varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY (id)
    ) $this->charset;");

    dbDelta("CREATE TABLE $this->PROFtablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      WPID bigint(20) NOT NULL DEFAULT 0,
      roles varchar(60) NOT NULL DEFAULT '',
      nickname varchar(60) NOT NULL DEFAULT '',
      cover_img varchar(256) NOT NULL DEFAULT '',
      prof_img varchar(256) NOT NULL DEFAULT '',
      color_1 varchar(60) NOT NULL DEFAULT '',
      color_2 varchar(60) NOT NULL DEFAULT '',
      status varchar(60) NOT NULL DEFAULT '',
      discord_username varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY (id)
    ) $this->charset;");
  }

  function registerUser() {
    $response_code = 0;

    $username = sanitize_text_field($_POST['field-username']);
    $password = sanitize_text_field($_POST['field-pass']);
    $email = sanitize_text_field($_POST['field-email']);

    $user_id = username_exists( $username );
    if ( ! $user_id && email_exists( $email ) == false ) {
      // username and email do NOT exist
      // register user with WP
      $user_id = wp_create_user( $username, $password, $email );

      if ( ! is_wp_error( $user_id ) ) {
        // Get User Meta Data (Sensitive, Password included. DO NOT pass to front end.)
        $user = get_user_by('id', $user_id);
        // $user->set_role( $role );
        $user->set_role('subscriber');

        // LOG USER IN
        wp_clear_auth_cookie();
        wp_set_current_user($user->data->ID);
        wp_set_auth_cookie($user->data->ID);

        // SUCCESS
        $response_code = 200;
      } else {
        // error with wp_create_user
        $response_code = $user_id->get_error_message() . $username;
      }
    } else {
      // error: username or email is taken
      $response_code = 501;
    }

    // redirect
    wp_safe_redirect(site_url('/u//' . strtolower($username) . '?cw-svr-status=' . $response_code));
    exit;
  }

  function updateProfile( $request = null ) {
    // Determine if we need to CREATE a new row or UPDATE a row
    // get Profile Data
    $WPID = "";
    if(isset($_POST['WPID'])) $WPID = sanitize_text_field($_POST['WPID']);
    $profileData = $this->getProfile($WPID);

    // setup data
    $profile = array();
    // if POST value has a value, add to user array
    // SQL field name = post field value
    if(isset($_POST['field-banner-img-url'])) $profile['cover_img'] = sanitize_text_field($_POST['field-banner-img-url']);
    if(isset($_POST['field-profile-img-url'])) $profile['prof_img'] = sanitize_text_field($_POST['field-profile-img-url']);
    if(isset($_POST['field-color-1'])) $profile['color_1'] = sanitize_text_field($_POST['field-color-1']);
    if(isset($_POST['field-color-2'])) $profile['color_2'] = sanitize_text_field($_POST['field-color-2']);
    if(isset($_POST['field-display-name'])) $profile['nickname'] = sanitize_text_field($_POST['field-display-name']);
    if(isset($_POST['field-status'])) $profile['status'] = sanitize_text_field($_POST['field-status']);
    if(isset($_POST['field-discord-username'])) $profile['discord_username'] = sanitize_text_field($_POST['field-discord-username']);

    global $wpdb;
    // $wpdb->show_errors();

    if(!empty($profileData)) {
      // if profile data is set, UPDATE
      $where = [ 'WPID' => $WPID ];
      $wpdb->update($this->PROFtablename, $profile, $where);
    } else {
      // if profile data is NOT set, CREATE
      $profile['WPID'] = $WPID; // add WPID, not needed for update
      $wpdb->insert($this->PROFtablename, $profile);
    }

    // $wpdb->print_error();
    $response_code = 0;

    if($wpdb->last_error !== '') {
      $response_code = 500;
    } else {
      $response_code = 200;
    }

    // redirect
    wp_safe_redirect(site_url('/u//' . strtolower($profile['nickname']) . '/edit?cw-svr-status=' . $response_code));
    exit;
  }

  function updateAccount( $request = null ) {
    // Determine if we need to CREATE a new row or UPDATE a row
    // get Account Data
    $WPID = "";
    if(isset($_POST['WPID'])) $WPID = sanitize_text_field($_POST['WPID']);
    $accountData = $this->getAccount($WPID);
    $profile = $this->getProfile($WPID);

    // setup data
    $account = array();
    // if POST value has a value, add to user array
    // SQL field name = post field value
    if(isset($_POST['field-fname'])) $account['fname'] = sanitize_text_field($_POST['field-fname']);
    if(isset($_POST['field-lname'])) $account['lname'] = sanitize_text_field($_POST['field-lname']);
    if(isset($_POST['field-address-1'])) $account['address1'] = sanitize_text_field($_POST['field-address-1']);
    if(isset($_POST['field-address-2'])) $account['address2'] = sanitize_text_field($_POST['field-address-2']);
    if(isset($_POST['field-city'])) $account['city'] = sanitize_text_field($_POST['field-city']);
    if(isset($_POST['field-state'])) $account['state'] = sanitize_text_field($_POST['field-state']);
    if(isset($_POST['field-zip'])) $account['zip'] = sanitize_text_field($_POST['field-zip']);

    global $wpdb;
    // $wpdb->show_errors();

    if(!empty($accountData)) {
      // if acct data is set, UPDATE
      $where = [ 'WPID' => $WPID ];
      $wpdb->update($this->ACCTtablename, $account, $where);
    } else { 
      // if acct data is NOT set, CREATE
      $account['WPID'] = $WPID; // add WPID, not needed for update
      $wpdb->insert($this->ACCTtablename, $account);
    }

    // $wpdb->print_error();
    $response_code = 0;

    if($wpdb->last_error !== '') {
      $response_code = 500;
    } else {
      $response_code = 200;
    }

    // redirect
    wp_safe_redirect(site_url('/u//' . strtolower($profile->nickname) . '/account?cw-svr-status=' . $response_code));
    exit;
  }

  function getAccount( $id ) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $this->ACCTtablename;
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE WPID=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }
  
  function getProfile( $id ) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $this->PROFtablename;
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE WPID=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }
}