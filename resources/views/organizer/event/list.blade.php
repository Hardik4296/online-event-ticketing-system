@extends('organizer-app')

@section('content')
<div class="rent-venue-tabs">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="row" id="tabs">
                    <div class="col-lg-12">
                        <div class="heading-tabs">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5 class="mt-3">Event List</h5>
                                </div>
                                <div class="col-lg-4">
                                    <div class="main-dark-button">
                                        @if(!$events->isEmpty())
                                            <a href="{{ route('organizer.events.export') }}">Export</a>
                                        @else
                                            <a href="javascript:void(0)" disabled style="cursor:not-allowed" title="No events to export" >Export</a>
                                        @endif
                                        <a href="{{ route('organizer.events.create') }}">Create Event</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <table class="table" id="eventListTable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">City</th>
                                    <th scope="col">Ticket Booked</th>
                                    <th scope="col">Available Ticket</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $key => $event)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$event->title}}</td>
                                    <td>{{$event->formatted_event_date}}</td>
                                    <td>{{$event->formatted_event_duration}}</td>
                                    <td>{{substr($event->location, 0, 20) . '..' }}</td>
                                    <td>{{$event->city?->name}}</td>
                                    <td>{{$event->totalBookedTicket}}</td>
                                    <td>{{$event->totalAvailableTicket}}</td>
                                    <td>{{$event->status}}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Action buttons">
                                            <a href="{{ route('organizer.events.edit', encrypt($event->id)) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </a>&nbsp;
                                            <a href="{{ route('organizer.events.detail', encrypt($event->id)) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script>
    $(document).ready(function() {
        $('#eventListTable').DataTable();
    });
</script>
@endpush
@endsection
