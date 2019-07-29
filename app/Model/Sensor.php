<?php

namespace App\Model;

class Sensor extends Base
{
    protected $table = 'sensor';

    const HUM = 1;
    const TEMP = 2;

    protected $fillable = [
        'name', 'room_id', 'active', 'topic',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
