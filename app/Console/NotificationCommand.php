<?php
namespace App\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Psr\Container\ContainerInterface;
use App\Service\ContractService;

class NotificationCommand
{
    protected $output;
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(OutputInterface $output)
    {
        $this->output = $output;
        $this->notificationContract();
    }
    
    protected function notificationContract()
    {
        $contract_service = $this->container->get(ContractService::class);
        $date = now()->addDay(10)->format('Y-m-d');
        $contracts = $contract_service->where('expired_alert', false)->where('end_date', '<', $date)->get();
        $pdo = $contract_service->getManager()->getConnection()->getPdo();
        $in_transaction = $pdo->inTransaction();
        if (!$in_transaction) {
            $pdo->beginTransaction();
        }
        foreach ($contracts as $contract) {
            $contract_service->alert($contract);
        }
        if (!$in_transaction) {
            $pdo->commit();
        }
    }
}
