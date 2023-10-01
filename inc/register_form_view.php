<?php
function register_form_view() {
    // Start the object buffer, which saves output instead of outputting it.
    ob_start(); ?>
    <div class="cdub-box"><?php 
        if( is_user_logged_in() ){
            // if user is logged in show account settings ?>
            <h1>My Account OR My Profile</h1>
        <?php } else {
            // make sure user is logged in first ?>
            <div class="form-box register">
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
                    <button type="button" class="btn btn-primary action-register-user">Register Now</button>
                    <div class="server-msg-box hidden"></div>
                </div>
            </div>
        <?php } ?>
    </div><?php
    // Return everything in the object buffer.
    return ob_get_clean();
}