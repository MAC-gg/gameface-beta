import $ from 'jquery';

class regApprovalActions {
    constructor() {
        console.log("regApprovalActions.js loaded.");
        this.events();
    }

    events() {
        $('.btn-approve').on("click", this.approvePlayer.bind(this));
    }

    approvePlayer(e) {
        console.log("approvePlayer triggered");
        let id_to_toggle = $(e.target).closest(".btn-approve").data("regid");
        console.log("ajax call made to: " + searchData.root_url + '/wp-json/cw/v1/manageReg');

        $.ajax({
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, /* SEND NONCE KEY FOR SESSION */
            url: searchData.root_url + '/wp-json/cw/v1/manageReg',
            type: 'POST',
            data: { 'regid':  id_to_toggle}, /* data-regid from HTML */
            success: (response) => {
                console.log(response);
            },
            error: (response) => {
                console.log(response);
            }
        });
    }
}

let OBJregApprovalActions = new regApprovalActions();