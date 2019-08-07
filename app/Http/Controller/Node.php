<?php

namespace App\Http\Controller;

use App\Service\NodeService;
use Symfony\Component\HttpFoundation\Response;
use Bluerhinos\phpMQTT;

class Node extends Api
{
    protected $actions = [
        'update', 'turnOffAll', 'ifttt',
    ];

    protected function getService() : NodeService
    {
        return $this->get(NodeService::class);
    }

    protected function getAclData(): array
    {
        return [
            'get' => ['read', 'node'],
            'post' => ['create', 'node'],
            'put' => ['update', 'node'],
            'delete' => ['delete', 'node'],
        ];
    }

    protected function ifttt(): Response
    {
        $status = $this->getJsonData('status');
        $topic = $this->getJsonData('topic');
        if (isset($status)) {
            $device = $this->getService()->where('topic', $topic)->first();
            $device->active = $status;
            $device->save();
            return $this->json([
                'status' => true,
            ]);
        }
        return $this->json([
            'status' => false,
        ]);
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
