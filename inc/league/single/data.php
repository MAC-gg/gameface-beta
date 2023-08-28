<?php 

/* LEAGUE SINGLE DATA */

class data {
    function __construct($l) {
        global $wpdb;
        $tablename = $wpdb->prefix . "gameface_leagues";

        // CREATE QUERIES
        $query = "SELECT * FROM $tablename WHERE leagueLink = '$l'";
        $countQuery = "SELECT COUNT(*) FROM $tablename WHERE leagueLink = '$l'";

        // EXECUTE QUERIES AND SAVE DATA HERE
        $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->placeholders));
        $this->l = $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
    }
}