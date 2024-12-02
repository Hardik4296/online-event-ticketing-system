$(document).ready(function () {

    loadUpcomingEvents();

    let keyword = '';
    let city_id = '';
    let date = '';

    function loadUpcomingEvents(keyword, city_id, date, page = 1) {
        // Show the loader
        $('.loader').show();
        setTimeout(() => {
            $.ajax({
                url: FILTER_EVENTS_API_URL,
                method: 'POST',
                data: {
                    keyword: keyword,
                    city_id: city_id,
                    date: date,
                    page: page,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#events-list').html(response);
                    $('#pagination-wrapper').html(response.pagination);
                    $('.loader').hide();
                }
            });
        }, 1500);
    }

    $('#filter-form').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        keyword = formData.get('keyword');
        city_id = formData.get('city_id');
        date = formData.get('date');

        loadUpcomingEvents(keyword, city_id, date);
    });

    // Handle pagination link clicks
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();

        const page = $(this).attr('href').split('page=')[1];
        loadUpcomingEvents(keyword, city_id, date, page);
    });
});
