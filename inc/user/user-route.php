<?php get_header();

$u = sanitize_text_field(get_query_var('u'));
$up = sanitize_text_field(get_query_var('up'));

echo "<h2>" . $u . ' ' . $up . "</h2>";

if($u && $up):
  
  // SUBPAGES
  switch($up){
    case 'manage':
      // MANAGE PAGE
      $cdubGlobal->getHeader(array(
        'title' => 'User Profile',
        'u'     => $u,
        'up'    => $up
      ));
      require_once plugin_dir_path(__FILE__) . 'vws/VWmanage.php';
      break;
  }

elseif($u):

  // SINGLE USER VIEW
  $cdubGlobal->getHeader(array(
    'title' => 'User Profile',
    'u'     => $u
  ));
  require_once plugin_dir_path(__FILE__) . 'vws/VWprofile.php';

else:

  if( is_page('my-account') ) {
    // DEFAULT MY ACCOUNT
    $cdubGlobal->getHeader();
    require_once plugin_dir_path(__FILE__) . 'vws/VWaccount.php';
  }

  if( is_page('register') ) {
    // DEFAULT MY ACCOUNT
    $cdubGlobal->getHeader(array(
      'title' => 'Register a New Account'
    ));
    require_once plugin_dir_path(__FILE__) . 'vws/VWregister.php';
  }

endif; get_footer();