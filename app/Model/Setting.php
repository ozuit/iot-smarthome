<?php

namespace App\Model;

class Setting extends Base
{
    protected $table = 'setting';

    protected $fillable = [
        'active_fan_sensor', 'limit_fan_sensor', 'active_motion_detection', 'active_gas_warning',
    ];
}
