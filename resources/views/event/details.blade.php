@extends('app')

@section('content')
<div class="page-heading-shows-events">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Tickets On Sale Now!</h2>
                <span>Check out upcoming and past shows & events and grab your ticket right now.</span>
            </div>
        </div>
    </div>
</div>

<div class="ticket-details-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="left-image">
                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{$event->title}}">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="right-content">

                    <h4>{{$event->title}}</h4>
                    <span>{{$event->description}}</span>
                    <ul>
                        <li><i class="fa fa-clock-o"></i>
                            <h6>{{ $event->formatted_event_date }}<br>{{ $event->formatted_event_duration }}</h6>
                        </li>
                        <li><i class="fa fa-map-marker"></i>{{ $event->location }}</li>
                    </ul>

                    @if($totalTicketAvailable)
                        <span>There Are {{ $totalTicketAvailable }} Tickets Left</span>
                    @else
                        <span class="text-danger">Opps! There Are No Tickets</span>
                    @endif

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
                            <button class="minus" data-ticket-id="{{ $ticket->id }}">-</button>
                            <input type="number" class="input-text qty text quantity-input"
                                data-ticket-id="{{ $ticket->id }}" min="1" max="10" value="0">
                            <button class="plus" data-ticket-id="{{ $ticket->id }}">+</button>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($totalTicketAvailable > 0)
                <div class="total">

                    <h4>Total: <span class="total_amount" id="total-amount">$0</span></h4>
                    <div class="main-dark-button">
                        @guest
                        <a href="#" data-toggle="modal" data-target="#loginModal">
                            Login To Buy Tickets
                        </a>
                        @else
                        <a href="#" id="purchase-tickets">
                            Buy Tickets
                        </a>
                        @endguest
                    </div>
                </div>
                <div class="warn mt-3">
                    <p>*You Can Only Buy 10 Tickets Per Type For This Show</p>
                </div>
                @include('partials.event.payment-form')
                @endif
            </div>
        </div>
    </div>
</div>

<div class="rent-venue-application">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="heading-text">
                    <h4>Comments</h4>
                </div>
                @auth
                   <div class="contact-form mb-30">
                    <form id="comment-form">
                        <div class="row">
                            <div class="col-lg-12">
                                <fieldset>
                                    <textarea name="about-event-hosting" rows="2" id="comment"
                                        placeholder="Ask a question or comment..."></textarea>
                                </fieldset>
                            </div>
                            <div class="col-lg-12">
                                <fieldset>
                                    <button type="submit" id="form-submit" class="main-dark-button comment-button">Comment</button>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
                @endauth

                <input type="hidden" name="event_id" id="event_id" value="{{$id}}">
                <div class="comment-list" id="comments-list">
                    <div>
                        <div class="loader" style="display: none; text-align: center;margin: 50px 0px">
                            <img src="{{ asset('assets/images/loader.gif') }}" alt="loader">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom-scripts')
<script type="text/javascript" src="{{ asset ('js/services/payment_service.js') }}"></script>
<script type="text/javascript" src="{{ asset ('js/services/comment_service.js') }}"></script>
<script>
    const PURCHASE_TICKET_API_URL = `{{route('create.payment.intent')}}`;
        const CONFIRM_PAYMENT_API_URL = `{{route('confirm.payment')}}`;
        const POST_COMMENT_API_URL = `{{route('comments.store')}}`;
        const COMMENT_LIST_API_URL = `{{route('comments.index')}}`;
        const TICKET_DETAILS_URL = `{{route('ticket.details')}}`;
        const STRIPE_KEY = `{{ env("STRIPE_KEY") }}`;
</script>
@endpush
