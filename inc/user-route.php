<?php get_header();

$u = sanitize_text_field(get_query_var('u'));
$up = sanitize_text_field(get_query_var('up'));

?>
<div class="cw-box"><!-- user-route.php -->
  <div class="container">
    <?php
      if($u && $up):
        // requrie path so we only call require_once ONCE
        $require_path = "err404.php";

        // user check
        // user must be logged in
        if( !is_user_logged_in() ) {
          // require login content with redirect back here
          $require_path = 'errLogin.php';
        } else {
          // user check
          // logged in username must also match the url username
          $current_user = wp_get_current_user();
          if ( strtolower($current_user->user_login) != strtolower($u) ) {
            // require unauth content
            $require_path = 'err401.php';
          } else {
            // user is logged in and authorized
            // SUBPAGES
            switch($up){
              case 'edit':
                // EDIT PAGE
                $require_path = 'user/edit.php';
                break;
              case 'account':
                // Account PAGE
                $require_path = 'user/account.php';
                break;
            }
          }
        }

        require_once plugin_dir_path(__FILE__) . $require_path;
        
      elseif($u):

        // SINGLE USER VIEW
        $owner = false;
        if( is_user_logged_in() ) {
          $current_user = wp_get_current_user();
          $owner = strtolower($current_user->user_login) == strtolower($u);
        }
        require_once plugin_dir_path(__FILE__) . 'user/profile.php';

      endif;
    ?>
  </div><!-- END .container -->
</div><!-- END cw-box -->

<?php  get_footer();