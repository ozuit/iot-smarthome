<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Lib\JWT\JWT;
use App\Model\Device;
use App\Model\User;
use App\Service\DeviceService;
use App\Service\UserService;
use Psr\Container\ContainerInterface;

class ApiMiddleware
{
    private $container;
    private $jwt;
    private $deviceService;
    private $userService;

    public function __construct(ContainerInterface $container, JWT $jwt, DeviceService $deviceService, UserService $userService)
    {
        $this->container = $container;
        $this->jwt = $jwt;
        $this->deviceService = $deviceService;
        $this->userService = $userService;
    }

    public function __invoke(Request $request)
    {
        if (env('IS_MAINTENANCE')) {
            return new JsonResponse(null, JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }
        if ($authorization = $request->headers->get('Authorization')) {
            $matchs = null;
            preg_match("/bearer ([^\ ]*)/i", $authorization, $matchs);
            $token = $matchs[1] ?? null;
        } else {
            $token = $request->query->get('token', null);
        }
        $info = $this->jwt->match($token);
        if (is_null($info) || !is_object($info)) {
            return $this->errorJson(401);
        }
        $device = $this->deviceService->where('token', $token)->first();
        if (!$device instanceof Device || !$device->user instanceof User || !$device->user->active) {
            return $this->errorJson(401);
        }
        if ($info->timeout < time()) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Token Expired',
            ], JsonResponse::HTTP_FORBIDDEN);
        }
        $request->attributes->set('__authed', $device->user);
        $request->attributes->set('__device', $device);
        $this->container->set('__authed', $device->user);
        $this->container->set('__device', $device);
    }

    protected function errorJson($statusCode = 400) : JsonResponse
    {
        return new JsonResponse([
            'status' => false,
            'message' => 'Hey, who are you ?',
        ], $statusCode);
    }
}
