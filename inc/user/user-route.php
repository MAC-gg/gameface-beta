<?php get_header();

$u = sanitize_text_field(get_query_var('u'));
$up = sanitize_text_field(get_query_var('up'));

?>
<div class="cdub-box"><!-- user-route.php -->
  <div class="container">
    <?php
      if($u && $up) {
        
        // SUBPAGES
        switch($up){
          case 'manage':
            // /u/user-link/manage
            require_once plugin_dir_path(__FILE__) . 'vws/manage.php';
            break;
          case 'account':
            // /u/user-link/account
            require_once plugin_dir_path(__FILE__) . 'vws/account.php';
            break;
        }
        
      } else {

        // SINGLE USER VIEW
        require_once plugin_dir_path(__FILE__) . 'vws/profile.php';
        
      }
    ?>
  </div><!-- END .container -->
</div><!-- END cdub-box -->

<?php  get_footer();