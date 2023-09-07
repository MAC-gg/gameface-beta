
console.log('user-actions.js loaded.');

/*
class UserActions {
    constructor() {
        this.events();
        console.log('UserActions con');
    }

    events() {
        $('.action-register-user').on("click", this.registerUser.bind(this));
        console.log('UserActions events');
    }

    // CUSTOM METHODS
    registerUser(e) {
        var ourNewPost = {
            'username': $(".field-username").val(),
            'email': $(".field-email").val(),
            'password': $(".field-pass").val()
        }

        console.log('registerUser');
        
        $.ajax({
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, // SEND NONCE KEY FOR SESSION
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

    updateNote(e) {
        var thisNote = $(e.target).parents("li");

        var ourUpdatedPost = {
            'title': thisNote.find(".note-title-field").val(),
            'content': thisNote.find(".note-body-field").val()
        }
        
        $.ajax({
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, // SEND NONCE KEY FOR SESSION
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
            beforeSend: (xhr) => { xhr.setRequestHeader('X-WP-Nonce', searchData.nonce); }, // SEND NONCE KEY FOR SESSION
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
*/