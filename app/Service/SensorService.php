<?php

namespace App\Service;

use App\Lib\Api\SensorApi;

class SensorService extends ApiService
{
    protected $model_name = 'App\Model\Sensor';

    public function getApiLib()
    {
        return $this->{SensorApi::class};
    }

    protected function getFilterableFields() : array
    {
        return [
            'room_id', 'is_sensor',
        ];
    }

    protected function mapFilters() : array
    {
        return [
            'name' => function ($field, $value) {
                return [$field, 'like', "%$value%"];
            },
        ];
    }

}
