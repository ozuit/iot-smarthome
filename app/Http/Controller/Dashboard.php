<?php
namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Service\NodeService;
use App\Service\UserService;

class Dashboard extends Api
{
    protected function getService() : NodeService
    {
        return $this->get(NodeService::class);
    }

    protected $actions = [
        'data',
    ];

    protected function data()
    {
        $devices = $this->getService()->selectRaw('room_id, count(room_id) as number')->groupBy('room_id')->get();
        $result = [];
        foreach($devices as $device) {
            $result[] = [
                'label' => $device->room->name . ' NODES',
                'value' => $device->number
            ];
        }
        $result[] = [
            'label' => 'Members',
            'value' => $this->get(UserService::class)->count()
        ];
        return $this->json([
            'data' => $result
        ]);
    }
}
