<head><?php wp_head(); ?></head>
<?php get_header();

$s = get_query_var('season');
$m = get_query_var('match');
$mp = get_query_var('mp');

?>
<div class="cw-box"><!-- match-route.php -->
  <?php

    // requrie path so we only call require_once ONCE
    $require_path = "err404.php";

    // Check if season vairable is set and not empty
    if( isset($s) && !empty($s) ) {
      // Season is set and not empty
      // Check if team vairable is set and not empty
      if( isset($m) && !empty($m) ) {
        // Match is set and not empty
        // check to make sure season and match exist
        // SETUP DATA TO SEE IF SEASON AND MATCH LINK EXISTS
        $season = $SeasonDB->getS($s);
        $match = $MatchDB->getM($m, $season->id);
        $team1_capt = $TeamDB->getSingle($match->team1);
        $team2_capt = $TeamDB->getSingle($match->team2);
        if( $season && $match ) {
            $require_path = "/match/single.php";
            if (isset($mp) && !empty($mp)) {
                if( is_user_logged_in() ) {
                    // USER IS LOGGED IN
                    $current_user = wp_get_current_user();
                    if ($season->manager == $current_user->ID || $team1_capt == $current_user->ID || $team2_capt == $current_user->ID) { // USER IS AUTHORIZED
                        switch($mp){
                            case 'postpone':
                                // postpone page
                                $require_path = 'match/postpone.php';
                                break;
                        }
                    } else { // NOT AUTHORIZED
                        $require_path = 'err401.php';
                    }
                } else { // NOT LOGGED IN
                    $require_path = 'errLogin.php';
                }
            }
        }
      }
    }

    if($require_path == "/match/single.php") {
      wp_enqueue_script('cw_omedacitySandbox');
      wp_enqueue_script('cw_validation');
    }
    require_once plugin_dir_path(__FILE__) . $require_path;

  ?>
</div> <!-- END cw-box -->
<?php get_footer();