<?php

use Phinx\Seed\AbstractSeed;

class TestDb extends AbstractSeed
{
    public function run()
    {
        $users = [
            [
                'email' => 'admin@gmail.com',
                'phone' => null,
                'password' => password_hash(1234, PASSWORD_BCRYPT),
                'name' => 'Admin',
                'active' => true,
                'is_admin' => true,
                'roles' => json_encode(['ADMIN']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->insert('user', $users);
    }
}
