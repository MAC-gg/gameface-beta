<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Manage</h1>
    <div class="page-banner__intro">
        <p>Providing forever homes one search at a time.</p>
    </div>
    </div>  
</div>

<div class="container container--narrow page-section">

    <p>This page took <strong><?php echo timer_stop();?></strong> seconds to prepare. Found <strong><?php echo number_format($data->count); ?></strong> results (showing the first <?php echo count($data->leagues) ?>).</p>
    
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
            foreach($data->l as $league) { ?>
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