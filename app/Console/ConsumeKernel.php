<?php
namespace App\Console;

use Pho\Console\ConsoleKernel;

class ConsumeKernel extends ConsoleKernel
{
    public function commands()
    {
        $this->command('bernard:consume [queue]', ConsumeCommand::class);
    }
}
