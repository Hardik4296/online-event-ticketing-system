<?php

namespace Tests\Unit;

use App\Http\Controllers\EventController;
use App\Models\City;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\User;
use App\Services\CityService;
use App\Services\EventService;
use App\Services\TicketService;
use App\Services\TicketPurchaseService;
use DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use Tests\TestCase;

class EventUnitTest extends TestCase
{
    use RefreshDatabase;
    protected $eventController;
    protected $cityService;
    protected $eventService;
    protected $ticketService;
    protected $ticketPurchaseService;

    public function setUp(): void
    {
        parent::setUp();

        $this->cityService = Mockery::mock(CityService::class);
        $this->eventService = Mockery::mock(EventService::class);
        $this->ticketService = Mockery::mock(TicketService::class);
        $this->ticketPurchaseService = Mockery::mock(TicketPurchaseService::class);

        $this->eventController = new EventController(
            $this->cityService,
            $this->eventService,
            $this->ticketService,
            $this->ticketPurchaseService
        );
    }

    /**
     * Test for the details method
     */
    public function testEventDetails()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $eventId = $event->id;
        $encryptedId = encrypt($eventId);

        $this->eventService->shouldReceive('getEventDetails')->with($eventId)->andReturn($event);
        $this->ticketService->shouldReceive('totalTicketAvailable')->with($eventId)->andReturn(100);

        $response = $this->get(route('events.details', ['id' => $encryptedId]));

        $response->assertViewIs('event.details');
        $response->assertViewHas('event', $event);
        $response->assertViewHas('totalTicketAvailable', 0);
    }

    /**
     * Test for organizerEvents method
     */
    public function testOrganizerEvents()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $events = Event::factory()->count(5)->create(['organizer_id' => $organizer->id]);

        $this->eventService->shouldReceive('organizerEvents')->with($organizer->id)->andReturn($events);

        $response = $this->get(route('organizer.events.list'));

        $response->assertViewIs('organizer.event.list');
    }

    /**
     * Test for create method
     */
    public function testCreateEvent()
    {

        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);
        $cities = City::factory()->count(5)->create();

        $this->cityService->shouldReceive('getCityData')->andReturn($cities);

        $response = $this->get(route('organizer.events.create'));

        $response->assertViewIs('organizer.event.create');
        $response->assertViewHas('cities', $cities);
    }

    /**
     * Test for store method
     */
    public function testStoreEvent()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);
        $city = City::factory()->create();

        $requestData = [
            'title' => 'New Event',
            'description' => 'This is a new event description.',
            'status' => 'active',
            'event_date_time' => now()->addDay()->toDateTimeString(),
            'event_duration' => '02:00',
            'location' => 'New York',
            'city_id' => $city->id,
            'image' => UploadedFile::fake()->image('event_image.jpg'),
            'ticket_type' => ['general', 'vip'],
            'ticket_type.*' => 'general',
            'price' => ['100', '200'],
            'price.*' => '100',
            'quantity' => ['100', '50'],
            'quantity.*' => '100',
        ];

        $this->eventService->shouldReceive('store')->with($requestData)->andReturn(true);

        $response = $this->postJson(route('organizer.events.store'), $requestData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Event created successfully!',
        ]);
    }

    /**
     * Test for edit method
     */
    public function testEditEvent()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $eventId = $event->id;
        $encryptedId = encrypt($eventId);
        $cities = City::factory()->count(5)->create();

        $this->cityService->shouldReceive('getCityData')->andReturn($cities);
        $this->eventService->shouldReceive('getOrganizerEventDetails')->with($eventId)->andReturn($event);

        $response = $this->get(route('organizer.events.edit', ['id' => $encryptedId]));

        $response->assertViewIs('organizer.event.edit');
        $response->assertViewHas('event', $event);
    }

    /**
     * Test for update method
     */
    public function testUpdateEvent()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);
        $city = City::factory()->create();

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $requestData = [
            'title' => 'Updated Event',
            'description' => 'This is a new event description.',
            'status' => 'active',
            'event_date_time' => now()->addDay()->toDateTimeString(),
            'event_duration' => '02:00',
            'location' => 'New York',
            'city_id' => $city->id,
            'image' => UploadedFile::fake()->image('event_image.jpg'),
            'ticket_type' => ['general', 'vip'],
            'ticket_type.*' => 'general',
            'price' => ['100', '200'],
            'price.*' => '100',
            'quantity' => ['100', '50'],
            'quantity.*' => '100',
        ];

        $this->eventService->shouldReceive('update')->with($event->id, $requestData)->andReturn(true);

        $response = $this->putJson(route('organizer.events.update', ['id' => encrypt($event->id)]), $requestData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Event updated successfully!'
        ]);
    }

    /**
     * Test for organizerEventDetails method
     */
    public function testorganizerEventDetails()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);
        $cities = City::factory()->count(5)->create();

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $eventId = $event->id;
        $encryptedId = encrypt($eventId);

        $totalTickets = 0;

        $this->eventService->shouldReceive('getOrganizerEventDetails')->with($eventId)->andReturn($event);
        $this->cityService->shouldReceive('getCityData')->andReturn($cities);
        $this->ticketService->shouldReceive('totalTicketAvailable')->with($eventId)->andReturn($totalTickets);

        $response = $this->get(route('organizer.events.detail', ['id' => $encryptedId]));

        $response->assertViewIs('organizer.event.details');
        $response->assertViewHas('event', $event);
        $response->assertViewHas('totalTicketAvailable', $totalTickets);
    }

    /**
     * Test for getAttendees method
     */
    public function testGetAttendees()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);
        $city = City::factory()->create();

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $eventId = $event->id;
        $encryptedId = encrypt($eventId);
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 100,
            'available_quantity' => 10,
        ]);

        TicketPurchase::factory([
            'ticket_id' => $ticket->id,
            'event_id' => $event->id,
            'user_id' => $organizer->id
        ])->create();

        $attendees = DB::table('ticket_purchases')
            ->join('users', 'users.id', '=', 'ticket_purchases.user_id')
            ->select('group_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('MIN(ticket_purchases.user_id) as user_id'), 'users.name')
            ->where('event_id', $event->id)
            ->where('transaction_status', 'success')
            ->groupBy('group_id', 'users.name')
            ->get();

        $this->ticketPurchaseService->shouldReceive('getEventAttendees')->with($encryptedId)->andReturn($attendees);

        $response = $this->get(route('organizer.events.attendee.list', ['id' => $encryptedId]));

        $response->assertStatus(200);
    }

    /**
     * Test for export method
     */
    public function testExportEvents()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        Excel::fake();

        $response = $this->get(route('organizer.events.export'));

        $response->assertStatus(200);
        Excel::assertDownloaded('events.xlsx');
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
