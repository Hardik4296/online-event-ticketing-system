<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TicketFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the ticket details page loads successfully for a valid ticket.
     */
    public function test_ticket_details_loads_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'price' => 100]);
        $ticketPurchase = TicketPurchase::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'total_price' => $ticket->price * 2
        ]);

        $encryptedId = encrypt($ticketPurchase->group_id);

        $response = $this->get(route('ticket.details', ['id' => $encryptedId]));

        $response->assertStatus(200);
        $response->assertViewIs('ticket.details');
        $response->assertViewHas(['ticketPurchase', 'event', 'cities', 'totalAmount']);
    }

    /**
     * Test if the ticket details page returns 404 for a ticket that does not exist.
     */
    public function test_ticket_details_returns_404_for_invalid_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $invalidId = Crypt::encrypt(999);

        $response = $this->get(route('ticket.details', ['id' => $invalidId]));

        $response->assertStatus(404);
    }

    /**
     * Test if the ticket details page returns 404 when the user does not own the ticket.
     */
    public function test_ticket_details_returns_404_for_unauthorized_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $anotherUser = User::factory()->create();
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'price' => 100]);
        $ticketPurchase = TicketPurchase::factory()->create([
            'user_id' => $anotherUser->id,
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'total_price' => $ticket->price * 2
        ]);

        $encryptedId = Crypt::encrypt($ticketPurchase->group_id);

        $response = $this->get(route('ticket.details', ['id' => $encryptedId]));

        $response->assertStatus(404);
    }

    /**
     * Test if exceptions are properly handled and a 404 response is returned.
     */
    public function test_ticket_details_handles_exceptions()
    {
        Log::shouldReceive('error')
            ->once()
            ->with(
                \Mockery::any(),
                \Mockery::any()
            );

        $user = User::factory()->create();
        $this->actingAs($user);

        $invalidId = 'invalid-encrypted-id';

        $response = $this->get(route('ticket.details', ['id' => $invalidId]));

        $response->assertStatus(404);
    }
}
