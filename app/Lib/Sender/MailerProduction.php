<?php

namespace App\Lib\Sender;

use Swift_Mailer;
use Swift_Message;
use Exception;

class MailerProduction implements Mailer
{
    protected $mailer;

    protected $logger;

    public function __construct(Swift_Mailer $mailer, SenderLogger $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function send(Swift_Message $email) : bool
    {
        try {
            $sent = $this->mailer->send($email);
            if ($sent > 0) {
                return true;
            }
            $this->logger->error('Mailer', new Exception("No mail sent!"));
            return false;
        } catch (Exception $e) {
            $this->logger->error('Mailer', $e);
            return false;
        }
    }
}
