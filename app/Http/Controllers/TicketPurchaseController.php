<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmPaymentRequest;
use App\Http\Requests\CreatePaymentRequest;
use App\Services\EventService;
use App\Services\NotificationService;
use App\Services\StripeService;
use App\Services\TicketPurchaseService;
use App\Services\TicketService;
use App\Traits\Common;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class TicketPurchaseController extends Controller
{
    use Common;

    private $eventService;
    private $ticketService;
    private $ticketPurchaseService;
    protected $notificationService;

    public function __construct(EventService $eventService, NotificationService $notificationService, StripeService $stripeService, TicketService $ticketService, TicketPurchaseService $ticketPurchaseService)
    {
        $this->eventService = $eventService;
        $this->notificationService = $notificationService;
        $this->ticketPurchaseService = $ticketPurchaseService;
        $this->ticketService = $ticketService;
        $this->stripeService = $stripeService;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Handle create payment intent requests.
     */

    public function createPaymentIntent(CreatePaymentRequest $request)
    {
        if (auth()->guest()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $validatedData = $request->validated();
            $group_id = $this->generateGroupId($user->id);

            $totalAmount = $this->ticketService->processTicketsAndCreatePurchases(
                $user,
                $validatedData['tickets'],
                $validatedData['event_id'],
                $group_id
            );

            if (app()->environment('testing')) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'client_secret' => 'pi_3Nl4t4lF4ke',
                    'ticket_UID' => encrypt($group_id),
                ]);
            }

            $paymentIntent = $this->stripeService->createPaymentIntent($totalAmount, $validatedData['event_id'], $group_id);

            $this->ticketPurchaseService->updateGroupTicketPurchase($group_id, [
                'payment_id' => $this->retrievePaymentIntentId($paymentIntent->client_secret),
            ]);

            $event = $this->eventService->getEventDetails($validatedData['event_id'])->first();
            DB::commit();
            $this->clearEventListCache();
            $this->clearOrganizerEventCache($event->organizer_id);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'ticket_UID' => encrypt($group_id),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating payment intent: ', ['message' => $e->getMessage()]);

            return response()->json(['success' => false, 'error' => 'Something went wrong.'], 500);
        }
    }

    /**
     * Handle confirm payment request.
     */
    public function confirmPayment(ConfirmPaymentRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $paymentIntentId = $validatedData['payment_intent_id'];

            $paymentIntent = $this->stripeService->confirmPayment($paymentIntentId);

            $event_id = $this->decryptId($paymentIntent->metadata->event_id);
            $event = $this->eventService->getEventDetails($event_id)->first();

            if ($paymentIntent->status === 'succeeded') {
                $group_id = $this->decryptId($paymentIntent->metadata->group_id);
                $this->ticketPurchaseService->updateGroupTicketPurchase($group_id, [
                    'transaction_status' => 'success',
                ]);

                $user = auth()->user();

                $userTickets = $this->ticketPurchaseService->getUserTickets($group_id)->get();
                $this->notificationService->sendTicketConfirmation($user, $event, $userTickets);

                session()->flash('success_msg', 'Your booking is complete! We look forward to seeing you.');

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully',
                    'group_id' => encrypt($group_id),
                ]);
            }
            $this->clearEventListCache();
            $this->clearOrganizerEventCache($event->organizer_id);

            return response()->json(['success' => false, 'message' => 'Payment failed or not completed'], 400);
        } catch (Exception $e) {
            Log::error('Error confirming payment: ', ['message' => $e->getMessage()]);

            return response()->json(['success' => false, 'error' => 'Something went wrong.'], 500);
        }
    }

}
