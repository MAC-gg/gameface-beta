<!-- Season Single View -->
<!-- /inc/season/single.php -->

<?php // SETUP DATA

// MANAGER USER DATA
$manager_prof = $UserDB->getProfile($season->manager);
$manager_data = get_userdata($season->manager);

// GET LOGGED IN USER REGISTRATION
$user_reg = $SeasonRegDB->getSingleBySAndP($season->id, get_current_user_id());
$user_reg_type_label = ($user_reg && $user_reg->isWaitlist) ? "Waitlist" : "Registration";
$user_reg_status_label = ($user_reg && $user_reg->isApproved) ? "Approved" : "Pending";

// SEASON INFO VARS
$total_players_req = $season->teamNum * $season->teamSize;
$total_waitlist_req = $season->waitlistSize;
$current_approved_players = count($SeasonRegDB->getApprovedList($season->id));
$current_approved_waitlist = count($SeasonRegDB->getApprovedWaitlist($season->id));

// SEASON STATUS VARS
$isUserManager = $season->manager == get_current_user_id();
$isWaitlistOpen = ($season->status != "Registering" && $total_waitlist_req >= $current_approved_waitlist) ? true : false; ?>

<?php $cwGlobal->breadcrumbs($season); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Season: <strong><?php echo $season->title; ?></strong></h1>
        </div>
        <div class="cw-actions">
            <?php // MANAGER ACTIONS
            if( $isUserManager ) { 
                switch ( $season->status ) {
                    case 'Registering': ?>
                        <a class="btn btn-secondary" href="/s/<?php echo $s; ?>/approve">Approve Players</a><?php
                        break;
                    case 'Creating Teams': ?>
                        <a class="btn btn-secondary" href="/s/<?php echo $s; ?>/approve">View Waitlist</a>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/teams">Create Teams</a><?php
                        break;
                }
            } ?>
            <?php // PLAYER ACTIONS / STATUS DISPLAY
            switch ( $season->status ) {
                case 'Registering': 
                    if( !$user_reg ) { // NO REG - PRINT BUTTON ?>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/register">Register to Play</a><?php 
                    } else { // REGISTERED - PRINT STATUS ?>
                        <p>Your Registration Status</p>
                        <p class="<?php echo $user_reg_status_label; ?>"><strong><?php echo $user_reg_status_label; ?></strong></p>
                    <?php }
                    break;
                case 'Creating Teams': 
                    if( !$user_reg ) { // NO REG 
                        if( $isWaitlistOpen ) { // IF WAITLIST OPEN - PRINT BUTTON ?>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/register">Join Waitlist</a>
                    <?php } } else { // REGISTERED - PRINT STATUS ?>
                        <p>Your <?php echo $user_reg_type_label; ?> Status</p>
                        <p class="<?php echo $user_reg_status_label; ?>"><strong><?php echo $user_reg_status_label; ?></strong></p><?php
                    }
                    break;
            } ?>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="row cw-row">
    <div class="col-4">
        <div class="cw-box">
            <h2>Season Info</h2>
            <div class="cw-info-group">
                <p class="cw-label">Status</p>
                <p class="cw-info">
                    <span class="cw-tag bg-primary"><?php echo $season->status; ?></span>
                    <?php if( $user_reg && $user_reg->isApproved && $season->status == "Registering" ) { ?>
                        <span>Waiting for more players (<?php echo $current_approved_players; ?>/<?php echo $total_players_req; ?>)</span>
                    <?php } ?>
                </p>
            </div>
            <?php if( $season->status != "Registering" ) { ?>
                <div class="cw-info-group">
                    <p class="cw-label">Waitlist</p>
                    <p class="cw-info"><span class="cw-tag <?php echo $isWaitlistOpen ? "bg-success" : "bg-warning"; ?>"><?php echo $isWaitlistOpen ? "Open" : "Closed"; ?></span></p>
                </div>
            <?php } ?>
            <div class="cw-info-group">
                <p class="cw-label">Game</p>
                <p class="cw-info"><?php echo $season->game; ?></p>
            </div>
            <div class="cw-info-group">
                <p class="cw-label">Player Level</p>
                <p class="cw-info"><?php echo $season->playerLvl; ?></p>
            </div>
            <div class="cw-info-group">
                <p class="cw-label">Day/Time</p>
                <p class="cw-info"><?php echo $season->matchDay; ?>s @ <?php echo $season->matchTime; ?></p>
            </div>
            <div class="cw-info-group">
                <p class="cw-label">Manager</p>
                <p class="cw-info"><a href="/u/<?php echo strtolower($manager_data->user_login);?>"><?php echo isset($manager_prof->nickname) ? $manager_prof->nickname : $manager_data->user_login; ?></a></p>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="cw-box">
            <h2>Standings</h2>
            <?php $teams = $TeamDB->getList($season->id);
            if( $teams ) { ?>
                <table>
                    <tr>
                        <th>Team</th>
                        <th title="Wins">W</th>
                        <th title="Losses">L</th>
                        <th title="Games Won">GW</th>
                    </tr>
                    <?php foreach( $teams as $team ) { ?>
                        <tr>
                            <td class="cw-team-display"><a href="/s/<?php echo $s . "/t/" . $team->slug; ?>"><?php echo $team->title; ?></a></td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </div>
</div>
