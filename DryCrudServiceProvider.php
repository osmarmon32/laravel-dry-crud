<?php

namespace Reddireccion\DryCrud;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Reddireccion\DryCrud\Routing\ResponseFactory;
use Reddireccion\DryCrud\Routing\IResponse;

class DryCrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('APIResponse', function ($app) {
            return new ResponseFactory($app[ViewFactoryContract::class], $app['redirect']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
    }
}
