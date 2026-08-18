<?php namespace Qdev\LaravelMinify\Facades;

use Illuminate\Support\Facades\Facade;

class MinifyFacade extends Facade
{
    /**
     * Name of the binding in the IoC container
     */
    protected static function getFacadeAccessor(): string
    {
        return 'minify';
    }
}
