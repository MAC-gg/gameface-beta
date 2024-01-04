<!-- Template View -->
<!-- /inc/template.php -->
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
$cuid = get_query_var('cw-admin-cuid', get_current_user_id());
$isManager = $season->manager == $cuid;
$isTeam1Capt = $team1_data->capt == $cuid;
$isTeam2Capt = $team2_data->capt == $cuid;
 ?>
<?php $cwGlobal->process_svr_status("match"); ?>
<?php $cwGlobal->breadcrumbs($season, "Match: $m"); ?>
<?php // DEVELOPMENT ENV ONLY //
if(current_user_can('administrator')) { echo $cuid; ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
        <input type="hidden" name="action" value="cwadmincuid"><!-- creates hook for php plugin -->
        <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/m/<?php echo $m; ?>?cw-admin-cuid=">
        <label for="field-cuid" class="form-label">Change logged in player ID</label>
        <select name="field-cuid" id="field-cuid" class="form-select">
            <option value="1"<?php echo $cuid == 1 ? " selected" : ""; ?>>Admin (ID:1)</option>
            <option value="7"<?php echo $cuid == 7 ? " selected" : ""; ?>>Leela (ID:7)</option>
            <option value="6"<?php echo $cuid == 6 ? " selected" : ""; ?>>Fry (ID:6)</option>
            <option value="8"<?php echo $cuid == 8 ? " selected" : ""; ?>>Professy (ID:5)</option>
            <option value="9"<?php echo $cuid == 9 ? " selected" : ""; ?>>Homer (ID:9)</option>
            <option value="10"<?php echo $cuid == 10 ? " selected" : ""; ?>>Marge (ID:10)</option>
            <option value="11"<?php echo $cuid == 11 ? " selected" : ""; ?>>Bart (ID:11)</option>
            <option value="16"<?php echo $cuid == 16 ? " selected" : ""; ?>>Peter (ID:16)</option>
            <option value="17"<?php echo $cuid == 17 ? " selected" : ""; ?>>Lois (ID:17)</option>
        </select>
        <button class="btn btn-primary">Submit</button>
    </form>
<?php } ?>
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
                <?php $t1_playerList = explode(',', $team1_data->playerList);
                foreach( $t1_playerList as $player_id ) {
                    $player_prof = $UserDB->getProfile($player_id);
                    $player_data = get_userdata($player_id); 
                    $current_atten = $MatchDB->getCurrentAtten($match_data->id, $player_id); ?>
                    <p><?php echo $player_prof ? $player_prof->nickname : $player_data->user_login; ?>
                    <?php // Show attendance form when user is logged in
                    if( $player_id == $cuid || $isManager || $isTeam1Capt ) { 
                        // Show attendance form
                        $yes = array("1", "Yes", "success");
                        $no = array("0", "No", "danger");
                        $maybe = array("?", "Maybe", "warning");
                        $attenOpts = array($yes, $no, $maybe);
                        foreach( $attenOpts as $opt ) { ?>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                <input type="hidden" name="action" value="markattendance"><!-- creates hook for php plugin -->
                                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/m/<?php echo $m; ?>">
                                <input type="hidden" name="field-match" value="<?php echo $match_data->id; ?>">
                                <input type="hidden" name="field-player" value="<?php echo $player_id; ?>">
                                <input type="hidden" name="field-atten" value="<?php echo $opt[0]; ?>">
                                <button class="btn btn-<?php echo $opt[2]; ?>" title="<?php echo $opt[1]; ?>"<?php echo $opt == $current_atten ? " disabled" : ""; ?>><?php echo $opt[1]; ?></button>
                            </form>
                        <?php }
                    }
                } ?>
            </div>
        </div>
        <div class="col-6">
            <div class="cw-box">
                <h2><?php echo $team2_data->title; ?> Attendance</h2>
                <?php $t2_playerList = explode(',', $team2_data->playerList);
                foreach( $t2_playerList as $player_id ) {
                    $player_prof = $UserDB->getProfile($player_id);
                    $player_data = get_userdata($player_id);
                    $current_atten = $MatchDB->getCurrentAtten($match_data->id, $player_id); ?>
                    <p><?php echo $player_prof ? $player_prof->nickname : $player_data->user_login; ?>
                    <?php // Show attendance form when user is logged in
                    if( $player_id == $cuid || $isManager || $isTeam2Capt ) { 
                        // Show attendance form
                        $yes = array("1", "Yes", "success");
                        $no = array("0", "No", "danger");
                        $maybe = array("?", "Maybe", "warning");
                        $attenOpts = array($yes, $no, $maybe);
                        foreach( $attenOpts as $opt ) { ?>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                <input type="hidden" name="action" value="markattendance"><!-- creates hook for php plugin -->
                                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/m/<?php echo $m; ?>">
                                <input type="hidden" name="field-match" value="<?php echo $match_data->id; ?>">
                                <input type="hidden" name="field-player" value="<?php echo $player_id; ?>">
                                <input type="hidden" name="field-atten" value="<?php echo $opt[0]; ?>">
                                <button class="btn btn-<?php echo $opt[2]; ?>" title="<?php echo $opt[1]; ?>"<?php echo $opt == $current_atten ? " disabled" : ""; ?>><?php echo $opt[1]; ?></button>
                            </form>
                        <?php }
                    }
                } ?>
            </div>
        </div>
    <?php } else { // if after match, report ?>
        <p>After Match Datetime</p>
    <?php } ?>
</div>