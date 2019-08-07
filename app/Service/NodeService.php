<?php

namespace App\Service;

use App\Lib\Api\NodeApi;

class NodeService extends ApiService
{
    protected $model_name = 'App\Model\Node';

    public function getApiLib()
    {
        return $this->{NodeApi::class};
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
