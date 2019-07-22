<?php

namespace App\Model;

class Device extends Base
{
    protected $table = 'device';

    protected $fillable = [
        'device_id', 'user_id', 'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
