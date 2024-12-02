@extends('organizer-app')

@section('content')
<div class="rent-venue-application mt-3">
    <div class="container">
        <div class="row">
            <div class="heading-text">
                <h4>Edit Event</h4>
            </div>
            <div class="contact-form">
                <form id="event-form">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" name="title" type="text" id="update_title"
                                    placeholder="Event Title*" value="{{ old('title', $event->title ?? '') }}">
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" name="event_date_time" type="datetime-local"
                                    id="update_event_date_time"
                                    value="{{ old('event_date_time', $event->event_date_time ?? '') }}">
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <select name="event_duration" id="update_event_duration"
                                    class="form-control custom-select">
                                    <option value="" disabled selected>Select Event Duration (Hours)</option>
                                    @for ($i = 1; $i <= 12; $i++) <option
                                        value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00" {{ old('event_duration',
                                        substr($event->event_duration, 0, strrpos($event->event_duration, ':')) ?? '') == str_pad($i, 2, '0', STR_PAD_LEFT) . ':00' ?
                                        'selected' : '' }}>
                                        {{ $i }} {{ $i > 1 ? 'Hours' : 'Hour' }}
                                        </option>
                                        @endfor
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" name="location" type="text" id="update_location"
                                    placeholder="Event Location*" value="{{ old('location', $event->location ?? '') }}">
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <textarea name="description" type="text" id="update_description" class="form-control"
                                    placeholder="Event Description*">{{ old('description', $event->description ?? '') }}</textarea>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <select name="status" id="update_status" class="form-control custom-select">
                                    <option selected disabled>Select Event Status</option>
                                    <option value="inactive" {{ old('status', $event->status ?? '') == 'inactive' ? 'selected'
                                        : '' }}>In-Active
                                    </option>
                                    <option value="active" {{ old('status', $event->status ?? '') == 'active' ?
                                        'selected' : '' }}>Active
                                    </option>
                                    <option value="cancelled" {{ old('status', $event->status ?? '') == 'cancelled' ?
                                        'selected' : '' }}>Cancelled
                                    </option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <select name="city_id" id="update_city_id" class="form-control custom-select">
                                    <option selected disabled>Select City</option>
                                    @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('city_id', $event->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <fieldset>
                                <input class="form-control" type="file" name="image" id="update_image" accept="image/*">
                                @if(isset($event->image))
                                <img class="mt-3" src="{{ asset('storage/' . $event->image) }}" alt="Event Image" width="100"
                                    height="100">
                                @endif
                            </fieldset>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="heading-tabs">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5>Ticket Information</h5>
                                    <p class="text-danger">Once you create ticket type, you can not modify it.</p>
                                </div>
                                <div class="col-lg-1">
                                    <i id="add-ticket-btn" class="fa fa-plus btn btn-primary"></i>
                                </div>
                            </div>
                        </div>

                        <div id="ticket-section" class="mt-3">
                            @foreach ($event->tickets as  $ticket)
                                <div class="ticket-form row">
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset>
                                            <input class="form-control" name="ticket_type[]" readonly type="text" placeholder="Ticket Type*"
                                                value="{{ old('ticket_type', $ticket->ticket_type ?? '') }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-2 col-sm-12">
                                        <fieldset>
                                            <input class="form-control" name="price[]" type="number" readonly placeholder="Ticket Price*"
                                                value="{{ old('price', $ticket->price ?? '') }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset>
                                            <input class="form-control" name="quantity[]" type="number" readonly placeholder="Quantity*"
                                                value="{{ old('quantity', $ticket->quantity ?? '') }}">
                                        </fieldset>
                                    </div>

                                    <input type="hidden" name="ticket_id[]" value="{{ $ticket->id ?? '' }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <fieldset>
                            <button type="submit" id="form-submit" class="main-dark-button">
                                Update Event
                            </button>
                            <a href="{{ route('organizer.events.list') }}">
                                <button type="button" class="main-dark-button">
                                    Cancel
                                </button>
                            </a>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script type="text/javascript" src="{{ asset ('js/services/organizer/edit_event_service.js') }}"></script>
<script>
    const MY_EVENTS_URL = `{{route('organizer.events.list')}}`;
    const UPDATE_EVENT_API_URL = `{{route('organizer.events.update', $id)}}`
</script>
@endpush

@endsection
