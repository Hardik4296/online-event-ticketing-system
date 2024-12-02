<?php
namespace App\Services;
use Stripe\Stripe;

class StripeService
{
    public function createPaymentIntent($totalAmount, $event_id, $group_id)
    {
        return \Stripe\PaymentIntent::create([
            'amount' => $totalAmount * 100,
            'currency' => 'usd',
            'metadata' => [
                'group_id' => encrypt($group_id),
                'event_id' => encrypt($event_id)
            ],
        ]);
    }

    public function confirmPayment($paymentIntentId)
    {
        return \Stripe\PaymentIntent::retrieve($paymentIntentId);
    }
}
