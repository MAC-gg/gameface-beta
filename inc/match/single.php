<!-- MATCH SINGLE View -->
<!-- /inc/match/single.php -->
<?php // SETUP DATA HERE
// match data
$match_data = $MatchDB->getM($m, $season->id);
// team data
$team1_data = $TeamDB->getSingle($match->team1);
$team2_data = $TeamDB->getSingle($match->team2);

// match datetime
$matchDatetime = new DateTime($match->matchDatetime, new DateTimeZone("America/New_York"));
$rightNow = new DateTime("now", new DateTimeZone("America/New_York"));

// isAuthorized
$cuid = get_query_var('cw-admin-cuid', $current_user->ID);
$isManager = $season->manager == $cuid;
$isTeam1Capt = $team1_data->capt == $cuid;
$isTeam2Capt = $team2_data->capt == $cuid;

$profileData = $UserDB->getProfile($cuid);
$accountData = $UserDB->getAccount($cuid);
?>
<?php $cwGlobal->dev_only_options($cuid, "/s/$s/m/$m"); ?>
<div class="cw-util-bar">
    <?php $cwGlobal->getBreadcrumbs($season, "Match: $m"); ?>
    <?php $cwGlobal->getUserTray($cuid); ?>
</div>
<?php $cwGlobal->process_svr_status("match"); ?>
<div class="cw-header">
    <div class="flex items-center cw-thirds">
        <div class="cw-title-box">
            <h1>Week <?php echo $match->matchWeek; ?> Matchup</h1>
        </div>
        <div class="flex justify-center">
            <?php if( $match->isPostponed ) { ?>
                <p class="cw-tag bg-danger">Postponed</p>
            <?php } ?>
        </div>
        <div class="cw-actions">
            <div class="flex flex-col items-center">
                <p class="cw-tag"><?php echo $matchDatetime->format("F jS"); ?></p>
                <p class="cw-tag" style="margin-top:-5px;"><?php echo $matchDatetime->format("g:i A"); ?> EST</p>
            </div>
            <?php if( $isManager || $isTeam1Capt || $isTeam2Capt ) { ?>
                <a class="btn btn-primary" href="/s/<?php echo $s; ?>/m/<?php echo $m; ?>/postpone">Postpone</a>
            <?php } ?>
        </div>
    </div>
</div>
<div class="row cw-row">
    <div class="col-12 p-0">
        <div class="cw-vs-box">
            <a class="team1" href="/s/<?php echo $s; ?>/t/<?php echo $team1_data->slug; ?>">
                <?php echo $team1_data->title; ?>
            </a>
            <span class="cw-vs">VS</span>
            <a class="team2" href="/s/<?php echo $s; ?>/t/<?php echo $team2_data->slug; ?>">
                <?php echo $team2_data->title; ?>
            </a>
        </div>
    </div>
    <?php if ( $rightNow < $matchDatetime ) { 
        // if before match, attendance ?>
        <div class="col-6">
            <div class="cw-box">
                <h2><?php echo $team1_data->title; ?> Attendance</h2>
                <div class="cw-player-lineitem-header">
                    <p>Player</p>
                    <p>Reported Attendance</p>
                </div>
                <?php // PRINT TEAM 1 ATTENDANCE
                $t1_playerList = TeamDB::getPlayerList($team1_data->slug, $season->id);
                foreach($t1_playerList as $player) { ?>
                    <div class="cw-player-lineitem">
                        <div class="cw-player-title">
                            <?php echo $player->WPID == $team1_data->capt ? '<i class="bi bi-star-fill"></i>' : ''; ?>
                            <p><?php echo $player->displayName ? $player->displayName : "make a profile"; ?></p>
                        </div>
                        <?php if ($cuid == $player->WPID || $isTeam1Capt || $isManager) { 
                            MatchDB::printAttendanceOptions($match_data->id, $player->WPID); 
                        } else {
                            MatchDB::printCurrentAttendance($match_data->id, $player->WPID);
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="col-6">
            <div class="cw-box">
                <h2><?php echo $team2_data->title; ?> Attendance</h2>
                <div class="cw-player-lineitem-header">
                    <p>Player</p>
                    <p>Reported Attendance</p>
                </div>
                <?php // PRINT TEAM 1 ATTENDANCE
                $t2_playerList = TeamDB::getPlayerList($team2_data->slug, $season->id);
                foreach($t2_playerList as $player) { ?>
                    <div class="cw-player-lineitem">
                        <div class="cw-player-title">
                            <?php echo $player->WPID == $team2_data->capt ? '<i class="bi bi-star-fill"></i>' : ''; ?>
                            <p><?php echo $player->displayName ? $player->displayName : "make a profile"; ?></p>
                        </div>
                        <?php if ($cuid == $player->WPID || $isTeam2Capt || $isManager) {
                            MatchDB::printAttendanceOptions($match_data->id, $player->WPID); 
                        } else {
                            MatchDB::printCurrentAttendance($match_data->id, $player->WPID);
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } else { // if after match, report ?>
        <p>After Match Datetime</p>
    <?php } ?>
</div>