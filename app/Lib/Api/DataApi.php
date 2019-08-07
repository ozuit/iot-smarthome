<?php

namespace App\Lib\Api;

use App\Model\Data;

class DataApi extends BaseApi
{
    protected $route = 'api_v1_data_find';

    protected function transform(Data $model)
    {
        return [
            'id' => $model->id,
            'node_id' => $model->node_id,
            'room_id' => $model->room_id,
            'value' => $model->value,
            'type' => $model->type,
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
        ];
    }
}
