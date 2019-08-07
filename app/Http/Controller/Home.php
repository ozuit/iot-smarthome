<?php
namespace App\Http\Controller;

use Pho\Http\Controller;
use Symfony\Component\HttpFoundation\Response;

class Home extends Controller
{
    public function index()
    {
        return $this->json(['name' => 'Smarthome IoT API']);
    }
}
