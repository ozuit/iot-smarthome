<?php
namespace App\Http\Controller;

use Pho\Http\Controller;
use Symfony\Component\HttpFoundation\Response;

class Home extends Controller
{
    public function index()
    {
        return $this->json(['name' => 'Phuong Nam Digital API']);
    }

    public function text2speech()
    {
        $message = urlencode('Chúc mọi người ngủ ngon');
        $stream_mp3 = file_get_contents("https://translate.google.com/translate_tts?ie=UTF-8&q=$message&tl=vi&client=tw-ob");
        // file_put_contents('test.mp3', $stream_mp3);

        return new Response($stream_mp3, 200, [
            'Content-Type' => 'audio/mpeg',
        ]);
    }
}
