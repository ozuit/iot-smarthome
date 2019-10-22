<?php

namespace App\Http\Controller;

use App\Service\DataService;
use App\Service\NodeService;
use Symfony\Component\HttpFoundation\Response;
use Bluerhinos\phpMQTT;

class Node extends Api
{
    protected $actions = [
        'update', 'turnOffAll', 'ifttt', 'sleepMode', 'movieMode', 'bookMode', 'agentget', 'agentput'
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

    protected function signature($payload)
    {
        $ts = time();
        $data = $ts . '|' . $payload;
        return md5($data . '|' . env('MQTT_SECRET_KEY')) . '|' . $data;
    }

    protected function ifttt($internal_token): Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        $topic = $this->getJsonData('topic', 'trash');
        $status = $this->getJsonData('status', '');
        $payload = $this->signature($status);
        
        if (isset($status) && $internal_token == env('INTERNAL_TOKEN')) {
            if ($mqtt->connect()) {
                $mqtt->publish($topic, $payload);
                $mqtt->close();

                $device = $this->getService()->where('topic', $topic)->first();
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
        ]);
    }

    protected function update($id): Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        $topic = $this->getJsonData('topic', 'trash');
        $payload = $this->getJsonData('payload', '0');
        $status = $this->getJsonData('status');

        if (isset($status)) {
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

        $mqtt = new phpMQTT($server, $port, $client_id);
        if ($mqtt->connect()) {
            $devices = $this->getService()->where('is_sensor', 0)->get();
            foreach($devices as $device) {
                $mqtt->publish($device->topic, $this->signature('0'));
                $device->active = 0;
                $device->save();
            }
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

    protected function sleepMode() : Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        if ($mqtt->connect()) {
            $devices = $this->getService()->where('is_sensor', 0)->get();
            foreach($devices as $device) {
                if ($device->topic == 'smarthome/bed-room/fan/device1') {
                    $mqtt->publish($device->topic, $this->signature('1'));
                    $device->active = 1;
                    $device->save();
                } else {
                    $mqtt->publish($device->topic, $this->signature('0'));
                    $device->active = 0;
                    $device->save();
                }
            }
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
    
    protected function movieMode() : Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        if ($mqtt->connect()) {
            $devices = $this->getService()->where('is_sensor', 0)->get();
            foreach($devices as $device) {
                if ($device->topic == 'smarthome/living-room/light/device2' || $device->topic == 'smarthome/living-room/fan/device1') {
                    $mqtt->publish($device->topic, $this->signature('1'));
                    $device->active = 1;
                    $device->save();
                } else {
                    $mqtt->publish($device->topic, $this->signature('0'));
                    $device->active = 0;
                    $device->save();
                }
            }
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
    
    protected function bookMode() : Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');

        $mqtt = new phpMQTT($server, $port, $client_id);
        if ($mqtt->connect()) {
            $devices = $this->getService()->where('is_sensor', 0)->get();
            foreach($devices as $device) {
                if ($device->topic == 'smarthome/living-room/light/device2' || $device->topic == 'smarthome/living-room/light/device1' || $device->topic == 'smarthome/living-room/fan/device1') {
                    $mqtt->publish($device->topic, $this->signature('1'));
                    $device->active = 1;
                    $device->save();
                } else {
                    $mqtt->publish($device->topic, $this->signature('0'));
                    $device->active = 0;
                    $device->save();
                }
            }
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

    protected function agentget($internal_token) : Response
    {
        $type = $this->getQueryParam('type', '');
        
        if (isset($type) && $internal_token == env('INTERNAL_TOKEN')) {
            if ($type == 'temp') {
                $sensor = $this->get(DataService::class)->where('topic', 'smarthome/bed-room/sensor/temp/sensor1')->orderBy('id', 'desc')->first();
                return $this->json([
                    'status' => true,
                    'value' => $sensor->value
                ]);
            } else if ($type == 'hum') {
                $sensor = $this->get(DataService::class)->where('topic', 'smarthome/bed-room/sensor/hum/sensor1')->orderBy('id', 'desc')->first();
                return $this->json([
                    'status' => true,
                    'value' => $sensor->value
                ]);
            }
        }

        return $this->json([
            'status' => false,
        ]);
    }

    protected function agentput($internal_token) : Response
    {
        $server = env('MQTT_SERVER');
        $port = env('MQTT_PORT');
        $client_id = env('MQTT_CLIENT_ID');
        $mqtt = new phpMQTT($server, $port, $client_id);

        $fulfillment = mb_strtolower($this->getJsonData('fulfillment'));
        $state = '';
        $devices = [];
        $rooms = [];

        if (isset($fulfillment) && $internal_token == env('INTERNAL_TOKEN')) {
            if (strpos($fulfillment, 'mở') !== false && strpos($fulfillment, 'tắt') === false) {
                $state = '1';
            } else if (strpos($fulfillment, 'mở') === false && strpos($fulfillment, 'tắt') !== false) {
                $state = '0';
            }
            
            if (strpos($fulfillment, 'đèn') !== false && strpos($fulfillment, 'quạt') === false) {
                $devices = ['light'];
            } else if (strpos($fulfillment, 'đèn') === false && strpos($fulfillment, 'quạt') !== false) {
                $devices = ['fan'];
            } else if ((strpos($fulfillment, 'đèn') === false && strpos($fulfillment, 'quạt') !== false) || strpos($fulfillment, 'tất cả thiết bị') !== false) {
                $devices = ['light', 'fan'];
            }

            if (strpos($fulfillment, 'phòng khách') !== false) {
                $rooms[] = 'living-room';
            }
            if (strpos($fulfillment, 'phòng ngủ') !== false) {
                $rooms[] = 'bed-room';
            }
            if (strpos($fulfillment, 'phòng tắm') !== false) {
                $rooms[] = 'bath-room';
            }
            if (strpos($fulfillment, 'nhà bếp') !== false) {
                $rooms[] = 'kitchen';
            }
            if (strpos($fulfillment, 'tất cả các phòng') !== false) {
                $rooms = ['kitchen', 'bath-room', 'bed-room', 'living-room'];
            }

            if ($state != '' && $mqtt->connect()) {
                foreach($rooms as $room) {
                    foreach($devices as $device) {
                        $topic = "smarthome/$room/$device/device1";
                        $mqtt->publish($topic, $this->signature($state));
                        $node = $this->getService()->where('topic', $topic)->first();
                        $node->active = $state;
                        $node->save();
                        if ($device == 'light' && $room == 'living-room') {
                            $topic = "smarthome/living-room/light/device2";
                            $mqtt->publish($topic, $this->signature($state));
                            $node = $this->getService()->where('topic', $topic)->first();
                            $node->active = $state;
                            $node->save();
                        }
                    }
                }
            }

            return $this->json([
                'status' => true,
            ]);
        }

        return $this->json([
            'status' => false,
        ]);
    }
}
