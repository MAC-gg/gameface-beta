<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account OR My Profile</h1>
<?php } else {
    // make sure user is logged in first ?>
    <div class="row justify-content-center">
        <div class="col-lg-6 form-box register">
            <h2>Register</h2>

            <div class="server-msg-box hidden">
                <p class="server-msg"></p>
            </div>

            <div class="field-box">
                <label for="field-username" class="form-label">Username</label>
                <input type="text" name="field-username" class="field-username req username form-control" id="field-username" placeholder="U5ernam3">
            </div>
            
            <div class="field-box">
                <label for="field-pass" class="form-label">Password</label><br />
                <input type="password" name="field-pass" class="field-pass req password form-control" id="field-pass" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;">
                <div class="progress password-strength" role="progressbar" aria-label="Password Strength" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height: 3px">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div class="field-box">
                <label for="field-email" class="form-label">Email Address</label><br />
                <input type="email" name="field-email" class="field-email req email form-control" id="field-email" placeholder="name@example.com">
            </div>

            <div class="action-box">
                <button type="button" class="btn btn-primary action-register-user">Register Now</button>
            </div>
        </div>
    </div>
<?php }