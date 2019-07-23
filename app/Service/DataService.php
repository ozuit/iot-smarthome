<?php

namespace App\Service;

use App\Lib\Api\DataApi;

class DataService extends ApiService
{
    protected $model_name = 'App\Model\Data';

    public function getApiLib()
    {
        return $this->{DataApi::class};
    }

}
