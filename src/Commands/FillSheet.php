<?php

namespace Rhinodontypicus\EloquentSpreadsheets\Commands;

use Illuminate\Console\Command;
use Rhinodontypicus\EloquentSpreadsheets\ModelObserver;

class FillSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloquent-spreadsheets:fill {modelType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill sheet with models';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelType = $this->argument('modelType');

        if (empty(config('laravel-eloquent-spreadsheets')["sync_models"][$modelType])) {
            $this->warn('Model not registered in config');
            return;
        }

        $model = new $modelType;

        foreach ($model->all() as $modelItem) {
            (new ModelObserver())->updated($modelItem);
            $this->info("Dispatched model with ID: {$modelItem->id}");
        }

        $this->info("Dispatching completed");
    }
}
