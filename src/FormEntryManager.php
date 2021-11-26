<?php

namespace FormEntries;

use Illuminate\Support\Facades\Route;

class FormEntryManager
{
    /**
     * Indicates if laravel should run migrations for package.
     *
     * @var bool
     */
    public static bool $runsMigrations = true;

    /**
     * Configure laravel to not register current package migrations.
     *
     * @return static
     */
    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    public function routes(): static
    {
        Route::post(
            config('forms-entries.routing.path'),
            \FormEntries\Http\Controllers\SendFormEntryController::class
        )->name('forms-entries.submit');

        return $this;
    }
}
