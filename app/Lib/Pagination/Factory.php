<?php

namespace App\Lib\Pagination;

use Illuminate\Contracts\View\Factory as BaseFactory;

class Factory implements BaseFactory
{
    public function exists($view)
    {
        return true;
    }

    public function file($path, $data = [], $mergeData = [])
    {
        return new View();
    }

    public function make($view, $data = [], $mergeData = [])
    {
        return new View();
    }

    public function share($key, $value = null)
    {
        return null;
    }

    public function composer($views, $callback)
    {
        return [];
    }

    public function creator($views, $callback)
    {
        return [];
    }

    public function addNamespace($namespace, $hints)
    {
        return $this;
    }

    public function replaceNamespace($namespace, $hints)
    {
        return $this;
    }
}