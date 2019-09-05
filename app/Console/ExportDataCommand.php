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
        })->skip(1000)->take(7395)->get();
        $records = [];
        foreach($data as $item) {
            $records[] = [
                $item->created_at->format('Hi'),
                $item->node_id,
                strval($item->value),
            ];
        }
        // array_unshift($records, ['time','node_id','value']);
        $filepath = ROOT_PATH."/public/exports/data.xlsx";
        $export = $container->get(Excel::class)->export($records);
        $export->save($filepath);
        $output->writeln("Export success!");
    }
}
