import $ from 'jquery';

export function is_valid() {
    console.log('global-validations.js loaded.');
    clear_errors();
    let is_valid = true;

    /* is NOT blank */
    let req_fields = $("input.req");
    $.map(req_fields, (field, i)=>{
        if(field.value == "") is_valid = true;
        show_error(field, "req", "");
    });
    // */

    /* is VALID email 
    let email_fields = $("input.email");
    $.map(email_fields, (field, i)=>{
        if(email_fields.value == "") is_valid = true;
        show_error(field, "req", "");
    });
    // */

    return is_valid;
}

function show_error(field, code, error) {
    let box = field.parent();
    box.addClass("invalid");
    box.addClass("invalid-" + code);
    let msg = field.next("invalid-msg-" + code);
    if(!msg) { // if no custom validation msg
        // inject default error message
        msg = $("<p class='invalid-msg-" + code + "'>" + error + "</p>");
        box.append(msg);
    }
}

function clear_errors() {
    let field_boxes = $(".field-box");
    $.map(field_boxes, (box, i)=>{
        // REMOVE global invalid
        box.removeClass("invalid");
        // REMOVE invalid codes
        // regex - start with invalid-, then any 3-10 chars
        box.removeClass((index, className) => {
            return (className.match(/^invalid-[a-z]{3,10}/gm) || []).join(' ');
        });
    });
}