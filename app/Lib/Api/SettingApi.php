<?php

namespace App\Lib\Api;

use App\Model\Setting;

class SettingApi extends BaseApi
{
    protected $route = 'api_v1_setting_find';

    protected function transform(Setting $model)
    {
        return [
            'active_fan_sensor' => $model->active_fan_sensor,
            'limit_fan_sensor' => $model->limit_fan_sensor,
            'active_motion_detection' => $model->active_motion_detection,
            'active_gas_warning' => $model->active_gas_warning,
        ];
    }
}
