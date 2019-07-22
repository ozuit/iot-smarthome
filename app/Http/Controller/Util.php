<?php

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Service\UtilService;

class Util extends Api
{
    protected $actions = [
        'area',
    ];

    protected function getService() : UtilService
    {
        return $this->get(UtilService::class);
    }

    protected function area(): Response
    {
        $service = $this->getService();

        return $this->json($service->getArea());
    }
}
