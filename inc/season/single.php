<!-- Season Single View -->
<!-- /inc/season/single.php -->
<div class="cw-header">
    <div class="flex items-center justify-between">
        <h1><?php echo $season->title; ?></h1>
        <div class="cw-actions">
            <?php // USER ACTIONS
            if( $SeasonRegDB->getSingle(get_current_user_id()) ) { ?>
                <p>You are Registered!</p>
            <?php } else { ?>
                <a class="btn btn-primary" href="/s/<?php echo $s; ?>/register">Register to Play</a>
            <?php } ?>
            <?php // MANAGER ACTIONS
            if( $season->manager == get_current_user_id() ) { ?>
                <a class="btn btn-primary" href="/s/<?php echo $s; ?>/approve">Approve Players</a>
            <?php } ?>
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
            <td><?php echo $season->dayTime; ?></td>
            <td><?php echo $season->manager; ?></td>
            <td><?php echo $season->sstatus; ?></td>
        </tr>
    </table>
</div>
