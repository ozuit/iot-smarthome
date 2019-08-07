<?php


use Phinx\Migration\AbstractMigration;

class Sensor extends AbstractMigration
{
    public function change()
    {
        $this->table('node', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
            ->addColumn('name', 'string', ['null' => true, 'default' => null])
            ->addColumn('active', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('room_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('topic', 'string', ['null' => true, 'default' => null])
            ->addColumn('is_sensor', 'boolean', ['null' => false, 'default' => false, 'after' => 'topic'])
            ->addForeignKey('room_id', 'room', 'id', ['delete' => 'SET NULL'])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => null])
            ->addIndex('created_at')
            ->addIndex('topic', ['unique' => true])
            ->create();
    }
}
