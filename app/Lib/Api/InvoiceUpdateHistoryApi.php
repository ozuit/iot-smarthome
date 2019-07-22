<?php

namespace App\Lib\Api;

use App\Model\InvoiceUpdateHistory;
use App\Service\InvoiceTypeService;
use App\Service\UserService;

class InvoiceUpdateHistoryApi extends BaseApi
{
    // protected $include_variables = [
    //     'user',
    // ];

    protected function transform(InvoiceUpdateHistory $model)
    {
        return [
            'id' => $model->id,
            'field' => $model->field,
            'old' => $this->getValue($model, true),
            'new' => $this->getValue($model, false),
            'author' => $model->user ? $model->user->name : '',
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => $this->dateFormat($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }

    protected function getValue($model, $isOld)
    {
        $fn = 'get_'.$model->field;
        if (method_exists($this, 'get_'.$model->field)) {
            return $isOld ? $this->$fn($model->old, null) : $this->$fn(null, $model->new);
        }
        return $isOld ? $model->old : $model->new;
    }
    
    protected function get_invoice_type_id($old = null, $new = null)
    {
        $invoice_type = $this->get(InvoiceTypeService::class)->find($old ?? $new);
        return $invoice_type->name ?? '';
    }
    
    protected function get_creator_id($old = null, $new = null)
    {
        $creator = $this->get(UserService::class)->find($old ?? $new);
        return $creator->name ?? '';
    }
    
    protected function get_status($old = null, $new = null)
    {
        $status = [
            'CHUATHANHTOAN' => 'Chưa thanh toán',
            'DATHANHTOAN' => 'Đã thanh toán',
            'HUY' => 'Huỷ'
        ];
        return $status[$old ?? $new] ?? '';
    }
    
    protected function get_checkout_method($old = null, $new = null)
    {
        $checkout_method = [
            'CHUYENKHOANCANHAN' => 'CK cá nhân',
            'CHUYENKHOANCTY' => 'CK công ty',
            'TIENMAT' => 'Tiền mặt',
            'QUETTHE' => 'Quẹt thẻ',
        ];
        return $checkout_method[$old ?? $new] ?? '';
    }

    // protected function includeUser(Invoice $invoice)
    // {
    //     return $this->get(InvoiceUpdateHistoryApi::class)->render($invoice->update_histories);
    // }
}
