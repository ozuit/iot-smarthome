<?php
namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Service\SensorService;
use App\Service\UserService;

class Dashboard extends Api
{
    protected function getService() : SensorService
    {
        return $this->get(SensorService::class);
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
                'label' => $device->room->name . ' DEVICES',
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
