<?php

namespace App\Lib\Sender;

use Exception;
use DateTime;

class SenderLogger
{
    protected $path;

    protected $logger;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function info(string $type, string $message)
    {
        $message = sprintf('Sender.%s "%s"', $type, $message);
        $this->write($message);
    }

    public function error(string $type, Exception $e)
    {
        $message = sprintf('Sender.%s "%s, called in %s on line %s"', $type, $e->getMessage(), $e->getFile(), $e->getLine());
        $this->write($message);
    }

    public function write(string $message)
    {
        $datetime = new DateTime();
        $message = sprintf("[%s] %s", $datetime->format('Y-m-d H:i:s'), $message);
        $logger = $this->getLoger();
        fwrite($logger, $message."\n");
    }

    protected function getLoger()
    {
        if ($this->logger === null) {
            $path = $this->path;
            if (!is_readable($path)) {
                touch($path);
            }
            $this->logger = fopen($path, 'a');
        }
        return $this->logger;
    }
}
