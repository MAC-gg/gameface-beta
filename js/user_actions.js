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
                url: searchData.root_url + '/wp-json/cw/v1/user/register',
                type: 'POST',
                data: ourNewPost,
                success: () => {
                    $(".server-msg-box").empty();
                    $(".server-msg-box").removeClass (function (index, className) {
                        return (className.match (/(^|\s)status-\S+/g) || []).join(' ');
                    });
                    $(".server-msg-box").addClass("status-success");
                    $(".server-msg-box").append('<span class="bold text-success">SUCCESS!</span>');
                    $(".server-msg-box").removeClass("hidden");
                },
                error: (response) => {
                    $(".server-msg-box").empty();
                    $(".server-msg-box").removeClass (function (index, className) {
                        return (className.match (/(^|\s)status-\S+/g) || []).join(' ');
                    });
                    $(".server-msg-box").addClass("status-error");
                    $(".server-msg-box").append('<span class="bold text-danger">ERROR</span>');
                    $(".server-msg-box").append('<span class="text-danger">' + response.responseJSON.message + '</span>');
                    $(".server-msg-box").removeClass("hidden");
                }
            });
        }
    }
}

let userActions = new user_actions();