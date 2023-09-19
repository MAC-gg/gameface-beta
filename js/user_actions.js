import $ from 'jquery';

class user_actions {
    constructor() {
        console.log("user-actions.js loaded.");
        this.events();
    }

    events() {
        $('.action-register-user').on("click", this.register_user.bind(this));
    }

    register_user(e) {
        console.log("triggered");
        let is_valid = $(e.target).parent(".form-box").find(".field-box.invalid");
        console.log(is_valid);
        if (!is_valid.length) { return; } else {
            let ourNewPost = {
                "username":$(".field-username").value,
                "email":$(".field-email").value,
                "password":$(".field-pass").value
            }
            
            $.ajax({
                beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, // SEND NONCE KEY FOR SESSION
                url: searchData.root_url + '/wp-json/cdub/v1/user/register',
                type: 'POST',
                data: ourNewPost,
                success: (response) => {
                    console.log('SUCCESS');
                    console.log(response);
                    location.href = '/leagues/';
                },
                error: (response) => {
                    console.log('ERROR');
                    console.log(response);
                }
            });
        }
    }
}

let userActions = new user_actions();