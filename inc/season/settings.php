<?php // SETUP DATA
$cuid = get_query_var('cw-admin-cuid', $current_user->ID);
$profileData = $UserDB->getProfile($cuid);
$accountData = $UserDB->getAccount($cuid);

$cwGlobal->dev_only_options($cuid, "/s/$s/"); ?>
<div class="cw-util-bar">
    <?php $cwGlobal->getBreadcrumbs($season); ?>
    <?php $cwGlobal->getUserTray($cuid); ?>
</div>
<?php $cwGlobal->process_svr_status("season"); ?>
<div class="container container--narrow page-section">
    
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
    <input type="hidden" name="action" value="updateLeague"><!-- creates hook for php plugin -->
        <table class="pet-adoption-table">
        <tr>
            <th>Name</th>
            <th>Link</th>
            <th>Number of Teams</th>
            <th>Team Size</th>
            <th>Game</th>
            <?php if (current_user_can('administrator')){ ?>
            <th>Update</th>
            <?php } ?>
        </tr>
        <?php
            foreach($LeagueDB->getL($l) as $league) { ?>
            <tr>
                <td><input type="text" name="upName" disabled value="<?php echo $league->leagueName; ?>"></td>
                <td><input type="text" name="upLink" disabled value="<?php echo $league->leagueLink; ?>"></td>
                <td><input type="text" name="upGame" disabled value="<?php echo $league->game; ?>"></td>
                <td><input type="text" name="upTeamsNum" disabled value="<?php echo $league->numTeams; ?>"></td>
                <td><input type="text" name="upTeamsSize" disabled value="<?php echo $league->teamSize; ?>"></td>
                <?php if(current_user_can('administrator')) { ?>
                <td style="text-align:center;"><button class="update-button">X</button></td>
                <?php } ?>
            </tr>
            <?php } // CLOSE FOREACH
        ?>
        </table>
    </form>

    <?php 
    if(current_user_can('administrator')) { ?>

        <!-- DELET FORM 
        <form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="POST">
        <input type="hidden" name="action" value="deleteleague">
        <input type="hidden" name="idtodelete" value="<?php echo $league->id; ?>">
        <button class="delete-pet-button">X</button>
        </form>
        -->
    <?php } ?>
    
</div>