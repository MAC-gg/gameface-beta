<!-- League List View -->
<!-- /inc/league/vws/VWlist.php -->
<!-- ===== NOTES ===== -->

<div class="container container--narrow page-section">
    
    <table class="pet-adoption-table">
    <tr>
        <th>Name</th>
        <th>Link</th>
        <th>Number of Teams</th>
        <th>Team Size</th>
        <th>Game</th>
        <?php if (current_user_can('administrator')){ ?>
        <th>Delete</th>
        <?php } ?>
    </tr>
    <?php
        foreach($LeagueDB->getList() as $league) {
            $site_url = site_url();
            $leagueLink = "";
            if( $league->Link != "" ) {
                $leagueLink = "<a href=" . $site_url . "/l/" . $league->Link . ">";
            }
            ?>
        <tr>
            <td><?php echo $leagueLink; echo $league->leagueName; echo ($leagueLink ? "</a>" : "");?></td>
            <td><?php echo $league->Link; ?></td>
            <td><?php echo $league->TeamNum; ?></td>
            <td><?php echo $league->TeamSize; ?></td>
            <td><?php echo $league->Game; ?></td>
            <?php if(current_user_can('administrator')) { ?>
            <td style="text-align:center;">
                <form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="POST">
                <input type="hidden" name="action" value="deleteleague">
                <input type="hidden" name="idtodelete" value="<?php echo $league->id; ?>">
                <button class="delete-pet-button">X</button>
                </form>
            </td>
            <?php } ?>
        </tr>
        <?php } // CLOSE FOREACH
    ?>
    </table>

    <?php 
    if(current_user_can('administrator')) { 
        $protocols = array('http://', 'http://www.', 'www.', 'https://', 'https://www.');
        $url = str_replace($protocols, '', get_bloginfo('wpurl')); ?>

        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="create-pet-form" method="POST">
            <input type="hidden" name="action" value="createleague"><!-- creates hook for php plugin -->

            <div class="field-container">
                <label for="incName">League Name</label><br />
                <input type="text" name="incName" placeholder="One Fire League of Absolute Legends">
            </div>
            
            <div class="field-container">
                <label for="incLink">League Link</label> (ex. <?php echo $url; ?>/l/your-league-link)<br />
                <input type="text" name="incLink" placeholder="one-fire-league-of-absolute-legends">
            </div>

            <div class="field-container">
                <label for="incGame">Game</label><br />
                <input type="text" name="incGame" placeholder="League of Legends">
            </div>

            <div class="field-container">
                <label for="incTeamsNum">Total Teams in League</label><br />
                <input type="text" name="incTeamNum" placeholder="4">
            </div>

            <div class="field-container">
                <label for="incTeamSize">Total Players on Each Team</label><br />
                <input type="text" name="incTeamSize" placeholder="5">
            </div>

            <button>Add League</button>
        </form>
    <?php } ?>
    
</div>