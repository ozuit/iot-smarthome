<?php

namespace App\Service;

use App\Lib\JWT\JWT;
use App\Model\User;
use App\Model\Device;

class DeviceService extends Base
{
    protected $model_name = 'App\Model\Device';

    public function saveDevice(User $user, string $device_id) : Device
    {
        $device = $this->firstOrNew([
            'device_id' => $device_id,
            'user_id' => $user->getKey(),
        ]);
        $device->token = $this->genarateToken($user);
        $device->save();

        return $device;
    }

    public function refreshToken(Device $device)
    {
        $user = $device->user;
        if ($user instanceof User) {
            $device->token = $this->genarateToken($user);
            $device->save();

            return $device;
        }

        return null;
    }
    
    public function genarateToken(User $user) : string
    {
        return $this->{JWT::class}->encode($user);
    }
}
