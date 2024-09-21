<?php

namespace Nextvikas\Authenticator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class MinifyServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->registerPublishables();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
    }

    protected function registerPublishables()
    {
        $this->publishes([
            __DIR__.'/../config/authenticator.php' => config_path('authenticator.php'),
        ], 'config');
    }

    public function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/authenticator.php', 'authenticator.php');
    }
}
