<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class MatchDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->MATCHtablename = $wpdb->prefix . "cw_match";
    $this->ATTENtablename = $wpdb->prefix . "cw_atten";
    $this->limit = 10;

    $this->onActivate();

    // match form actions
    add_action('admin_post_postponematch', array($this, 'postponeMatch'));
    add_action('admin_post_nopriv_postponematch', array($this, 'postponeMatch'));

    // attendance form actions
    add_action('admin_post_markattendance', array($this, 'markAttendance'));
    add_action('admin_post_nopriv_markattendance', array($this, 'markAttendance'));
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE IF NOT EXISTS $this->MATCHtablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      season bigint(20) NOT NULL DEFAULT 0,
      team1 bigint(20) NOT NULL DEFAULT 0,
      team2 bigint(20) NOT NULL DEFAULT 0,
      slug varchar(60) NOT NULL DEFAULT '',
      matchWeek varchar(60) NOT NULL DEFAULT '',
      matchDatetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      wTeam bigint(20) NOT NULL DEFAULT 0,
      lTeam bigint(20) NOT NULL DEFAULT 0,
      isReported BOOLEAN NOT NULL DEFAULT 0,
      isPostponed BOOLEAN NOT NULL DEFAULT 0,
      isCanceled BOOLEAN NOT NULL DEFAULT 0,
      PRIMARY KEY  (id)
    ) $this->charset;");

    dbDelta("CREATE TABLE IF NOT EXISTS $this->ATTENtablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      match bigint(20) NOT NULL DEFAULT 0,
      player bigint(20) NOT NULL DEFAULT 0,
      atten char(1) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  // CREATE MATCH - matches are automatically made with this function
  static function createMatch($season, $slug, $w, $t1, $t2, $when) {
    $response_code = 0;
    
    global $wpdb;
    // $wpdb->show_errors();
    $tablename = $wpdb->prefix . "cw_match";

    $new_match = array();
    $new_match['slug'] = sanitize_text_field($slug);
    $new_match['season'] = sanitize_text_field($season);
    $new_match['team1'] = sanitize_text_field($t2);
    $new_match['team2'] = sanitize_text_field($t1);
    $new_match['matchWeek'] = sanitize_text_field($w);
    $new_match['matchDatetime'] = sanitize_text_field(strtotime($when));

    $wpdb->insert($tablename, $new_match);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    // $wpdb->print_error();
    return $response_code;
  }

  /* ========================== */
  /* ====== FORM ACTIONS ====== */
  /* ========================== */
  function postponeMatch() {
    $response_code = 0;
    $m = sanitize_text_field($_POST['field-match']);
    $match = self::getSingle($m);

    global $wpdb;
    // $wpdb->show_errors();

    // if POST value has a value, add to season array
    // field name = post value
    $new_match_datetime = array();
    if( isset($_POST['field-date']) && isset($_POST['field-hours']) && isset($_POST['field-mins']) ) { 
      $new_date = strtotime(sanitize_text_field($_POST['field-date']));
      $new_datetime = mktime(sanitize_text_field($_POST['field-hours']), sanitize_text_field($_POST['field-mins']), 0, date('m', $new_date), date('d', $new_date), date('Y', $new_date));

      $new_match_datetime['matchDatetime'] = date("Y-m-d H:i:s", $new_datetime);
      $new_match_datetime['isPostponed'] = 1;
    }

    $where = array(
      'id' => $match->id
    );

    $wpdb->update($this->MATCHtablename, $new_match_datetime, $where);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    // PRINT ERRORS
    // $wpdb->print_error();
    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code . $wpdb->last_error);
    exit;
  }

  function markAttendance() {
    $response_code = 0;
    $m = sanitize_text_field($_POST['field-match']);
    $p = sanitize_text_field($_POST['field-player']);
    
    // do not allow button press if atten is unchanged

    global $wpdb;
    // $wpdb->show_errors();

    // if POST value has a value, add to atten array
    // field name = post value
    $new_atten = array();
    if( $_POST['field-atten'] ) { 

      $new_atten['atten'] = sanitize_text_field($_POST['field-atten']);
    }

    $where = array(
      'match' => $m,
      'player' => $p
    );

    $wpdb->update($this->ATTENtablename, $new_atten, $where);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    // PRINT ERRORS
    // $wpdb->print_error();
    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code . $wpdb->last_error);
    exit;
  }

  /* ========================== */
  /* ===== VIEW FUNCTIONS ===== */
  /* ========================== */
  // GET SINGLE MATCH BY SLUG (M)
  static function getM($m, $season) {
    if(isset($m) && isset($season)) {
      $values = array();
      array_push($values, $m);
      array_push($values, $season);

      global $wpdb;
      $tablename = $wpdb->prefix . "cw_match";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE slug=%d AND season=%d";
      return $wpdb->get_row($wpdb->prepare($query, $values));
    }
    return false;
  }

  // GET SINGLE MATCH BY ID
  static function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_match";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }

  // GET LIST of matches in a season
  static function getList($season) {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_match";
    $query = "SELECT * FROM $tablename ";
    $query .= "WHERE season=%d";
    return $wpdb->get_results($wpdb->prepare($query, $season));
  }

  // GET UPCOMING LIST of matches by week
  static function getUpcomingList($season, $week) {
    $values = array();
    array_push($values, $season);
    array_push($values, $week);

    global $wpdb;
    $tablename = $wpdb->prefix . "cw_match";
    $query = "SELECT * FROM $tablename ";
    $query .= "WHERE season=%d AND matchWeek=%d";
    return $wpdb->get_results($wpdb->prepare($query, $values));
  }

  static function getCurrentAtten($m, $p) {
    if(isset($m) && isset($p)) {
      $values = array();
      array_push($values, $m);
      array_push($values, $p);

      global $wpdb;
      $tablename = $wpdb->prefix . "cw_atten";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE match=%d AND player=%d";
      return $wpdb->get_row($wpdb->prepare($query, $values));
    }
    return false;
  }
}