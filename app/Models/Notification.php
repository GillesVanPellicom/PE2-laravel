<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message_template_id', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean', // Automatically cast is_read to a boolean
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messageTemplate()
    {
        return $this->belongsTo(MessageTemplate::class);
    }

    public function getFormattedMessageAttribute()
    {
        $message = $this->messageTemplate->message ?? '';
        $data = json_decode($this->data, true);

        foreach ($data as $key => $value) {
            $message = str_replace("{" . $key . "}", $value, $message);
        }

        return $message;
    }
}


