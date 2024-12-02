<?php

namespace App\Services;

use App\Mail\TicketConfirmationMail;
use Mail;

class NotificationService
{
    public function sendTicketConfirmation($user, $event, $userTickets)
    {
        Mail::to($user->email)->send(new TicketConfirmationMail($user, $event, $userTickets));
    }
}
