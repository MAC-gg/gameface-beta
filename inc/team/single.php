<!-- Template View -->
<!-- /inc/template.php -->
<?php // SETUP DATA HERE
$playerList = explode(',', $team->playerList);
$capt_prof = $UserDB->getProfile($team->capt);
$capt_data = get_userdata($team->capt); ?>
<?php $cwGlobal->breadcrumbs($season, array("Team: $team->title", "brand-neutral")); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Team: <strong><?php echo $team->title; ?></strong></h1>
        </div>
        <div class="cw-actions">
            <?php if( $team->capt == get_current_user_id() ) { ?> 
                <a class="btn btn-secondary" href="/s/<?php echo $s; ?>/t/<?php echo $t; ?>/settings"><i class="bi bi-gear-fill"></i></a>
            <?php } ?>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("template"); ?>
</div>
<div class="row cw-row">
    <div class="col-4">
        <div class="cw-box">
            <h2>Team Info</h2>
            <div class="cw-info-group">
                <p class="cw-label">Name</p>
                <p class="cw-info"><?php echo $team->title; ?></p>
            </div>
            <div class="cw-info-group">
                <p class="cw-label">Captain</p>
                <p class="cw-info"><a href="/u/<?php echo strtolower($capt_data->user_login);?>"><?php echo isset($capt_prof->nickname) ? $capt_prof->nickname : $capt_data->user_login; ?></a></p>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="cw-box">
            <h2>Players</h2>
            <?php if( $playerList ) { ?>
                <table>
                    <tr>
                        <th>Player</th>
                        <th>Stat 1</th>
                        <th>Stat 2</th>
                        <th>Stat 3</th>
                    </tr>
                    <?php foreach( $playerList as $player ) { 
                        $player_prof = $UserDB->getProfile($player);
                        $user_data = get_userdata($player); ?>
                        <tr>
                            <td class="cw-player-display">
                                <a href="/u/<?php echo strtolower($user_data->user_login);?>">
                                    <?php echo $team->capt == $player ? "<i class='bi bi-star-fill'></i>" : ""; ?> 
                                    <?php echo isset($player_prof->nickname) ? $player_prof->nickname : $user_data->user_login; ?>
                                </a>
                            </td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </div>
</div>