<?php

use Phinx\Migration\AbstractMigration;

class Device extends AbstractMigration
{
    public function change()
    {
        $this->table('device', [
            'collation' => 'utf8_unicode_ci',
        ])
            ->addColumn('user_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('device_id', 'string', ['null' => true, 'default' => null])
            ->addColumn('token', 'string', ['null' => true, 'default' => null])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => null])
            ->addForeignKey('user_id', 'user', 'id', ['delete' => 'SET NULL'])
            ->addIndex('device_id')
            ->addIndex('created_at')
            ->create();
    }
}
