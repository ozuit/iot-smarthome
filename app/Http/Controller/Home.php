<?php
namespace App\Http\Controller;

use Pho\Http\Controller;

class Home extends Controller
{
    public function index()
    {
        return $this->json(['name' => 'Phuong Nam Digital API']);
    }
}
