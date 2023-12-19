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
      color1 varchar(60) NOT NULL DEFAULT '',
      mascot varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  static function createTeam($team, $season, $captId, $playerIds) {
    $response_code = 0;
    
    global $wpdb;
    $wpdb->show_errors();
    $tablename = $wpdb->prefix . "cw_team";

    $slug = "t" . str_replace("Team ", "", $team );

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
  static function getT($t) {
    if(isset($t)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_team";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE slug=%s";
      return $wpdb->get_row($wpdb->prepare($query, $t));
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
  static function getList($season) {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_team";
    $query = "SELECT * FROM $tablename ";
    $query .= "WHERE season=%d";
    return $wpdb->get_results($wpdb->prepare($query, $season));
  }

  // FORM ACTIONS
  function updateTeam() {
    $response_code = 0;
    $team = sanitize_text_field($_POST['field-team']);

    global $wpdb;
    // $wpdb->show_errors();

    // if POST value has a value, add to season array
    // field name = post value
    $new_team = array();
    if(isset($_POST['field-title'])) $new_team['title'] = sanitize_text_field($_POST['field-title']);
    if(isset($_POST['field-color1'])) $new_team['color1'] = sanitize_text_field($_POST['field-color1']);
    if(isset($_POST['field-mascot'])) $new_team['mascot'] = sanitize_text_field($_POST['field-mascot']);

    $where = array(
      'id' => $team
    );

    $wpdb->update($this->tablename, $new_team, $where);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code);
    exit;
  }
}