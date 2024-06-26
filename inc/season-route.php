<head><?php wp_head(); ?></head>
<?php get_header();

$s = sanitize_text_field(get_query_var('season'));
$sp = sanitize_text_field(get_query_var('sp'));

?>
<div class="cw-box"><!-- season-route.php -->
  <?php
    if($s && $sp):
      // requrie path so we only call require_once ONCE
      $require_path = "err404.php";

      // user check
      // user must be logged in
      if( !is_user_logged_in() ) {
        // require login content with redirect back here
        $require_path = 'errLogin.php';
      } else {
        // check to make sure season exists
        // USER IS LOGGED IN
        $current_user = wp_get_current_user();
        // SETUP DATA TO SEE IF SEASON LINK EXISTS
        $season = $SeasonDB->getS($s);
        if( $season ) { // SEASON EXISTS
            if ($season->manager == $current_user->ID) { // USER IS AUTHORIZED
                switch($sp){
                    case 'settings':
                        // settings page
                        $require_path = 'season/settings.php';
                        break;
                    case 'approve':
                        // APPROVE PAGE
                        $require_path = 'season/approve.php';
                        break;
                    case 'register':
                        // REGISTER PAGE
                        $require_path = 'season/register.php';
                        break;
                    case 'teams':
                        // CREATE TEAMS PAGE
                        $require_path = 'season/teams.php';
                        break;
                    case 'sched':
                        // FINALIZE SCHED PAGE
                        $require_path = 'season/sched.php';
                        break;
                }
            } else { // NOT AUTHORIZED
                $require_path = 'err401.php';
            }
        }
      }

      require_once plugin_dir_path(__FILE__) . $require_path;

    elseif($s):

      // SINGLE SEASON VIEW
      // SETUP DATA TO SEE IF SEASON LINK EXISTS
      $season = $SeasonDB->getS($s);
      if( !$season ) {
        // === 404 VIEW
        require_once plugin_dir_path(__FILE__) . 'err404.php';
      } else {
        // === VIEW
        // $userRole = get user roles ( make function: .getRole($s) )
        require_once plugin_dir_path(__FILE__) . 'season/single.php';
      }

    endif;
  ?>
</div> <!-- END cw-box -->
<?php get_footer();