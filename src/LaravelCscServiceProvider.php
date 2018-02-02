<?php

namespace Lemberg\LaravelCsc;

use Illuminate\Support\ServiceProvider;
use Lemberg\LaravelCsc\Console\CodeStyleCommand;

class LaravelCscServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [__DIR__ . '/config/config.php' => config_path('code-style.php')],
            'config'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.code-style', function () {
            return new CodeStyleCommand;
        });

        $this->commands(['command.code-style']);

        $this->mergeConfigFrom(
            __DIR__ . '/config/config.php',
            'code-style'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.code-style'];
    }
}
