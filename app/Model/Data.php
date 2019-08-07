<?php

namespace App\Model;

class Data extends Base
{
    protected $table = 'data';

    protected $fillable = [
        'node_id', 'room_id', 'type', 'value',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
    
    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id', 'id');
    }
}
