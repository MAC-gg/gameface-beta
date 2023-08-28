<?php 

/* LEAGUE LIST DATA */

class data {
    function __construct() {
        global $wpdb;
        $tablename = $wpdb->prefix . "gameface_leagues";
        
        $this->args = $this->getArgs();
        $this->placeholders = $this->createPlaceholders();

        $query = "SELECT * FROM $tablename ";
        $countQuery = "SELECT COUNT(*) FROM $tablename ";
        $query .= $this->createWhereText();
        $countQuery .= $this->createWhereText();
        $query .= " LIMIT 100";

        $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->placeholders));
        $this->leagues = $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
    }

    function getArgs() {
        $temp = array(
            'leagueName' => sanitize_text_field($_GET['leagueName']),
            'leagueLink' => sanitize_text_field($_GET['leagueLink']),
            'numTeams' => sanitize_text_field($_GET['numTeams']),
            'teamSize' => sanitize_text_field($_GET['teamSize']),
            'game' => sanitize_text_field($_GET['game']),
        );

        return array_filter($temp, function($x) { 
            return $x;
        });
    }

    function createPlaceholders() {
        return array_map(function($x) {
            return $x;
        }, $this->args);
    }

    function createWhereText() {
        $whereQuery = "";

        if(count($this->args)) {
            $whereQuery = "WHERE ";
        }

        $currentPosition = 0;
        foreach($this->args as $index => $item) {
            $whereQuery .= $this->specificQuery($index);
            if($currentPosition != count($this->args) - 1) {
                $whereQuery .= " AND ";
            }
            $currentPosition++;
        }

        return $whereQuery;
    }

    function specificQuery($index) {
        switch($index) {
            case "minweight":
                return "petweight >= %d";
            case "maxweight":
                return "petweight <= %d";
            case "minyear":
                return "birthyear >= %d";
            case "maxyear":
                return "birthyear <= %d";
            default:
                return $index . " = %s";
        }
    }
}

$data = new data();