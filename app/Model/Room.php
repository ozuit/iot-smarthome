<?php

namespace App\Model;

class Room extends Base
{
    protected $table = 'room';

    protected $fillable = [
        'name',
    ];

    public function sensors()
    {
        return $this->hasMany(Sensor::class, 'room_id', 'id');
    }
}
