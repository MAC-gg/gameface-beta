<!-- Season Single View -->
<!-- /inc/season/single.php -->
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
    <?php $player_regs = $SeasonRegDB->getList($season->id);
    if($player_regs) { ?>
        <table>
        <tr>
            <th>Player</th>
        </tr>
        <?php
            foreach($player_regs as $player_reg) {
                $site_url = site_url();
                ?>
            <tr>
                <td><?php echo $player_reg->player; ?></td>
            </tr>
            <?php } // CLOSE FOREACH
        ?>
        </table>
    <?php } else { ?>
        <p>There are no seasons available at this time.</p>
    <?php } ?>
</div>
