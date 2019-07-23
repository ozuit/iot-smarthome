<?php

namespace App\Service;

use App\Lib\Api\RoomApi;

class RoomService extends ApiService
{
    protected $model_name = 'App\Model\Room';

    public function getApiLib()
    {
        return $this->{RoomApi::class};
    }

}
