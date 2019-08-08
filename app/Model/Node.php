<?php

namespace App\Model;

class Node extends Base
{
    protected $table = 'node';

    const HUM = 1;
    const TEMP = 2;

    protected $fillable = [
        'name', 'room_id', 'active', 'topic', 'is_sensor',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
