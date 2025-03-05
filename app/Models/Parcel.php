<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcel extends Model
{
    protected $fillable = [
        'reference',
        'country_code',
        'delivery_method_id',
        'weight_class_id',
        'destination_location_id',
        'delivery_price',
        'weight_price',
        'total_price',
        // Receiver details
        'firstname',
        'lastname',
        'company',
        'email',
        'phone',
        'address_id',
        // Sender details
        'sender_firstname',
        'sender_lastname',
        'sender_email',
        'sender_phone',
        'sender_address_id',
    ];

    public function deliveryMethod(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class);
    }

    public function weightClass(): BelongsTo
    {
        return $this->belongsTo(WeightClass::class);
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }
} 