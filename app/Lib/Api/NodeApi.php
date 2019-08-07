<?php

namespace App\Lib\Api;

use App\Model\Node;

class NodeApi extends BaseApi
{
    protected $route = 'api_v1_node_find';

    protected $include_variables = [
        'room',
    ];

    protected function transform(Node $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'topic' => $model->topic,
            'room_id' => $model->room_id,
            'active' => $model->active,
        ];
    }

    protected function includeRoom(Node $node)
    {
        return $this->get(RoomApi::class)->render($node->room);
    }
}
