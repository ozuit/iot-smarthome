<?php

namespace App\Lib\Sender;

use Swift_Message;

interface Mailer
{
    public function send(Swift_Message $mail) : bool;
}