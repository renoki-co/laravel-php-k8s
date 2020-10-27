<?php

namespace RenokiCo\LaravelK8s;

use Illuminate\Support\Facades\Facade;

class LaravelK8sFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel.k8s';
    }
}
