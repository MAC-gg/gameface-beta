<?php

/*
  Plugin Name: [cw] Arena Beta
  Version: 0.1
  Author: MAC
  Author URI: https://www.mac.gg/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class cwGlobal {
  function __construct() {

    $this->plugin_url = plugin_dir_url(__FILE__);

    // ACTIONS
    // add_action('activate_gameface-beta/vw-functions.php', array($this, 'onActivate'));
    add_action('init', array($this, 'setup_url_rewrites'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));

    // FILTERS
    add_filter('query_vars', array($this, 'setup_url_vars'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);

    // other funcs
    // --- setup pages on activate
    // --- login / logout
    // --- current_user_can / role management
    // --- get header / breadcrumbs
    // --- form setup / server communication handling
  }

  function setup_url_vars( $vars ) {

    // user
    $vars[] = "u";
    $vars[] = "up";
    return $vars;

  }

  function setup_url_rewrites() {

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

    global $wp_rewrite;
    $wp_rewrite->flush_rules(false);

  }

  function loadAssets() {

    wp_enqueue_script('jquery', "https://code.jquery.com/jquery-3.7.1.min.js", array(), null, true);

    // register scripts to be called later in shortcodes and on certain pages
    // STYLES 
    wp_register_style( 'cw_login_form', plugin_dir_url(__FILE__) . '/bundled/css/user_styles.min.css', array(), '1.0' );
    wp_register_style( 'cw_user_styles', plugin_dir_url(__FILE__) . '/bundled/css/user_styles.min.css');

    // SCRIPTS
    wp_register_script('cw_validation', plugin_dir_url(__FILE__) . '/bundled/js/validation.js', array('jquery'), null, true);
    wp_register_script('cw_user_actions', plugin_dir_url(__FILE__) . '/bundled/js/user_actions.js', array('jquery'), null, true);

    // do this to generalize the getJSON url for deployment
    wp_localize_script('cw_user_actions', 'searchData', array(
      'root_url' => get_site_url(),
      'nonce' => wp_create_nonce('wp_rest') // KEY TO USER SECCION TO ACCESS REST
    ));

    /* User pages get user assets */
    if ( get_query_var( 'u', false ) ) {
      // enqueue styles/scripts here for user pages
      wp_enqueue_style('cw_user_styles');
    }

    if ( get_query_var( 'l', false ) ) {
      // enqueue styles/scripts here for league pages
    }
  }

  function loadTemplate($template) {
    if ( get_query_var( 'l', false ) ) {
      return plugin_dir_path(__FILE__) . 'inc/league/league-route.php';
    }

    if ( get_query_var( 'u', false ) ) {
      return plugin_dir_path(__FILE__) . 'inc/user-route.php';
    }

    return $template;
  }

  function getHeader($args = array()) {
    // SET DEFAULT TITLE VALUE
    $title = array_key_exists('title', $args) ? $args['title'] : get_the_title();
    $type = array_key_exists('title', $args) ? $args['title'] : get_the_title(); ?>
      <h1><?php echo $title; ?></h1>
  <?php }
}
$cwGlobal = new cwGlobal();

// USER FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'user-functions.php');
$UserDB = new UserDB();

// LEAGUE FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'leaguedb-functions.php');
$LeagueDB = new LeagueDB();

/* SHORTCODE SETUP 
* This did NOT work in the global object
* SHORTCODES NEEDED:
*    - League List */

/* Register Form */
add_shortcode('cw_register_form', 'cw_register_form_sc_handler');
function cw_register_form_sc_handler() { 
  $returnHTML = "";

  if( is_user_logged_in() ){
    // if user is logged in show account settings
    $returnHTML .= "You are already logged in. Log out to create a new account.";
  } else {
    // make sure user is logged in first
    // load scripts/styles
    wp_enqueue_style('cw_user_styles');
    wp_enqueue_script('cw_validation');
    wp_enqueue_script('cw_user_actions');

    $returnHTML = '
      <div class="cw-box">
        <div class="row justify-content-center">
          <div class="form-box register">

              <div class="server-msg-box hidden">
                  <p class="server-msg"></p>
              </div>
  
              <div class="field-box">
                  <label for="field-username" class="form-label">Username</label>
                  <input type="text" name="field-username" class="req username form-control" id="field-username" placeholder="U5ernam3">
              </div>
              
              <div class="field-box">
                  <label for="field-pass" class="form-label">Password</label>
                  <input type="password" name="field-pass" class="req password form-control" id="field-password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;">
                  <div class="progress password-strength" role="progressbar" aria-label="Password Strength" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height: 3px">
                      <div class="progress-bar" style="width: 0%"></div>
                  </div>
              </div>
  
              <div class="field-box">
                  <label for="field-email" class="form-label">Email Address</label>
                  <input type="email" name="field-email" class="req email form-control" id="field-email" placeholder="name@example.com">
              </div>
  
              <div class="action-box">
                  <button type="button" class="btn btn-primary action-register-user">Register Now</button>
              </div>
          </div>
        </div>
      </div>';
  }
    
  // Output needs to be return
  return $returnHTML;
}
// */

/* Login Form Shortcode */
function cw_login_form_shortcode_handler($atts = [], $content = null) {
  wp_enqueue_style( 'cw_login_form' );
  return 
    "<div class='cw_login_form'>" . 
      wp_login_form( array(
        'echo' => false
      ) ) . 
    "</div>";
}
// */