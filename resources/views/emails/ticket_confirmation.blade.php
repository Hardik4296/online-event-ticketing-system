<!DOCTYPE html>
<html>

<head>
    <title>Ticket Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            background: #4CAF50;
            padding: 20px;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            color: #333333;
        }

        .content p {
            margin: 10px 0;
        }

        .event-details {
            margin: 20px 0;
            border: 1px solid #dddddd;
            padding: 15px;
            border-radius: 8px;
            background: #f5f5f5;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            margin-top: 20px;
        }

    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Thank You for Your Purchase!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>We are thrilled to confirm your booking for the event <strong>{{ $event->title }}</strong>.</p>
            <div class="event-details">
                <p><strong>Event:</strong> {{ $event->title }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</p>
                <p><strong>Location:</strong> {{ $event->location }}</p>
            </div>
            <ul>
            @foreach ($userTicket as $item)
                <li>{{ $item->ticket->ticket_type }} - {{ $item->quantity }} tickets</li>
            @endforeach
            </ul>
            <p>Keep this email as confirmation of your booking. We look forward to seeing you there!</p>
            <p>If you have any questions, feel free to reach out to us.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
