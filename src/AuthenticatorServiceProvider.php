<?php

namespace Nextvikas\Authenticator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class AuthenticatorServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false; // Specifies whether the provider's loading is deferred (false means it's not).

    /**
     * Bootstrap the application events.
     * This method is called after all other service providers have been registered, and it is meant
     * to handle events, load routes, views, migrations, and middleware.
     */
    public function boot()
    {
        // Load routes from the specified file.
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
        // Load views from the specified directory and assign a namespace for easy reference.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'authenticator');
        
        // Load the migrations from the specified directory.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        // Register a custom middleware alias called 'authenticator' which maps to the TwoStepAuthenticator class.
        $this->app['router']->aliasMiddleware('authenticator', \Nextvikas\Authenticator\Middleware\TwoStepAuthenticator::class);
        
        // Call the method that registers publishable files (config, migrations).
        $this->registerPublishables();
    }

    /**
     * Register the service provider.
     * This method is used to bind classes or services into the service container.
     */
    public function register()
    {
        // Register configuration settings.
        $this->registerConfig();
    }

    /**
     * Handle publishable resources such as configuration files and database migrations.
     */
    protected function registerPublishables()
    {
        // Publishes the config file, allowing the user to override it by copying it to the application's config directory.
        $this->publishes([
            __DIR__.'/../config/authenticator.php' => config_path('authenticator.php'),
        ], 'config');

        // Uncomment the following block to allow publishing migrations.
        // This publishes migrations to the application's database/migrations directory.
        // $this->publishes([
        //     __DIR__.'/../database/migrations/' => database_path('migrations'),
        // ], 'migrations');
    }

    /**
     * Register the configuration file for the package.
     * This merges the package's configuration file with the application's configuration.
     */
    public function registerConfig()
    {
        // Merge the default configuration file for 'authenticator' with the application's existing config.
        $this->mergeConfigFrom(__DIR__.'/../config/authenticator.php', 'authenticator');
    }
}
