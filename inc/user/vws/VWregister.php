<?php if( is_user_logged_in() ){
    // if user is logged in show account settings ?>
    <h1>My Account OR My Profile</h1>
<?php } else {
    // make sure user is logged in first ?>
    <h1>Register</h1>

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
    <script>
        console.log("script");
        jQuery('.action-register-user').on("click", function(e) {
            console.log("triggered");
            var ourNewPost = {
                'username': jQuery(".field-username").val(),
                'email': jQuery(".field-email").val(),
                'password': jQuery(".field-pass").val()
            }

            console.log('registerUser');
            
            jQuery.ajax({
                beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, // SEND NONCE KEY FOR SESSION
                url: searchData.root_url + '/wp-json/cdub/v1/user/register',
                type: 'POST',
                data: ourNewPost,
                success: (response) => {
                    console.log('SUCCESS');
                    console.log(response);
                },
                error: (response) => {
                    console.log('ERROR');
                    console.log(response);
                }
            });
        });
    </script>
<?php }