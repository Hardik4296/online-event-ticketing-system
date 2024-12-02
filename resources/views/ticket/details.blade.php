@extends('app')

@section('content')

<div class="page-heading-shows-events">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Congratulations!</h2>
                <span>You have successfully purchased your tickets</span>
            </div>
        </div>
    </div>
</div>

<div class="ticket-details-page">
    <div class="container">
        <div class="row ">
            @if(session()->has('success_msg'))
            <div class="col-lg-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session()->get('success_msg') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            @endif

            <div class="col-lg-8 mt-5">
                <div class="left-image event-image-div">
                    <img src="{{ asset('storage/' . $event->image) }}" class="event-image" alt="">
                </div>
            </div>
            <div class="col-lg-4 mt-5">
                <div class="right-content">

                    <h4>{{$event->title}}</h4>
                    <span>{{$event->description}}</span>
                    <ul>
                        <li>
                            <i class="fa fa-clock-o"></i>
                            <h6>{{ $event->formatted_event_date }}<br>{{ $event->formatted_event_duration }}</h6>
                        </li>
                        <li><i class="fa fa-map-marker"></i>{{ $event->location }}</li>
                    </ul>
                    @foreach ($ticketPurchase as $ticket)
                    <div class="quantity-content">
                        <div class="left-content">
                            <h6>{{ $ticket['ticket']['ticket_type']}}</h6>
                            <p>${{ number_format($ticket['ticket']['price'],2) }} per ticket</p>
                        </div>
                        <div class="right-content">
                            <div class="quantity buttons_added">
                                <input type="number" disabled class="input-text qty text quantity-input"
                                    value="{{ $ticket['quantity']}}">
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="total">
                        <h4>Total: $<span class="total_amount" >{{ number_format($totalAmount, 2) }}</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
