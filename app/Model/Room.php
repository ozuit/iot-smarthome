<?php

namespace App\Model;

class Room extends Base
{
    protected $table = 'room';

    const LIVINGROOM = 1;
    const BEDROOM = 2;
    const BATHROOM = 3;
    const KITCHEN = 4;

    protected $fillable = [
        'name', 'topic',
    ];

    public function nodes()
    {
        return $this->hasMany(Node::class, 'room_id', 'id');
    }
}
