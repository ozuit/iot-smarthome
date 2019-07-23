<?php
namespace App\Http;

use Pho\Routing\RouteLoader;
use Pho\Routing\Routing;
use App\Http\Middleware\ApiMiddleware;

class Router extends RouteLoader
{
    private function to($controller, $method)
    {
        return '\\App\\Http\Controller\\'.$controller.'::'.$method;
    }

    public function routes(Routing $routing)
    {
        $r = $this;

        $routing->get('/', $this->to('Home', 'index'), 'home');
        $routing->get('/text2speech', $this->to('Home', 'text2speech'), 'text2speech');
        $routing->post('/api/v1/user/login', $r->to('User', 'login'), 'api_v1_user_login');
        $routing->put('/api/v1/user/resetpass', $r->to('User', 'resetpass'), 'api_v1_user_resetpass');

        $routing->group('/api/v1', function ($group) use ($r) {
            $group->get('/user', $r->to('User', 'api'), 'api_v1_user_get');
            $group->put('/user/change', $r->to('User', 'change'), 'api_v1_user_change');
            $group->get('/user/{id}', $r->to('User', 'api'), 'api_v1_user_find');
            $group->post('/user', $r->to('User', 'api'), 'api_v1_user_post');
            $group->put('/user/{id}', $r->to('User', 'api'), 'api_v1_user_put');
            $group->post('/user/logout', $r->to('User', 'logout'), 'api_v1_user_logout');
        }, [
            '_before' => [
                ApiMiddleware::class
            ],
        ]);
    }
}
