<?php

namespace Tests\Unit;

use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class DashboardUnitTest extends TestCase
{
    protected $eventService;

    public function setUp(): void
    {
        parent::setUp();

        $this->eventService = Mockery::mock(EventService::class);

        $this->app->instance(EventService::class, $this->eventService);
    }

    public function testDashboardReturnsCorrectViewWithEventCounts()
    {
        $organizer = User::factory(['role' => 'organizer'])->create();
        $this->actingAs($organizer);

        $this->eventService->shouldReceive('myEventCount')->once()->andReturn(10);
        $this->eventService->shouldReceive('myActiveEventCount')->once()->andReturn(5);
        $this->eventService->shouldReceive('myInActiveEventCount')->once()->andReturn(3);
        $this->eventService->shouldReceive('myCancelledEventCount')->once()->andReturn(2);

        $response = $this->get(route('organizer.dashboard'));

        $response->assertViewIs('organizer.index');
        $response->assertViewHas('myTotalEvents', 10);
        $response->assertViewHas('myActiveEvents', 5);
        $response->assertViewHas('myInActiveEvents', 3);
        $response->assertViewHas('myCancelledEvents', 2);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
