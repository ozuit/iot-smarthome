<?php

namespace App\Lib\Api;

use App\Model\Sensor;

class SensorApi extends BaseApi
{
    protected $route = 'api_v1_sensor_find';

    protected $include_variables = [
        'room',
    ];

    protected function transform(Sensor $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'room_id' => $model->room_id,
            'active' => $model->active,
        ];
    }

    protected function includeRoom(Sensor $sensor)
    {
        return $this->get(RoomApi::class)->render($sensor->room);
    }
}
