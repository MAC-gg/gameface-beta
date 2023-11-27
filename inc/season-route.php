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
        // SETUP DATA TO SEE IF SEASON LINK EXISTS
        $season = $SeasonDB->getS($s);
        if( $season ) {
          // user check
          // logged in user should have a role matching username and season name
          // get username
          // get user roles ( make function: .getRole($s) )
          
          if(false) {
            // if user role is manager, allow these pages
            switch($sp){
              case 'manage':
                // MANAGE PAGE
                $require_path = 'season/manage.php';
                break;
              case 'register':
                // REGISTER PAGE
                $require_path = 'season/register.php';
                break;
            }
          } else {
            // if user role is NOT manager, allow these pages
            // SEASON SUBPAGES
            switch($sp){
              case 'register':
                // REGISTER PAGE
                $require_path = 'season/register.php';
                break;
            }
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