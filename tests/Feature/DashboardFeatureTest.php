<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\EventService;
use Mockery;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test the dashboard method.
     */
    public function testDashboard()
    {
        $user = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($user);

        $mockEventService = Mockery::mock(EventService::class);

        $mockEventService->shouldReceive('myEventCount')->once()->andReturn(10);
        $mockEventService->shouldReceive('myActiveEventCount')->once()->andReturn(5);
        $mockEventService->shouldReceive('myInActiveEventCount')->once()->andReturn(3);
        $mockEventService->shouldReceive('myCancelledEventCount')->once()->andReturn(2);

        $this->app->instance(EventService::class, $mockEventService);

        $response = $this->get(route('organizer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('organizer.index');
        $response->assertViewHasAll([
            'myTotalEvents' => 10,
            'myActiveEvents' => 5,
            'myInActiveEvents' => 3,
            'myCancelledEvents' => 2,
        ]);
    }
}
