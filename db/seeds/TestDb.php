<?php

use Phinx\Seed\AbstractSeed;

class TestDb extends AbstractSeed
{
    public function run()
    {
        $users = [
            [
                'email' => 'admin@gmail.com',
                'phone' => '0784090893',
                'password' => password_hash(1234, PASSWORD_BCRYPT),
                'name' => 'Tống Duy Tân',
                'active' => true,
                'is_admin' => true,
                'roles' => json_encode(['ADMIN']),
                'address' => '100/84 Lê Quang Định, P14, Q. Bình Thạnh, HCM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->insert('user', $users);
    }
}
