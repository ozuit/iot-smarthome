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
            $group->delete('/user/{id}', $r->to('User', 'api'), 'api_v1_user_delete');
            $group->post('/user/logout', $r->to('User', 'logout'), 'api_v1_user_logout');

            $group->get('/room', $r->to('Room', 'api'), 'api_v1_room_get');
            $group->get('/room/{id}', $r->to('Room', 'api'), 'api_v1_room_find');
            $group->post('/room', $r->to('Room', 'api'), 'api_v1_room_post');
            $group->put('/room/{id}', $r->to('Room', 'api'), 'api_v1_room_put');
            
            $group->get('/sensor', $r->to('Sensor', 'api'), 'api_v1_sensor_get');
            $group->get('/sensor/{id}', $r->to('Sensor', 'api'), 'api_v1_sensor_find');
            $group->post('/sensor', $r->to('Sensor', 'api'), 'api_v1_sensor_post');
            $group->put('/sensor/{id}', $r->to('Sensor', 'api'), 'api_v1_sensor_put');
            
            $group->get('/data', $r->to('Data', 'api'), 'api_v1_data_get');
            $group->get('/data/{id}', $r->to('Data', 'api'), 'api_v1_data_find');
            $group->post('/data', $r->to('Data', 'api'), 'api_v1_data_post');
            $group->put('/data/{id}', $r->to('Data', 'api'), 'api_v1_data_put');
        }, [
            '_before' => [
                ApiMiddleware::class
            ],
        ]);
    }
}
