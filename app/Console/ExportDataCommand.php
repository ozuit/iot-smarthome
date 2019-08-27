<?php
namespace App\Console;

use App\Lib\Office\Excel;
use App\Service\DataService;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Container\ContainerInterface;

class ExportDataCommand
{
    public function __invoke(ContainerInterface $container, OutputInterface $output)
    {
        $data = $container->get(DataService::class)->whereHas('node', function($query) {
            $query->where('is_sensor', 0);
        })->get();
        $records = [];
        foreach($data as $item) {
            $records[] = [
                $item->created_at->format('H:i:s'),
                $item->node_id,
                strval($item->value),
            ];
        }
        $filepath = ROOT_PATH."/public/exports/data.xlsx";
        $export = $container->get(Excel::class)->export($records);
        $export->save($filepath);
        $output->writeln("Export success!");
    }
}
