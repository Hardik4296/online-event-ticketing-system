$(document).ready(function () {
    function loadAttendee() {
        const event = $('#event_id').val();
        setTimeout(() => {
            $.ajax({
                url: ATTENDEE_LIST_API_URL + "/" + event,
                method: 'GET',
                success: function (response) {
                    $('#attendee-list').html(response);
                    $('.loader').hide();
                }
            });
        }, 1500);
    }

    $('.loader').show();
    loadAttendee();
});
