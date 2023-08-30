<?php

/*
  Plugin Name: Gameface Beta
  Version: 0.1
  Author: MAC
  Author URI: https://www.mac.gg/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( plugin_dir_path( __FILE__ ) . 'leaguedb-functions.php');
$LeagueDB = new LeagueDB();

// VIEW FUNCTION SETUP
// loaded last because it uses functions from above classes
include( plugin_dir_path( __FILE__ ) . 'vw-functions.php');
$VW = new VW();