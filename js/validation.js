import $ from 'jquery';
import { passwordStrength } from 'check-password-strength';

export default class validation {
    constructor() {
        console.log("validation.js loaded.");
        this.events();
    }

    events() {
        // valid check on keyup
        $('input.req')          .on("keyup", this.reqCheck.bind(this));
        $('input.email')        .on("keyup", this.emailCheck.bind(this));
        $('input.url')          .on("keyup", this.urlCheck.bind(this));
        $('input.urlimg')       .on("keyup", this.urlimgCheck.bind(this));
        $('input.username')     .on("keyup", this.usernameCheck.bind(this));
        $('input.password')     .on("keyup", this.passwordCheck.bind(this));
        $('input.display-name') .on("keyup", this.displayNameCheck.bind(this));

        // onsubmit valid check
        $('form.onsubmit-valid-check') .on("submit", this.validCheck.bind(this));
    }

    validCheck(e) {
        let form = $(e.target);
        form.find('input.req')          .trigger("keyup");
        form.find('input.email')        .trigger("keyup");
        form.find('input.username')     .trigger("keyup");
        form.find('input.password')     .trigger("keyup");
        form.find('input.url')          .trigger("keyup");
        form.find('input.urlimg')       .trigger("keyup");
        form.find('input.display-name') .trigger("keyup");
        return !(form.find(".is-invalid").length > 0);
    }

    formIsValid(form) {
        // CAN THIS BE ELIMINATED
        // ONLY USED IN REST API REGISTER USER
        // valid check on form submit
        form.find('input.req')          .trigger("keyup");
        form.find('input.email')        .trigger("keyup");
        form.find('input.username')     .trigger("keyup");
        form.find('input.password')     .trigger("keyup");
        form.find('input.url')          .trigger("keyup");
        form.find('input.urlimg')       .trigger("keyup");
        form.find('input.display-name') .trigger("keyup");
        return !(form.find(".is-invalid").length > 0);
    }

    reqCheck(e) {
        if(e.target.value != "") { 
            clear_field_error($(e.target));
         } else {
            show_field_error($(e.target), "Please enter the required information");
        }
    }

    emailCheck(e) {
        if(e.target.value == "") { 
            // reqCheck handles this
        } else {
            let email = e.target.value;
            let is_valid_email = email.match(
                /^[a-z0-9!#$%&'*+\/\=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|[a-z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b/
            );
            if(is_valid_email) {
                clear_field_error($(e.target));
            } else {
                show_field_error($(e.target), "Please enter a valid email address");
            }
        }
    }

    urlCheck(e) {
        if(e.target.value == "") { 
            // reqCheck handles this
        } else {
            let url = e.target.value;
            let is_valid_url = url.match(
                /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/
            );
            if(is_valid_url) {
                clear_field_error($(e.target));
            } else {
                show_field_error($(e.target), "Please enter a valid URL");
            }
        }
    }

    urlimgCheck(e) {
        if(e.target.value == "") { 
            // reqCheck handles this
        } else {
            let url = e.target.value;
            let is_valid_url = url.match(
                /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*).(?:png|jpg|jpeg)/
            );
            if(is_valid_url) {
                clear_field_error($(e.target));
            } else {
                show_field_error($(e.target), "Please enter a valid image URL");
            }
        }
    }

    usernameCheck(e) {
        if(e.target.value == "") { 
            // reqCheck handles this
        } else {
            let username = e.target.value;
            let is_valid_username = username.match(
                /^[a-zA-Z0-9-]*$/
            );
            if(is_valid_username) {
                clear_field_error($(e.target));
            } else {
                show_field_error($(e.target), "Please enter a valid username");
            }
        }
        
    }

    passwordCheck(e) {
        if($(e.target).val() == "") { 
            // reqCheck handles this
        } else {
            let password = $(e.target).val();
            if(passwordStrength(password).id != 3){
                show_field_error($(e.target), "Please enter a stronger password");
                updatePasswordStrength($(e.target), passwordStrength(password).id);
            } else {
                clear_field_error($(e.target));
                updatePasswordStrength($(e.target), passwordStrength(password).id);
            }
        }
    }

    displayNameCheck(e) {
        if($(e.target).value == "") { 
            // reqCheck handles this
        } else {
            let displayname = $(e.target).val();
            let username = $(e.target).data("username");
            let is_valid_displayname = username == displayname.toLowerCase();
            if(is_valid_displayname) {
                clear_field_error($(e.target));
            } else {
                show_field_error($(e.target), "Please enter a display name that matches your username");
            }
        }
    }
}
let validCheck = new validation();

function show_field_error(field, error) {
    let box = field.parent();
    if(box.hasClass("input-group")) { box = field.parent().parent(); }

    if(!box.hasClass("is-invalid")) {
        field.addClass("is-invalid");
        box.addClass("is-invalid");
        box.append($("<p class='error-msg'>" + error + "</p>"));
    } else {
        clear_field_error(field);
        field.addClass("is-invalid");
        box.addClass("is-invalid");
        box.append($("<p class='error-msg'>" + error + "</p>"));
    }
}

function clear_field_error(field) {
    let box = field.parent();
    if(box.hasClass("input-group")) { box = field.parent().parent(); }

    field.removeClass("is-invalid");
    box.removeClass("is-invalid");
    box.find("p.error-msg").remove();
}

function updatePasswordStrength(field, strength) {
    let progBox = field.parent(".field-box").find(".password-strength");
    let progBar = progBox.find(".progress-bar");
    
    switch(strength) {
        case 0:
            progBox.attr("aria-valuenow", 0);
            progBar.css("width", '0%');
            break;
        case 1:
            progBox.attr("aria-valuenow", 33);
            progBar.css("width", '33%');
            break;
        case 2:
            progBox.attr("aria-valuenow", 66);
            progBar.css("width", '66%');
            break;
        case 3:
            progBox.attr("aria-valuenow", 100);
            progBar.css("width", '100%');
            break;
    }
    
}