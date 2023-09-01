<!-- League Single View -->
<!-- /inc/league/vws/VWsingle.php -->

<div class="container container--narrow page-section">
    
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
            <td><?php echo $league->leagueName; ?></td>
            <td><?php echo $league->Link; ?></td>
            <td><?php echo $league->Game; ?></td>
            <td><?php echo $league->TeamNum; ?></td>
            <td><?php echo $league->TeamSize; ?></td>
            <?php if(current_user_can('administrator')) { ?>
            <td style="text-align:center;"><a href="/l/<?php echo $l;?>/manage" class="update-button">X</a></td>
            <?php } ?>
        </tr>
        <?php } // CLOSE FOREACH
    ?>
    </table>

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