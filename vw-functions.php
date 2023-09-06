<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class VW {
  function __construct() {
    // add_action('activate_gameface-beta/vw-functions.php', array($this, 'onActivate'));
    add_action('init', array($this, 'setup_url'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);
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

    if ( get_query_var( 'u', false ) || is_page('my-account') || is_page('register') ) {
      return plugin_dir_path(__FILE__) . 'inc/user/user-route.php';
    }

    return $template;
  }

  function setup_url() {
    // accept and access these url parameters
    global $wp;
    // league
    $wp->add_query_var('l');
    $wp->add_query_var('lp');
    // user
    $wp->add_query_var('u');
    $wp->add_query_var('up');

    // MAKE URL PRETTY
    // ORDER MATTERS HERE
    // match cases like '/l/league-link/manage/'
    add_rewrite_rule('l/([^/]*)/?([^/]*)/?$', 'index.php?l=$matches[1]&lp=$matches[2]', 'top');
    // match cases like '/l/league-link/'
    add_rewrite_rule('l/([^/]*)/?', 'index.php?l=$matches[1]', 'top');

    // match cases like '/u/user-link/manage/'
    add_rewrite_rule('u/([^/]*)/?([^/]*)/?$', 'index.php?u=$matches[1]&up=$matches[2]', 'top');
    // match cases like '/u/user-link/'
    add_rewrite_rule('u/([^/]*)/?', 'index.php?u=$matches[1]', 'top');

  }

  function getVal($key) {
    global $cdub_error_data;
    $result = $cdub_error_data[$key];

    if($result) { return $result; }
    return "";
  }

  function handleError() {
    global $cdub_error;
    global $cdub_error_desc; ?>
      <h2><?php echo "test"; ?>Error: <?php echo $cdub_error; ?></h2>
      <p>Error Description: <?php echo $cdub_error_desc; ?></p>
  <?php }

  static function clearErrors() {
    global $cdub_error;
    global $cdub_error_desc;
    global $cdub_error_data;

    $cdub_error = "";
    $cdub_error_desc = "";
    $cdub_error_data = array();
  }

  function sendErrors() {
    global $cdub_error;
    global $cdub_error_desc;
    global $cdub_error_data;

    $cdub_error = "test";
    $cdub_error_desc = "this is a test error description";
    $cdub_error_data->name = "testName";
    $cdub_error_data->email = "test@email.com";
  }

  function getHeader() {
    $l = sanitize_text_field(get_query_var('l'));
    $lp = sanitize_text_field(get_query_var('lp'));
    
    $LeagueDB = new LeagueDB();
    // quick actions?
    // breadcrumbs?

    if($l && $lp) {
      // League Sub page
      $leagueName = $LeagueDB->getL($l)[0]->leagueName;
      echo "<strong>League:</strong> <a href='/l/$l/'>$leagueName</a> > $lp";
      echo "<h1>" . $lp . "</h1>";
    } elseif ($l) {
      // league single
      $leagueName = $LeagueDB->getL($l)[0]->leagueName;
      echo "<a href='/leagues/'>< League List</a>";
      echo "<h1>League: " . $leagueName . "</h1>";
    } else {
      // league list
      echo "<h1>League List</h1>";
    }
  }
}