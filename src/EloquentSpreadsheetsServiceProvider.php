<?php

namespace Rhinodontypicus\EloquentSpreadsheets;

use Illuminate\Support\ServiceProvider;
use Rhinodontypicus\EloquentSpreadsheets\Commands\FillSheet;
use Rhinodontypicus\EloquentSpreadsheets\Commands\Sync;

class EloquentSpreadsheetsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-eloquent-spreadsheets.php' => config_path('laravel-eloquent-spreadsheets.php'),
        ], 'config');

        $this->bootObservers();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-eloquent-spreadsheets.php', 'laravel-eloquent-spreadsheets'
        );

        $this->app->singleton(SpreadsheetService::class, function() {
            $client = (new SpreadsheetClient(config('laravel-eloquent-spreadsheets.credentials_path')))->client();

            return new SpreadsheetService($client);
        });

        $this->app->bind('command.eloquent-spreadsheets:fill', FillSheet::class);
        $this->app->bind('command.eloquent-spreadsheets:sync', Sync::class);
        $this->commands([
            'command.eloquent-spreadsheets:fill',
            'command.eloquent-spreadsheets:sync',
        ]);
    }

    /**
     * @return bool
     */
    private function bootObservers()
    {
        $models = config('laravel-eloquent-spreadsheets.sync_models');

        if (empty($models)) {
            return false;
        }

        foreach ($models as $modelClass => $modelConfig) {
            $modelClass::observe(new ModelObserver());
        }

        return true;
    }
}
