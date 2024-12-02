    // Add dynamic ticket fields
    document.getElementById('add-ticket-btn').addEventListener('click', function() {
        var ticketSection = document.getElementById('ticket-section');
        var ticketForm = document.querySelector('.ticket-form').cloneNode(true);
        ticketSection.appendChild(ticketForm);

        var inputs = ticketForm.querySelectorAll('input');
        inputs.forEach(function(input) {
            input.removeAttribute('readonly');
            input.value = '';
        });

        var hiddenInput = ticketForm.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.remove();
        }
    });

    // Remove ticket form
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-ticket')) {
            e.target.closest('.ticket-form').remove();
        }
    });

    // AJAX form submission
    $("#event-form").on("submit", function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: UPDATE_EVENT_API_URL,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message || 'Event updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'Ok',
                    allowOutsideClick: true
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.backdrop) {
                        window.location.href = MY_EVENTS_URL;
                    }
                });
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        if (field.startsWith('ticket_type.') || field.startsWith('price.') || field.startsWith('quantity.')) {
                            let match = field.match(/\.(\d+)/);
                            if (match) {
                                let index = match[1];
                                let fieldName = field.split('.')[0];
                                let input = $(`[name="${fieldName}[]"]`).eq(index);
                                input.addClass('is-invalid').after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                            }
                        } else {
                            let input = $(`[id="update_${field}"]`);
                            input.addClass('is-invalid').after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                        }
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Event update failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
        });
    });
