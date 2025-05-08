<?php

namespace App\Models;

use App\Services\Router\Types\Node;
use Illuminate\Database\Eloquent\Model;

class CourierRoute extends Model
{
    //
    protected $fillable = [
        "courier",
        "start_location",
        "current_location",
        "end_location"
    ];

    public function courier(){
        return $this->belongsTo(Employee::class, "courier");
    }

    public function startLocation() : Node|null {
        return Node::fromId($this->start_location);
    }

    public function currentLocation() : Node|null {
        return Node::fromId($this->current_location);
    }

    public function endLocation() : Node|null {
        return Node::fromId($this->end_location);
    }

}
