<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class MatchDB {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->MATCHtablename = $wpdb->prefix . "cw_match";
    $this->ATTENtablename = $wpdb->prefix . "cw_atten";
    $this->limit = 10;

    $this->onActivate();

    // match form actions
    add_action('admin_post_postponematch', array($this, 'postponeMatch'));
    add_action('admin_post_nopriv_postponematch', array($this, 'postponeMatch'));

    // attendance form actions
    add_action('admin_post_markattendance', array($this, 'markAttendance'));
    add_action('admin_post_nopriv_markattendance', array($this, 'markAttendance'));
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->MATCHtablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      season bigint(20) NOT NULL DEFAULT 0,
      team1 bigint(20) NOT NULL DEFAULT 0,
      team2 bigint(20) NOT NULL DEFAULT 0,
      slug varchar(60) NOT NULL DEFAULT '',
      matchWeek varchar(60) NOT NULL DEFAULT '',
      matchDatetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      isPostponed BOOLEAN NOT NULL DEFAULT 0,
      PRIMARY KEY  (id)
    ) $this->charset;");

    dbDelta("CREATE TABLE IF NOT EXISTS $this->ATTENtablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      m bigint(20) NOT NULL DEFAULT 0,
      p bigint(20) NOT NULL DEFAULT 0,
      atten char(1) NOT NULL DEFAULT '',
      PRIMARY KEY (id)
    ) $this->charset;");
  }

  // CREATE MATCH - matches are automatically made with this function
  static function createMatch($season, $slug, $w, $t1, $t2, $when) {
    $response_code = 0;
    
    global $wpdb;
    // $wpdb->show_errors();
    $tablename = $wpdb->prefix . "cw_match";

    $new_match = array();
    $new_match['slug'] = sanitize_text_field($slug);
    $new_match['season'] = sanitize_text_field($season);
    $new_match['team1'] = sanitize_text_field($t2);
    $new_match['team2'] = sanitize_text_field($t1);
    $new_match['matchWeek'] = sanitize_text_field($w);
    $new_match['matchDatetime'] = sanitize_text_field($when);

    $wpdb->insert($tablename, $new_match);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    // $wpdb->print_error();
    return $response_code;
  }

  /* ========================== */
  /* ====== FORM ACTIONS ====== */
  /* ========================== */
  function postponeMatch() {
    $response_code = 0;
    $m = sanitize_text_field($_POST['field-match']);
    $match = self::getSingle($m);

    global $wpdb;
    // $wpdb->show_errors();

    // if POST value has a value, add to season array
    // field name = post value
    $new_match_datetime = array();
    if( isset($_POST['field-date']) && isset($_POST['field-hours']) && isset($_POST['field-mins']) ) { 
      $new_date = strtotime(sanitize_text_field($_POST['field-date']));
      $new_datetime = mktime(sanitize_text_field($_POST['field-hours']), sanitize_text_field($_POST['field-mins']), 0, date('m', $new_date), date('d', $new_date), date('Y', $new_date));

      $new_match_datetime['matchDatetime'] = date("Y-m-d H:i:s", $new_datetime);
      $new_match_datetime['isPostponed'] = 1;
    }

    $where = array(
      'id' => $match->id
    );

    $wpdb->update($this->MATCHtablename, $new_match_datetime, $where);

    $response_code = $wpdb->last_error !== '' ? 500 : 200;

    // PRINT ERRORS
    // $wpdb->print_error();
    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code . $wpdb->last_error);
    exit;
  }

  function markAttendance() {
    $response_code = 0;
    $m = sanitize_text_field($_POST['field-match']);
    $p = sanitize_text_field($_POST['field-player']);
    
    // Make new data obj
    // if POST value has a value, add to atten array
    // field name = post value
    $new_atten = array();
    if( $_POST['field-atten'] ) { 
      $new_atten['atten'] = sanitize_text_field($_POST['field-atten']);
    }

    // get current atten
    $current_atten = self::getCurrentAtten($m, $p);
    // SETUP DB
    global $wpdb;
    // $wpdb->show_errors();
    if($current_atten) {
      // UPDATE
      $where = array(
        'm' => $m,
        'p' => $p
      );

      $wpdb->update($this->ATTENtablename, $new_atten, $where);

      $response_code = $wpdb->last_error !== '' ? 500 : 200;

    } else {
      // CREATE

      $new_atten['m'] = $m;
      $new_atten['p'] = $p;

      $wpdb->insert($this->ATTENtablename, $new_atten);
      $response_code = $wpdb->last_error !== '' ? 500 : 200;
    }

    // PRINT ERRORS
    // $wpdb->print_error();
    wp_safe_redirect($_POST['redirect'] . "?cw-svr-status=" . $response_code . $wpdb->last_error);
    exit;
  }

  /* ========================== */
  /* ===== VIEW FUNCTIONS ===== */
  /* ========================== */
  // GET SINGLE MATCH BY SLUG (M)
  static function getM($m, $season) {
    if(isset($m) && isset($season)) {
      $values = array();
      array_push($values, $m);
      array_push($values, $season);

      global $wpdb;
      $tablename = $wpdb->prefix . "cw_match";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE slug=%d AND season=%d";
      return $wpdb->get_row($wpdb->prepare($query, $values));
    }
    return false;
  }

  // GET SINGLE MATCH BY ID
  static function getSingle($id) {
    if(isset($id)) {
      global $wpdb;
      $tablename = $wpdb->prefix . "cw_match";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE id=%d";
      return $wpdb->get_row($wpdb->prepare($query, $id));
    }
    return false;
  }

  // GET LIST of matches in a season
  static function getList($season) {
    global $wpdb;
    $tablename = $wpdb->prefix . "cw_match";
    $query = "SELECT * FROM $tablename ";
    $query .= "WHERE season=%d";
    return $wpdb->get_results($wpdb->prepare($query, $season));
  }

  // GET UPCOMING LIST of matches by week
  static function getUpcomingList($season, $week) {
    $values = array();
    array_push($values, $season);
    array_push($values, $week);

    global $wpdb;
    $tablename = $wpdb->prefix . "cw_match";
    $query = "SELECT * FROM $tablename ";
    $query .= "WHERE season=%d AND matchWeek=%d";
    return $wpdb->get_results($wpdb->prepare($query, $values));
  }

  // GET PLAYERS CURRENT REPORTED ATTENDANCE
  static function getCurrentAtten($m, $p) {
    if(isset($m) && isset($p)) {
      $values = array();
      array_push($values, $m);
      array_push($values, $p);

      global $wpdb;
      $tablename = $wpdb->prefix . "cw_atten";
      $query = "SELECT * FROM $tablename ";
      $query .= "WHERE m=%d AND p=%d";
      return $wpdb->get_row($wpdb->prepare($query, $values));
    }
    return false;
  }

  static function printAttendanceOptions($m, $p) {
    $match_data = self::getSingle($m);
    $season_data = SeasonDB::getSingle($match_data->season);
    $current_atten = self::getCurrentAtten($m, $p) ? self::getCurrentAtten($m, $p)->atten : "Not Reported"; ?>
    <div class="cw-attendance">
        <div class="cw-attendance-forms">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                <input type="hidden" name="action" value="markattendance"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $season_data->slug; ?>/m/<?php echo $match_data->slug; ?>">
                <input type="hidden" name="field-match" value="<?php echo $m; ?>">
                <input type="hidden" name="field-player" value="<?php echo $p; ?>">
                <input type="hidden" name="field-atten" value="Y">
                <button class="btn btn-success" title="Yes"<?php echo 'Y' == $current_atten ? " disabled" : ""; ?>><i class="bi bi-check-lg"></i> Yes</button>
            </form>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                <input type="hidden" name="action" value="markattendance"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $season_data->slug; ?>/m/<?php echo $match_data->slug; ?>">
                <input type="hidden" name="field-match" value="<?php echo $m; ?>">
                <input type="hidden" name="field-player" value="<?php echo $p; ?>">
                <input type="hidden" name="field-atten" value="N">
                <button class="btn btn-danger" title="No"<?php echo 'N' == $current_atten ? " disabled" : ""; ?>><i class="bi bi-x"></i> No</button>
            </form>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                <input type="hidden" name="action" value="markattendance"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $season_data->slug; ?>/m/<?php echo $match_data->slug; ?>">
                <input type="hidden" name="field-match" value="<?php echo $m; ?>">
                <input type="hidden" name="field-player" value="<?php echo $p; ?>">
                <input type="hidden" name="field-atten" value="?">
                <button class="btn btn-warning" title="Maybe"<?php echo '?' == $current_atten ? " disabled" : ""; ?>><i class="bi bi-question"></i> Maybe</button>
            </form>
        </div>
    </div>
  <?php }

  static function printCurrentAttendance($m, $p) { 
      $current_atten = self::getCurrentAtten($m, $p) ? self::getCurrentAtten($m, $p)->atten : "Not Reported"; ?>
      <div class="cw-current-attendance">
          <?php switch ($current_atten) {
              case 'Y': ?>
                  <p class="cw-tag-yes"><i class="bi bi-check-lg"></i> Yes</p>
                  <?php break;
              case 'N': ?>
                  <p class="cw-tag-no"><i class="bi bi-x"></i> No</p>
                  <?php break;
              case '?': ?>
                  <p class="cw-tag-maybe"><i class="bi bi-question"></i> Maybe</p>
                  <?php break;
              default: ?>
                  <p class="cw-tag-not-reported"><i class="bi bi-ban"></i> Not Reported</p>
          <?php } ?>
      </div>
  <?php }
}