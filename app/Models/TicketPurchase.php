<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPurchase extends Model
{
    use HasFactory;
    protected $table = 'ticket_purchases';

    protected $fillable = [
        'ticket_UID',
        'group_id',
        'event_id',
        'user_id',
        'ticket_id',
        'quantity',
        'total_price',
        'payment_id',
        'transaction_status',
    ];

    public function ticket(): BelongsTo        {
        return $this->belongsTo(Ticket::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
