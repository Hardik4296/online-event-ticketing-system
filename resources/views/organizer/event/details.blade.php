@extends('organizer-app')

@section('content')
<div class="ticket-details-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="left-image">
                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{$event->title}}">
                </div>
                <div class="left-image mt-5">
                    <div class="heading-text">
                        <h4>Attendee</h4>
                    </div>
                    <div class="attendee-list mt-3" id="attendee-list">
                        <div class="loader" style="display: none; text-align: center;margin: 50px 0px">
                            <img src="{{ asset('assets/images/loader.gif') }}" alt="loader">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="right-content">
                    <input type="hidden" name="event_id" id="event_id" value="{{$id}}">
                    <h4>{{$event->title}}</h4>
                    <span>{{$event->description}}</span>
                    <ul>
                        <li><i class="fa fa-clock-o"></i>
                            <h6>{{ $event->formatted_event_date }}<br>{{ $event->formatted_event_duration }}</h6>
                        </li>
                        <li><i class="fa fa-map-marker"></i>{{ $event->location }}</li>
                    </ul>
                    <span><h5>Available Tickets</h5></span>
                    @foreach ($event->tickets as $ticket)
                    <div class="quantity-content">
                        <div class="left-content">
                            <h6>{{ $ticket->ticket_type }}</h6>
                            <p>${{ number_format($ticket->price,2) }} per ticket</p>
                        </div>
                        <div class="right-content">
                            @if($ticket->available_quantity <= 0) <div class="main-red-button">
                                <a href="javascript:void(0);">
                                    Sold Out
                                </a>
                        </div>
                        @else
                        <div class="quantity buttons_added">
                            <div class="main-dark-button">
                                <a href="javascript:void(0);">
                                    {{ $ticket->available_quantity}}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($totalTicketAvailable == 0)
                Tickets Sold Out
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom-scripts')
<script type="text/javascript" src="{{ asset ('js/services/event_service.js') }}"></script>
<script>
    const ATTENDEE_LIST_API_URL = `{{route('organizer.events.attendee.list')}}`;
</script>
@endpush
