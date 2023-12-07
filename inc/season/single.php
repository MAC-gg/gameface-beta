<!-- Season Single View -->
<!-- /inc/season/single.php -->

<?php // SETUP DATA
$user_reg = $SeasonRegDB->getSingleBySAndP($season->id, get_current_user_id());
$total_players_req = $season->teamNum * $season->teamSize;
$total_waitlist_req = $season->waitlistSize;
$current_approved_players = count($SeasonRegDB->getApprovedList($season->id));
$current_approved_waitlist = count($SeasonRegDB->getApprovedWaitlist($season->id));
$statuses = array(); 
if($current_approved_waitlist == $total_waitlist_req) {
    array_push($statuses, array("bg-danger", "Closed Waitlist"));
} else {
    array_push($statuses, array("bg-warning", "Open Waitlist"));
} ?>

<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <div class="cw-breadcrumbs">
                <a href="/"><i class="bi bi-house-fill"></i></a> > 
                <a href="<?php echo $SeasonDB->breadcrumbURL; ?>">Season List</a> > 
                <span><?php echo $season->title; ?></span>
            </div>
            <h1><?php echo $season->title; ?></h1>
            <div class="cw-season-status">
                <p>Status: <span class="bg-primary"><?php echo $season->status; ?></span>
                    <?php foreach($statuses as $status) { ?>
                        <span class="<?php echo $status[0]; ?>"><?php echo $status[1]; ?></span>
                    <?php } ?>
                </p>
                <?php if( $user_reg && $user_reg->isApproved && $season->status == "Registering" ) { ?>
                        <p>Waiting for more players (<?php echo $count; ?>/<?php echo $total; ?>)</p>
                <?php } ?>
            </div>
        </div>
        <div class="cw-actions">
            <?php // MANAGER ACTIONS
            if( $season->manager == get_current_user_id() ) { 
                switch ( $season->status ) {
                    case 'Registering': ?>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/approve">Approve Players</a><?php
                        break;
                    case 'Creating Teams': ?>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/approve">Waitlist</a>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/teams">Create Teams</a><?php
                        break;
                }
            } ?>
            <?php // PLAYER ACTIONS / STATUS DISPLAY
            if( $season->status == "Registering" ) {
                if( !$user_reg ) { ?>
                    <a class="btn btn-primary" href="/s/<?php echo $s; ?>/register">Register to Play</a>
                <?php } else { 
                    $status = $user_reg->isApproved ? "Approved" : "Pending"; ?>
                    <div class="cw-status-tags">
                        <div>
                            <p>Your Registration Status</p>
                            <p class="<?php echo $status; ?>"><strong><?php echo $status; ?></strong></p>
                        </div>
                    </div>
                <?php }
            } else {
                if( !$user_reg ) { 
                    if( $season->waitlistSize != $current_approved_waitlist ) { ?>
                        <a class="btn btn-primary" href="/s/<?php echo $s; ?>/register">Join Waitlist</a>
                    <?php } ?>
                <?php } else { 
                    $status = $user_reg->isApproved ? "Approved" : "Pending"; ?>
                        <div class="cw-status-tags">
                            <div>
                                <p>Your <?php echo $user_reg->isWaitlist ? "Waitlist" : "Registration"; ?> Status</p>
                                <p class="<?php echo $status; ?>"><strong><?php echo $status; ?></strong></p>
                            </div>
                        </div>
                <?php }
            } ?>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="cw-content-box cw-season-single">
    <table>
        <tr>
            <th>Title</th>
            <th>Game</th>
            <th>Player Level</th>
            <th>Day/Time</th>
            <th>Manager</th>
            <th>Status</th>
        </tr>
        <tr>
            <td><?php echo $season->title; ?></td>
            <td><?php echo $season->game; ?></td>
            <td><?php echo $season->playerLvl; ?></td>
            <td><?php echo $season->matchDay; ?>s @ <?php echo $season->matchTime; ?></td>
            <td><?php echo $season->manager; ?></td>
            <td><?php echo $season->status; ?></td>
        </tr>
    </table>
</div>
