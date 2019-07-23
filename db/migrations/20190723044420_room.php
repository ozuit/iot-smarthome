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
            ->create();
    }
}
