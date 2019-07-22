<?php
namespace App\Console;

use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand
{
    public function __invoke($name, OutputInterface $output)
    {
        $output->writeln("Hello ".$name);
    }
}
