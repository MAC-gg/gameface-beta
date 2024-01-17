<!-- Season Single View -->
<!-- /inc/season/single.php -->

<?php  // SETUP DATA
$cuid = get_query_var('cw-admin-cuid', $current_user->ID);
$profileData = $UserDB->getProfile($cuid);
$accountData = $UserDB->getAccount($cuid);

// PLAYER REGISTRATION
// DEFAULT VIEW VALUES
$PorW = "Players";
$total = $season->teamNum * $season->teamSize;
$count = count($SeasonRegDB->getApprovedList($season->id));
$player_regs = $SeasonRegDB->getUnapprovedList($season->id);
$approved_player_regs = $SeasonRegDB->getApprovedList($season->id);

// WAITLIST VALUES
if( $season->status != "Registering" ) {
    $PorW = "Waitlist";
    $total = $season->waitlistSize;
    $count = count($SeasonRegDB->getApprovedWaitlist($season->id));
    $player_regs = $SeasonRegDB->getUnapprovedWaitlist($season->id);
    $approved_player_regs = $SeasonRegDB->getApprovedWaitlist($season->id);
}

$complete_progress = $total != 0 ? ($count / $total) * 100 : 100;

$cwGlobal->dev_only_options($cuid, "/s/$s/"); ?>
<div class="cw-util-bar">
    <?php $cwGlobal->getBreadcrumbs($season, "Create Teams"); ?>
    <?php $cwGlobal->getUserTray($cuid); ?>
</div>
<?php $cwGlobal->process_svr_status("season"); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Approve <?php echo $PorW; ?></h1>
        </div>
        <div class="cw-actions">
            <?php if ($complete_progress == 100) {
                if($season->status == "Registering") { ?>
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                        <input type="hidden" name="action" value="closereg"><!-- creates hook for php plugin -->
                        <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>">
                        <input type="hidden" name="season" value="<?php echo $season->id; ?>">
                        <button class="btn btn-primary btn-lock-in" title="Lock-in"><i class="bi bi-lock-fill"></i> Lock-in</button>
                    </form>
                <?php } else { ?>
                        <p class="cw-status">Waitlist Status<br />
                        <span class="cw-tag bg-danger">Closed</span></p>
                    </div>
                <?php }
            } else { 
                if($season->status == "Registering") { /* PROGRESS BAR */ ?>
                    <div class="cw-unlock-progress">
                        <button class="btn btn-primary" title="Lock-in" disabled><i class="bi bi-lock-fill"></i> Lock-in</button>
                        <div class="progress" role="progressbar" aria-label="Completion Progress" aria-valuenow="<?php echo $complete_progress; ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width:<?php echo $complete_progress; ?>%;"></div>
                        </div>
                        <p><i class="bi bi-unlock-fill"></i> Approve Players (<?php echo $count; ?>/<?php echo $total; ?>)</p>
                    </div>
                <?php } else { ?>
                    <p class="cw-status">Waitlist Status<br />
                    <span class="cw-tag bg-success">Open</span></p>
                <?php }
            } ?>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="row cw-row">
    <div class="col-12">
        <div class="cw-box">
            <h2>Unapproved <?php echo $PorW; ?></h2>
            <?php if($player_regs) { ?>
                <table>
                <tr>
                    <th>Player</th>
                    <th>In-game Username</th>
                    <th>Total Hours</th>
                    <th>Recent Hours</th>
                    <th>Pref Pos</th>
                    <th>Backup Pos</th>
                    <th>Other Games</th>
                    <th>Capt Pool</th>
                    <th>Actions</th>
                </tr>
                <?php
                    foreach($player_regs as $player_reg) {
                        $player_prof = $UserDB->getProfile($player_reg->player);
                        $user_data = get_userdata($player_reg->player);
                        ?>
                    <tr>
                        <td><a href="/u/<?php echo strtolower($user_data->user_login);?>"><?php echo isset($player_prof->displayName) ? $player_prof->displayName : $user_data->user_login; ?></a></td>
                        <td><?php echo $player_reg->gameUsername; ?></td>
                        <td><?php echo $player_reg->hrsTotal; ?></td>
                        <td><?php echo $player_reg->hrs3Months; ?></td>
                        <td><?php echo $player_reg->prefPos; ?></td>
                        <td><?php echo $player_reg->otherPos; ?></td>
                        <td><?php echo $player_reg->otherCompGames; ?></td>
                        <td><?php echo $player_reg->wantsCap; ?></td>
                        <td>
                            <?php if($complete_progress != 100) { ?> 
                                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                    <input type="hidden" name="action" value="togapprovereg"><!-- creates hook for php plugin -->
                                    <input type="hidden" name="redirect" value="<?php echo $s; ?>/approve">
                                    <input type="hidden" name="regid" value="<?php echo $player_reg->id; ?>">
                                    <button class="btn btn-success btn-approve" title="Approve"><i class="bi bi-check"></i></button>
                                </form>
                            <?php } ?>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                <input type="hidden" name="action" value="deletereg"><!-- creates hook for php plugin -->
                                <input type="hidden" name="redirect" value="<?php echo $s; ?>/approve">
                                <input type="hidden" name="regid" value="<?php echo $player_reg->id; ?>">
                                <button class="btn btn-danger btn-delete" title="Delete" onclick="return confirm('Are you sure?');"><i class="bi bi-trash3"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php } // CLOSE FOREACH
                ?>
                </table>
            <?php } else { ?>
                <p>There are no players registered at this time.</p>
            <?php } ?>
        </div>
    </div>
    <div class="col-12">
        <div class="cw-box">
            <h2>Approved <?php echo $PorW; ?></h2>
            <?php if($approved_player_regs) { ?>
                <table>
                <tr>
                    <th>Player</th>
                    <th>In-game Username</th>
                    <th>Total Hours</th>
                    <th>Recent Hours</th>
                    <th>Pref Pos</th>
                    <th>Backup Pos</th>
                    <th>Other Games</th>
                    <th>Capt Pool</th>
                    <th>Disapprove?</th>
                </tr>
                <?php
                    foreach($approved_player_regs as $player_reg) {
                        $player_prof = $UserDB->getProfile($player_reg->player);
                        $user_data = get_userdata($player_reg->player);
                        ?>
                    <tr>
                        <td><?php echo isset($player_prof->displayName) ? $player_prof->displayName : $user_data->user_login; ?></td>
                        <td><?php echo $player_reg->gameUsername; ?></td>
                        <td><?php echo $player_reg->hrsTotal; ?></td>
                        <td><?php echo $player_reg->hrs3Months; ?></td>
                        <td><?php echo $player_reg->prefPos; ?></td>
                        <td><?php echo $player_reg->otherPos; ?></td>
                        <td><?php echo $player_reg->otherCompGames; ?></td>
                        <td><?php echo $player_reg->wantsCap; ?></td>
                        <td>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                <input type="hidden" name="action" value="togapprovereg"><!-- creates hook for php plugin -->
                                <input type="hidden" name="redirect" value="<?php echo $s; ?>/approve">
                                <input type="hidden" name="regid" value="<?php echo $player_reg->id; ?>">
                                <button class="btn btn-secondary btn-disapprove" title="Disapprove"><i class="bi bi-x"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php } // CLOSE FOREACH
                ?>
                </table>
            <?php } else { ?>
                <p>There are no approved players at this time.</p>
            <?php } ?>
        </div>
    </div>
</div>
