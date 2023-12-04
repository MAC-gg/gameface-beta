<!-- Season Single View -->
<!-- /inc/season/single.php -->

<?php // INCLUDE THESE FILES
    wp_enqueue_script('cw_regApprovalActions');
?>

<div class="cw-header">
    <div class="flex items-center justify-between">
        <h1>Approve Players: <?php echo $season->title; ?></h1>
        <div class="cw-actions">
            <a class="btn btn-secondary" href="/s/<?php echo $s; ?>">Cancel</a>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="cw-content-box">
    <div class="cw-content-part">
        <h2>Unapproved Players</h2>
        <?php $player_regs = $SeasonRegDB->getUnapprovedList($season->id);
        if($player_regs) { ?>
            <table>
            <tr>
                <th>Player</th>
                <th>Hours (Total)</th>
                <th>Hours (3 months)</th>
                <th>Preferred Pos</th>
                <th>Second Pos</th>
                <th>Other Comp Games</th>
                <th>Approve?</th>
            </tr>
            <?php
                foreach($player_regs as $player_reg) {
                    $site_url = site_url();
                    $player_prof = $UserDB->getProfile($player_reg->player);
                    $user_data = get_userdata($player_reg->player);
                    ?>
                <tr>
                    <td><a href="/u/<?php echo strtolower($user_data->user_login);?>"><?php echo isset($player_prof->nickname) ? $player_prof->nickname : $user_data->user_login; ?></a></td>
                    <td><?php echo $player_reg->hrsTotal; ?></td>
                    <td><?php echo $player_reg->hrs3Months; ?></td>
                    <td><?php echo $player_reg->prefPos; ?></td>
                    <td><?php echo $player_reg->otherPos; ?></td>
                    <td><?php echo $player_reg->otherCompGames; ?></td>
                    <td><button class="btn btn-success btn-approve" data-regid="<?php echo $player_reg->id; ?>"><i class="bi bi-check"></i></button></td>
                </tr>
                <?php } // CLOSE FOREACH
            ?>
            </table>
        <?php } else { ?>
            <p>There are no players registered at this time.</p>
        <?php } ?>
    </div>
    <div class="cw-content-part">
        <h2>Approved Players</h2>
        <?php $player_regs = $SeasonRegDB->getApprovedList($season->id);
        if($player_regs) { ?>
            <table>
            <tr>
                <th>Player</th>
                <th>Hours (Total)</th>
                <th>Hours (3 months)</th>
                <th>Preferred Pos</th>
                <th>Second Pos</th>
                <th>Other Comp Games</th>
                <th>Disapprove?</th>
            </tr>
            <?php
                foreach($player_regs as $player_reg) {
                    $site_url = site_url();
                    $player_prof = $UserDB->getProfile($player_reg->player);
                    $user_data = get_userdata($player_reg->player);
                    ?>
                <tr>
                    <td><?php echo isset($player_prof->nickname) ? $player_prof->nickname : $user_data->user_login; ?></td>
                    <td><?php echo $player_reg->hrsTotal; ?></td>
                    <td><?php echo $player_reg->hrs3Months; ?></td>
                    <td><?php echo $player_reg->prefPos; ?></td>
                    <td><?php echo $player_reg->otherPos; ?></td>
                    <td><?php echo $player_reg->otherCompGames; ?></td>
                    <td><button class="btn btn-danger btn-disapprove" title="Disapprove"><i class="bi bi-x"></i></button></td>
                </tr>
                <?php } // CLOSE FOREACH
            ?>
            </table>
        <?php } else { ?>
            <p>There are no approved players at this time.</p>
        <?php } ?>
    </div>
</div>
