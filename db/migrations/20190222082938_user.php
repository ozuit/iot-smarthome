<?php

use Phinx\Migration\AbstractMigration;

class User extends AbstractMigration
{
    public function change()
    {
        $this->table('user', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
            ->addColumn('email', 'string', ['null' => true, 'default' => null])
            ->addColumn('phone', 'string', ['limit' => 50, 'null' => true, 'default' => null])
            ->addColumn('password', 'string', ['limit' => 60, 'null' => true, 'default' => null])
            ->addColumn('code', 'string', ['limit' => 60, 'null' => true, 'default' => null])
            ->addColumn('name', 'string', ['null' => true, 'default' => null])
            ->addColumn('active', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('is_admin', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('roles', 'json', ['null' => true, 'default' => null])
            ->addColumn('address', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('note', 'text', ['null' => true, 'default' => null])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => null])
            ->addIndex('email')
            ->addIndex('phone')
            ->addIndex('active')
            ->addIndex('created_at')
            ->create();
    }
}
