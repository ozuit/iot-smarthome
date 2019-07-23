<?php

namespace App\Http\Controller;

use App\Service\DataService;

class Data extends Api
{
    protected function getService() : DataService
    {
        return $this->get(DataService::class);
    }

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'data'],
            'post' => ['create', 'data'],
            'put' => ['update', 'data'],
            'delete' => ['delete', 'data'],
        ];
    }
}
