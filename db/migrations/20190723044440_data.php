<?php


use Phinx\Migration\AbstractMigration;

class Data extends AbstractMigration
{
    public function change()
    {
        $this->table('data', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
            ->addColumn('sensor_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('type', 'integer', ['null' => true, 'default' => null])
            ->addColumn('room_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('value', 'integer', ['null' => true, 'default' => null])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('sensor_id', 'sensor', 'id', ['delete' => 'SET NULL'])
            ->addForeignKey('room_id', 'room', 'id', ['delete' => 'SET NULL'])
            ->create();
    }
}
