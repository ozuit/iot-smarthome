<?php

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('ROOT_PATH') or define('ROOT_PATH', realpath(dirname(__DIR__)));

if (!function_exists('app_path')) {
    function app_path(string $path) : string
    {
        return rtrim(ROOT_PATH.DS.'app'.DS.ltrim($path, '/'), '/');
    }
}
if (!function_exists('storage_path')) {
    function storage_path(string $path) : string
    {
        return rtrim(ROOT_PATH.DS.'storage'.DS.ltrim($path, '/'), '/');
    }
}
if (!function_exists('resources_path')) {
    function resources_path(string $path) : string
    {
        return rtrim(ROOT_PATH.DS.'resources'.DS.ltrim($path, '/'), '/');
    }
}
if (!function_exists('public_path')) {
    function public_path(string $path) : string
    {
        return rtrim(ROOT_PATH.DS.'public'.DS.ltrim($path, '/'), '/');
    }
}
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        static $variables;

        if ($variables === null) {
            $variables = (new Dotenv\Environment\DotenvFactory([
                new Dotenv\Environment\Adapter\EnvConstAdapter,
                new Dotenv\Environment\Adapter\ServerConstAdapter
            ]))->createImmutable();
        }

        return PhpOption\Option::fromValue($variables->get($key))
            ->map(function ($value) {
                switch (strtolower($value)) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return;
                }

                if (preg_match('/([\'"])(.*)\1/', $value, $matches)) {
                    return $matches[2];
                }

                return $value;
            })
            ->getOrCall(function () use ($default) {
                return value($default);
            });
    }
}

if (function_exists('c')) {
    throw new Exception('function "c" is already existed !');
} else {
    function c(string $key = null) {
        global $pho_container;
        return is_null($key) ? $pho_container : $pho_container->get($key);
    }
}

if (!function_exists('now')) {
    function now() {
        return new Carbon\Carbon();
    }
}