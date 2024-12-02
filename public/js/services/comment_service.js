
$(document).ready(function () {
    // Load comments
    function loadComments() {
        const event_id = $('#event_id').val();
        setTimeout(() => {
            $.ajax({
                url: COMMENT_LIST_API_URL + "/" + event_id,
                method: 'GET',
                success: function (response) {
                    $('#comments-list').html(response);
                    $('.loader').hide();
                }
            });
        }, 1500);
    }

    $('.loader').show();
    loadComments();

    // Submit comment
    $('#comment-form').on('submit', function (e) {
        e.preventDefault();
        const comment = $('#comment').val();
        const event_id = $('#event_id').val();

        // Clear any previous errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $('.comment-button').prop('disabled', true);
        $('.comment-button').text('Sending...');

        $.ajax({
            url: POST_COMMENT_API_URL,
            method: 'POST',
            data: {
                comment: comment,
                event_id: event_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $('#comment').val('');
                loadComments();
                $('.comment-button').prop('disabled', false);
                $('.comment-button').text('Comment');
            },
            error: function (xhr) {

                 $('.comment-button').prop('disabled', false);
                 $('.comment-button').text('Comment');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        let input = $(`[id="${field}"]`);
                        input.addClass('is-invalid').after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    }
                } else {

                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Registration failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
        });
    });
});
