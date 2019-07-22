<?php

namespace App\Lib\Pagination;

class DefaultViewFactory extends Factory
{
    public function make($view, $data = [], $mergeData = [])
    {
        return new DefaultView($data['paginator'] ?? null);
    }
}