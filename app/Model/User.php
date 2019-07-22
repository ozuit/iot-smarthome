<?php

namespace App\Model;

class User extends Base
{
    protected $table = 'user';

    protected $fillable = [
        'name', 'email', 'password', 'active', 'roles', 'area_id',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'active' => 'boolean',
        'roles' => 'json',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'creator_id', 'id');
    }

    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }

    public function getRoles()
    {
        $roles = $this->roles ?: [];
        $admin_roles = $this->isAdmin() ? ['ADMIN'] : [];
        return array_unique(array_merge($roles, $admin_roles));
    }

    public function verify($password) : bool
    {
        return password_verify($password, $this->password);
    }

    public function isAdmin()
    {
        return boolval($this->is_admin);
    }

    public function hasRole(string $role_name)
    {
        return $this->isAdmin() || in_array($role_name, $this->roles);
    }
}
