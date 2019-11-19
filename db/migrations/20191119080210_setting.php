<?php


use Phinx\Migration\AbstractMigration;

class Setting extends AbstractMigration
{
    public function change()
    {
        $this->table('setting', [
            'collation' => 'utf8mb4_unicode_ci',
        ])
            ->addColumn('active_fan_sensor', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('limit_fan_sensor', 'float', ['null' => true, 'default' => null])
            ->addColumn('active_motion_detection', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('active_gas_warning', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('created_at', 'timestamp', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'default' => null])
            ->create();
    }
}
