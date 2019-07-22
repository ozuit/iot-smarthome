<?php

namespace App\Lib\JWT;

use Firebase\JWT\JWT as BaseJWT;
use Exception;

class JWT
{
    static protected $instance;

    protected static function getInstance() : JWT
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function getPrivateKey() : string
    {
        return env('INTERNAL_TOKEN', md5('SECRET_KEY'));
    }

    public function encode($user) : string
    {
        $token = [
            'roles' => $user->getRoles(),
            'email' => $user->email,
            'user_id' => $user->id,
            'timeout' => time() + (60 * 60 * 24 * 30),// 30 days
        ];

        return BaseJWT::encode($token, $this->getPrivateKey());
    }

    public function match($jwt)
    {
        try {
            return BaseJWT::decode($jwt, $this->getPrivateKey(), ['HS256']);
        } catch (Exception $e) {
            return null;
        }
    }
}
