<?php get_header();

$l = sanitize_text_field(get_query_var('l'));
$lp = sanitize_text_field(get_query_var('lp'));

if($l && $lp):
  // LEAGUE SUBPAGES
  // each case is a new view / new data
  switch($lp){
    case 'manage':
      // MANAGE PAGE
      // === DATA
      require_once plugin_dir_path(__FILE__) . 'manage/data.php';
      // === VIEW
      require_once plugin_dir_path(__FILE__) . 'manage/vw.php';
      break;
  }

elseif($l):
  // SINGLE LEAGUE VIEW
  // === CONTROLLER
  require_once plugin_dir_path(__FILE__) . 'single/data.php';

  // SETUP DATA TO SEE IF LEAGUE LINK EXISTS
  $data = new data($l);
  if( count($data->leauge) == 0 ) {
    // === 404 VIEW
    require_once plugin_dir_path(__FILE__) . 'single/vw.php';
  } else {
    // === VIEW
    require_once plugin_dir_path(__FILE__) . 'single/vw.php';
  }

else:
  // DEFAULT LEAGUE LIST
  // === CONTROLLER
  require_once plugin_dir_path(__FILE__) . 'list/data.php'; 
  // === VIEW
  require_once plugin_dir_path(__FILE__) . 'list/vw.php';

endif; get_footer();