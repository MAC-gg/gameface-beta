<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account</h1>
<?php } else {
    // make sure user is logged in first ?>
    <h1>Log in OR Sign up</h1>
    <?php echo wp_login_form(); ?>
<?php }