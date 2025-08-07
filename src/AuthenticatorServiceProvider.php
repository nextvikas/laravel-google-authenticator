<?php

namespace Nextvikas\Authenticator;

use Nextvikas\Authenticator\Middleware\TwoStepAuthenticator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AuthenticatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'authenticator');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app['router']->aliasMiddleware('authenticator', TwoStepAuthenticator::class);

        $this->registerPublishables();
    }

    public function register()
    {
        $this->registerConfig();
    }

    protected function registerPublishables()
    {
        $this->publishes([
            __DIR__.'/../config/authenticator.php' => config_path('authenticator.php'),
        ], 'authenticator-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/authenticator'),
        ], 'authenticator-views');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'authenticator-migrations');
    }

    public function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/authenticator.php', 'authenticator');
    }
}