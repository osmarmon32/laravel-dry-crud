<?php

namespace Reddireccion\DryCrud\Support\Facades;

use \Facade;
use Reddireccion\DryCrud\Validation\Validator as ReddireccionValidator;

class Validator extends Facade;
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ReddireccionValidator::class;
    }
}