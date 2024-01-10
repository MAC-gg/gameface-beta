<!-- Finalize Sched View -->
<!-- /inc/season/sched.php -->
<?php
    $team_list = $TeamDB->getList($season->id);
    $duration = $season->duration;
    $matchDate = isset($season->startDate) ? strtotime($season->startDate) : strtotime($season->matchDay);
    $matchHour = explode(':', $season->matchTime)[0];
    $matchMins = explode(':', $season->matchTime)[1];
    $matchDatetime = mktime($matchHour, $matchMins, 0, date('m', $matchDate), date('d', $matchDate), date('Y', $matchDate));
?>
<?php $cwGlobal->breadcrumbs($season, "<i class='bi bi-lock-fill'></i> Finalize Schedule"); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Finalize Schedule</h1>
        </div>
        <div class="cw-actions">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="btn-form">
                <input type="hidden" name="action" value="startseason"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>">
                <input type="hidden" name="season" value="<?php echo $season->id; ?>">
                <button class="btn btn-primary" title="Start Season">Start Season</button>
            </form>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="row cw-row">
    <div class="col-8">
        <div class="cw-box">
            <h2>Projected Schedule</h2>
            <?php
            // $away = array_splice($team_list,(count($team_list)/2));
            // $home = $team_list;

            $odd = array();
            $even = array();
            foreach ($team_list as $k => $v) {
                if ($k % 2 == 0) {
                    $even[] = $v;
                } else {
                    $odd[] = $v;
                }
            }

            $tempMatchDate = $matchDatetime;
            for($i = 1; $i <= $duration; $i++) { ?>
                <div class="cw-sub-box">
                    <div class="cw-sub-box-title">
                        <h3>Week <?php echo $i; ?></h3>
                        <p><?php echo date("D M j", $tempMatchDate); ?> @ <?php echo date("G:i", $tempMatchDate); ?></p>
                    </div>
                    <div class="cw-match-box">
                        <?php for( $j = 0; $j < count($even); $j++ ) { ?>
                            <div class="cw-match">
                                <span><?php echo $even[$j]->title; ?></span>
                                <span class="cw-vs">VS</span>
                                <span><?php echo $odd[$j]->title; ?></span>
                            </div>
                        <?php }
                        /* DISPLAY ARRAYS FOR TESTING 
                        echo "Even<br />";
                        foreach($even as $e) { echo $e->title . "<br />"; }
                        echo "<br />Odd<br />";
                        foreach($odd as $o) { echo $o->title . "<br />"; }
                        */

                        if(count($even)+count($odd)-1 > 2){
                            // array_unshift - adds to front of array
                            // array_shift - returns the first item in array
                            // array_spice - cut array, where, how long
                            // array_push - add to the back of the array
                            // array_pop - returns the last item in array
                            array_unshift( $even, array_shift( array_splice( $odd,0,1 ) ) );
                            array_push( $odd, array_pop($even) );
                        }
                        $tempMatchDate = strtotime("+7 days", $tempMatchDate); ?>
                    </div>
                </div>
            <?php } ?>    
        </div>
    </div>
    <div class="col-4">
        <div class="cw-box">
            <h2>Schedule Details</h2>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="updatesched"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/sched">
                <input type="hidden" name="season" value="<?php echo $season->id; ?>">
                
                <div class="field-box">
                    <label for="field-startdate" class="form-label">When should matches start?</label>
                    <select name="field-startdate" id="field-startdate" class="req form-select">
                        <?php $tempMatchDate = strtotime($season->matchDay);
                        for( $i = 1; $i <= 4; $i++ ) {
                            echo "<option value='" . date("Y-m-d H:i:s", $tempMatchDate) . "'>" . date("D M j", $tempMatchDate) . "</option>";
                            $tempMatchDate = strtotime("+7 days", $tempMatchDate);
                        } ?>
                    </select>
                </div>

                <div class="cw-action-box">
                    <button class="btn btn-primary">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>