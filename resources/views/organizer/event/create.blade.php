@extends('organizer-app')

@section('content')
<div class="rent-venue-application mt-3">
    <div class="container">
        <div class="row">
            <div class="heading-text">
                <h4>Create Event</h4>
            </div>
            <div class="contact-form">
                <form id="event-form" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <!-- Event Details -->
                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" name="title" type="text" id="create_title" placeholder="Event Title*" >
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" name="event_date_time" type="datetime-local" id="create_event_date_time" >
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <select name="event_duration" id="create_event_duration" class="form-control custom-select">
                                    <option value="" disabled selected>Select Event Duration (Hours)</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00">{{ $i }} {{ $i > 1 ? 'Hours' : 'Hour' }}</option>
                                    @endfor
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" name="location" type="text" id="create_location" placeholder="Event Location*">
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <textarea name="description" type="text" id="create_description" class="form-control"
                                    placeholder="Event Description*" ></textarea>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <select name="status" id="create_status" class="form-control custom-select">
                                    <option selected>Status</option>
                                    <option value="inactive">In-Active</option>
                                    <option value="active">Active</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <select name="city_id" id="create_city_id" class="form-control custom-select">
                                    <option selected>Select City</option>
                                    @foreach ($cities as $city)
                                    <option value="{{$city->id}}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" type="file" accept="image/*" name="image" id="create_image" accept="image/*" >
                            </fieldset>
                        </div>

                        <div class="col-lg-12">
                            <div class="heading-tabs">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5>Ticket Information</h5>
                                    </div>
                                    <div class="col-lg-1">
                                        <i id="add-ticket-btn" class="fa fa-plus btn btn-primary"></i>
                                    </div>
                                </div>
                            </div>

                            <div id="ticket-section" class="mt-3">
                                <!-- Ticket Fields will be dynamically added here -->
                                <div class="ticket-form row">
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset>
                                            <input class="form-control" name="ticket_type[]" type="text" placeholder="Ticket Type*" >
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2 col-sm-12">
                                        <fieldset>
                                            <input class="form-control" name="price[]" type="number" placeholder="Ticket Price*" >
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset>
                                            <input class="form-control" name="quantity[]" type="number" placeholder="Quantity*" >
                                        </fieldset>
                                    </div>
                                    <div class="col-md-1">
                                        <i class="remove-ticket btn btn-danger fa fa-trash"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" id="form-submit" class="main-dark-button">
                                    Create Event
                                </button>
                                <a href="{{ route('organizer.events.list') }}">
                                    <button type="button" class="main-dark-button">
                                        Cancel
                                    </button>
                                </a>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script type="text/javascript" src="{{ asset ('js/services/organizer/create_event_service.js') }}"></script>
<script>
    const CREATE_EVENT_API_URL = `{{route('organizer.events.store')}}`;
    const MY_EVENTS_URL = `{{route('organizer.events.list')}}`;
</script>
@endpush

@endsection
