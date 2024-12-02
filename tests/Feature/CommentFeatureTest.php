<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper method to create a test user.
     */
    private function createTestUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Test comment list retrieval (index method).
     */
    public function test_can_fetch_comments_for_a_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $comment = Comment::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);
        $response = $this->getJson(
            route('comments.index', ['id' => encrypt($event->id)]),
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'comments' => [
                    '*' => ['id', 'event_id', 'user_id', 'comment'],
                ],
            ])
            ->assertJson([
                'success' => true,
                'comments' => [
                    [
                        'id' => $comment->id,
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'comment' => $comment->comment,
                    ],
                ],
            ]);
    }

    /**
     * Test comment list retrieval fails with invalid post id (index method).
     */
    public function test_cannot_fetch_comments_for_invalid_post_id()
    {
        $user = User::factory()->create();
        Event::factory()->create();

        $this->actingAs($user);
        $invalidEventId = encrypt(999999);
        $response = $this->getJson(
            route('comments.index', ['id' => $invalidEventId]),
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(404);
    }

    /**
     * Test successful comment creation (store method).
     */
    public function test_can_store_a_comment()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $this->actingAs($user);
        $payload = [
            'event_id' => encrypt($event->id),
            'comment' => 'This is a test comment.',
        ];
        $response = $this->postJson(route('comments.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Comment added successfully',
            ]);

        $this->assertDatabaseHas('comments', [
            'event_id' => $event->id,
            'comment' => 'This is a test comment.',
        ]);
    }
}
