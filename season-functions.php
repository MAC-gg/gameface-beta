<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeasonDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "cw_season";
    $this->limit = 10;

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

    // update sched
    add_action('admin_post_updatesched', array($this, 'updateSched'));
    add_action('admin_post_nopriv_updatesched', array($this, 'updateSched'));

    // start reg
    add_action('admin_post_startseason', array($this, 'startSeason'));
    add_action('admin_post_nopriv_startseason', array($this, 'startSeason'));

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
      statName1 varchar(60) NOT NULL DEFAULT '',
      statName2 varchar(60) NOT NULL DEFAULT '',
      statName3 varchar(60) NOT NULL DEFAULT '',
      statName4 varchar(60) NOT NULL DEFAULT '',
      statName5 varchar(60) NOT NULL DEFAULT '',
      teamNum bigint(20) NOT NULL DEFAULT 2,
      teamSize bigint(20) NOT NULL DEFAULT 1,
      waitlistSize bigint(20) NOT NULL DEFAULT 1,
      playerLvl varchar(60) NOT NULL DEFAULT '',
      matchDay varchar(60) NOT NULL DEFAULT '',
      matchTime varchar(60) NOT NULL DEFAULT '',
      duration bigint(20) NOT NULL DEFAULT 1,
      playoffSize bigint(20) NOT NULL DEFAULT 1,
      startDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      currentWeek bigint(20) NOT NULL DEFAULT 0,
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
      if(isset($_POST['inc-duration'])) $season['duration'] = sanitize_text_field($_POST['inc-duration']);
      if(isset($_POST['inc-playoffSize'])) $season['playoffSize'] = sanitize_text_field($_POST['inc-playoffSize']);

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
  static function getS($s) {
    if(isset($s)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season";
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
      $tablename = $wpdb->prefix . "cw_season";
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
    $tablename = $wpdb->prefix . "cw_season";
    $query = "SELECT * FROM $tablename";
    return $wpdb->get_results($query);
  }
  /* END GET LIST */

  static function progressSeasonStatus($id) {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_season";

    // if id is valid and set
    if(isset($id) && !empty($id)) {
      
      // GRAB Season SINGLE
      $single = self::getSingle($id);
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
      $wpdb->update($tablename, $new_status_data, $where);

      // UPDATE RESPONSE CODE
      return $wpdb->last_error !== '' ? 500 : 200;
    }

    //something went wrong
    return 500;
  }

  static function progressSeasonWeek($id) {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_season";

    // if id is valid and set
    if(isset($id) && !empty($id)) {
      
      // GRAB Season SINGLE
      $single = self::getSingle($id);
      $current_week = $single->currentWeek;
      $new_week = $current_week++;

      // TOGGLE isApproved AND UPDATE
      $where = [ 'id' => $id ];
      $new_week_data = ['currentWeek' => $new_week];
      $wpdb->update($tablename, $new_week_data, $where);

      // UPDATE RESPONSE CODE
      return $wpdb->last_error !== '' ? 500 : 200;
    }

    //something went wrong
    return 500;
  }

  function closeReg() {
    $response_code = 0;
    $s = sanitize_text_field($_POST['season']);

    if(isset($s) && !empty($s)) {
      // update season status
      $response_code = self::progressSeasonStatus($s);
      if($response_code != 200) {
        // it didn't worked
        $response_code = 502;
      }
    } else {
      // something wrong with season id
      $response_code = 501;
    }

    wp_safe_redirect(site_url($_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
  }

  function updateSched() {
    $response_code = 0;
    $s = sanitize_text_field($_POST['season']);

    global $wpdb;
    // $wpdb->show_errors();

    // if POST value has a value, add to season array
    // field name = post value
    $new_season = array();
    if(isset($_POST['field-startdate'])) $new_team['startDate'] = sanitize_text_field($_POST['field-startdate']);

    $where = array(
      'id' => $s
    );

    $wpdb->update($this->tablename, $new_team, $where);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code);
    exit;
  }

  function startSeason() {
    $response_code = 0;
    $s = sanitize_text_field($_POST['season']);
    $season = self::getSingle($s);
    $team_list = TeamDB::getList($s);
    $duration = $season->duration;
    $matchDate = isset($season->startDate) ? strtotime($season->startDate) : strtotime($season->matchDay);
    $matchHour = explode(':', $season->matchTime)[0];
    $matchMins = explode(':', $season->matchTime)[1];
    $matchDatetime = mktime($matchHour, $matchMins, 0, date('m', $matchDate), date('d', $matchDate), date('Y', $matchDate));

    $odd = array();
    $even = array();
    foreach ($team_list as $k => $v) {
        if ($k % 2 == 0) {
            $even[] = $v;
        } else {
            $odd[] = $v;
        }
    }

    $tempMatchDate = $matchDatetime;
    $m = 1;
    // Loop thru the weeks
    for($i = 1; $i <= $duration; $i++) {
      // loop thru the teams
      for( $j = 0; $j < count($even); $j++ ) {
        // create datetime
        $tempMatchDatetime = mktime($matchHour, $matchMins, 0, date('m', $tempMatchDate), date('d', $tempMatchDate), date('Y', $tempMatchDate));

        // Create match
        $response_code = MatchDB::createMatch($s, $m++, $i, $even[$j]->id, $odd[$j]->id, date("Y-m-d H:i:s", $tempMatchDatetime));

        // if error, break
        if($response_code != 200) { break; }
      }

      // Update lists and date for the loop
      if(count($even)+count($odd)-1 > 2){
          // array_unshift - adds to front of array
          // array_shift - returns the first item in array
          // array_spice - cut array, where, how long
          // array_push - add to the back of the array
          // array_pop - returns the last item in array
          array_unshift( $even, array_shift( array_splice( $odd,0,1 ) ) );
          array_push( $odd, array_pop($even) );
      }
      $tempMatchDate = strtotime("+7 days", $tempMatchDate);

      // if error, break
      if($response_code != 200) { break; }
    }

    if($response_code == 200) {
      $response_code = self::progressSeasonStatus($s);
    }

    if($response_code == 200) {
      $response_code = self::progressSeasonWeek($s);
    }

    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code);
    exit;
  }

  function cw_season_list_sc_handler() { 
    // Start the object buffer, which saves output instead of outputting it.
    ob_start();

    include( plugin_dir_path( __FILE__ ) . 'inc/shortcodes/season-list-view.php');

    // Return everything in the object buffer.
    return ob_get_clean();
  }
}