<?php

namespace App\Service;

use App\Lib\Data\Result;
use App\Model\User;
use App\Lib\Api\UserApi;

class UserService extends ApiService
{
    protected $model_name = 'App\Model\User';

    public function getApiLib()
    {
        return $this->{UserApi::class};
    }

    public function auth($email, $password, $device_id)
    {
        $result = new Result([
            'status' => false,
        ]);
        
        if ($email && $password && $device_id) {
            $user = $this->where('email', $email)->orWhere('phone', $email)->first();
            if ($user instanceof User && $user->active) {
                $check_code = is_null($user->password) && $password == $user->code;
                if ($check_code || $user->verify($password)) {
                    $deviceService = $this->{DeviceService::class};
                    $device = $deviceService->saveDevice($user, $device_id);
                    $result->set('status', true);
                    $result->set('user', $user);
                    $result->set('token', $device->token);
                    $result->set('first_login', $check_code);
                }
            }
        }

        return $result;
    }

    public function randomPassword($length = 8) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
