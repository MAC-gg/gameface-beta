<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class NotiDB {
    function __construct() {

        global $wpdb;
        $this->charset = $wpdb->get_charset_collate();
        $this->limit = 10;

        // two tables, one for account, one for profile
        $this->tablename = $wpdb->prefix . "cw_noti";
        
        $this->onActivate();

        // SCHEDULED CRON HOOK SETUP
        // FIRST - register cron tasks
        add_action('cw_send_mark_atten_noti', array($this, 'cw_mark_atten_noti'));

        // THEN - schedule cron tasks
        // if not already scheduled, then schedule task (first time, how often, hook to run)
        if ( ! wp_next_scheduled( 'cw_send_mark_atten_noti' ) ) {
            wp_schedule_event( time(), 'hourly', 'cw_send_mark_atten_noti' );
        }
        // END CRON HOOK SETUP

        // ACTION HOOK SETUP
        // edit account action hook
        /*
        add_action('admin_post_updateaccount', array($this, 'updateAccount'));
        add_action('admin_post_nopriv_updateaccount', array($this, 'updateAccount'));
        */
    }

    function onActivate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta("CREATE TABLE $this->tablename (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        WPID bigint(20) NOT NULL DEFAULT 0,
        msg varchar(256) NOT NULL DEFAULT '',
        type varchar(60) NOT NULL DEFAULT '',
        ref varchar(60) NOT NULL DEFAULT '',
        refID bigint(20) NOT NULL DEFAULT 0,
        isRead BOOLEAN NOT NULL DEFAULT 0,
        created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
        ) $this->charset;");
    }

    function cw_mark_atten_noti() {
        // get all matches
        // iterate
        // if matchdate is less than 3 days away
        // if noti has not already been sent
        // get player list for team1 and team2 with TeamDB::getPlayerList
        // iterate thru players
        // enter noti
        // send email
        // log errors via response code
        // if response is good, mark that noti has been sent
        // if response is bad, send email to admin

        // when match complete, cleanup noti

        $response_code = 0;
        
        global $wpdb;
        // $wpdb->show_errors();
        $tablename = $wpdb->prefix . "cw_noti";

        $new_noti = array();
        $new_noti['WPID'] = 1;
        $new_noti['msg'] = "test noti";
        $new_noti['type'] = "urgent";
        $new_noti['ref'] = "match";
        $new_noti['refID'] = 145;
        $new_noti['created'] = date("Y-m-d H:i:s", time());

        $wpdb->insert($tablename, $new_noti);

        $response_code = $wpdb->last_error !== '' ? 500 : 200;

        // $wpdb->print_error();
        return $response_code;
    }

    static function getNotiList($p) {
        global $wpdb;
        $tablename = $wpdb->prefix . "cw_noti";
        $query = "SELECT * FROM $tablename ";
        $query .= "WHERE WPID=%d";
        return $wpdb->get_results($wpdb->prepare($query, $p));
    }

    static function linkBuilder($ref, $refID) {
        $return_url = "";
        switch($ref){
            case 'season':
                $season = SeasonDB::getSingle($refID);
                $return_url = '/s/' . $season->slug;
                break;
            case 'team':
                $team = TeamDB::getSingle($refID);
                $season = SeasonDB::getSingle($team->season);
                $return_url = '/s/' . $season->slug . '/t/' . $team->slug;
                break;
            case 'match':
                $match = MatchDB::getSingle($refID);
                $season = SeasonDB::getSingle($match->season);
                $return_url = '/s/' . $season->slug . '/m/' . $match->slug;
                break;
        }
        return $return_url;
    }
}