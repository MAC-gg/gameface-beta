<head><?php wp_head(); ?></head>
<?php get_header();

$s = sanitize_text_field(get_query_var('season'));
$t = sanitize_text_field(get_query_var('team'));
$tp = sanitize_text_field(get_query_var('tp'));

?>
<div class="cw-box"><!-- season-route.php -->
  <?php

    // requrie path so we only call require_once ONCE
    $require_path = "err404.php";

    // Check if season vairable is set and not empty
    if( isset($s) && !empty($s) ) {
        // Season is set and not empty
        // Check if team vairable is set and not empty
        if( isset($t) && !empty($t) ) {
            // Team is set and not empty
            // check to make sure season and team exist
            // SETUP DATA TO SEE IF SEASON AND TEAM LINK EXISTS
            $season = $SeasonDB->getS($s);
            $team = $TeamDB->getT($t, $season->id);
            if( $season && $team ) { // SEASON AND TEAM EXIST
                $require_path = "/team/single.php";
                if (isset($tp) && !empty($tp)) {
                    if( is_user_logged_in() ) {
                        // USER IS LOGGED IN
                        $current_user = wp_get_current_user();
                        if ($season->manager == $current_user->ID || $team->capt == $current_user->ID) { // USER IS AUTHORIZED
                            switch($tp){
                                case 'settings':
                                    // settings page
                                    $require_path = 'team/settings.php';
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

    require_once plugin_dir_path(__FILE__) . $require_path;

  ?>
</div> <!-- END cw-box -->
<?php get_footer();