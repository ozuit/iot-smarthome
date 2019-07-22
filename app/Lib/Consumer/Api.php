<?php

namespace App\Lib\Consumer;

use Exception;
use PDOException;
use DI\Container;
use App\Service\CourseService;
use Swift_Message;

class Api
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function api($message) {
        echo "{$message->cmd}\n";
        try {
            if ($message->cmd === 'queued_emails') {
                $this->queued_emails($message);
            }
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
        } catch (PDOException $e) {
            echo $e->getMessage()."\n";
        }
    }

    protected function queued_emails($message)
    {
        /**
         * Cầu trúc
         * $cmd : 'queued_emails'
         * $message
         *  - from      : 'test@mail.com'
         *  - from      : 'test@mail.com|Test Name'
         *  - to        : 'test2@mail.com'
         *  - to        : 'test2@mail.com|Test 2 Name'
         *  - to        : ['test2@mail.com|Test 2 Name', 'test3@mail.com|Test 3 Name']
         *  - subject   : 'test subject'
         *  - body      : '<h4>test body</h4>'
         * $messages : [$message, ...]
         */
        $sender = $this->container->get('pn.mailer');
        $messages = isset($message->messages) ? $message->messages : (isset($message->message) ? [$message->message] : []);
        $count = 0;
        $ok = [];
        foreach ($messages as $message) {
            $from = array_filter(explode('|', $message['from'] ?? ''));
            if (isset($from[0]) && strpos($from[0], '@') > 0) {
                $to_raw = $message['to'] ?? '';
                if (is_array($to_raw)) {
                    foreach ($to_raw as $k => $v) {
                        $to_raw[$k] = array_filter(explode('|', trim($v)));
                    }
                    $tos = $to_raw;
                } else {
                    $tos = [array_filter(explode('|', trim($message['to'] ?? '')))];
                }
                foreach ($tos as $to) {
                    if (isset($to[0]) && strpos($to[0], '@') > 0) {
                        $email = new Swift_Message();
                        $email->setSubject($message['subject'] ?? 'empty subject')
                            ->setFrom($from[0], $from[1] ?? substr($from[0], 0, strpos($from[0], '@')))
                            ->setTo($to[0], $to[1] ?? substr($to[0], 0, strpos($to[0], '@')))
                            ->setBody($message['body'] ?? '<h5>empty body</h5>', 'text/html');
                        if ($sender->send($email)) {
                            $count++;
                            $ok[] = $to[0];
                        }
                    }
                }
            }
        }
        if ($ok) {
            echo implode('; ', $ok)."\n";
        }
        echo sprintf("Gửi thành công %s email!\n", $count);
    }
}
