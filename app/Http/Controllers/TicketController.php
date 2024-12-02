<?php

namespace App\Http\Controllers;

use App\Services\CityService;
use App\Services\TicketPurchaseService;
use App\Traits\Common;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class TicketController extends Controller
{

    use Common;
    protected $cityService;
    protected $ticketPurchaseService;

    public function __construct(CityService $cityService, TicketPurchaseService $ticketPurchaseService)
    {
        $this->cityService = $cityService;
        $this->ticketPurchaseService = $ticketPurchaseService;
    }

    /**
     * Handle ticket detail page request
     */
    public function ticketDetails(Request $request, string $id)
    {
        try {
            $ticketId = $this->decryptId($id);

            $ticketPurchase = $this->ticketPurchaseService->getTicketPurchase($ticketId);

            if ($ticketPurchase->isEmpty()) {
                Log::error('Ticket purchase not found', ['ticket_id' => $ticketId]);

                abort(404, 'Ticket purchase not found.');
            }

            $firstTicket = $ticketPurchase->first();
            if ($firstTicket['user_id'] != auth()->id()) {
                Log::error('Ticket not found for the user', ['ticket_id' => $ticketId, 'user_id' => auth()->id()]);

                abort(404, 'Ticket not found with user.');
            }

            $event = $firstTicket['event'];
            $cities = $this->cityService->getCityData();

            $totalAmount = $ticketPurchase->reduce(function ($carry, $ticket) {
                return $carry + ($ticket['ticket']['price'] * $ticket['quantity']);
            }, 0);

            return view('ticket.details', compact('ticketPurchase', 'event', 'cities', 'totalAmount'));

        } catch (DecryptException $e) {
            Log::error('Decryption failed', ['id' => $id, 'message' => $e->getMessage()]);

            abort(404, 'Something went wrong.');
        } catch (Exception $e) {
            Log::error('Error loading ticket details', ['message' => $e->getMessage(), 'ticket_id' => $id]);

            abort(404, 'Something went wrong.');
        }
    }

}
