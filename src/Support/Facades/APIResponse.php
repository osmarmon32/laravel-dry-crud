<?php

namespace Reddireccion\DryCrud\Support\Facades;

use \Illuminate\Support\Facades\Facade;
use \Reddireccion\DryCrud\Routing\ResponseFactory;

class APIResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ResponseFactory::class;
    }
}