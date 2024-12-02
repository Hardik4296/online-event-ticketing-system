<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the home page loads with cities.
     */
    public function test_home_loads_with_cities()
    {
        City::factory()->count(5)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewHas('cities');
    }

    /**
     * Test if the load upcoming events functionality works with filters.
     */
    public function test_load_upcoming_events_with_filters()
    {
        $city = City::factory()->create();
        $event1 = Event::factory()->create(['city_id' => $city->id, 'title' => 'Test Event 1']);
        $event2 = Event::factory()->create(['city_id' => $city->id, 'title' => 'Another Event']);

        $response = $this->postJson(route('load.upcoming.events', ['city_id' => $city->id, 'keyword' => 'Test', 'page' => 1]));

        $response->assertStatus(200);
        $response->assertViewHas('upComingEvents');

        $events = $response->viewData('upComingEvents');
        $this->assertTrue($events->contains($event1));
        $this->assertFalse($events->contains($event2));
    }

    /**
     * Test if pagination works correctly for upcoming events.
     */
    public function test_pagination_for_upcoming_events()
    {
        Event::factory()->count(10)->create();

        $response = $this->postJson(route('load.upcoming.events', ['page' => 1]));

        $response->assertStatus(200);
        $response->assertViewHas('upComingEvents');

        $events = $response->viewData('upComingEvents');
        $this->assertCount(6, $events);
    }

    /**
     * Test if the home page handles exceptions correctly.
     */
    public function test_home_handles_exception()
    {
        $mock = $this->createMock(\App\Services\CityService::class);
        $mock->expects($this->once())
            ->method('getCityData')
            ->willThrowException(new \Exception('Error loading cities'));

        $this->app->instance(\App\Services\CityService::class, $mock);

        $response = $this->get(route('home'));

        $response->assertStatus(404);
    }
}
