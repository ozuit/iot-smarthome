<?php

namespace App\Http\Controller;

use App\Service\RoomService;

class Room extends Api
{
    protected function getService() : RoomService
    {
        return $this->get(RoomService::class);
    }

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'room'],
            'post' => ['create', 'room'],
            'put' => ['update', 'room'],
            'delete' => ['delete', 'room'],
        ];
    }
}
