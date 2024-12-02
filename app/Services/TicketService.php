<?php

namespace App\Services;

use App\Models\Ticket;
use App\Services\TicketPurchaseService;
use App\Traits\Common;
use Exception;

class TicketService
{
    use Common;
    protected $ticket;

    public function __construct()
    {
        $this->ticket = new Ticket();
    }

    /**
     * Handle lock for update
     */
    public function LockForUpdate(int $id)
    {
        return $this->ticket->find($id);
    }

    /**
     * Handle update qty
     */
    public function updateQty(int $id, int $qty)
    {
        return $this->ticket->where('id', $id)->decrement('available_quantity', $qty);
    }

    /**
     * Handle total ticket available
     */
    public function totalTicketAvailable(int $event_id)
    {
        return $this->ticket->where('event_id', $event_id)->sum('available_quantity');
    }

    /**
     * Handle store
     */
    public function store(array $data)
    {
        return $this->ticket::create($data);
    }

    /**
     * Handle ticket details
     */
    public function ticketDetails(int $id)
    {
        return $this->ticket::with('event')->where('id', $id);
    }

    public function processTicketsAndCreatePurchases($user, $ticketRequests, $event_id, $group_id)
    {
        $totalAmount = 0;
        $ticketPurchaseService = new TicketPurchaseService();

        foreach ($ticketRequests as $ticketRequest) {
            $ticket = $this->lockForUpdate($ticketRequest['ticket_id']);
            $quantity = $ticketRequest['quantity'];

            if ($ticket->available_quantity < $quantity) {
                throw new Exception('Not enough tickets available.');
            }

            $totalAmount += $ticket->price * $quantity;
            $this->updateQty($ticket->id, $quantity);

            $ticket_UID = $this->generateRandomId($user->id);
            $ticketPurchaseService->createTicketPurchase([
                'ticket_UID' => $ticket_UID,
                'group_id' => $group_id,
                'event_id' => $event_id,
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'quantity' => $quantity,
                'total_price' => $ticket->price * $quantity,
                'payment_id' => null,
                'transaction_status' => 'pending',
                'created_at' => now(),
            ]);
        }

        return $totalAmount;
    }
}
