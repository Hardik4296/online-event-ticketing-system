<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoadUpComingRequest;
use App\Services\CityService;
use App\Services\EventService;
use App\Traits\Common;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    use Common;
    protected $cityService;
    protected $eventService;

    public function __construct(CityService $cityService, EventService $eventService)
    {
        $this->cityService = $cityService;
        $this->eventService = $eventService;
    }

    /**
     * Handle home request
     */
    public function home()
    {
        try {
            $cities = $this->cityService->getCityData();

            return view('home', compact('cities'));
        } catch (Exception $e) {
            Log::error('Error fetching city data for home: ', ['message' => $e->getMessage()]);

            return abort(404, 'Something went wrong.');
        }
    }

    /**
     * Handle get filtered events request
     */
    private function getFilteredEvents(Request $request)
    {
        try {

            $cacheKey = $this->generateEventCacheKey($request);

            return Cache::remember($cacheKey, 3600, function () use ($request) {
                $query = $this->eventService->getEventData();

                if ($request->get('city_id')) {
                    $query->where('city_id', $request->get('city_id'));
                }

                if ($request->get('date')) {
                    $query->whereDate('event_date_time', date('Y-m-d', strtotime($request->get('date'))));
                }

                if ($request->get('keyword')) {
                    $query->where('title', 'like', '%' . $request->get('keyword') . "%");
                    $query->orWhere('description', 'like', '%' . $request->get('keyword') . "%");
                    $query->orWhere('location', 'like', '%' . $request->get('keyword') . "%");
                }

                return $query->paginate(6);
            });

        } catch (Exception $e) {
            Log::error('Error fetching filtered events', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Handle load upcoming events request
     */
    public function loadUpcomingEvents(LoadUpComingRequest $request)
    {
        try {

            $validatedData = $request->validated();

            $upComingEvents = $this->getFilteredEvents($request);


            return view('event.index', compact('upComingEvents'));
        } catch (Exception $e) {
            Log::error('Error loading upcoming events', ['message' => $e->getMessage()]);

        }
    }
}
