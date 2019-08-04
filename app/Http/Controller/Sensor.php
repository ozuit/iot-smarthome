<?php

namespace App\Http\Controller;

use App\Service\SensorService;
use Symfony\Component\HttpFoundation\Response;
use Bluerhinos\phpMQTT;

class Sensor extends Api
{
    protected $actions = [
        'update', 'turnOffAll',
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

    protected function update($id): Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        $topic = $this->getJsonData('topic', 'trash');
        $payload = $this->getJsonData('payload', '');
        $status = $this->getJsonData('status', '');

        if ($status != '') {
            if ($mqtt->connect()) {
                $mqtt->publish($topic, $payload);
                $mqtt->close();

                $device = $this->getService()->find($id);
                $device->active = $status;
                $device->save();
                
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

        return $this->json([
            'status' => false,
            'message' => 'New status is missing!'
        ]);
    }

    protected function turnOffAll() : Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');
        $topic = $this->getJsonData('topic', 'trash');
        $payload = $this->getJsonData('payload', '');

        $mqtt = new phpMQTT($server, $port, $client_id);
        if ($mqtt->connect()) {
            $mqtt->publish($topic, $payload);
            $mqtt->close();

            $this->getService()->where('is_sensor', 0)->update([
                'active' => 0
            ]);

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
