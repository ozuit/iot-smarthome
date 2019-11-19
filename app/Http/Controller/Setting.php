<?php

namespace App\Http\Controller;

use App\Service\SettingService;

class Setting extends Api
{
    protected function getService() : SettingService
    {
        return $this->get(SettingService::class);
    }

}
