// Default
document.addEventListener('DOMContentLoaded', function () {
    var dateTimeInput = document.getElementById('create_event_date_time');
    var now = new Date();

    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');

    dateTimeInput.min = `${year}-${month}-${day}T${hours}:${minutes}`;
});

// Add dynamic ticket fields
document.getElementById('add-ticket-btn').addEventListener('click', function() {
    var ticketSection = document.getElementById('ticket-section');
    var ticketForm = document.querySelector('.ticket-form').cloneNode(true);
    ticketSection.appendChild(ticketForm);

    var inputs = ticketForm.querySelectorAll('input');
    inputs.forEach(function (input) {
        input.value = '';
    });
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
        url: CREATE_EVENT_API_URL,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire({
                title: 'Success!',
                text: response.message || 'Event created successfully!',
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
                        let input = $(`[id="create_${field}"]`);
                        input.addClass('is-invalid').after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    }
                }
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON.message || 'Event creation failed. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        },
    });
});
