<?php

namespace FormEntries\Tests;

use FormEntries\Facades\FormEntryManager;
use FormEntries\Tests\Fixtures\Http\Controllers\TestPublicFormController;
use FormEntries\Tests\Fixtures\Http\Controllers\TraceRequestController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    protected function getPackageProviders($app)
    {
        return [
            \FormEntries\ServiceProvider::class,
        ];
    }

    protected function defineWebRoutes($router)
    {
        $router->get('testing/trace-request', TraceRequestController::class);
        $router->post('testing/public-form', TestPublicFormController::class);
        FormEntryManager::routes();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Register laravel migrations (users table) so RefreshDatabase runs them,
        // testbench 9+ executes "migrate:fresh" what drops tables created via loadLaravelMigrations().
        $app->afterResolving('migrator', fn ($migrator) => $migrator->path($app->basePath('migrations')));

        // $app['config']->set('forms-entries.some_key', 'some_value');
    }
}
