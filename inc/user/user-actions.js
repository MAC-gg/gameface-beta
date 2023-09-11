
console.log('user-actions.js loaded.');

jQuery('.action-register-user').on("click", function(e) {
    console.log("triggered");
    let ourNewPost = {
        "username":jQuery(".field-username").val(),
        "email":jQuery(".field-email").val(),
        "password":jQuery(".field-pass").val()
    }

    console.log('registerUser' + ourNewPost.username);
    
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