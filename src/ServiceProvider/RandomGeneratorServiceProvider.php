<?php

namespace Saacsos\Randomgenerator\ServiceProvider;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class RandomGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        App::bind('randnerator', function()
        {
            return new \Saacsos\Randomgenerator\Util\RandomGenerator;
        });
    }
}