<?php

namespace App\Lib\Pagination;

use Illuminate\Contracts\View\View as BaseView;

class View implements BaseView
{
    public function name()
    {
        return '';
    }

    public function with($key, $value = null)
    {
        return $this;
    }

    public function render()
    {
        return '';
    }
}