<?php
    wp_enqueue_style('cw_main_styles');
    wp_enqueue_script('cw_validation');
?>
<div class="cw-box">
    <div class="cw-content-box">
        <?php $seasonList = $this->getList();
        if($seasonList) { ?>
            <table>
            <tr>
                <th>Title</th>
                <th>Game</th>
                <th>Player Level</th>
                <th>Day/Time</th>
                <th>Manager</th>
                <th>Status</th>
            </tr>
            <?php
                foreach($seasonList as $season) {
                    $site_url = site_url();
                    $seasonLink = "";
                    if( $season->slug != "" ) {
                        $seasonLink = "<a href=" . $site_url . "/s/" . $season->slug . ">";
                    }
                    ?>
                <tr>
                    <td><?php echo $seasonLink; echo $season->title; echo ($seasonLink ? "</a>" : "");?></td>
                    <td><?php echo $season->game; ?></td>
                    <td><?php echo $season->playerLvl; ?></td>
                    <td><?php echo $season->matchDay; ?>s @ <?php echo $season->matchTime; ?></td>
                    <td><?php echo $season->manager; ?></td>
                    <td><?php echo $season->status; ?></td>
                </tr>
                <?php } // CLOSE FOREACH
            ?>
            </table>
        <?php } else { ?>
            <p>There are no seasons available at this time.</p>
        <?php } ?>
    </div>

    <div class="cw-content-box">
        <?php 
        if(current_user_can('administrator')) { 
            $protocols = array('http://', 'http://www.', 'www.', 'https://', 'https://www.');
            $url = str_replace($protocols, '', get_bloginfo('wpurl')); ?>
            <h2>Start a New Season</h2>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="createseason"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"><!-- creates hook for php plugin -->
                <input type="hidden" name="inc-manager" value="<?php echo get_current_user_id(); ?>">

                <div class="field-box">
                    <label for="inc-title" class="form-label">Season Title</label>
                    <input type="text" name="inc-title" id="inc-title" class="req form-control">
                </div>
                
                <div class="field-box">
                    <label for="inc-slug" class="form-label">Season Slug</label>
                    <input type="text" name="inc-slug" id="inc-slug" class="req slug form-control">
                    <div class="form-text">ex. <?php echo $url; ?>/s/your-league-link</div>
                </div>

                <div class="field-box">
                    <label for="inc-game" class="form-label">Game</label>
                    <input type="text" name="inc-game" id="inc-game" class="req form-control">
                </div>

                <div class="field-box">
                    <label for="inc-matchDay" class="form-label">Match Day</label>
                    <select name="inc-matchDay" id="inc-matchDay" class="req form-select">
                        <option value="">-- Select Day --</option>
                        <option>Sunday</option>
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                        <option>Saturday</option>
                    </select>
                    <div class="form-text">What day of the week each match occurs</div>
                </div>

                <div class="field-box">
                    <label for="inc-matchTime" class="form-label">Match Time</label>
                    <input type="time" name="inc-matchTime" id="inc-matchTime" class="req form-control" min="08:00" max="22:00" step="1800">
                    <div class="form-text">What time of the day each match occurs</div>
                </div>

                <div class="field-box">
                    <label for="inc-teamNum" class="form-label">Number of Teams</label>
                    <input type="number" name="inc-teamNum" id="inc-teamNum" class="req number form-control" value="2" min="2" max="8">
                </div>

                <div class="field-box">
                    <label for="inc-teamSize" class="form-label">Team Size</label>
                    <input type="number" name="inc-teamSize" id="inc-teamSize" class="req number form-control" value="1" min="1" max="6">
                    <div class="form-text">Number of players on each team</div>
                </div>

                <div class="field-box">
                    <label for="inc-waitlistSize" class="form-label">Waitlist Size</label>
                    <input type="number" name="inc-waitlistSize" id="inc-waitlistSize" class="req number form-control" value="1" min="1" max="6">
                    <div class="form-text">Number of players standing by to play when needed</div>
                </div>

                <div class="field-box">
                    <label for="inc-playerLvl" class="form-label">Player / Season Level</label>
                    <input type="text" name="inc-playerLvl" id="inc-playerLvl" class="form-control">
                    <div class="form-text">ex. Plat and under</div>
                </div>

                <div class="action-box">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        <?php } ?>
    </div>
</div>