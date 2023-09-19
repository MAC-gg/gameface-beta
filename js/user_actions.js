import $ from 'jquery';
import validation from './validation.js';

let validCheck = new validation();

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
        let is_valid = validCheck.formIsValid($(e.target).parent().parent());
        console.log(is_valid);
        if (!is_valid) { console.log("not valid"); return; } else {
            console.log("valid");
            let ourNewPost = {
                "username":$("input#field-username")[0].value,
                "email":$("input#field-email")[0].value,
                "password":$("input#field-password")[0].value
            }
            console.log(ourNewPost.username);
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