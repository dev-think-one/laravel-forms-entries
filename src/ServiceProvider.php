<?php

namespace FormEntries;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/forms-entries.php' => config_path('forms-entries.php'),
            ], 'config');

            $this->commands([
            ]);

            $this->registerMigrations();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/forms-entries.php', 'forms-entries');
    }


    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (FormEntryManager::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
