<?php

namespace App\Services;

use App\Models\Event;
use App\Services\TicketService;
use App\Traits\Common;
use DB;

class EventService
{
    use Common;
    protected $event;
    protected $ticketService;

    public function __construct()
    {
        $this->event = new Event();
        $this->ticketService = new TicketService();
    }

    /**
     * Handle get event data
     */
    public function getEventData()
    {
        return $this->event
            ->select('events.id', 'events.city_id','events.organizer_id', 'events.image', 'events.title', 'events.description', 'events.event_date_time', 'events.event_duration', 'events.location', 'events.status' , 'events.ticket_price_start_from')
            ->with('city')
            ->where('status', 'active')
            ->where('event_date_time', '>=', now())
            ->orderBy('event_date_time', 'asc');
    }

    /**
     * Handle get event details
     */
    public function getEventDetails(int $id)
    {
        return $this->event
            ->select('events.id', 'events.city_id','events.organizer_id', 'events.image', 'events.title', 'events.description', 'events.event_date_time', 'events.event_duration', 'events.location', 'events.status', 'events.ticket_price_start_from')
            ->with('tickets','city')
            ->where([
                'status' => 'active',
                'id' => $id
            ])->first();
    }

    /**
     * Handle get organizer event details
     */
    public function getOrganizerEventDetails(int $id)
    {
        return $this->event
            ->select('events.id', 'events.city_id', 'events.organizer_id', 'events.image', 'events.title', 'events.description', 'events.event_date_time', 'events.event_duration', 'events.location', 'events.status' )
            ->with('tickets', 'city')
            ->where([
                'organizer_id' => auth()->user()->id,
                'id' => $id
            ])->firstOrFail();
    }

    /**
     * Handle my events
     */
    public function organizerEvents(int $id)
    {
        $events = $this->event::with('city')
            ->select(
                'events.id',
                'events.city_id',
                'events.organizer_id',
                'events.image',
                'events.title',
                'events.description',
                'events.event_date_time',
                'events.event_duration',
                'events.location',
                'events.status',
                'events.created_at',
                DB::raw('(SELECT SUM(available_quantity) FROM tickets WHERE tickets.event_id = events.id) as totalAvailableTicket'),
                DB::raw('(SELECT SUM(quantity) FROM ticket_purchases WHERE ticket_purchases.event_id = events.id AND ticket_purchases.transaction_status = "success") as totalBookedTicket')
            )
            ->where('events.organizer_id', $id)
            ->where('events.event_date_time', '>=', now())
            ->orderBy('events.event_date_time', 'asc')
            ->get();

        return $events;
    }

    /**
     * Handle store
     */
    public function store(array $data)
    {
        $imagePath = null;

        if ($data['image'] ?? false) {
            $imagePath = $data['image']->store('event_images', 'public');
        }

        $organizerId = auth()->user()->id;

        $eventData = [
            'organizer_id' => $organizerId,
            'city_id' => $data['city_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'event_date_time' => $data['event_date_time'],
            'event_duration' => $data['event_duration'],
            'location' => $data['location'],
            'status' => $data['status'],
            'ticket_price_start_from' => $data['price'] ? min($data['price']) : 0,
        ];

        if ($imagePath) {
            $eventData['image'] = $imagePath;
        }

        $event = $this->event::create($eventData);

        foreach ($data['ticket_type'] as $index => $type) {
            $this->ticketService->store([
                'event_id' => $event->id,
                'ticket_type' => $type,
                'price' => $data['price'][$index],
                'quantity' => $data['quantity'][$index],
                'available_quantity' => $data['quantity'][$index],
                'description' => $data['description'][$index],
            ]);
        }

        $this->clearEventListCache();
        $this->clearOrganizerEventCache($organizerId);

        return true;
    }

    /**
     * Handle update
     */
    public function update(int $id, array $data)
    {
        $event = $this->getOrganizerEventDetails($id);

        $eventData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? $event['description'],
            'event_date_time' => $data['event_date_time'],
            'event_duration' => $data['event_duration'],
            'location' => $data['location'],
            'city_id' => $data['city_id'],
            'status' => $data['status'],
            'ticket_price_start_from' => $data['price'] ? min($data['price']) : 0,
        ];

        if (!empty($data['image'])) {
            $eventData['image'] = $data['image']->store('event_images', 'public');
        }

        $this->event::where('id', $id)->update($eventData);

        if (!empty($data['ticket_type'])) {
            foreach ($data['ticket_type'] as $index => $ticketType) {
                $ticketData = [
                    'ticket_type' => $ticketType,
                    'price' => $data['price'][$index],
                    'quantity' => $data['quantity'][$index],
                    'available_quantity' => $data['quantity'][$index],
                ];

                if (!isset($data['ticket_id'][$index])) {
                    $ticketData['event_id'] = $event->id;
                    $this->ticketService->store($ticketData);
                }
            }
        }

        $this->clearEventListCache();
        $this->clearOrganizerEventCache(auth()->user()->id);

        return true;
    }

    /**
     * Handle my event count
     */
    public function myEventCount($userId)
    {
        return $this->event::where('organizer_id', $userId)->count();
    }

    /**
     * Handle my active event count
     */
    public function myActiveEventCount($userId)
    {
        return $this->event::where('organizer_id', $userId)->where('status', 'active')->count();
    }

    /**
     * Handle my active event count
     */
    public function myInActiveEventCount($userId)
    {
        return $this->event::where('organizer_id', $userId)->where('status', 'inactive')->count();
    }

    /**
     * Handle my revenue
     */
    public function myCancelledEventCount($userId)
    {
        return $this->event::where('organizer_id', $userId)->where('status', 'cancelled')->count();
    }
}
