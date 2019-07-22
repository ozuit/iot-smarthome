<?php

namespace App\Lib\Api;

use App\Model\InvoiceType;
use App\Service\UserService;

class InvoiceTypeApi extends BaseApi
{
    protected $route = 'api_v1_invoice_type_find';

    protected $include_variables = [
        'usersThu', 'usersChi',
    ];

    protected function transform(InvoiceType $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'thu_user_ids' => $model->thu_user_ids,
            'chi_user_ids' => $model->chi_user_ids,
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => $this->dateFormat($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }

    protected function includeUsersThu(InvoiceType $invoice_type)
    {
        $thu_user_ids = $invoice_type->thu_user_ids;
        if ($thu_user_ids && is_array($thu_user_ids)) {
            $users = $this->get(UserService::class)->whereIn('id', $thu_user_ids)->get();

            return $users->count() > 0 ? $this->get(UserApi::class)->render($users) : null;
        }

        return null;
    }
    
    protected function includeUsersChi(InvoiceType $invoice_type)
    {
        $chi_user_ids = $invoice_type->chi_user_ids;
        if ($chi_user_ids && is_array($chi_user_ids)) {
            $users = $this->get(UserService::class)->whereIn('id', $chi_user_ids)->get();

            return $users->count() > 0 ? $this->get(UserApi::class)->render($users) : null;
        }

        return null;
    }
}
