<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account OR My Profile</h1>
<?php } else {
    // make sure user is logged in first ?>
    <div class="row justify-content-center">
        <div class="col-4">
            <div style="width:100%">
                <h1>Register</h1>

                <div class="field-box">
                    <label for="field-username" class="form-label">Username</label>
                    <input type="text" name="field-username" class="field-username req form-control" id="field-username" placeholder="U5ernam3">
                </div>
                
                <div class="field-box">
                    <label for="field-pass" class="form-label">Password</label><br />
                    <input type="password" name="field-pass" class="field-pass req form-control" id="field-pass" placeholder="test">
                </div>

                <div class="field-box">
                    <label for="field-email" class="form-label">Email Address</label><br />
                    <input type="email" name="field-email" class="field-email req form-control" id="field-email" placeholder="name@example.com">
                </div>

                <div class="action-box">
                    <button type="button" class="btn btn-primary action-register-user">Register Now</button>
                </div>
            </div>
        </div>
    </div>
<?php }