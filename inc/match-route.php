<head><?php wp_head(); ?></head>
<?php get_header();

$s = sanitize_text_field(get_query_var('season'));
$m = sanitize_text_field(get_query_var('match'));
$mp = sanitize_text_field(get_query_var('mp'));

?>
<div class="cw-box"><!-- season-route.php -->
  <?php

    // requrie path so we only call require_once ONCE
    $require_path = "err404.php";

    // Check if season vairable is set and not empty
    if( isset($s) && !empty($s) ) {
      // Season is set and not empty
      // Check if team vairable is set and not empty
      if( isset($m) && !empty($m) ) {
        // Team is set and not empty
        // check to make sure season and team exist
        // SETUP DATA TO SEE IF SEASON AND TEAM LINK EXISTS
        $season = $SeasonDB->getS($s);
        $match = $MatchDB->getM($m, $season->id);
        if( $season && $match ) {
          $require_path = "/match/single.php";
          switch($mp){
            case 'postpone':
              // postpone page
              $require_path = 'match/postpone.php';
              break;
          }
        }
      }
    }

    require_once plugin_dir_path(__FILE__) . $require_path;

  ?>
</div> <!-- END cw-box -->
<?php get_footer();