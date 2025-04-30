<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    /**
     * The ticket this message belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}