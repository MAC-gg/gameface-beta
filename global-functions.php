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

    // REDIRECT AFTER LOGOUT
    add_action('wp_logout', array($this, 'auto_redirect_after_logout'));

    // DEVELOPMENT ENV FORM ACTION
    // DISABLE WHEN LIVE
    add_action('admin_post_cwadmincuid', array($this, 'cwAdminCUID'));
    add_action('admin_post_nopriv_cwadmincuid', array($this, 'cwAdminCUID'));
    
    // other funcs
    // --- setup pages on activate
    // --- login / logout
    // --- current_user_can / role management
    // --- get header / breadcrumbs
    // --- form setup / server communication handling
  }

    function setup_url_vars( $vars ) {

      // DANGER dev var ONLY
      $vars[] = "cw-admin-cuid";

      // server
      $vars[] = "cw-svr-status";

      // match
      $vars[] = "match";
      $vars[] = "mp";

      // team
      $vars[] = "team";
      $vars[] = "tp";

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

      // MATCHES
      // match cases like '/s/season-link/m/256/edit'
      add_rewrite_rule('s/([^/]*)/m/([^/]*)/([^/]*)/?$', 'index.php?season=$matches[1]&match=$matches[2]&mp=$matches[3]', 'top');
      // match cases like '/s/season-link/m/256'
      add_rewrite_rule('s/([^/]*)/m/([^/]*)/?', 'index.php?season=$matches[1]&match=$matches[2]', 'top');

      // TEAMS
      // match cases like '/s/season-link/t/test/edit'
      add_rewrite_rule('s/([^/]*)/t/([^/]*)/([^/]*)/?$', 'index.php?season=$matches[1]&team=$matches[2]&tp=$matches[3]', 'top');
      // match cases like '/s/season-link/t/test'
      add_rewrite_rule('s/([^/]*)/t/([^/]*)/?', 'index.php?season=$matches[1]&team=$matches[2]', 'top');

      // SEASON
      // match cases like '/s/season-link/manage/'
      add_rewrite_rule('s/([^/]*)/?([^/]*)/?$', 'index.php?season=$matches[1]&sp=$matches[2]', 'top');
      // match cases like '/s/season-link/'
      add_rewrite_rule('s/([^/]*)/?', 'index.php?season=$matches[1]', 'top');

      // USER
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
      wp_register_style( 'cw_main_styles', plugin_dir_url(__FILE__) . '/bundled/css/main.min.css', array(), '1.0');
      wp_register_style( 'cw_user_styles', plugin_dir_url(__FILE__) . '/bundled/css/user_styles.min.css', array(), '1.0');
      wp_register_style( 'cw_season_styles', plugin_dir_url(__FILE__) . '/bundled/css/season_styles.min.css', array(), '1.0');

      // SCRIPTS
      wp_register_script('cw_validation', plugin_dir_url(__FILE__) . '/bundled/js/validation.js', array('jquery'), null, true);
      // wp_register_script('cw_regApprovalActions', plugin_dir_url(__FILE__) . '/bundled/js/regApprovalActions.js', array('jquery'), null, true);

      // do this to generalize the getJSON url for deployment
      /*
      wp_localize_script('cw_regApprovalActions', 'searchData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest') // KEY TO USER SECCION TO ACCESS REST
      )); */

      /* User pages get user assets */
      if ( get_query_var( 'u', false ) ) {
        // enqueue styles/scripts here for user pages
        wp_enqueue_style('cw_main_styles');
        wp_enqueue_style('cw_user_styles');
      }

      if ( get_query_var( 'season', false ) ) {
        // enqueue styles/scripts here for season pages
        // wp_enqueue_style('cw_main_styles');
        wp_enqueue_style('cw_season_styles');
      }

      if ( get_query_var( 'team', false ) ) {
        // enqueue styles/scripts here for season pages
        // wp_enqueue_style('cw_main_styles');
        wp_enqueue_style('cw_season_styles');
      }

      if ( get_query_var( 'match', false ) ) {
        // enqueue styles/scripts here for season pages
        wp_enqueue_style('cw_main_styles');
        // wp_enqueue_style('cw_season_styles');
      }
    }

    function loadTemplate($template) {
      
      // ORDER MATTERS HERE
      if ( get_query_var( 'match', false ) ) {
        return plugin_dir_path(__FILE__) . 'inc/match-route.php';
      }

      if ( get_query_var( 'team', false ) ) {
        return plugin_dir_path(__FILE__) . 'inc/team-route.php';
      }

      if ( get_query_var( 'season', false ) ) {
        return plugin_dir_path(__FILE__) . 'inc/season-route.php';
      }

      if ( get_query_var( 'u', false ) ) {
        return plugin_dir_path(__FILE__) . 'inc/user-route.php';
      }

      return $template;
    }

    function auto_redirect_after_logout(){
      wp_safe_redirect( home_url() );
      exit;
    }

    function cw_register_form_sc_handler() { 
      // Start the object buffer, which saves output instead of outputting it.
      ob_start();

      include( plugin_dir_path( __FILE__ ) . 'inc/shortcodes/register-form-view.php');

      // Return everything in the object buffer.
      return ob_get_clean();
    }

    static function process_svr_status($obj = "") { 
      $status = get_query_var('cw-svr-status');
      if ($status) {
        $style = "danger";
        $msg = "There was an error entering the data.";
        if($status == '200') {
          $style = "success";
          if($obj) {
            $msg = "Your " . $obj . " has been updated successfully.";
          } else {
            $msg = "The database was updated successfully.";
          }
        } else {
          // CUSTOM ERROR MESSAGES
          switch($status) {
            case "cw502":
              $msg = "The username or email entered has already been taken.";
              break;
            case "cw503":
              $msg = "Too many players entered on one team. Try again.";
              break;
            case "cw504":
              $msg = "Only one captain on each team. Try again.";
              break;
          }
        } 
        
        if ( $style == "success" ) {
          $msg = "<strong>Success!</strong> " . $msg;
        } else {
          $msg = '<strong>Error ' . $status . '</strong>: ' . $msg;
        } ?>
        
        <div class="cw-svr-msg <?php echo $style; ?>">
          <p><?php echo $msg; ?></p>
        </div>
        
        <?php
      }
    }

    function breadcrumbs($s, $sp = "", $t = [], $tp = []) {
        if ( $t ) { ?>
            <div class="cw-breadcrumbs">
                <div><a href="/"><i class="bi bi-house-fill"></i></a></div>
                <div><a href="/seasons">Season List</a></div>
                <div><a href="/s/<?php echo $s->slug; ?>">Season: <?php echo $s->title; ?></a></div>
                <div>
                    <?php if ($tp) { ?><a href="/s/<?php echo $s->slug; ?>/t/<?php echo $t->slug; ?>"><?php } else { ?><span><?php } ?>
                    Team: <?php echo $t->title; ?>
                    <?php if ($tp) { ?></a><?php } else { ?></span><?php } ?>
                </div>
                <?php if ($tp) { ?>
                    <div class="<?php echo $tp[1]; ?>"><span><?php echo $tp[0]; ?></span></div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="cw-breadcrumbs">
                <div><a href="/"><i class="bi bi-house-fill"></i></a></div>
                <div><a href="/seasons">Season List</a></div>
                <div>
                    <?php if(!empty($sp)) { ?><a href="/s/<?php echo $s->slug; ?>"><?php } else { ?><span><?php } ?>
                    Season: <?php echo $s->title; ?>
                    <?php if(!empty($sp)) { ?></a><?php } else { ?></span><?php } ?>
                </div>
                <?php if(!empty($sp)) { ?>
                    <div><span><?php echo $sp; ?></span></div>
                <?php } ?>
            </div>
        <?php }
    }

    function userBreadcrumbs($u, $up = "") { ?>
        <div class="cw-breadcrumbs">
            <div><a href="/"><i class="bi bi-house-fill"></i></a></div>
            <div><a href="/u/<?php echo $u->displayName; ?>">Profile: <?php echo $u->displayName; ?></a></div>
            <?php if ( !empty($up) ) { ?>
                <div><span><?php echo $up; ?></span></div>
            <?php } ?>
        </div>
    <?php }
  
    // DEVELOPMENT ENV ONLY
    function cwAdminCUID() {
      wp_safe_redirect($_POST['redirect'] . $_POST['field-cuid']);
    }
}
$cwGlobal = new cwGlobal();

// USER FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'user-functions.php');
$UserDB = new UserDB();

// SEASON FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'season-functions.php');
$SeasonDB = new SeasonDB();

// SEASON Reg FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'season-reg-functions.php');
$SeasonRegDB = new SeasonRegDB();

// TEAM FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'team-functions.php');
$TeamDB = new TeamDB();

// MATCH FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'match-functions.php');
$MatchDB = new MatchDB();

// MATCH FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'noti-functions.php');
$NotiDB = new NotiDB();

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