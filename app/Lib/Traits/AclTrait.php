<?php

namespace App\Lib\Traits;

use Psr\Container\ContainerInterface;
use Zend\Permissions\Acl\Acl;

trait AclTrait
{
    protected $acl_instance;

    protected $role_list;

    protected function isAllow($resourse, $action, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->getAcl()->isAllowed($role, $resourse, $action)) {
                return true;
            }
        }

        return false;
    }

    private function getAcl(): Acl
    {
        if (!$this->acl_instance) {
            $acl_processor = $this->getContainer()->get('acl');
            $this->acl_instance = $acl_processor->getAcl();
        }
        return $this->acl_instance;
    }

    protected abstract function getContainer(): ContainerInterface;
    protected abstract function getAclData(): array;
}
