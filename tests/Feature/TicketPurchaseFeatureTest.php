<?php

namespace Tests\Feature;

use App\Mail\TicketConfirmationMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketPurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketPurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that payment intent is created successfully.
     */
    public function test_create_payment_intent()
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 100,
            'available_quantity' => 10,
        ]);

        $paymentData = [
            'event_id' => encrypt($event->id),
            'tickets' => [
                [
                    'ticket_id' => $ticket->id,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->postJson(route('create.payment.intent'), $paymentData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'client_secret', 'ticket_UID']);

        $this->assertDatabaseHas('ticket_purchases', [
            'user_id' => $user->id,
            'transaction_status' => 'pending'
        ]);
    }

    /**
     * Test payment confirmation after Stripe payment succeeds.
     */
    public function test_confirm_payment_success()
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create();

        $paymentData = [
            'payment_intent_id' => 'pi_12345'
        ];

        \Mockery::mock('alias:' . \Stripe\PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->with('pi_12345')
            ->andReturn((object) [
                'status' => 'succeeded',
                'metadata' => (object) [
                    'group_id' => encrypt(1),
                    'event_id' => encrypt($event->id),
                ]
            ]);

        $response = $this->postJson(route('confirm.payment'), $paymentData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Payment confirmed successfully'
        ]);

        Mail::assertSent(TicketConfirmationMail::class);
    }

    /**
     * Test payment confirmation when payment fails.
     */
    public function test_confirm_payment_failure()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create();

        $paymentData = [
            'payment_intent_id' => 'pi_12345'
        ];

        \Mockery::mock('alias:' . \Stripe\PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->with('pi_12345')
            ->andReturn((object) [
                'status' => 'failed',
                'metadata' => (object) [
                    'group_id' => encrypt(1),
                    'event_id' => encrypt($event->id),
                ]
            ]);

        $response = $this->postJson(route('confirm.payment'), $paymentData);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Payment failed or not completed'
        ]);
    }
}
