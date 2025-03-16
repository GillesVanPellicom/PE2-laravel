<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageMovement extends Model {
  //
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    'package_id',
    'from_location_id',
    'to_location_id',
    'handled_by_courier_id',
    'vehicle_id',
    'departure_time',
    'arrival_time',
    'check_in_time',
    'check_out_time'
  ];

  protected $appends = ['hop_departed', 'hop_arrived'];


  public function package() {
    return $this->belongsTo(Package::class, 'package_id');
  }

  public function fromLocation() {
    return $this->belongsTo(Location::class, 'from_location_id');
  }

  public function toLocation() {
    return $this->belongsTo(Location::class, 'to_location_id');
  }

  public function getHopDepartedAttribute(): bool {
    return !is_null($this->departure_time);
  }

  public function getHopArrivedAttribute(): bool {
    return !is_null($this->arrival_time);
  }
}
