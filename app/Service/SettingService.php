<?php

namespace App\Service;

use App\Lib\Api\SettingApi;

class SettingService extends ApiService
{
    protected $model_name = 'App\Model\Setting';

    public function getApiLib()
    {
        return $this->{SettingApi::class};
    }

}
