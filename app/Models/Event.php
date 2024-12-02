<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'organizer_id',
        'city_id',
        'title',
        'description',
        'event_date_time',
        'event_duration',
        'image',
        'location',
        'status',
        'ticket_price_start_from'
    ];

    protected $casts = [
        'event_date_time' => 'datetime',
    ];

    public function getFormattedEventDateAttribute()
    {
        return $this->event_date_time->format('M d l');
    }

    public function getFormattedEventDurationAttribute()
    {
        $duration = \Carbon\CarbonInterval::createFromFormat('H:i:s', $this->event_duration);

        $startTime = $this->event_date_time;
        $endTime = $startTime->copy()->add($duration);

        return $startTime->format('h:i A') . ' - ' . $endTime->format('h:i A');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function totalAvailableTicket(): string
    {
        return $this->hasMany(Ticket::class)->sum('available_quantity');
    }

    public function totalBookedTicket(): string
    {
        return $this->hasMany(TicketPurchase::class)->where('transaction_status', 'success')->sum('quantity');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(TicketPurchase::class)->where('transaction_status', 'success');
    }
}

