<?php

namespace App\Http\Controller;

use App\Service\SensorService;

class Sensor extends Api
{
    protected function getService() : SensorService
    {
        return $this->get(SensorService::class);
    }

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'sensor'],
            'post' => ['create', 'sensor'],
            'put' => ['update', 'sensor'],
            'delete' => ['delete', 'sensor'],
        ];
    }
}
