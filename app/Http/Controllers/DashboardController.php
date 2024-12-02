<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Handle dashboard
     */
    public function dashboard()
    {
        try {
            $userId = auth()->user()->id;
            $myTotalEvents = $this->eventService->myEventCount($userId);
            $myActiveEvents = $this->eventService->myActiveEventCount($userId);
            $myInActiveEvents = $this->eventService->myInActiveEventCount($userId);
            $myCancelledEvents = $this->eventService->myCancelledEventCount($userId);

            return view('organizer.index', compact('myTotalEvents', 'myActiveEvents', 'myInActiveEvents', 'myCancelledEvents'));
        } catch (Exception $e) {
            Log::error('Error in organizer dashboard', ['message' => $e->getMessage()]);

            abort(404, 'Something went wrong.');
        }
    }
}
