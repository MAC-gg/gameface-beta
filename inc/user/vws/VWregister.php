<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account OR My Profile</h1>
<?php } else {
    // make sure user is logged in first ?>
    <h1>Register</h1>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="create-pet-form" method="POST">
        <input type="hidden" name="action" value="createuser"><!-- creates hook for php plugin -->

        <div class="field-container">
            <label for="incUsername">Username</label><br />
            <input type="text" name="incUsername">
        </div>
        
        <div class="field-container">
            <label for="incPass">Password</label><br />
            <input type="text" name="incPass">
        </div>

        <div class="field-container">
            <label for="incEmail">Email Address</label><br />
            <input type="text" name="incEmail">
        </div>

        <div class="field-container">
            <label for="incGamertag">GamerTag</label><br />
            <input type="text" name="incGamertag">
        </div>

        <button>Register Now</button>
    </form>
<?php }