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

    add_action('admin_post_savetempteams', array($this, 'saveTempTeams'));
    add_action('admin_post_nopriv_savetempteams', array($this, 'saveTempTeams'));

    add_action('admin_post_removetempteam', array($this, 'removeTempTeam'));
    add_action('admin_post_nopriv_removetempteam', array($this, 'removeTempTeam'));

    add_action('admin_post_maketeamcapt', array($this, 'togMakeTeamCapt'));
    add_action('admin_post_nopriv_maketeamcapt', array($this, 'togMakeTeamCapt'));

    add_action('admin_post_maketeams', array($this, 'makeTeams'));
    add_action('admin_post_nopriv_maketeams', array($this, 'makeTeams'));

  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      player bigint(20) NOT NULL DEFAULT 0,
      season bigint(20) NOT NULL DEFAULT 0,
      hrsTotal varchar(60) NOT NULL DEFAULT '',
      hrs3Months varchar(60) NOT NULL DEFAULT '',
      otherCompGames varchar(60) NOT NULL DEFAULT '',
      wantsCap boolean NOT NULL DEFAULT 0,
      partyMem varchar(60) NOT NULL DEFAULT '',
      prefPos varchar(60) NOT NULL DEFAULT '',
      otherPos varchar(60) NOT NULL DEFAULT '',
      gameUsername varchar(60) NOT NULL DEFAULT '',
      isApproved boolean NOT NULL DEFAULT 0,
      isWaitlist boolean NOT NULL DEFAULT 0,
      tempTeam varchar(60) NOT NULL DEFAULT '',
      tempTeamCapt boolean NOT NULL DEFAULT 0,
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  /* GET SINGLE entry based on id */
  static function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET SINGLE entry based on id */
  static function getSingleBySAndP($s, $player) {
    if(isset($s) && isset($player)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE season=%d AND player=%d";
      return $wpdb->get_row($wpdb->prepare($query, array($s, $player)));
    }
    return false;
  }
  /* END GET SINGLE */

  /* GET APPROVED LIST */
  static function getApprovedList($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE season = %d AND isApproved = 1 AND isWaitlist = 0";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  /* GET UNAPPROVED */
  static function getUnapprovedList($s) {
    if(isset($s)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE season = $s AND isApproved = 0 AND isWaitlist = 0";
      return $wpdb->get_results($wpdb->prepare($query, $s));
    }
    return false;
  }

  /* GET APPROVED WAITLIST */
  static function getApprovedWaitlist($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE season = $id AND isWaitlist = 1 AND isApproved = 1";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  /* GET APPROVED WAITLIST */
  static function getUnapprovedWaitlist($id) {
    if(isset($id) && !empty($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE season = $id AND isWaitlist = 1 AND isApproved = 0";
      return $wpdb->get_results($wpdb->prepare($query, $id));
    }
    return false;
  }

  /* GET PROJ TEAM LIST */
  static function getProjTeamList($season, $team) {
    $values = array();
    array_push($values, $season);
    array_push($values, $team);

    if(isset($season) && !empty($season)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_season_reg";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE season = %d AND tempTeam = %s AND isWaitlist=0";
      return $wpdb->get_results($wpdb->prepare($query, $values));
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

  function saveTempTeams() {
    $response_code = 0;
    $season = sanitize_text_field($_POST['season']);
    $teamNum = sanitize_text_field($_POST['teamNum']);
    $teamSize = sanitize_text_field($_POST['teamSize']);

    global $wpdb;
    // $wpdb->show_errors();

    // getProjTeamList($season, $team)

    for($i = 1; $i <= $teamNum; $i++) {
      $team = "Team " . $i;

      // Get keys for Team X
      $keys = array_keys($_POST, $team);
      // if keys (# of players) > teamSize -> break
      if(count($keys) > $teamSize) {
        // TOO MANY PLAYERS ON THE TEAM
        $response_code = "cw503"; break;
      }

      // get current team count
      $current_count = count($this->getProjTeamList($season, "Team $i"));
      // if # of current players + # of new players > teamSize -> break
      if ( ($current_count + count($keys)) > $teamSize ) {
        // TOO MANY PLAYERS ON THE TEAM
        $response_code = "cw503"; break;
      }

      if( $response_code == 500 ) { break; }
      foreach($keys as $key) {
        $player = str_replace("inc-tempTeam-", "", $key);

        $where = [ 'season' => $season, 'player' => $player ];
        $data = ['tempTeam' => sanitize_text_field($_POST[$key]) ];
        $wpdb->update($this->tablename, $data, $where);
        // UPDATE RESPONSE CODE
        $response_code = $wpdb->last_error !== '' ? 500 : 200;
      }
    }

    // PRINT ERRORS
    // $wpdb->print_error();

    wp_safe_redirect(site_url($_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
  }

  function removeTempTeam() {
    $response_code = 0;
    $regid = sanitize_text_field($_POST['regid']);

    global $wpdb;
    // $wpdb->show_errors();

    // if regid is valid and set
    if(isset($regid) && !empty($regid)) {

      // TOGGLE isApproved AND UPDATE
      $where = [ 'id' => $regid ];
      $data = ['tempTeam' => ""];
      $wpdb->update($this->tablename, $data, $where);

      // UPDATE RESPONSE CODE
      $response_code = $wpdb->last_error !== '' ? 500 : 200;

    } else {
      // regid not valid or not set
      $response_code = 501;
    }
    
    // PRINT ERRORS
    // $wpdb->print_error();

    wp_safe_redirect(site_url($_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
  }

  function togMakeTeamCapt() {
    $response_code = 0;
    $regid = sanitize_text_field($_POST['regid']);

    global $wpdb;
    // $wpdb->show_errors();

    // if regid is valid and set
    if(isset($regid) && !empty($regid)) {

      // GRAB REG SINGLE
      $single = $this->getSingle($regid);
      $tempTeam = $single->tempTeam;
      $tempTeamCapt = $single->tempTeamCapt;
      
      if( !$tempTeamCapt ) {
        // VALIDATE
        // grab proj team list
        $tempTeamList = $this->getProjTeamList($single->season, $single->tempTeam);
        $isUniqueCapt = true;
        foreach( $tempTeamList as $player_reg ) {
          // loop thru each player
          // if any player is already capt
          if( $player_reg->id == $regid ) { continue; }
          if( $player_reg->tempTeamCapt == 1 ) {
            $isUniqueCapt = false;
            break;
          }
        }

        if( $isUniqueCapt ) {
          // good to make capt
          $where = [ 'id' => $regid ];
          $data = ['tempTeamCapt' => 1];
          $wpdb->update($this->tablename, $data, $where);

          // UPDATE RESPONSE CODE
          $response_code = $wpdb->last_error !== '' ? 500 : 200;
        } else {
          // not a unique capt
          $response_code = "cw504";
        }
      } else {
        // just turn it off
        $where = [ 'id' => $regid ];
        $data = ['tempTeamCapt' => 0];
        $wpdb->update($this->tablename, $data, $where);

        // UPDATE RESPONSE CODE
        $response_code = $wpdb->last_error !== '' ? 500 : 200;
      }

    } else {
      // regid not valid or not set
      $response_code = 501;
    }
    
    // PRINT ERRORS
    // $wpdb->print_error();

    wp_safe_redirect(site_url($_POST['redirect'] . "?cw-svr-status=" . $response_code));
    exit;
  }

  function makeTeams() {
    $response_code = 0;
    $season = sanitize_text_field($_POST['season']);
    $teamSize = sanitize_text_field($_POST['teamSize']);
    $teamNum = sanitize_text_field($_POST['teamNum']);

    // VALIDATE - ensure teams are correct size with 1 capt each
    // loop thru teams
    for($i = 1; $i <= $teamNum; $i++) {
      // get proj team
      $playerList = self::getProjTeamList($season, "Team " . $i);
      
      // TEAM SIZE
      // if count is not equal to team size, break
      if( count($playerList) != $teamSize ) {
        $response_code = 501; break;
      }

      // UNIQUE CAPT
      // loop thru each player to see who is captain
      $total_capts = 0;
      foreach( $playerList as $player_reg ) {
        if( $player_reg->tempTeamCapt ) { $total_capts++; }
      }
      // if there is more than one capt, break
      if( $total_capts > 1 ) {
        $response_code = 502; break;
      }

      // END VALIDATION
      // START DB INSERT
      // create player list
      // create capt id
      $playerIds = ",";
      $captId = "";
      foreach( $playerList as $player_reg ) {
        // loop thru and get list of player ids
        if( $playerIds == "" ) {
          $playerIds .= $player_reg->player;
        } else {
          $playerIds .= "," . $player_reg->player;
        }

        // loop thru and get capt id
        if( $player_reg->tempTeamCapt ) {
          $captId = $player_reg->id;
        }
      }
      $playerIds .= ",";

      // create team, statically
      $response_code = TeamDB::createTeam("Team " . $i, $season, $captId, $playerIds);
    }

    if( $response_code == 200 ) {
      // update season status
      SeasonDB::progressSeasonStatus($season);

      // clean up registrations
      self::cleanupReg($season);
    }

    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code);
    exit;
  }

  static function cleanupReg($season) {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_season_reg";
    $wpdb->delete($tablename, array('season' => $season, 'isWaitlist' => 0));

    // RETURN RESPONSE
    return $wpdb->last_error !== '' ? false : true;
  }
}