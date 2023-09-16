import $ from 'jquery';
import { is_valid } from './is_valid';

console.log('user-actions.js loaded.');

$('.action-register-user').on("click", function(e) {
    console.log("triggered");
    console.log(is_invalid());
    if (is_valid()) { return; } else {
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
});