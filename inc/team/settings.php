<!-- Template View -->
<!-- /inc/template.php -->
<?php // SETUP DATA HERE
$playerList = explode(',', $team->playerList); ?>
<?php $cwGlobal->breadcrumbs($season, [], $team, array("Settings", "danger")); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Team Settings</h1>
        </div>
        <div class="cw-actions">
            <a class="btn btn-secondary" href="/s/<?php echo $s; ?>/t/<?php echo $t; ?>">Cancel</a>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("team"); ?>
</div>
<div class="row cw-row">
    <div class="col-6">
        <div class="cw-box">
            <h2>Team Preview</h2>
        </div>
    </div>
    <div class="col-6">
        <div class="cw-box">
            <h2>Change Team Settings</h2>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="updateteam"><!-- creates hook for php plugin -->
                <input type="hidden" name="field-team" value="<?php echo $team->id; ?>">
                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/t/<?php echo $t; ?>">

                <div class="field-box">
                    <label for="field-title" class="form-label">Team Name</label>
                    <input type="text" name="field-title" id="field-title" class="req form-control" value="<?php echo $team->title; ?>">
                </div>

                <div class="field-box">
                    <label for="field-color1" class="form-label">Choose a <strong>Primary Color</strong></label>
                    <select name="field-color1" id="field-color1" class="form-select">
                        <option></option>
                        <option>Top</option>
                        <option>Jungle</option>
                        <option>Midlane</option>
                        <option>ADC</option>
                        <option>Support</option>
                    </select>
                </div>

                <div class="field-box">
                    <label for="field-mascot" class="form-label">Choose a <strong>Mascot</strong></label>
                    <select name="field-mascot" id="field-mascot" class="form-select">
                        <option></option>
                        <option>Top</option>
                        <option>Jungle</option>
                        <option>Midlane</option>
                        <option>ADC</option>
                        <option>Support</option>
                        <option>Flex</option>
                    </select>
                </div>

                <div class="cw-action-box">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>