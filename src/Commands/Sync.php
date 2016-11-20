<?php

namespace Rhinodontypicus\EloquentSpreadsheets\Commands;

use Illuminate\Console\Command;
use Rhinodontypicus\EloquentSpreadsheets\SpreadsheetService;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloquent-spreadsheets:sync {modelType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sheet with site';

    private $config;
    private $startColumn;
    private $endColumn;
    private $idColumn;

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

        if (empty(config('laravel-eloquent-spreadsheets')['sync_models'][$modelType])) {
            $this->warn('Model not registered in config');

            return;
        }

        $this->prepareConfig($modelType);
        $model = new $modelType;

        $range = "{$this->config['list_name']}!{$this->startColumn}2:{$this->endColumn}";
        $response = app(SpreadsheetService::class)->service()->spreadsheets_values->get(
            $this->config['spreadsheet_id'],
            $range
        );
        $values = $response->getValues();

        if (empty($values)) {
            $this->warn('Sheet is empty');

            return;
        }

        foreach ($values as $value) {
            if (
                ! is_array($value) ||
                empty($value[getLettersToIdsTable($this->startColumn, $this->endColumn)[$this->idColumn]])
            ) {
                continue;
            }

            $id = $value[getLettersToIdsTable($this->startColumn, $this->endColumn)[$this->idColumn]];
            $modelItem = $model->whereId($id)->first();

            if (! $modelItem) {
                continue;
            }

            $this->updateModel($modelItem, $value);
        }
    }

    private function updateModel($modelItem, $value)
    {
        foreach ($this->config['sync_attributes'] as $attributeKey => $attributeColumn) {
            $id = getLettersToIdsTable($this->startColumn, $this->endColumn)[$attributeColumn];

            if (empty($value[$id])) {
                continue;
            }

            $modelItem->{$attributeKey} = $value[$id];
        }

        if (empty($modelItem->getDirty())) {
            return;
        }

        $modelItem->removeObservableEvents(['updated']);
        $modelItem->save();
        $modelItem->addObservableEvents(['updated']);

        $this->info("Model updated: {$modelItem->id}");
    }

    private function prepareConfig($modelType)
    {
        $this->config = config('laravel-eloquent-spreadsheets')['sync_models'][$modelType];

        asort($this->config['sync_attributes']);
        $this->startColumn = array_first($this->config['sync_attributes']);
        $this->endColumn = array_last($this->config['sync_attributes']);
        $this->idColumn = $this->config['sync_attributes']['id'];
    }
}
