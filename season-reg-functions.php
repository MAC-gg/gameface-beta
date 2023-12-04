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

    add_action('admin_post_approvereg', array($this, 'approveReg'));
    add_action('admin_post_nopriv_approvereg', array($this, 'approveReg'));

    add_action('admin_post_deletereg', array($this, 'deleteReg'));
    add_action('admin_post_nopriv_deletereg', array($this, 'deleteReg'));

    /* SETUP APPROVE / DISAPPROVE ROUTES */
    add_action('rest_api_init', 'cwRegApproveRoutes');

  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      player bigint(20) NOT NULL DEFAULT 0,
      season bigint(20) NOT NULL DEFAULT 0,
      isApproved boolean NOT NULL DEFAULT 0,
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

  /* GET SINGLE entry based on player id (S) */
  function getSingle($s) {
    if(isset($s)) {
      global $wpdb;
      $tablename = $this->tablename;
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE player=%d";
      return $wpdb->get_row($wpdb->prepare($query, $s));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET APPROVED LIST */
  function getApprovedList($s) {
    if(isset($s)) {
      global $wpdb;
      $query = "SELECT * FROM $this->tablename ";
      $query .= "WHERE season = $s AND isApproved = 1";
      $query .= " LIMIT $this->limit";
      return $wpdb->get_results($wpdb->prepare($query, $s));
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

  function createReg() {
    $reg = array();

    // if POST value has a value, add to season array
    // field name = post value

    /*
    player bigint(20) NOT NULL DEFAULT 0,
    season bigint(20) NOT NULL DEFAULT 0,
    isApproved BOOL NOT NULL DEFAULT 0,
    hrsTotal varchar(60) NOT NULL DEFAULT '',
    hrs3Months varchar(60) NOT NULL DEFAULT '',
    otherCompGames varchar(60) NOT NULL DEFAULT '',
    wantsCap BOOL NOT NULL DEFAULT '',
    partyMem varchar(60) NOT NULL DEFAULT '',
    prefPos varchar(60) NOT NULL DEFAULT '',
    otherPos varchar(60) NOT NULL DEFAULT '',
    gameUsername varchar(60) NOT NULL DEFAULT '',
    */
    
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

    global $wpdb;
    $wpdb->insert($this->tablename, $reg);

    wp_safe_redirect(site_url('//s//' . $_POST['redirect']));
    exit;
  }

  /* *** APPROVE / DISAPPROVE REST API *** */
  // Setup Action Route
  // use js to send player reg id in getjson method
  // handle data update in callback

  /* ROUTES */
  function cwRegApproveRoutes() {
      register_rest_route('cw/v1', 'manageReg', array(
          'methods' => 'POST',
          'callback' => 'togApproveReg'
      ));

      register_rest_route('cw/v1', 'manageReg', array(
          'methods' => "DELETE",
          'callback' => 'deleteReg'
      ));
  }

  function togApproveReg() {
    $regid = sanitize_text_field($data['regid']);

    global $wpdb;
    // $wpdb->show_errors();

    if(!empty($regid)) {
      // if profile data is set, UPDATE
      $where = [ 'id' => $regid ];
      $toggleData = ['isApproved' => 'NOT isApproved'];
      $wpdb->update($this->tablename, $toggleData, $where);
    }

    exit;
  }

  function deleteReg() {
     
  }
}