<?php

/*
  Plugin Name: [cdub] Arena Beta
  Version: 0.1
  Author: MAC
  Author URI: https://www.mac.gg/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class cdubGlobal {
  function __construct() {
    // add_action('activate_gameface-beta/vw-functions.php', array($this, 'onActivate'));
    add_action('init', array($this, 'cw_add_query_vars'));
    add_action('init', array($this, 'cw_add_rewrite_rules'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);

    // LOGIN FORM SHORTCODE
    add_shortcode( 'cw_login_form', array($this, 'cw_login_form_shortcode_handler' ));
    add_shortcode( 'cw_register_form', array($this, 'cw_register_form_shortcode_handler' ));

    // other funcs
    // --- setup pages on activate
    // --- login / logout
    // --- current_user_can / role management
    // --- get header / breadcrumbs
    // --- form setup / server communication handling
  }

  function cw_login_form_shortcode_handler($atts = [], $content = null) {
    wp_enqueue_style( 'cw_login_form' );
    return 
      "<div class='cw_login_form'>" . 
        wp_login_form( array(
          'echo' => false
        ) ) . 
      "</div>";
  }

  function cw_register_form_shortcode_handler($atts = [], $content = null) {
    wp_enqueue_style( 'register_form_view' );
    wp_enqueue_script('cdub_validation');
    wp_enqueue_script('cdub_user_actions');
    require('inc/register_form_view.php');
    return register_form_view();
  }

  function cw_add_query_vars() {
    // accept and access these url parameters
    global $wp;
    // league
    $wp->add_query_var('l');
    $wp->add_query_var('lp');
    // user
    $wp->add_query_var('u');
    $wp->add_query_var('up');
  }

  function cw_add_rewrite_rules() {
    global $wp_rewrite;

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

    $wp_rewrite->flush_rules(false);

  }

  function loadAssets() {

    wp_enqueue_script('jquery', "https://code.jquery.com/jquery-3.7.1.min.js", array(), null, true);

    /* REGISTER SHORTCODE STYLES */
    // OPTIMIZE THESE FILES
    wp_register_style( 'cw_login_form', plugin_dir_url(__FILE__) . '/bundled/css/user_styles.min.css', array(), '1.0' );
    wp_register_style( 'register_form_view', plugin_dir_url(__FILE__) . '/bundled/css/user_styles.min.css', array(), '1.0' );

    /* REGISTER SHORTCODE SCRIPTS */
    // validations
    wp_register_script('cdub_validation', plugin_dir_url(__FILE__) . '/bundled/js/validation.js', array('jquery'), null, true);

    // give the keys to the file
    wp_register_script('cdub_user_actions', plugin_dir_url(__FILE__) . '/bundled/js/user_actions.js', array('jquery'), null, true);
    // do this to generalize the getJSON url for deployment
    wp_localize_script('cdub_user_actions', 'searchData', array(
      'root_url' => get_site_url(),
      'nonce' => wp_create_nonce('wp_rest') // KEY TO USER SECCION TO ACCESS REST
    ));

    if ( get_query_var( 'u', false ) ) {
      wp_enqueue_style('cdub_user_styles', plugin_dir_url(__FILE__) . '/bundled/css/user_styles.min.css');
    }

    if ( get_query_var( 'l', false ) || is_page('leagues') ) {
      wp_enqueue_style('leaguetemplate', plugin_dir_url(__FILE__) . 'league-template.css');
    }
  }

  function loadTemplate($template) {
    if ( get_query_var( 'l', false ) || is_page('leagues') ) {
      return plugin_dir_path(__FILE__) . 'inc/league/league-route.php';
    }

    if ( get_query_var( 'u', false ) ) {
      return plugin_dir_path(__FILE__) . 'inc/user/user-route.php';
    }

    return $template;
  }

  function getHeader($args = array()) {
    // SET DEFAULT TITLE VALUE
    $title      = array_key_exists('title', $args) ? $args['title'] : get_the_title();
    $subtitle   = array_key_exists('title', $args) ? $args['title'] : get_the_title(); ?>
      <h1><?php echo $title; ?></h1>
  <?php }
}
$cdubGlobal = new cdubGlobal();

// USER FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'user-functions.php');
$UserDB = new UserDB();

// LEAGUE FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'leaguedb-functions.php');
$LeagueDB = new LeagueDB();