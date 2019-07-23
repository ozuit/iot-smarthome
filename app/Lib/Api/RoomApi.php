<?php

namespace App\Lib\Api;

class RoomApi extends BaseApi
{
    protected $route = 'api_v1_room_find';

    protected function transform(Room $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}
