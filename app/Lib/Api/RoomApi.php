<?php

namespace App\Lib\Api;

use App\Model\Room;

class RoomApi extends BaseApi
{
    protected $route = 'api_v1_room_find';

    protected function transform(Room $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'topic' => $model->topic,
            'number' => $model->sensors()->count(),
        ];
    }
}
