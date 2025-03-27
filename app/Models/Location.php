<?php

namespace App\Models;

use App\Services\Router\Types\NodeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Location find(mixed $id)
 */
class Location extends Model
{
  use HasFactory;

  protected $casts = [
    'location_type' => NodeType::class,
  ];

  protected $fillable = [
    'infrastructure_id',
    'description',
    'location_type',
    'addresses_id',
    'contact_number',
    'opening_hours',
    'is_active',
    'latitude',
    'longitude',
  ];

  public function address() {
    return $this->belongsTo(Address::class, 'addresses_id');
  }

  public function parcels()
  {
    return $this->hasMany(Package::class, 'destination_location_id');
  }

  public function packages()
  {
    return $this->hasMany(Package::class, 'origin_location_id');
  }
}