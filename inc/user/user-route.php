<?php get_header();

$u = sanitize_text_field(get_query_var('u'));
$up = sanitize_text_field(get_query_var('up'));

?><div class="cdub-box"><!-- user-route.php --><?php

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

?></div><!-- END cdub-start --><?php
?>
<h1>Register</h1>

<div class="field-box">
    <label for="field-username">Username</label><br />
    <input type="text" name="field-username" class="field-username req">
    <p class="msg"></p>
</div>
<?php
endif; get_footer();