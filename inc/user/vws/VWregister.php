<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account OR My Profile</h1>
<?php } else {
    // make sure user is logged in first ?>
    <h1>Register</h1>

    <div class="field-box">
        <label for="field-username" class="form-label">Email address</label>
        <input type="email" class="field-username" id="field-username" placeholder="name@example.com" class="field-username req">
    </div>
    
    <div class="field-box">
        <label for="field-pass">Password</label><br />
        <input type="text" name="field-pass" class="field-pass req">
        <p class="msg"></p>
    </div>

    <div class="field-box">
        <label for="field-email">Email Address</label><br />
        <input type="text" name="field-email" class="field-email req">
        <p class="msg"></p>
    </div>

    <div class="field-box">
        <label for="field-gamertag">GamerTag</label><br />
        <input type="text" name="field-gamertag" class="field-gamertag req">
        <p class="msg"></p>
    </div>

    <button class="action-register-user">Register Now</button>
<?php }