<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";

    protected $fillable = [
        'event_id',
        'ticket_type',
        'price',
        'quantity',
        'available_quantity',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
