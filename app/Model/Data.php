<?php

namespace App\Model;

class Data extends Base
{
    protected $table = 'data';

    protected $fillable = [
        'sensor_id', 'room_id', 'type', 'value',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
    
    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'sensor_id', 'id');
    }
}
