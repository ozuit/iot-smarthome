<?php

namespace App\Lib\Acl;

use Symfony\Component\Yaml\Yaml;

class AclProcessor
{
    private $data;
    private $acl;
    private $log;

    public function __construct(string $yml_path)
    {
        $this->log = [];
        $this->data = Yaml::parseFile($yml_path);
    }

    private function init()
    {
        $this->acl = new Acl();
        $this->processData();
    }

    private function addResource($resource, $parent = null)
    {
        if ($resource) {
            foreach ($resource as $key => $value) {
                $this->acl->addResource($key, $parent);
                $this->log[] = sprintf("addResource(%s, %s);", $key, $this->logFormat($parent));
                $this->addResource($value, $key);
            }
        }
    }

    private function addRoles(array $roles)
    {
        if ($roles) {
            foreach ($roles as $role => $parents) {
                $this->acl->addRole($role, $parents);
                $this->log[] = sprintf("addRole(%s, %s);", $role, $this->logFormat($parents));
            }
        }
    }

    private function addRules(array $rules)
    {
        if ($rules) {
            foreach ($rules as $rule) {
                $action = array_shift($rule);
                call_user_func_array([$this->acl, $action], $rule);
                $this->log[] = sprintf("%s(%s);", $action, implode(", ", array_map([$this, 'logFormat'], $rule)));
            }
        }
    }

    private function processData()
    {
        $resources = $this->data['resources'] ?? [];
        $roles = $this->data['roles'] ?? [];
        $rules = $this->data['rules'] ?? [];

        $this->addResource($resources);
        $this->addRoles($roles);
        $this->addRules($rules);
    }

    private function logFormat($value)
    {
        return $value == null ? 'null' : is_array($value) ? json_encode($value) : $value;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function getAcl()
    {
        if (!$this->acl) {
            $this->init();
        }
        return $this->acl;
    }
}
