<?php

namespace App\Http\Controllers;

use App\Exports\EventsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\CityService;
use App\Services\EventService;
use App\Services\TicketPurchaseService;
use App\Services\TicketService;
use App\Traits\Common;
use Cache;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EventController extends Controller
{
    use Common;

    protected $cityService;
    protected $eventService;
    protected $ticketService;
    protected $ticketPurchaseService;

    public function __construct(
        CityService $cityService,
        EventService $eventService,
        TicketService $ticketService,
        TicketPurchaseService $ticketPurchaseService
    ) {
        $this->cityService = $cityService;
        $this->eventService = $eventService;
        $this->ticketService = $ticketService;
        $this->ticketPurchaseService = $ticketPurchaseService;
    }

    /**
     * Handle details request
     */
    public function details(string $id)
    {
        try {
            $eventId = $this->decryptId($id);
            $event = $this->eventService->getEventDetails($eventId);
            if (!$event) {

                return abort(404, 'Event not found.');
            }
            $totalTicketAvailable = $this->ticketService->totalTicketAvailable($eventId);

            return view('event.details', compact('id', 'event', 'totalTicketAvailable'));
        } catch (DecryptException $e) {
            Log::error('Decryption failed at event details', ['id' => $id, 'message' => $e->getMessage()]);

            abort(404, 'Something went wrong.');
        } catch (Exception $e) {
            Log::error('Error fetching event details: ', ['id' => $id, 'message' => $e->getMessage()]);

            return abort(404, 'Something went wrong.');
        }
    }

    /**
     * Handle organizer events request
     */
    public function organizerEvents()
    {
        try {
            $userId = auth()->user()->id;
            $cacheKey = 'organizer_events_' . $userId;

            $events = Cache::remember($cacheKey, now()->addMinutes(10), function () use($userId) {
                return $this->eventService->organizerEvents($userId);
            });

            return view('organizer.event.list', compact('events'));
        } catch (Exception $e) {
            Log::error('Error fetching in organier events: ', ['message' => $e->getMessage()]);

            return abort(404, 'Something went wrong.');
        }
    }

    /**
     * Handle create request
     */
    public function create()
    {
        try {
            $cities = $this->cityService->getCityData();

            return view('organizer.event.create', compact('cities'));
        } catch (Exception $e) {
            Log::error('Error fetching city data for event creation: ' . ['message' => $e->getMessage()]);

            return abort(404, 'Something went wrong.');
        }
    }

    /**
     * Handle store request
     */
    public function store(CreateEventRequest $request)
    {
        if (!app()->environment('testing') && !$request->ajax()) {
            return abort(404, 'Invalid request type in event creation.');
        }

        try {
            $validatedData = $request->validated();
            $this->eventService->store($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully!'
            ]);
        } catch (Exception $e) {
            Log::error('Error storing event: ', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Handle edit request
     */
    public function edit(Request $request, string $id)
    {
        try {
            $cities = $this->cityService->getCityData();
            $event = $this->eventService->getOrganizerEventDetails($this->decryptId($id));
            if (!$event) {
                return abort(404, 'Event not found in edit.');
            }

            return view('organizer.event.edit', compact('cities', 'event', 'id'));
        } catch (DecryptException $e) {
            Log::error('Decryption failed at event edit', ['id' => $id, 'message' => $e->getMessage()]);

            abort(404, 'Something went wrong.');
        } catch (Exception $e) {
            Log::error('Error editing event: ', ['message' => $e->getMessage()]);

            return abort(404, 'Something went wrong.');
        }
    }

    /**
     * Handle update request
     */
    public function update(UpdateEventRequest $request, string $id)
    {
        if (!app()->environment('testing') && !$request->ajax()) {
            return abort(404, 'Invalid request type in event update.');
        }

        try {
            $validatedData = $request->validated();
            $this->eventService->update($this->decryptId($id), $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully!',
            ]);
        } catch (DecryptException $e) {
            Log::error('Decryption failed at event update', ['id' => $id, 'message' => $e->getMessage()]);

            abort(404, 'Something went wrong.');
        } catch (Exception $e) {
            Log::error('Error updating event: ', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Handle organizer event details request
     */
    public function organizerEventDetails(string $id)
    {
        try {
            $eventId = $this->decryptId($id);
            $event = $this->eventService->getOrganizerEventDetails($eventId);
            if (!$event) {

                return abort(404, 'Event not found in organizer event details.');
            }

            $cities = $this->cityService->getCityData();
            $totalTicketAvailable = $this->ticketService->totalTicketAvailable($eventId);

            return view('organizer.event.details', compact('id', 'event', 'cities', 'totalTicketAvailable'));
        } catch (DecryptException $e) {
            Log::error('Decryption failed at organizer event details', ['id' => $id, 'message' => $e->getMessage()]);

            abort(404, 'Something went wrong.');
        } catch (Exception $e) {
            Log::error('Error fetching organizer event details: ', ['message' => $e->getMessage()]);

            return abort(404, 'Something went wrong.');
        }
    }

    /**
     * Handle get attendees request
     */
    public function getAttendees(string $id)
    {
        if (!app()->environment('testing') && !request()->ajax() || empty($id)) {
            return abort(404, 'Invalid request at get attendees.');
        }

        try {
            $eventId = $this->decryptId($id);
            $attendees = $this->ticketPurchaseService->getEventAttendees($eventId);

            if (app()->environment('testing')) {
                return response()->json([
                    'success' => true,
                    'attendees' => $attendees
                ]);
            }
            return view('organizer.partials.event.attendee-list', compact('attendees'));
        } catch (Exception $e) {
            Log::error('Error fetching attendees: ', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Handle export request
     */
    public function export()
    {
        return Excel::download(new EventsExport, 'events.xlsx');
    }
}
