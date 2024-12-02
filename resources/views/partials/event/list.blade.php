@php
$availableTicket = $event->totalAvailableTicket();
@endphp
<div class="col-lg-4">
    <div class="ticket-item">
        <div class="thumb">
            <img src="{{ asset('storage/' . $event->image) }}" width="350" height="255"
                on:error="this.src='https://via.placeholder.com/350x255'" alt="">
            <div class="price">
                <span>1 ticket<br>from <em>${{$event->ticket_price_start_from ?
                        number_format($event->ticket_price_start_from,2) : 0}}</em></span>
            </div>
        </div>
        <div class="down-content">

            @if($availableTicket)
            <span>There Are {{ $availableTicket }} Tickets Left</span>
            @else
            <span class="text-danger">Opps! There Are No Tickets</span>
            @endif
            <h4 title="{{ $event->title }}">{{ substr($event->title, 0, 20) . '..' }}</h4>
            <ul>
                <li><i class="fa fa-clock-o"></i> {{$event->formatted_event_date}}: {{$event->formatted_event_duration}}
                </li>
                <li><i class="fa fa-map-marker"></i>{{substr($event->location, 0, 20) . '..' }}, At {{$event->city->name}}</li>
            </ul>

            @if($availableTicket)
            <div class="main-dark-button">
                <a href="{{route('events.details',[encrypt($event->id)])}}">Purchase Tickets</a>
            </div>
            @else
            <div class="main-red-button">
                <a href="{{route('events.details',[encrypt($event->id)])}}" class="text-white">Sold</a>
            </div>
            @endif
        </div>
    </div>
</div>
