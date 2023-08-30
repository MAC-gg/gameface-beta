<?php

/*
  Plugin Name: Gameface Beta
  Version: 0.1
  Author: MAC
  Author URI: https://www.mac.gg/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class LeagueDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "gameface_leagues";
    $this->limit = 10;

    /* FOR GET LIST */
    $this->args = $this->getArgs();
    $this->placeholders = $this->createPlaceholders();

    add_action('activate_gameface-beta/leaguedb-functions.php', array($this, 'onActivate'));
    add_action('init', array($this, 'setup_url'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);

    add_action('admin_post_createleague', array($this, 'createLeague'));
    add_action('admin_post_nopriv_createleague', array($this, 'createLeague'));

    add_action('admin_post_deleteleague', array($this, 'deleteLeague'));
    add_action('admin_post_nopriv_deleteleague', array($this, 'deleteLeague'));

    add_action('admin_post_updateleague', array($this, 'updateLeague'));
    add_action('admin_post_nopriv_updateleague', array($this, 'updateLeague'));
  }

  function loadAssets() {
    if ( get_query_var( 'l', false ) || is_page('leagues') ) {
      wp_enqueue_style('leaguetemplate', plugin_dir_url(__FILE__) . 'league-template.css');
    }
  }

  function loadTemplate($template) {
    if ( get_query_var( 'l', false ) || is_page('leagues') ) {
      return plugin_dir_path(__FILE__) . 'inc/league/league-route.php';
    }
    return $template;
  }

  function setup_url() {
    // league-route.php catches these requests
    
    // accept and access these url parameters
    global $wp; 
    $wp->add_query_var('l');
    $wp->add_query_var('lp');

    // MAKE URL PRETTY
    // ORDER MATTERS HERE
    // match cases like '/l/league-link/manage/'
    add_rewrite_rule('l/([^/]*)/?([^/]*)/?$', 'index.php?l=$matches[1]&lp=$matches[2]', 'top');
    // match cases like '/l/league-link/'
    add_rewrite_rule('l/([^/]*)/?', 'index.php?l=$matches[1]', 'top');

  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      leagueName varchar(60) NOT NULL DEFAULT '',
      leagueLink varchar(60) NOT NULL DEFAULT '',
      numTeams smallint(5) NOT NULL DEFAULT 0,
      teamSize smallint(5) NOT NULL DEFAULT 0,
      game varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;");
  }

  function deleteLeague() {
    if(current_user_can('administrator')) {
      $id = sanitize_text_field($_POST['idtodelete']);
      global $wpdb;
      $wpdb->delete($this->tablename, array('id' => $id));
      wp_safe_redirect(site_url('/leagues'));
    } else {
      wp_safe_redirect(site_url());
    }
    exit;
  }

  function createLeague() {
    if(current_user_can('administrator')) {
      $league = array();
      $league['leagueName'] = sanitize_text_field($_POST['incName']);
      $league['leagueLink'] = sanitize_text_field($_POST['incLink']);
      $league['numTeams'] = sanitize_text_field($_POST['incTeamNum']);
      $league['teamSize'] = sanitize_text_field($_POST['incTeamSize']);
      $league['game'] = sanitize_text_field($_POST['incGame']);
      global $wpdb;
      $wpdb->insert($this->tablename, $league);
      wp_safe_redirect(site_url('/leagues'));
    } else {
      wp_safe_redirect(site_url());
    }
    exit;
  }

  /* GET SINGLE LEAGUE (L) */
  function getL($l) {
    global $wpdb;
    $tablename = $wpdb->prefix . "gameface_leagues";
    $query = "SELECT * FROM $tablename WHERE leagueLink = '$l'";
    return $wpdb->get_results($wpdb->prepare($query));
  }
  /* END GET sinGLE */

  /* GET LIST using query variables from URL */
  function getList() {
    global $wpdb;
    $tablename = $wpdb->prefix . "gameface_leagues";
    $query = "SELECT * FROM $tablename ";
    $query .= $this->createWhereText();
    $query .= " LIMIT $this->limit";
    return $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
  }

  function getArgs() {
    // get args from URL and sanitize
    $temp = array(
        'leagueName' => sanitize_text_field($_GET['leagueName']),
        'leagueLink' => sanitize_text_field($_GET['leagueLink']),
        'numTeams' => sanitize_text_field($_GET['numTeams']),
        'teamSize' => sanitize_text_field($_GET['teamSize']),
        'game' => sanitize_text_field($_GET['game']),
    );

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
}

$LeagueDB = new LeagueDB();