import $ from 'jquery';

class UserActions {
    constructor() {
        this.events();
    }

    events() {
        $('#my-notes').on("click", ".delete-note", this.deleteNote);
        $('#my-notes').on("click", '.edit-note', this.editNote.bind(this));
        $('#my-notes').on("click", '.update-note', this.updateNote.bind(this));
        $('.action-register-user').on("click", this.registerUser.bind(this));
    }

    /* CUSTOM METHODS */
    registerUser(e) {
        var ourNewPost = {
            'username': $(".field-username").val(),
            'email': $(".field-email").val(),
            'password': $(".field-pass").val()
        }
        
        $.ajax({
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, /* SEND NONCE KEY FOR SESSION */
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
    }

    editNote(e) {
        var thisNote = $(e.target).parents("li");
        if(thisNote.data("state") == "editable") {
            this.makeNoteReadOnly(thisNote);
        } else {
            this.makeNoteEditable(thisNote);
        }
    }

    makeNoteEditable(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        thisNote.find(".update-note").addClass('update-note--visible');
        thisNote.data("state", "editable");
    }

    makeNoteReadOnly(thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
        thisNote.find(".update-note").removeClass('update-note--visible');
        thisNote.data("state", "cancel");
    }

    updateNote(e) {
        var thisNote = $(e.target).parents("li");

        var ourUpdatedPost = {
            'title': thisNote.find(".note-title-field").val(),
            'content': thisNote.find(".note-body-field").val()
        }
        
        $.ajax({
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, /* SEND NONCE KEY FOR SESSION */
            url: searchData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'POST',
            data: ourUpdatedPost,
            success: (response) => {
                this.makeNoteReadOnly(thisNote);
                console.log('SUCCESS');
            },
            error: (response) => {
                console.log('ERROR');
            }
        });
    }

    deleteNote(e) {
        var thisNote = $(e.target).parents("li");
        
        $.ajax({
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, /* SEND NONCE KEY FOR SESSION */
            url: searchData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: 'DELETE',
            success: (response) => {
                thisNote.slideUp();
                console.log('SUCCESS');
                if(response.userNoteCount < 5) {
                    $('.note-limit-message').removeClass('active');
                }
            },
            error: (response) => {
                console.log('ERROR');
            }
        });
    }
}

export default UserActions;