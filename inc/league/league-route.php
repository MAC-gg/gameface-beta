<?php get_header();

$l = sanitize_text_field(get_query_var('l'));
$lp = sanitize_text_field(get_query_var('lp'));

if($l && $lp):
  // LEAGUE SUBPAGES
  switch($lp){
    case 'manage':
      // MANAGE PAGE
      $VW->getHeader();
      require_once plugin_dir_path(__FILE__) . 'vws/VWmanage.php';
      break;
  }

elseif($l):
  // SINGLE LEAGUE VIEW
  // SETUP DATA TO SEE IF LEAGUE LINK EXISTS
  $data = $LeagueDB->getL($l);
  if( count($data) == 0 ) {
    // === 404 VIEW
    require_once plugin_dir_path(__FILE__) . 'vws/VWsingle.php';
  } else {
    // === VIEW
    $VW->getHeader();
    require_once plugin_dir_path(__FILE__) . 'vws/VWsingle.php';
  }

else:
  // DEFAULT LEAGUE LIST
  $cdubGlobal->getHeader(array(
    'title' => 'League List'
  ));
  require_once plugin_dir_path(__FILE__) . 'vws/VWlist.php';

endif; get_footer();