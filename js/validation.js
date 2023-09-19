import $ from 'jquery';
import { passwordStrength } from 'check-password-strength';

export default class validation {
    constructor() {
        console.log("validation.js loaded.")
        this.events();
    }

    events() {
        $('input.req')      .on("keyup", this.reqCheck.bind(this));
        $('input.email')    .on("keyup", this.emailCheck.bind(this));
        $('input.username') .on("keyup", this.usernameCheck.bind(this));
        $('input.password') .on("keyup", this.passwordCheck.bind(this));
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

    usernameCheck(e) {
        if(e.target.value == "") { 
            // reqCheck handles this
        } else {
            let username = e.target.value;
            let is_valid_username = username.match(
                /^[a-z0-9-]*$/
            );
            if(is_valid_username) {
                clear_field_error($(e.target));
            } else {
                show_field_error($(e.target), "Please enter a valid username");
            }
        }
        
    }

    passwordCheck(e) {
        if(e.target.value == "") { 
            // reqCheck handles this
        } else {
            let password = e.target.value;
            if(passwordStrength(password).id != 3){
                show_field_error($(e.target), "Please enter a stronger password");
                updatePasswordStrength($(e.target), passwordStrength(password).id);
            } else {
                clear_field_error($(e.target));
                updatePasswordStrength($(e.target), passwordStrength(password).id);
            }
        }
    }

    formIsValid(form) {
        form.find('input.req')      .trigger("keyup");
        form.find('input.email')    .trigger("keyup");
        form.find('input.username') .trigger("keyup");
        form.find('input.password') .trigger("keyup");
        return !(form.find(".invalid").length > 0);
    }
}

function show_field_error(field, error) {
    let box = field.parent(".field-box");
    if(!box.hasClass("invalid")) {
        box.addClass("invalid");
        box.append($("<p class='error-msg'>" + error + "</p>"));
    } else {
        clear_field_error(field);
        box.addClass("invalid");
        box.append($("<p class='error-msg'>" + error + "</p>"));
    }
}

function clear_field_error(field) {
    let box = field.parent(".field-box");
    box.removeClass("invalid");
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