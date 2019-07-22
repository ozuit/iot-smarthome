<?php

namespace App\Lib\Acl;

use Zend\Permissions\Acl\Acl as A;
use Exception;

class Acl extends A
{
    public function getPrivilegesForAllRole($roles = null)
    {
        if (!is_array($roles)) {
            $roles = $this->getRoles();
        }
        $result = [];
        $all_actions = [];
        $rules = $this->rules;
        $byResourceId = $rules['byResourceId'];
        foreach ($byResourceId as $resource => $dataRole) {
            $dr = $dataRole['byRoleId'] ?? [];
            foreach ($dr as $role => $dp) {
                $byPrivilegeId = $dp['byPrivilegeId'];
                $all_actions[$resource] = array_merge($all_actions[$resource] ?? [], array_keys($byPrivilegeId));
            }
        }
        $all_actions = array_map('array_unique', $all_actions);
        foreach ($all_actions as $resource => $actions) {
            $result[$resource]['__RESOURCE__'] = [$resource];
            foreach ($roles as $role) {
                foreach ($actions as $action) {
                    try {
                        if ($this->isAllowed($role, $resource, $action)) {
                            $result[$resource][$role][] = $action;
                        }
                    } catch (Exception $e) {
                    }
                }
                if (!isset($result[$resource][$role])) {
                    $result[$resource][$role] = [];
                }
            }
        }
        return [
            'keys' => array_merge(['__RESOURCE__'], $roles),
            'result' => array_values($result),
        ];
    }
}
