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

    // SHORTCODE
    /* Register Form */
    add_shortcode('cw_register_form', array($this, 'cw_register_form_sc_handler'));
    add_shortcode('cw_login_form', array($this, 'cw_login_form_sc_handler'));
    
    // other funcs
    // --- setup pages on activate
    // --- login / logout
    // --- current_user_can / role management
    // --- get header / breadcrumbs
    // --- form setup / server communication handling
  }

  function setup_url_vars( $vars ) {

    // server
    $vars[] = "cw-svr-status";

    // season
    $vars[] = "season";
    $vars[] = "sp";

    // user
    $vars[] = "u";
    $vars[] = "up";
    return $vars;

  }

  function setup_url_rewrites() {

    // MAKE URL PRETTY
    // ORDER MATTERS HERE
    // match cases like '/s/season-link/manage/'
    add_rewrite_rule('s/([^/]*)/?([^/]*)/?$', 'index.php?season=$matches[1]&sp=$matches[2]', 'top');
    // match cases like '/s/season-link/'
    add_rewrite_rule('s/([^/]*)/?', 'index.php?season=$matches[1]', 'top');

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
    wp_register_script('cw_regApprovalActions', plugin_dir_url(__FILE__) . '/bundled/js/regApprovalActions.js', array('jquery'), null, true);

    // do this to generalize the getJSON url for deployment
    wp_localize_script('cw_regApprovalActions', 'searchData', array(
      'root_url' => get_site_url(),
      'nonce' => wp_create_nonce('wp_rest') // KEY TO USER SECCION TO ACCESS REST
    ));

    /* User pages get user assets */
    if ( get_query_var( 'u', false ) ) {
      // enqueue styles/scripts here for user pages
      wp_enqueue_style('cw_user_styles');
    }

    if ( get_query_var( 'season', false ) ) {
      // enqueue styles/scripts here for season pages
      wp_enqueue_style('cw_user_styles');
    }
  }

  function loadTemplate($template) {
    if ( get_query_var( 'season', false ) ) {
      return plugin_dir_path(__FILE__) . 'inc/season-route.php';
    }

    if ( get_query_var( 'u', false ) ) {
      return plugin_dir_path(__FILE__) . 'inc/user-route.php';
    }

    return $template;
  }

  function cw_register_form_sc_handler() { 
    // Start the object buffer, which saves output instead of outputting it.
    ob_start();

    include( plugin_dir_path( __FILE__ ) . 'inc/shortcodes/register-form-view.php');

    // Return everything in the object buffer.
    return ob_get_clean();
  }

  function process_svr_status($obj) { 
    if (get_query_var('cw-svr-status')) {
      $style = "danger";
      $msg = "There was an error entering the data.";
      if(get_query_var('cw-svr-status') == '200') {
        $style = "success";
        if($obj) {
          $msg = "Your " . $obj . " has been updated successfully.";
        } else {
          $msg = "The database was updated successfully.";
        }
      } ?>
      
      <div class="cw-svr-msg <?php echo $style; ?>">
        <p><?php echo $msg; ?></p>
      </div>
      
      <?php
    }
  }
}
$cwGlobal = new cwGlobal();

// USER FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'user-functions.php');
$UserDB = new UserDB();

// SEASON FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'season-functions.php');
$SeasonDB = new SeasonDB();

// SEASON FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'season-reg-functions.php');
$SeasonRegDB = new SeasonRegDB();

/* Login Form Shortcode */
function cw_login_form_sc_handler($atts = [], $content = null) {
  wp_enqueue_style( 'cw_login_form' );
  return 
    "<div class='cw_login_form'>" . 
      wp_login_form( array(
        'echo' => false
      ) ) . 
    "</div>";
}
// */