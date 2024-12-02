<div class="col-lg-12">
    <div class="heading">
        <h2>Events </h2>
    </div>
</div>
@if(count($upComingEvents) == 0)
    <div class="col-lg-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Opps!</strong> There Are No Upcoming Events
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif

@foreach ($upComingEvents as $key => $event)

@include('partials.event.list', ['event' => $event])

@endforeach

<!-- Pagination -->
<div class="col-lg-12">
    <div class="pagination">
        {{ $upComingEvents->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
