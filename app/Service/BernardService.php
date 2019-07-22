<?php

namespace App\Service;

use Bernard\Message\DefaultMessage;
use Bernard\Producer;

class BernardService
{
    protected $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function sendMessage(string $to, string $cmd, array $params = [])
    {
        $params['cmd'] = $cmd;
        $message = new DefaultMessage(ucfirst($to), $params);
        $this->producer->produce($message, $to);
    }
}
