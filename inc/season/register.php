
<!-- Season Register View -->
<!-- /inc/season/register.php -->
<div class="cw-header">
    <div class="flex items-center justify-between">
        <h1>Register to Play: <?php echo $season->title; ?></h1>
        <div class="cw-actions">
            <a class="btn btn-secondary" href="/s/<?php echo $s; ?>">Cancel</a>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>
</div>
<div class="cw-content-box cw-season-single">
    <h2>Registration Questionaire</h2>
    
    <!-- CHECK IF ALREADY REGISTERED -->
    <!-- DISPLAY STATUS -->
    
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
        <input type="hidden" name="action" value="createreg"><!-- creates hook for php plugin -->
        <input type="hidden" name="inc-player" value="<?php echo get_current_user_id(); ?>">
        <input type="hidden" name="inc-season" value="<?php echo $season->id; ?>">

        <div class="field-box">
            <label for="inc-hrsTotal" class="form-label">How many hours have you played <strong><?php echo $season->game; ?></strong> total?</label>
            <input type="text" name="inc-hrsTotal" id="inc-hrsTotal" class="req form-control">
        </div>
        
        <div class="field-box">
            <label for="inc-hrs3Months" class="form-label">How many hours have you played <strong><?php echo $season->game; ?></strong> in the last 3 months?</label>
            <input type="text" name="inc-hrs3Months" id="inc-hrs3Months" class="req form-control">
        </div>

        <div class="field-box">
            <label for="inc-otherCompGames" class="form-label">List any other games that you play competitively</label>
            <input type="text" name="inc-otherCompGames" id="inc-otherCompGames" class="req form-control">
            <div class="form-text">If none, leave blank.</div>
        </div>

        <div class="field-box">
            <label for="inc-wantsCap" class="form-label">Are you interested in being a team captain?</label>
            <input type="text" name="inc-wantsCap" id="inc-wantsCap" class="req form-control">
            <div class="form-text">Responsibilites include coordinating and reporting matches, not necessarily coaching or being an IGL.</div>
        </div>

        <div class="field-box">
            <label for="inc-partyMem" class="form-label">List any players that you would like to play with this season</label>
            <input type="text" name="inc-partyMem" id="inc-partyMem" class="req form-control">
            <div class="form-text">No gaurentees</div>
        </div>

        <div class="field-box">
            <label for="inc-prefPos" class="form-label">What is your preferred position?</label>
            <select name="inc-prefPos" id="inc-prefPos" class="form-select">
                <option></option>
                <option>Top</option>
                <option>Jungle</option>
                <option>Midlane</option>
                <option>ADC</option>
                <option>Support</option>
            </select>
        </div>

        <div class="field-box">
            <label for="inc-otherPos" class="form-label">What is your secondary position?</label>
            <select name="inc-otherPos" id="inc-otherPos" class="form-select">
                <option></option>
                <option>Top</option>
                <option>Jungle</option>
                <option>Midlane</option>
                <option>ADC</option>
                <option>Support</option>
            </select>
        </div>

        <div class="field-box">
            <label for="inc-gameUsername" class="form-label">What is your <strong><?php echo $season->game; ?></strong> username?</label>
            <input type="text" name="inc-gameUsername" id="inc-gameUsername" class="req form-control">
        </div>

        <div class="action-box">
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>