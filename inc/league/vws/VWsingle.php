<!-- League Single View -->
<!-- /inc/league/vws/VWsingle.php -->
<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">League <?php echo $l ." ". $p; ?></h1>
    <div class="page-banner__intro">
        <p>Providing forever homes one search at a time.</p>
    </div>
    </div>  
</div>

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