<?php

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Service\DataService;
use App\Model\Node;

class Data extends Api
{
    protected function getService() : DataService
    {
        return $this->get(DataService::class);
    }

    protected $actions = [
        'temp', 'hum',
    ];

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'data'],
            'post' => ['create', 'data'],
            'put' => ['update', 'data'],
            'delete' => ['delete', 'data'],
        ];
    }

    protected function temp(): Response
    {
        $temps = $this->getService()->where('node_id', Node::TEMP)->orderBy('created_at', 'desc')->limit(30)->pluck('value')->all();
        return $this->json([
            'status' => true,
            'data' => $temps,
        ]);
    }
    
    protected function hum(): Response
    {
        $hums = $this->getService()->where('node_id', Node::HUM)->orderBy('created_at', 'desc')->limit(30)->pluck('value')->all();
        return $this->json([
            'status' => true,
            'data' => $hums,
        ]);
    }
}
