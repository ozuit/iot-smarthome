<?php


use Phinx\Migration\AbstractMigration;

class AlterSensorTable extends AbstractMigration
{
    public function change()
    {
        $this->table('sensor', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
        ->addColumn('is_sensor', 'boolean', ['null' => false, 'default' => false, 'after' => 'topic'])
        ->save();
    }
}
