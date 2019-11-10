<?php

namespace Reddireccion\DryCrud\Util;

use \Route;

class DryHelper
{
    /**
     * Return the model name corresponding to a give controller
     *
     * @param  string $namespace for the model
     * @param  string $controllerName optional, if none given will get it from request
     * @return string
     */
    public  static function modelClassNameFromController($namespace,$controllerName=''){
        if(!$controllerName){
            $controllerName=self::currentController();
        }
        return  $namespace.str_replace( 'Controller','',$controllerName);
    }
    /**
     * Get the controller name from the request route
     *
     * @return string
     */
    public static function currentController()
    {
        $route = Route::getCurrentRoute()->getAction();
        $start = strrpos($route['controller'],'\\')+1;
        $end = strpos($route['controller'], '@');
        return substr($route['controller'], $start, $end - $start);
    }
}