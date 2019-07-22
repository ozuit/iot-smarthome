<?php

namespace App\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Bernard\Consumer;
use Bernard\QueueFactory;

class ConsumeCommand
{
    protected $consumer;
    protected $queues;

    public function __construct(Consumer $consumer, QueueFactory $queues)
    {
        $this->consumer = $consumer;
        $this->queues = $queues;
    }

    public function __invoke($queue, OutputInterface $output)
    {
        $queue = $this->queues->create($queue);

        $this->consumer->consume($queue);
    }
}
