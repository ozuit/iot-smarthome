<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use App\Exception\ModelNotFoundException;
use App\Model\Base as BaseModel;

abstract class Base
{
    protected $model_name;

    protected $container;

    protected $db;

    public function __construct(ContainerInterface $container, Manager $db)
    {
        if ($this->model_name) {
            if (!class_exists($this->model_name)) {
                throw new ModelNotFoundException(sprintf('Model [%s] not found!', $this->model_name));
            }
        }

        $this->container = $container;
        $this->db = $db;
    }

    public function getManager(): Manager
    {
        return $this->db;
    }

    public function createNew(array $data = []) :? BaseModel
    {
        $model_name = $this->model_name;
        $model = new $model_name();
        if ($model instanceof BaseModel) {
            $model->fill($data);
    
            return $model;
        }
        return null;
    }

    public function __get(string $key)
    {
        return $this->container->get($key);
    }

    public function __call($name, $arguments)
    {
        if ($this->model_name) {
            return call_user_func_array([$this->model_name, $name], $arguments);
        }

        return call_user_func_array([$this->db->getDatabaseManager()->connection(), $name], $arguments);
    }
}