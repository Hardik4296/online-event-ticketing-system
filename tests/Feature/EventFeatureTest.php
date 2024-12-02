<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class EventFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the details method with a valid event ID.
     */
    public function test_event_details_with_valid_id()
    {
        $event = Event::factory()->create();

        $response = $this->get(route('events.details', ['id' => encrypt($event->id)]));

        $response->assertStatus(200)
            ->assertViewIs('event.details')
            ->assertViewHas('id', function ($encryptedId) use ($event) {
                return decrypt($encryptedId) === $event->id;
            })
            ->assertViewHasAll([
                'event',
                'totalTicketAvailable',
            ]);
    }

    /**
     * Test the details method with an invalid event ID.
     */
    public function test_event_details_with_invalid_id()
    {
        $invalidId = encrypt(99999);

        $response = $this->get(route('events.details', ['id' => $invalidId]));

        $response->assertStatus(404);
    }

    /**
     * Test my events list retrieval for an authenticated user.
     */
    public function test_my_events()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $response = $this->get(route('organizer.events.list'));

        $response->assertStatus(200)
            ->assertViewIs('organizer.event.list')
            ->assertViewHas('events');
    }

    /**
     * Test the store method for creating a new event.
     */
    public function test_store_event()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $city = City::factory()->create();

        $payload = [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'status' => 'active',
            'event_date_time' => now()->addDays(10)->toDateTimeString(),
            'event_duration' => '02:00',
            'location' => 'Test Location',
            'city_id' => $city->id,
            'image' => UploadedFile::fake()->image('event.jpg'),
            'ticket_type' => ['Regular', 'VIP'],
            'price' => ['20', '50'],
            'quantity' => ['100', '50'],
        ];

        $response = $this->postJson(route('organizer.events.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Event created successfully!',
            ]);

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'status' => 'active',
        ]);
    }

    /**
     * Test the store method with missing required fields.
     */
    public function test_store_event_with_missing_fields()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $payload = [
            'title' => '',
        ];

        $response = $this->postJson(route('organizer.events.store'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'status', 'event_date_time', 'event_duration', 'location', 'city_id', 'image', 'ticket_type', 'price', 'quantity']);
    }

    /**
     * Test the edit method with a valid event ID.
     */
    public function test_edit_event_with_valid_id()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->get(route('organizer.events.edit', ['id' => encrypt($event->id)]));

        $response->assertStatus(200)
            ->assertViewIs('organizer.event.edit')
            ->assertViewHasAll(['cities', 'event']);
    }

    /**
     * Test the edit method with an invalid event ID.
     */
    public function test_edit_event_with_invalid_id()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $invalidId = encrypt(99999);

        $response = $this->get(route('organizer.events.edit', ['id' => $invalidId]));

        $response->assertStatus(404);
    }

    /**
     * Test the update method for updating an event.
     */
    public function test_update_event()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);
        $city = City::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $payload = [
            'title' => 'Updated Event',
            'description' => 'Updated Description',
            'event_date_time' => now()->addDays(15)->toDateTimeString(),
            'event_duration' => '03:00',
            'location' => 'Updated Location',
            'city_id' => $city->id,
            'status' => 'active',
            'ticket_type' => ['Regular', 'VIP'],
            'price' => ['20', '50'],
            'quantity' => ['100', '50'],
        ];

        $response = $this->putJson(route('organizer.events.update', ['id' => encrypt($event->id)]), $payload);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Event updated successfully!',
            ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event',
        ]);
    }

    /**
     * Test the update method with missing required fields.
     */
    public function test_update_event_with_missing_fields()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $payload = [
            'title' => '',
        ];

        $response = $this->putJson(route('organizer.events.update', ['id' => encrypt($event->id)]), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'event_date_time', 'event_duration', 'location', 'city_id', 'ticket_type', 'price', 'quantity']);
    }

    /**
     * Test my event details method.
     */
    public function test_my_event_details_with_valid_id()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->get(route('organizer.events.detail', ['id' => encrypt($event->id)]));

        $response->assertStatus(200)
            ->assertViewIs('organizer.event.details')
            ->assertViewHasAll(['id', 'event', 'cities', 'totalTicketAvailable']);
    }

    /**
     * Test get attendees method with a valid event ID.
     */
    public function test_get_attendees_with_valid_id()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $eventId = encrypt($event->id);

        $response = $this->get(route('organizer.events.attendee.list', ['id' => $eventId]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test export events method.
     */
    public function testExportEvents()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        Event::factory()->count(5)->create(['organizer_id' => $organizer->id]);

        Excel::fake();

        $response = $this->get(route('organizer.events.export'));

        $response->assertStatus(200);

        Excel::assertDownloaded('events.xlsx');
    }
}
