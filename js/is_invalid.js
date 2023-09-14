import $ from 'jquery';

export function is_invalid() {
    console.log('global-validations.js loaded.');
    let is_invalid = false;

    let req_fields = $("input.req");
    $.map(req_fields, (field, i)=>{
        if(field.val() == "") is_invalid = true;
    });

    return is_invalid;
}