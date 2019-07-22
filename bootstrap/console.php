<?php

use function DI\autowire;
use Pho\ServiceProvider\ConsoleServiceProvider;
use Pho\Console\ConsoleKernel;
use App\Console\Kernel;

$app->register(new ConsoleServiceProvider(), [
    ConsoleKernel::class => autowire(Kernel::class)->method('commands'),
]);
