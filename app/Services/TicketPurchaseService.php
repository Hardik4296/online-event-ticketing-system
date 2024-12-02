<?php

namespace App\Services;

use App\Models\TicketPurchase;
use DB;

class TicketPurchaseService
{
    protected $TicketPurchase;

    public function __construct()
    {
        $this->TicketPurchase = new TicketPurchase();
    }

    /**
     * Handle create ticket purchase
     */
    public function createTicketPurchase(array $data)
    {
        return $this->TicketPurchase->create($data);
    }

    /**
     * Handle get ticket purchase
     */
    public function getTicketPurchase(string $group_id)
    {
        return $this->TicketPurchase->with('event', 'ticket')->where(['group_id' => $group_id, 'transaction_status' => 'success', 'user_id' => auth()->id()])->get();
    }

    /**
     * Handle update group ticket purchase
     */
    public function updateGroupTicketPurchase(string $group_id, array $data)
    {
        return $this->TicketPurchase->where('group_id', $group_id)->update($data);
    }

    /**
     * Handle get user tickets
     */
    public function getUserTickets(string $group_id)
    {
        return $this->TicketPurchase->with('ticket')->where('group_id', $group_id);
    }

    /**
     * Handle get event attendees
     */
    public function getEventAttendees(int $event_id)
    {
        $data = DB::table('ticket_purchases')
            ->join('users', 'users.id', '=', 'ticket_purchases.user_id')
            ->select('group_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('MIN(ticket_purchases.user_id) as user_id'), 'users.name')
            ->where('event_id', $event_id)
            ->where('transaction_status', 'success')
            ->groupBy('group_id', 'users.name')
            ->orderBy('ticket_purchases.id','desc')
            ->get();

        return $data;
    }
}


