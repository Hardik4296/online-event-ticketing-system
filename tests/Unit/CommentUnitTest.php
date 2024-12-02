<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Services\CommentService;
use App\Services\EventService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class CommentUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $commentService;
    protected $eventService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commentService = Mockery::mock(CommentService::class);
        $this->eventService = Mockery::mock(EventService::class);

        $this->app->instance(CommentService::class, $this->commentService);
        $this->app->instance(EventService::class, $this->eventService);
    }

    /**
     * Test index method with valid request
     */
    public function testIndexValidRequest()
    {
        $eventId = 1;
        $encryptedId = encrypt($eventId);

        $this->eventService->shouldReceive('getEventDetails')->with($eventId)->andReturn(true);
        $this->commentService->shouldReceive('getComments')->with($eventId)->andReturn(['comment1', 'comment2']);

        $response = $this->getJson(route('comments.index', ['id' => $encryptedId]));

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'comments' => ['comment1', 'comment2'],
                 ]);
    }

    /**
     * Test index method with invalid event
     */
    public function testIndexInvalidEvent()
    {
        $eventId = 999;
        $encryptedId = encrypt($eventId);

        $this->eventService->shouldReceive('getEventDetails')->with($eventId)->andReturn(false);

        $response = $this->getJson(route('comments.index', ['id' => $encryptedId]));

        $response->assertStatus(404);
    }

    /**
     * Test index method with decryption failure
     */
    public function testIndexDecryptionFailure()
    {
        $encryptedId = encrypt(1);

        $response = $this->getJson(route('comments.index', ['id' => $encryptedId]));

        $response->assertStatus(404);
    }

    /**
     * Test store method with valid data
     */
    public function testStoreValidComment()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $data = [
            'comment' => 'This is a test comment.',
            'event_id' => encrypt($event->id),
        ];

        $this->commentService->shouldReceive('createComment')->with([
            'comment' => 'This is a test comment.',
            'event_id' => $event->id,
        ])->andReturn(true);

        $response = $this->postJson(route('comments.store'), $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Comment added successfully',
            ]);
    }

    /**
     * Test store method with invalid data
     */
    public function testStoreInvalidComment()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $data = [
            'comment' => '',
            'event_id' => encrypt($event->id),
        ];

        $response = $this->postJson(route('comments.store'), $data);

        $response->assertStatus(422);
    }

    /**
     * Test store method for non-AJAX request
     */
    public function testStoreNonAjaxRequest()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $data = [
            'comment' => 'This is a test comment.',
            'event_id' => encrypt($event->id),
        ];

        $response = $this->post(route('comments.store'), $data);

        $response->assertStatus(404);
    }
}
