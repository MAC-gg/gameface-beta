<!-- Create Teams View -->
<!-- /inc/season/teams.php -->
<?php
    $approved_player_regs = $SeasonRegDB->getApprovedList($season->id);
?>
<?php $cwGlobal->breadcrumbs($season, "<i class='bi bi-lock-fill'></i> Create Teams"); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Create Teams</h1>
        </div>
        <div class="cw-actions">
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="row cw-row">
    <div class="col-12">
        <div class="cw-box">
            <h2>Available Players</h2>
            <?php if($approved_player_regs && count($SeasonRegDB->getProjTeamList($season->id, "")) != 0) { ?>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="savetempteams"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/teams">
                <input type="hidden" name="season" value="<?php echo $season->id; ?>">
                <input type="hidden" name="teamNum" value="<?php echo $season->teamNum; ?>">
                <input type="hidden" name="teamSize" value="<?php echo $season->teamSize; ?>">
                <table>
                    <tr>
                        <th>Player</th>
                        <th>In-game Username</th>
                        <th>Total Hours</th>
                        <th>Recent Hours</th>
                        <th>Pref Pos</th>
                        <th>Backup Pos</th>
                        <th>Party</th>
                        <th>Capt</th>
                        <th>Select Team</th>
                    </tr>
                    <?php foreach($approved_player_regs as $player_reg) {
                        $player_prof = $UserDB->getProfile($player_reg->player);
                        $user_data = get_userdata($player_reg->player);
                        if($player_reg->tempTeam == "") { ?>
                            <tr>
                                <td><a href="/u/<?php echo strtolower($user_data->user_login);?>"><?php echo isset($player_prof->displayName) ? $player_prof->displayName : $user_data->user_login; ?></a></td>
                                <td><?php echo $player_reg->gameUsername; ?></td>
                                <td><?php echo $player_reg->hrsTotal; ?></td>
                                <td><?php echo $player_reg->hrs3Months; ?></td>
                                <td><?php echo $player_reg->prefPos; ?></td>
                                <td><?php echo $player_reg->otherPos; ?></td>
                                <td><?php echo $player_reg->partyMem; ?></td>
                                <td><?php echo $player_reg->wantsCap; ?></td>
                                <td>
                                    <select name="inc-tempTeam-<?php echo $player_reg->player; ?>" id="inc-tempTeam-<?php echo $player_reg->player; ?>" class="form-select">
                                        <option>--</option>
                                        <?php for($i = 1; $i <= $season->teamNum; $i++) { 
                                            if( count($SeasonRegDB->getProjTeamList($season->id, "Team $i")) != $season->teamSize) { ?>
                                                <option>Team <?php echo $i; ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                        <?php }
                    } // CLOSE FOREACH ?>
                </table>

                <div class="cw-action-box">
                    <button class="btn btn-primary" title="Update Teams">Update Teams</button>
                </div>
            </form>
            <?php } else { ?>
                <p><strong>Good Job!</strong> There are no other available players at this time.</p>
            <?php } ?>
        </div>
    </div>
    <div class="col-12">
        <div class="cw-box">
            <h2>Projected Teams</h2>
            <div class="cw-proj-teams">
                <?php for($i = 1; $i <= $season->teamNum; $i++) { 
                    $teamPlayers = $SeasonRegDB->getProjTeamList( $season->id, "Team $i" );
                    if(isset($teamPlayers) && !empty($teamPlayers)) { ?>
                        <div class="cw-player-list">
                            <h3>Team <?php echo $i; ?></h3>
                            <table>
                                <tr>
                                    <th>Player</th>
                                    <th>In-game</th>
                                    <th>Total Hrs</th>
                                    <th>Pos</th>
                                    <th>Capt</th>
                                    <th>Remove</th>
                                </tr>
                                    <?php foreach ($teamPlayers as $player_reg) { 
                                        $player_prof = $UserDB->getProfile($player_reg->player);
                                        $user_data = get_userdata($player_reg->player); ?>
                                        <tr>
                                            <td><a href="/u/<?php echo strtolower($user_data->user_login);?>"><?php echo isset($player_prof->displayName) ? $player_prof->displayName : $user_data->user_login; ?></a></td>
                                            <td><?php echo $player_reg->gameUsername; ?></td>
                                            <td><?php echo $player_reg->hrsTotal; ?></td>
                                            <td><?php echo $player_reg->prefPos; ?></td>
                                            <td>
                                                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                                    <input type="hidden" name="action" value="maketeamcapt"><!-- creates hook for php plugin -->
                                                    <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/teams">
                                                    <input type="hidden" name="regid" value="<?php echo $player_reg->id; ?>">
                                                    <button class="btn btn-primary" title="Make this player Captain"><i class="bi bi-<?php echo $player_reg->tempTeamCapt == 0 ? "star" : "star-fill"; ?>"></i></button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                                                    <input type="hidden" name="action" value="removetempteam"><!-- creates hook for php plugin -->
                                                    <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/teams">
                                                    <input type="hidden" name="regid" value="<?php echo $player_reg->id; ?>">
                                                    <button class="btn btn-secondary" title="Remove"><i class="bi bi-x"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </table>
                        </div>
                    <?php }
                } ?>
            </div><!-- END TEAMS BOX -->

            <?php if ( count($SeasonRegDB->getProjTeamList($season->id, "")) == 0 ) { ?>
                <div class="cw-action-box">
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                        <input type="hidden" name="action" value="maketeams"><!-- creates hook for php plugin -->
                        <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>">
                        <input type="hidden" name="season" value="<?php echo $season->id; ?>">
                        <input type="hidden" name="teamSize" value="<?php echo $season->teamSize; ?>">
                        <input type="hidden" name="teamNum" value="<?php echo $season->teamNum; ?>">
                        <button class="btn btn-primary">Make Teams</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</div>