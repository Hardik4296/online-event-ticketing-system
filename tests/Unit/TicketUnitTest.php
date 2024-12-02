<?php

namespace Tests\Unit;

use App\Http\Controllers\TicketController;
use App\Models\City;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\User;
use App\Services\CityService;
use App\Services\TicketPurchaseService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class TicketUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $cityServiceMock;
    protected $ticketPurchaseServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cityServiceMock = Mockery::mock(CityService::class);
        $this->ticketPurchaseServiceMock = Mockery::mock(TicketPurchaseService::class);
    }

    /**
     * Test successful ticket details retrieval.
     */
    public function test_ticket_details_successfully_loaded()
    {
        $user = $this->createAuthenticatedUser();

        $city = City::factory()->create(['name' => 'City 1']);
        $organizer = User::factory(['role' => 'organizer'])->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'price' => 100, 'available_quantity' => 10, 'ticket_type' => 'Sample Ticket']);
        $ticketPurchase = TicketPurchase::factory()->create([
            'ticket_id' => $ticket->id,
            'event_id' => $event->id,
            'group_id' => 'group123',
            'user_id' => $user->id,
            'transaction_status' => 'success',
        ]);

        $this->mock(CityService::class, function ($mock) use ($city) {
            $mock->shouldReceive('getCityData')->once()->andReturn([$city]);
        });

        $this->mock(TicketPurchaseService::class, function ($mock) use ($ticketPurchase) {
            $mock->shouldReceive('getTicketPurchase')
                ->once()
                ->with('group123')
                ->andReturn(collect([$ticketPurchase]));
        });

        $response = $this->get(route('ticket.details', ['id' => encrypt('group123')]));

        $response->assertStatus(200);
        $response->assertViewIs('ticket.details');
        $response->assertViewHasAll([
            'ticketPurchase',
            'event',
            'cities',
            'totalAmount',
        ]);
    }


    /**
     * Test ticket not found when ticket ID is invalid.
     */
    public function test_ticket_details_ticket_not_found()
    {
        $user = $this->createAuthenticatedUser();

        $ticketId = 'valid-encrypted-id';
        $decryptedId = 123;

        $this->partialMock(TicketController::class, function ($mock) use ($ticketId, $decryptedId) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('decryptId')
                ->with($ticketId)
                ->andReturn($decryptedId);
        });

        $this->ticketPurchaseServiceMock->shouldReceive('getTicketPurchase')
            ->with($decryptedId)
            ->andReturn(collect());

        Log::shouldReceive('error')->once();

        $controller = new TicketController($this->cityServiceMock, $this->ticketPurchaseServiceMock);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $controller->ticketDetails(request(), $ticketId);
    }

    /**
     * Test decryption failure.
     */
    public function test_ticket_details_decryption_failure()
    {
        $this->createAuthenticatedUser();

        $ticketId = 'invalid-encrypted-id';

        $this->partialMock(TicketController::class, function ($mock) use ($ticketId) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('decryptId')
                ->with($ticketId)
                ->andThrow(DecryptException::class, 'Decryption error');
        });

        Log::shouldReceive('error')->once();

        $controller = new TicketController($this->cityServiceMock, $this->ticketPurchaseServiceMock);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $controller->ticketDetails(request(), $ticketId);
    }

    /**
     * Test unauthorized user accessing ticket details.
     */
    public function test_ticket_details_unauthorized_access()
    {
        $user = $this->createAuthenticatedUser();

        $ticketId = 'valid-encrypted-id';
        $decryptedId = 123;

        $this->partialMock(TicketController::class, function ($mock) use ($ticketId, $decryptedId) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('decryptId')
                ->with($ticketId)
                ->andReturn($decryptedId);
        });

        $ticketPurchaseData = collect([
            (object) ['id' => 1, 'user_id' => $user->id + 1, 'event' => (object) ['id' => 1, 'name' => 'Event 1'], 'ticket' => (object) ['price' => 100], 'quantity' => 2],
        ]);

        $this->ticketPurchaseServiceMock->shouldReceive('getTicketPurchase')
            ->with($decryptedId)
            ->andReturn($ticketPurchaseData);

        Log::shouldReceive('error')->once();

        $controller = new TicketController($this->cityServiceMock, $this->ticketPurchaseServiceMock);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $controller->ticketDetails(request(), $ticketId);
    }

    /**
     * Helper to create an authenticated user.
     */
    private function createAuthenticatedUser()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        return $user;
    }
}
