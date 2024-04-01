<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ScorecardDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_game_report";
    $this->limit = 10;

    $this->onActivate();

    // match form actions
    add_action('admin_post_reportgame', array($this, 'reportGame'));
    add_action('admin_post_nopriv_reportgame', array($this, 'reportGame'));

  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      s bigint(20) NOT NULL DEFAULT 0,
      m bigint(20) NOT NULL DEFAULT 0,
      winner bigint(20) NOT NULL DEFAULT 0,
      wscore bigint(20) NOT NULL DEFAULT 0,
      lscore bigint(20) NOT NULL DEFAULT 0,
      apiGameID varchar(255) NOT NULL DEFAULT '',
      PRIMARY KEY (id)
    ) $this->charset;");
  }

  /* ========================== */
  /* ====== FORM ACTIONS ====== */
  /* ========================== */
  function reportGame() {
    $response_code = 0;
    // GET PRESET VARS
    $m = sanitize_text_field($_POST['field-match']);
    $s = sanitize_text_field($_POST['field-season']);

    // Make new data obj
    // if POST value has a value, add to atten array
    // field name = post value
    $new_game = array();
    if( $_POST['field-winner'] ) { $new_game['winner'] = sanitize_text_field($_POST['field-winner']); }
    if( $_POST['field-gameID'] ) { $new_game['apiGameID'] = sanitize_text_field($_POST['field-gameID']); }
    if( $_POST['field-wscore'] ) { $new_game['wscore'] = sanitize_text_field($_POST['field-wscore']); }
    if( $_POST['field-lscore'] ) { $new_game['lscore'] = sanitize_text_field($_POST['field-lscore']); }
    // PRESET VARS
    $new_game['m'] = $m;
    $new_game['s'] = $s;

    // SETUP DB
    global $wpdb;
    // CREATE ROW
    $wpdb->insert($this->tablename, $new_game);
    // GET RESPONSE
    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    // PRINT ERRORS
    // $wpdb->print_error();
    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code . $wpdb->last_error);
    exit;
  }

  /* ========================== */
  /* ===== VIEW FUNCTIONS ===== */
  /* ========================== */
  // GET SINGLE GAME BY ASSOC MATCH
  static function getGamesByMatch($match) {
    if(isset($match)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_game_report";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE m=%d";
      return $wpdb->get_results($wpdb->prepare($query, $match));
    }
    return false;
  }

  // GET SINGLE MATCH BY ID
  static function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_game_report";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }
}