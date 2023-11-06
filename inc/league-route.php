<?php get_header();

$l = sanitize_text_field(get_query_var('l'));
$lp = sanitize_text_field(get_query_var('lp'));

?>
<div class="cw-box"><!-- league-route.php -->
  <?php
    if($l && $lp):
      // requrie path so we only call require_once ONCE
      $require_path = "err404.php";

      // user check
      // user must be logged in
      if( !is_user_logged_in() ) {
        // require login content with redirect back here
        $require_path = 'errLogin.php';
      } else {
        // user check
        // logged in user should have a role matching username and league name
        // get username
        // get user roles ( make function: .getRole($l) )
        
        if(false) {
          // if user role is manager, allow these pages
          switch($lp){
            case 'manage':
              // MANAGE PAGE
              require_once plugin_dir_path(__FILE__) . 'league/manage.php';
              break;
            case 'register':
              // REGISTER PAGE
              require_once plugin_dir_path(__FILE__) . 'league/register.php';
              break;
        } else {
          // if user role is NOT manager, allow these pages
          // LEAGUE SUBPAGES
          switch($lp){
            case 'register':
              // REGISTER PAGE
              require_once plugin_dir_path(__FILE__) . 'league/register.php';
              break;
          }
        }
      }

      require_once plugin_dir_path(__FILE__) . $require_path;

    elseif($l):

      // SINGLE LEAGUE VIEW
      // SETUP DATA TO SEE IF LEAGUE LINK EXISTS
      $data = $LeagueDB->getL($l);
      if( count($data) == 0 ) {
        // === 404 VIEW
        require_once plugin_dir_path(__FILE__) . 'err404.php';
      } else {
        // === VIEW
        // $userRole = get user roles ( make function: .getRole($l) )
        require_once plugin_dir_path(__FILE__) . 'league/single.php';
      }

    endif;
  ?>
</div> <!-- END cw-box -->
<?php get_footer();