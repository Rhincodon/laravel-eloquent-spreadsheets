<?php

namespace Rhinodontypicus\EloquentSpreadsheets\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Rhinodontypicus\EloquentSpreadsheets\Jobs\Traits\Saveable;
use Rhinodontypicus\EloquentSpreadsheets\SpreadsheetService;

class CreateModelInSpreadsheet implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Saveable;

    /**
     * @var
     */
    private $model;

    /**
     * @var
     */
    private $config;

    /**
     * Create a new job instance.
     * @param $model
     * @param $config
     */
    public function __construct($model, $config)
    {
        $this->model = $model;
        $this->config = $config;
        $this->prepareConfig();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rowToInsert = $this->getRowToInsert();
        $this->insertModelToSheet($rowToInsert);
    }

    /**
     * @return int
     */
    private function getRowToInsert()
    {
        $idColumn = $this->config['sync_attributes']['id'];
        $range = "{$this->config['list_name']}!{$idColumn}2:$idColumn";
        $response = app(SpreadsheetService::class)->service()->spreadsheets_values->get(
            $this->config['spreadsheet_id'],
            $range
        );

        $values = $response->getValues();

        return count($values) + 2;
    }
}
