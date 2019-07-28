<?php


use Phinx\Migration\AbstractMigration;

class Room extends AbstractMigration
{
    public function change()
    {
        $this->table('room', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
            ->addColumn('name', 'string', ['null' => true, 'default' => null])
            ->addColumn('topic', 'string', ['null' => true, 'default' => null])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => null])
            ->addIndex('created_at')
            ->addIndex('topic', ['unique' => true])
            ->create();
    }
}
