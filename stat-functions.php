<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class GameDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_stat";
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
      game bigint(20) NOT NULL DEFAULT 0,
      match bigint(20) NOT NULL DEFAULT 0,
      season bigint(20) NOT NULL DEFAULT 0,
      WPID bigint(20) NOT NULL DEFAULT 0,
      stat1 bigint(20) NOT NULL DEFAULT 0,
      stat2 bigint(20) NOT NULL DEFAULT 0,
      stat3 bigint(20) NOT NULL DEFAULT 0,
      stat4 bigint(20) NOT NULL DEFAULT 0,
      stat5 bigint(20) NOT NULL DEFAULT 0,
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  /* ========================== */
  /* ====== FORM ACTIONS ====== */
  /* ========================== */

  /* ========================== */
  /* ===== VIEW FUNCTIONS ===== */
  /* ========================== */
  // GET SINGLE GAME BY ASSOC MATCH
  static function getGamesByMatch($match) {
    if(isset($match)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_game";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE match=%d";
      return $wpdb->get_row($wpdb->prepare($query, $match));
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
}