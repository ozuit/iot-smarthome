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

}
