<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeasonDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_season";
    $this->limit = 10;

    $this->breadcrumbURL = '/seasons';

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

    // close reg
    add_action('admin_post_closereg', array($this, 'closeReg'));
    add_action('admin_post_nopriv_closereg', array($this, 'closeReg'));

    // SHORTCODES
    /* Season List */
    add_shortcode('cw_season_list', array($this, 'cw_season_list_sc_handler'));
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      manager bigint(20) NOT NULL DEFAULT 0,
      status varchar(60) NOT NULL DEFAULT '',
      title varchar(60) NOT NULL DEFAULT '',
      slug varchar(60) NOT NULL DEFAULT '',
      game varchar(60) NOT NULL DEFAULT '',
      teamNum bigint(20) NOT NULL DEFAULT 0,
      teamSize bigint(20) NOT NULL DEFAULT 0,
      waitlistSize bigint(20) NOT NULL DEFAULT 0,
      playerLvl varchar(60) NOT NULL DEFAULT '',
      matchDay varchar(60) NOT NULL DEFAULT '',
      matchTime varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  function createSeason() {
    $response_code = 0;

    global $wpdb;
    // $wpdb->show_errors();

    if(current_user_can('administrator')) {
      $season = array();

      // if POST value has a value, add to season array
      // field name = post value
      if(isset($_POST['inc-manager'])) $season['manager'] = sanitize_text_field($_POST['inc-manager']);
      if(isset($_POST['inc-title'])) $season['title'] = sanitize_text_field($_POST['inc-title']);
      if(isset($_POST['inc-slug'])) $season['slug'] = sanitize_text_field($_POST['inc-slug']);
      if(isset($_POST['inc-game'])) $season['game'] = sanitize_text_field($_POST['inc-game']);
      if(isset($_POST['inc-teamNum'])) $season['teamNum'] = sanitize_text_field($_POST['inc-teamNum']);
      if(isset($_POST['inc-teamSize'])) $season['teamSize'] = sanitize_text_field($_POST['inc-teamSize']);
      if(isset($_POST['inc-waitlistSize'])) $season['waitlistSize'] = sanitize_text_field($_POST['inc-waitlistSize']);
      if(isset($_POST['inc-playerLvl'])) $season['playerLvl'] = sanitize_text_field($_POST['inc-playerLvl']);
      if(isset($_POST['inc-matchDay'])) $season['matchDay'] = sanitize_text_field($_POST['inc-matchDay']);
      if(isset($_POST['inc-matchTime'])) $season['matchTime'] = sanitize_text_field($_POST['inc-matchTime']);

      $season['status'] = "Registering";

      $wpdb->insert($this->tablename, $season);

      // UPDATE RESPONSE CODE
      $response_code = $wpdb->last_error !== '' ? 500 : 200;

    } else {
      // user not admin
      $response_code = 401;
    }

    // PRINT ERRORS
    $wpdb->print_error();

    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code);
    exit;
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

  /* GET SINGLE SEASON BY SLUG (S) */
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

  /* GET SINGLE SEASON BY ID (S) */
  function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $this->tablename;
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
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

  function progressSeasonStatus($id) {
    global $wpdb;

    // if id is valid and set
    if(isset($id) && !empty($id)) {
      
      // GRAB Season SINGLE
      $single = $this->getSingle($id);
      $current_status = $single->status;
      $new_status = "";

      switch($current_status){
        case 'Registering':
          $new_status = "Creating Teams";
          break;
        case 'Creating Teams':
          $new_status = "Finalizing Schedule";
          break;
        case 'Finalizing Schedule':
          $new_status = "Regular Season";
          break;
      }

      // TOGGLE isApproved AND UPDATE
      $where = [ 'id' => $id ];
      $new_status_data = ['status' => $new_status];
      $wpdb->update($this->tablename, $new_status_data, $where);

      // UPDATE RESPONSE CODE
      return $wpdb->last_error !== '' ? false : true;
    }

    //something went wrong
    return false;
  }

  function closeReg() {
    $response_code = 0;
    $s = sanitize_text_field($_POST['season']);

    if(isset($s) && !empty($s)) {
      // update season status
      $diditwork = $this->progressSeasonStatus($s);
      if($diditwork) {
        // it worked
        $response_code = 200;
        // do anything extra here
        // OPEN WAITLIST
      } else {
        // something went wrong when updating the season status
        $response_code = 502;
      }
    } else {
      // something wrong with season id
      $response_code = 501;
    }

    wp_safe_redirect(site_url($_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
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