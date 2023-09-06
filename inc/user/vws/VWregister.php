<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account OR My Profile</h1>
<?php } else {
    // make sure user is logged in first ?>
    <h1>Register</h1>
    <form class="register-user-form">

        <div class="field-container">
            <label for="field-username">Username</label><br />
            <input type="text" name="field-username" class="field-username">
        </div>
        
        <div class="field-container">
            <label for="field-pass">Password</label><br />
            <input type="text" name="field-pass" class="field-pass">
        </div>

        <div class="field-container">
            <label for="field-email">Email Address</label><br />
            <input type="text" name="field-email" class="field-email">
        </div>

        <div class="field-container">
            <label for="field-gamertag">GamerTag</label><br />
            <input type="text" name="field-gamertag" class="field-gamertag">
        </div>

        <button class="action-register-user">Register Now</button>
    </form>
<?php }