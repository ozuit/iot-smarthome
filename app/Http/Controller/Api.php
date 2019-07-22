<?php

namespace App\Http\Controller;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Lib\Traits\AclTrait;

abstract class Api extends Base
{
    use AclTrait;

    protected $json_data;
    protected $query_data;
    protected $actions = [];
    protected $allow_export = false;
    private $authed;

    protected function getAuthed()
    {
        if ($this->authed === null) {
            $this->authed = $this->container->has('__authed') ? $this->get('__authed') : null;
        }
        return $this->authed;
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    protected function getAclData(): array
    {
        return [];
    }

    protected function hasPermision(string $key): bool
    {
        $acl_data = $this->getAclData();
        $current_data = array_values($acl_data[$key] ?? []);
        if ($current_data) {
            $user = $this->getAuthed();
            $roles = $user->roles;
            if (count($current_data) === 2) {
                $action = $current_data[0];
                $resource = $current_data[1];
    
                return $this->isAllow($resource, $action, $roles);
            }
        }
        return true;
    }

    public function api(): Response
    {
        $args = func_get_args();
        $method = strtolower($this->request->getMethod());
        if (!$this->hasPermision($method)) {
            return $this->error403();
        }
        $name = 'api'.ucfirst($method);
        if (!method_exists($this, $name)) {
            throw new \Exception(sprintf('Public/protected method [%s] is not exists in class [%s]', $name, static::class));
        }

        return call_user_func_array([$this, $name], $args);
    }

    public function __call($name, $arguments)
    {
        if (!in_array($name, $this->actions)) {
            throw new \Exception(sprintf("[%s] hasn't `%s` action!", get_class($this), $name));
        }
        if (!$this->hasPermision($name)) {
            return $this->error403();
        }
        return call_user_func_array([$this, $name], $arguments);
    }

    protected function error403(): JsonResponse
    {
        return $this->error("You don't have permission to access", 403);
    }

    protected function error(string $message, int $statusCode = 400): JsonResponse
    {
        return new JsonResponse([
            'status' => false,
            'message' => $message,
        ], $statusCode);
    }

    protected function isExportRequest()
    {
        return $this->request->query->get('_export', '0') === '1';
    }

    protected function getHeaderExport(): array
    {
        return [];
    }

    protected function getMapExport(): array
    {
        return [];
    }

    protected function apiGet() : Response
    {
        $service = $this->getService();
        $route_params = $this->request->attributes->get('_route_params');
        if (isset($route_params['id'])) {
            $id = intval($this->request->attributes->get('id'));
            $data = $service->getData($this->request, $id);
        } else {
            if ($this->isExportRequest()) {
                return $service->exportData($this->request);
            } else {
                $data = $service->getData($this->request);
            }
        }

        return $this->json($data);
    }

    protected function apiPost() : Response
    {
        $data = $this->getJsonData();
        $service = $this->getService();
        $model = $service->postData($this->request, $data);
        $data = $service->apiRender($model);
        $data['status'] = true;

        return $this->json($data);
    }

    protected function apiPut() : Response
    {
        $id = intval($this->request->attributes->get('id'));
        $data = $this->getJsonData();
        $service = $this->getService();
        $model = $service->putData($this->request, $data, $id);
        $data = $service->apiRender($model);
        $data['status'] = true;

        return $this->json($data);
    }

    protected function apiDelete() : Response
    {
        $id = intval($this->request->attributes->get('id'));
        $service = $this->getService();
        $bool = $service->deleteData($this->request, $id);
        
        return $this->json([
            'status' => $bool,
        ]);
    }

    abstract protected function getService();

    protected function getJsonData(string $key = null, $default = null)
    {
        if ($this->json_data === null) {
            $this->json_data = json_decode($this->request->getContent(), true) ?: [];
        }
        if (is_null($key)) {
            return $this->json_data;
        }
        return $this->json_data[$key] ?? $default;
    }

    protected function getQueryParam(string $key = null, $default = null)
    {
        if ($this->query_data === null) {
            $this->query_data = $this->request->query->all();
        }
        if (is_null($key)) {
            return $this->query_data;
        }
        return $this->query_data[$key] ?? $default;
    }
}
