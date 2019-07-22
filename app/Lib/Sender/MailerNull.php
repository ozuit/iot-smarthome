<?php

namespace App\Lib\Sender;

use Swift_Message;

class MailerNull implements Mailer
{
    protected $logger;

    public function __construct(SenderLogger $logger)
    {
        $this->logger = $logger;
    }

    public function send(Swift_Message $email) : bool
    {
        $froms = array_keys($email->getFrom());
        $tos = array_keys($email->getTo());
        $this->logger->info('Mailer', sprintf('Sent a email from %s to %s', $froms[0] ?? '', $tos[0] ?? ''));
        return true;
    }
}
