<div class="cw-box"><?php 
    if( is_user_logged_in() ){
        // if user is logged in show account settings ?>
        <p>You are already logged in. Log out to create a new account.</p>
    <?php } else {
        // make sure user is logged in first
        // load scripts/styles
        wp_enqueue_style('cw_user_styles');
        wp_enqueue_script('cw_validation');
        wp_enqueue_script('cw_user_actions'); ?>
        <div class="form-box register">
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="onsubmit-valid-check">
                <input type="hidden" name="action" value="registeruser"><!-- creates hook for php plugin -->
                <div class="field-box">
                    <label for="field-username" class="form-label">Username</label>
                    <input type="text" name="field-username" class="req username form-control" id="field-username" placeholder="U5ernam3">
                </div>
                
                <div class="field-box">
                    <label for="field-pass" class="form-label">Password</label>
                    <input type="password" name="field-pass" class="req password form-control" id="field-password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;">
                    <div class="progress password-strength" role="progressbar" aria-label="Password Strength" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height: 3px">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="field-box">
                    <label for="field-email" class="form-label">Email Address</label>
                    <input type="email" name="field-email" class="req email form-control" id="field-email" placeholder="name@example.com">
                </div>

                <div class="action-box">
                    <button class="btn btn-primary">Register</button>
                </div>
            </form>
        </div>
    <?php } ?>
</div>