
<ul>
    @foreach ($attendees as $attendee)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">{{ decryptData($attendee->name) }}</h5>
            <small>
                Ticket: {{ $attendee->total_quantity ?? 'No ticket assigned' }}
            </small>
        </div>
        <p>Confirmation email sent
            <span class="badge bg-primary rounded-pill">
                <i class="fa fa-check"></i>
            </span>
        </p>
    </li>
    @endforeach
</ul>
