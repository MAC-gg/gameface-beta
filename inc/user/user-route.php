<?php get_header(); ?>

<script>
  import UserActions from "user-actions.js";
  var userActions = new UserActions();
</script>

<?php $u = sanitize_text_field(get_query_var('u'));
$up = sanitize_text_field(get_query_var('up'));

echo "<h2>" . $u . ' ' . $up . "</h2>";

if($u && $up):
  // USER SUBPAGES
  switch($up){
    case 'manage':
      // MANAGE PAGE
      require_once plugin_dir_path(__FILE__) . 'vws/VWmanage.php';
      break;
  }

elseif($u):
  // SINGLE LEAGUE VIEW

  // === VIEW
  require_once plugin_dir_path(__FILE__) . 'vws/VWprofile.php';

else:

  if( is_page('my-account') ) {
    // DEFAULT MY ACCOUNT
    require_once plugin_dir_path(__FILE__) . 'vws/VWaccount.php';
  }

  if( is_page('register') ) {
    // DEFAULT MY ACCOUNT
    require_once plugin_dir_path(__FILE__) . 'vws/VWregister.php';
  }

endif; get_footer();