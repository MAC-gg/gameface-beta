<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TeamDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_team";
    $this->limit = 10;

    $this->onActivate();
    // add_action('activate_gameface-beta/season-functions.php', array($this, 'onActivate'));

    add_action('admin_post_updateteam', array($this, 'updateTeam'));
    add_action('admin_post_nopriv_updateteam', array($this, 'updateTeam'));

  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      season bigint(20) NOT NULL DEFAULT 0,
      capt bigint(20) NOT NULL DEFAULT 0,
      title varchar(60) NOT NULL DEFAULT '',
      slug varchar(60) NOT NULL DEFAULT '',
      playerList varchar(60) NOT NULL DEFAULT '',
      color varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  static function createTeam($team, $season, $captId, $playerIds) {
    $response_code = 0;
    
    global $wpdb;
    $wpdb->show_errors();
    $tablename = $wpdb->prefix . "cw_team";

    $slug = str_replace(" ", "-", strtolower($team) );

    $new_team = array();
    $new_team['title'] = sanitize_text_field($team);
    $new_team['slug'] = sanitize_text_field($slug);
    $new_team['season'] = sanitize_text_field($season);
    $new_team['capt'] = sanitize_text_field($captId);
    $new_team['playerList'] = sanitize_text_field($playerIds);

    $wpdb->insert($tablename, $new_team);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    $wpdb->print_error();
    return $response_code;
  }

  /* GET SINGLE SEASON BY SLUG (S) */
  static function getS($s) {
    if(isset($s)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_team";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE slug=%s";
      return $wpdb->get_row($wpdb->prepare($query, $s));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET SINGLE SEASON BY ID (S) */
  static function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_team";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET LIST using query variables from URL */
  static function getList() {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_team";
    $query = "SELECT * FROM $tablename ";
    return $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
  }
}