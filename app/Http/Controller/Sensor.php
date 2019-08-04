<?php

namespace App\Http\Controller;

use App\Service\SensorService;
use Symfony\Component\HttpFoundation\Response;
use Bluerhinos\phpMQTT;

class Sensor extends Api
{
    protected $actions = [
        'update',
    ];

    protected function getService() : SensorService
    {
        return $this->get(SensorService::class);
    }

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'sensor'],
            'post' => ['create', 'sensor'],
            'put' => ['update', 'sensor'],
            'delete' => ['delete', 'sensor'],
        ];
    }

    protected function update(): Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        $topic = $this->getJsonData('topic', 'trash');
        $payload = $this->getJsonData('payload', '');

        if ($mqtt->connect()) {
            $mqtt->publish($topic, $payload);
            $mqtt->close();
            
            return $this->json([
                'status' => true,
            ]);
        } else {
            return $this->json([
                'status' => false,
                'message' => 'Time out!'
            ]);
        }
    }
}
