<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeasonRegDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_season_reg";
    $this->limit = 10;

    $this->onActivate();
    // add_action('activate_gameface-beta/season-functions.php', array($this, 'onActivate'));

    add_action('admin_post_createreg', array($this, 'createReg'));
    add_action('admin_post_nopriv_createreg', array($this, 'createReg'));

    add_action('admin_post_togapprovereg', array($this, 'togApproveReg'));
    add_action('admin_post_nopriv_togapprovereg', array($this, 'togApproveReg'));

    add_action('admin_post_deletereg', array($this, 'deleteReg'));
    add_action('admin_post_nopriv_deletereg', array($this, 'deleteReg'));

  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      player bigint(20) NOT NULL DEFAULT 0,
      season bigint(20) NOT NULL DEFAULT 0,
      isApproved boolean NOT NULL DEFAULT 0,
      isWaitlist boolean NOT NULL DEFAULT 0,
      hrsTotal varchar(60) NOT NULL DEFAULT '',
      hrs3Months varchar(60) NOT NULL DEFAULT '',
      otherCompGames varchar(60) NOT NULL DEFAULT '',
      wantsCap boolean NOT NULL DEFAULT 0,
      partyMem varchar(60) NOT NULL DEFAULT '',
      prefPos varchar(60) NOT NULL DEFAULT '',
      otherPos varchar(60) NOT NULL DEFAULT '',
      gameUsername varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  /* GET SINGLE entry based on id */
  function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET SINGLE entry based on id */
  function getSingleBySAndP($s, $player) {
    if(isset($s) && isset($player)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season=%d AND player=%d";
      return $wpdb->get_row($wpdb->prepare($query, array($s, $player)));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET APPROVED LIST */
  function getApprovedList($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season = $id AND isApproved = 1";
      $query .= " LIMIT $this->limit";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  /* GET UNAPPROVED */
  function getUnapprovedList($s) {
    if(isset($s)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season = $s AND isApproved = 0";
      $query .= " LIMIT $this->limit";
      return $wpdb->get_results($wpdb->prepare($query, $s));
    }
    return false;
  }

  /* GET WAITLIST */
  function getWaitlist($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season = $id AND isWaitlist = 1";
      $query .= " LIMIT $this->limit";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  /* GET APPROVED WAITLIST */
  function getApprovedWaitlist($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season = $id AND isWaitlist = 1 AND isApproved = 1";
      $query .= " LIMIT $this->limit";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  /* GET APPROVED WAITLIST */
  function getUnapprovedWaitlist($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season = $id AND isWaitlist = 1 AND isApproved = 0";
      $query .= " LIMIT $this->limit";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  function createReg() {
    $response_code = 0;

    global $wpdb;

    // if POST value has a value, add to season array
    // field name = post value
    $reg = array();
    if(isset($_POST['inc-player'])) $reg['player'] = sanitize_text_field($_POST['inc-player']);
    if(isset($_POST['inc-season'])) $reg['season'] = sanitize_text_field($_POST['inc-season']);
    if(isset($_POST['inc-hrsTotal'])) $reg['hrsTotal'] = sanitize_text_field($_POST['inc-hrsTotal']);
    if(isset($_POST['inc-hrs3Months'])) $reg['hrs3Months'] = sanitize_text_field($_POST['inc-hrs3Months']);
    if(isset($_POST['inc-otherCompGames'])) $reg['otherCompGames'] = sanitize_text_field($_POST['inc-otherCompGames']);
    if(isset($_POST['inc-wantsCap'])) $reg['wantsCap'] = 1;
    if(isset($_POST['inc-partyMem'])) $reg['partyMem'] = sanitize_text_field($_POST['inc-partyMem']);
    if(isset($_POST['inc-prefPos'])) $reg['prefPos'] = sanitize_text_field($_POST['inc-prefPos']);
    if(isset($_POST['inc-otherPos'])) $reg['otherPos'] = sanitize_text_field($_POST['inc-otherPos']);
    if(isset($_POST['inc-gameUsername'])) $reg['gameUsername'] = sanitize_text_field($_POST['inc-gameUsername']);

    if(isset($_POST['inc-isWaitlist'])) $reg['isWaitlist'] = sanitize_text_field($_POST['inc-isWaitlist']);

    $wpdb->insert($this->tablename, $reg);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code);
    exit;
  }

  /* *** APPROVE / DISAPPROVE REST API *** */
  // Setup Action Route
  // use js to send player reg id in getjson method
  // handle data update in callback

  function togApproveReg() {
    $response_code = 0;
    $regid = sanitize_text_field($_POST['regid']);

    global $wpdb;
    $wpdb->show_errors();

    // if regid is valid and set
    if(isset($regid) && !empty($regid)) {
      
      // GRAB REG SINGLE
      $single = $this->getSingle($regid);
      $isApproved = $single->isApproved;

      // TOGGLE isApproved AND UPDATE
      $where = [ 'id' => $regid ];
      $toggleData = ['isApproved' => !$isApproved];
      $wpdb->update($this->tablename, $toggleData, $where);

      // UPDATE RESPONSE CODE
      $response_code = $wpdb->last_error !== '' ? 500 : 200;

    } else {
      // regid not valid or not set
      $response_code = 501;
    }
    
    // PRINT ERRORS
    $wpdb->print_error();

    wp_safe_redirect(site_url("//s//" . $_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
  }

  function deleteReg() {
    $response_code = 0;
    $regid = sanitize_text_field($_POST['regid']);

    // if regid is valid and set
    if(isset($regid) && !empty($regid)) {
      global $wpdb;
      // $wpdb->show_errors();

      $wpdb->delete($this->tablename, array('id' => $regid));

      // UPDATE RESPONSE CODE
      $response_code = $wpdb->last_error !== '' ? 500 : 200;

      // PRINT ERRORS
      // $wpdb->print_error();

    } else {
      // regid not valid or not set
      $response_code = 501;
    }

    wp_safe_redirect(site_url("//s//" . $_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
  }
}