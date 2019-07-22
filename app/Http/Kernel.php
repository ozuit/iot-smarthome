<?php
namespace App\Http;

use Pho\Http\Kernel as PhoKernel;
use App\Subscribe\CorsSubscribe;

class Kernel extends PhoKernel
{
    public function stacks()
    {
    }

    public function events()
    {
        $this->subscribe(CorsSubscribe::class);
    }
}
