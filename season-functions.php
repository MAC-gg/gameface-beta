<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeasonDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_season";
    $this->limit = 10;

    /* FOR GET LIST */
    $this->args = $this->getArgs();
    $this->placeholders = $this->createPlaceholders();

    $this->onActivate();
    // add_action('activate_gameface-beta/season-functions.php', array($this, 'onActivate'));

    add_action('admin_post_createseason', array($this, 'createSeason'));
    add_action('admin_post_nopriv_createseason', array($this, 'createSeason'));

    add_action('admin_post_deleteseason', array($this, 'deleteSeason'));
    add_action('admin_post_nopriv_deleteseason', array($this, 'deleteSeason'));

    add_action('admin_post_updateseason', array($this, 'updateSeason'));
    add_action('admin_post_nopriv_updateseason', array($this, 'updateSeason'));

    // SHORTCODES
     /* Season List */
     add_shortcode('cw_season_list', array($this, 'cw_season_list_sc_handler'));
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      manager bigint(20) NOT NULL DEFAULT 0,
      sstatus varchar(60) NOT NULL DEFAULT '',
      title varchar(60) NOT NULL DEFAULT '',
      slug varchar(60) NOT NULL DEFAULT '',
      game varchar(60) NOT NULL DEFAULT '',
      teamNum bigint(20) NOT NULL DEFAULT 0,
      teamSize bigint(20) NOT NULL DEFAULT 0,
      playerLvl varchar(60) NOT NULL DEFAULT '',
      dayTime varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  function deleteSeason() {
    if(current_user_can('administrator')) {
      $id = sanitize_text_field($_POST['idtodelete']);
      global $wpdb;
      $wpdb->delete($this->tablename, array('id' => $id));
      // wp_safe_redirect(site_url('/leagues'));
    } else {
      // wp_safe_redirect(site_url());
    }
    exit;
  }

  function createSeason() {
    if(current_user_can('administrator')) {
      $season = array();

      // if POST value has a value, add to season array
      // field name = post value

      /*
      manager bigint(20) NOT NULL DEFAULT 0,
      sstatus varchar(60) NOT NULL DEFAULT '',
      title varchar(60) NOT NULL DEFAULT '',
      slug varchar(60) NOT NULL DEFAULT '',
      game varchar(60) NOT NULL DEFAULT '',
      teamNum bigint(20) NOT NULL DEFAULT 0,
      teamSize bigint(20) NOT NULL DEFAULT 0,
      playerLvl varchar(60) NOT NULL DEFAULT '',
      dayTime varchar(60) NOT NULL DEFAULT '',
      */
      
      if(isset($_POST['inc-manager'])) $season['manager'] = sanitize_text_field($_POST['inc-manager']);
      if(isset($_POST['inc-title'])) $season['title'] = sanitize_text_field($_POST['inc-title']);
      if(isset($_POST['inc-slug'])) $season['slug'] = sanitize_text_field($_POST['inc-slug']);
      if(isset($_POST['inc-game'])) $season['game'] = sanitize_text_field($_POST['inc-game']);
      if(isset($_POST['inc-teamNum'])) $season['teamNum'] = sanitize_text_field($_POST['inc-teamNum']);
      if(isset($_POST['inc-teamSize'])) $season['teamSize'] = sanitize_text_field($_POST['inc-teamSize']);
      if(isset($_POST['inc-playerLvl'])) $season['playerLvl'] = sanitize_text_field($_POST['inc-playerLvl']);
      if(isset($_POST['inc-dayTime'])) $season['dayTime'] = sanitize_text_field($_POST['inc-dayTime']);

      $season['sstatus'] = "Registering";

      // add lm role to user
      // add lm to approved players?

      global $wpdb;
      $wpdb->insert($this->tablename, $season);
    } else {
      wp_safe_redirect(site_url());
    }
    exit;
  }

  /* GET SINGLE SEASON (S) */
  function getS($s) {
    if(isset($s)) {
      global $wpdb;
      $tablename = $this->tablename;
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE slug=%s";
      return $wpdb->get_row($wpdb->prepare($query, $s));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET LIST using query variables from URL */
  function getList() {
    global $wpdb;
    $query = "SELECT * FROM $this->tablename ";
    $query .= $this->createWhereText();
    $query .= " LIMIT $this->limit";
    return $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
  }

  function getArgs() {
    // get args from URL and sanitize
    $temp = array();

    // if URL param has a value, add to temp array
    // field name = url param value
    if(isset($_GET['seasonName'])) $temp['seasonName'] = sanitize_text_field($_GET['seasonName']);
    if(isset($_GET['Link'])) $temp['Link'] = sanitize_text_field($_GET['Link']);
    if(isset($_GET['TeamNum'])) $temp['TeamNum'] = sanitize_text_field($_GET['TeamNum']);
    if(isset($_GET['TeamSize'])) $temp['TeamSize'] = sanitize_text_field($_GET['TeamSize']);
    if(isset($_GET['Game'])) $temp['Game'] = sanitize_text_field($_GET['Game']);

    return array_filter($temp, function($x) { 
        return $x;
    });
  }

  function createPlaceholders() {
      return array_map(function($x) {
          return $x;
      }, $this->args);
  }

  function createWhereText() {
      $whereQuery = "";

      if(count($this->args)) {
          $whereQuery = "WHERE ";
      }

      $currentPosition = 0;
      foreach($this->args as $index => $item) {
          $whereQuery .= $this->specificQuery($index);
          if($currentPosition != count($this->args) - 1) {
              $whereQuery .= " AND ";
          }
          $currentPosition++;
      }

      return $whereQuery;
  }

  function specificQuery($index) {
      switch($index) {
          case "field_that_needs_digit":
              return "field_that_needs_digit = %d";
          default:
              return $index . " = %s";
      }
  }
  /* END GET LIST */

  function cw_season_list_sc_handler() { 
    // Start the object buffer, which saves output instead of outputting it.
    ob_start();

    include( plugin_dir_path( __FILE__ ) . 'inc/shortcodes/season-list-view.php');

    // Return everything in the object buffer.
    return ob_get_clean();
  }
}