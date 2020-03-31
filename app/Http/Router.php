<?php
namespace App\Http;

use Pho\Routing\RouteLoader;
use Pho\Routing\Routing;
use App\Http\Middleware\ApiMiddleware;
use App\Http\Middleware\InternalMiddleware;

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
        $routing->post('/api/v1/user/login', $r->to('User', 'login'), 'api_v1_user_login');
        $routing->put('/api/v1/user/resetpass', $r->to('User', 'resetpass'), 'api_v1_user_resetpass');
        $routing->put('/api/{internal_token}/node/update', $r->to('Node', 'ifttt'), 'api_v1_ifttt_put');
        $routing->put('/api/{internal_token}/iot-agent/turn-off-all', $r->to('Node', 'turnOffAll'), 'api_v1_agentall_put');
        $routing->put('/api/{internal_token}/iot-agent/update', $r->to('Node', 'agentput'), 'api_v1_agentput_put');
        $routing->put('/api/{internal_token}/iot-agent/sleep-mode', $r->to('Node', 'sleepMode'), 'api_v1_sleep_put');
        $routing->put('/api/{internal_token}/iot-agent/movie-mode', $r->to('Node', 'movieMode'), 'api_v1_movie_put');
        $routing->put('/api/{internal_token}/iot-agent/book-mode', $r->to('Node', 'bookMode'), 'api_v1_book_put');
        $routing->get('/api/{internal_token}/iot-agent/info', $r->to('Node', 'agentget'), 'api_v1_agentget_put');

        $routing->group('/api/v1', function ($group) use ($r) {
            $group->get('/me', $r->to('User', 'me'), 'api_v1_user_me');

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
            $group->delete('/room/{id}', $r->to('Room', 'api'), 'api_v1_room_delete');
            
            $group->get('/node', $r->to('Node', 'api'), 'api_v1_node_get');
            $group->get('/node/{id}', $r->to('Node', 'api'), 'api_v1_node_find');
            $group->post('/node', $r->to('Node', 'api'), 'api_v1_node_post');
            $group->put('/node/turn-off-all', $r->to('Node', 'turnOffAll'), 'api_v1_turn_off_all');
            $group->put('/node/sleep-mode', $r->to('Node', 'sleepMode'), 'api_v1_sleep_mode');
            $group->put('/node/movie-mode', $r->to('Node', 'movieMode'), 'api_v1_movie_mode');
            $group->put('/node/book-mode', $r->to('Node', 'bookMode'), 'api_v1_book_mode');
            $group->put('/node/update/{id}', $r->to('Node', 'update'), 'api_v1_node_update');
            $group->put('/node/{id}', $r->to('Node', 'api'), 'api_v1_node_put');
            $group->delete('/node/{id}', $r->to('Node', 'api'), 'api_v1_node_delete');
            
            $group->get('/data', $r->to('Data', 'api'), 'api_v1_data_get');
            $group->get('/data/temp', $r->to('Data', 'temp'), 'api_v1_data_temp_get');
            $group->get('/data/hum', $r->to('Data', 'hum'), 'api_v1_data_hum_get');
            $group->get('/data/{id}', $r->to('Data', 'api'), 'api_v1_data_find');
            $group->post('/data', $r->to('Data', 'api'), 'api_v1_data_post');
            $group->put('/data/{id}', $r->to('Data', 'api'), 'api_v1_data_put');

            $group->get('/dashboard/data', $r->to('Dashboard', 'data'), 'api_v1_dashboard_data_get');
            
            $group->get('/setting', $r->to('Setting', 'api'), 'api_v1_setting_get');
            $group->put('/setting/{id}', $r->to('Setting', 'api'), 'api_v1_setting_put');
        }, [
            '_before' => [
                ApiMiddleware::class
            ],
        ]);
    }
}
