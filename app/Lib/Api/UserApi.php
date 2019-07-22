<?php

namespace App\Lib\Api;

use App\Model\User;
use App\Service\UtilService;

class UserApi extends BaseApi
{
    protected $route = 'api_v1_user_find';

    protected function transform(User $model)
    {
        return [
            'id' => $model->id,
            'email' => $model->email,
            'phone' => $model->phone,
            'code' => $model->code,
            'name' => $model->name,
            'address' => $model->address,
            'note' => $model->note,
            'active' => $model->active,
            'is_admin' => $model->is_admin,
            'roles' => $model->getRoles(),
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => $this->dateFormat($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }
}
