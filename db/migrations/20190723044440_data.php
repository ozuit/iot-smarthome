<?php


use Phinx\Migration\AbstractMigration;

class Data extends AbstractMigration
{
    public function change()
    {
        $this->table('data', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
            ->addColumn('node_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('topic', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('value', 'float', ['null' => true, 'default' => null])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('node_id', 'node', 'id', ['delete' => 'SET NULL'])
            ->create();
    }
}
