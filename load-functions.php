<?php

/*
  Plugin Name: Gameface Beta
  Version: 0.1
  Author: MAC
  Author URI: https://www.mac.gg/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );

// USER FUNCTION SETUP
// loaded first because user functions are required for other classes
include( plugin_dir_path( __FILE__ ) . 'user-functions.php');
$UserDB = new UserDB();

// LEAGUE FUNCTION SETUP
include( plugin_dir_path( __FILE__ ) . 'leaguedb-functions.php');
$LeagueDB = new LeagueDB();

// VIEW FUNCTION SETUP
// loaded last because it uses functions from above classes
include( plugin_dir_path( __FILE__ ) . 'vw-functions.php');
$VW = new VW();